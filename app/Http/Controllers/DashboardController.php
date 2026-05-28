<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transfer;
use App\Models\PurchaseRequest;
use App\Models\Product;
use App\Enums\StatusEnum;
use App\Models\Movement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('Bodega')) {
            return $this->bodegaDashboard();
        }

        if ($user->hasRole('Administrador Restaurante')) {
            return $this->restaurantDashboard($user);
        }

        if ($user->hasRole('Auditor')) {
            return $this->auditDashboard();
        }

        // Si por alguna razón un usuario no tiene rol:
        abort(403, 'Tu cuenta no tiene un panel de inicio asignado. Contacta a soporte.');
    }

    // --- MÉTODOS PRIVADOS DE AISLAMIENTO DE DATOS ---

    private function adminDashboard()
    {
        // 1. KPIs: Los cuellos de botella del Administrador
        $pendingTransfersCount = Transfer::where('status', StatusEnum::PENDING)->count();
        $pendingPurchasesCount = PurchaseRequest::where('status', StatusEnum::PENDING)->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'stock_minimun')->count();

        // 2. Listas de Acción: Lo más viejo primero (FIFO - First In, First Out)
        $pendingTransfersList = Transfer::with(['requestingUser', 'destinationBranch'])
            ->where('status', StatusEnum::PENDING)
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        $pendingPurchasesList = PurchaseRequest::with('requestingUser')
            ->where('status', StatusEnum::PENDING)
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        $data = [
            'pending_transfers_count' => $pendingTransfersCount,
            'pending_purchases_count' => $pendingPurchasesCount,
            'low_stock_count'         => $lowStockCount,
            'pending_transfers_list'  => $pendingTransfersList,
            'pending_purchases_list'  => $pendingPurchasesList,
        ];

        return view('pages.dashboard.admin', compact('data'));
    }

    private function bodegaDashboard()
    {
        $user = Auth::user();

        // 1. KPI: Transferencias que la bodega DEBE empacar y enviar hoy
        $pendingShipments = Transfer::where('originating_branch', $user->office_id)
            ->whereIn('status', [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])
            ->count();

        // 2. KPI: Compras que la bodega DEBE salir a hacer al proveedor hoy
        $pendingPurchases = PurchaseRequest::where('status', StatusEnum::APPROVED)
            ->count();

        // 3. Listas de Acción (Para que ejecuten desde el dashboard sin navegar)
        $shipmentsList = Transfer::with('destinationBranch')
            ->where('originating_branch', $user->office_id)
            ->whereIn('status', [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $purchasesList = PurchaseRequest::with('requestingUser')
            ->where('status', StatusEnum::APPROVED)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $data = [
            'pending_shipments' => $pendingShipments,
            'pending_purchases' => $pendingPurchases,
            'shipments_list'    => $shipmentsList,
            'purchases_list'    => $purchasesList,
        ];

        return view('pages.dashboard.bodega', compact('data'));
    }

    private function restaurantDashboard($user)
    {
        // 1. KPI: Transferencias Activas (Lo que pedí y estoy esperando)
        $activeTransfers = Transfer::where('requesting_user', $user->id)
            ->whereNotIn('status', [StatusEnum::RECEIVED, StatusEnum::REJECTED])
            ->count();

        // 2. KPI: Compras en Proceso (Lo que pedí que compraran)
        $activePurchases = PurchaseRequest::where('requesting_user_id', $user->id)
            ->whereNotIn('status', [StatusEnum::PARTIALLY_APPROVED, StatusEnum::REJECTED])
            ->count();

        // 3. KPI: Alertas de Stock (Productos críticos globales)
        $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_minimun')->take(5)->get();
        $lowStockCount = Product::whereColumn('stock', '<=', 'stock_minimun')->count();

        // 4. Tablas de actividad reciente (Para que no tenga que ir al módulo a cada rato)
        $recentTransfers = Transfer::with('destinationBranch')
            ->where('requesting_user', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $data = [
            'active_transfers' => $activeTransfers,
            'active_purchases' => $activePurchases,
            'low_stock_count'  => $lowStockCount,
            'low_stock_list'   => $lowStockProducts,
            'recent_transfers' => $recentTransfers,
        ];

        return view('pages.dashboard.restaurant', compact('data'));
    }


    private function auditDashboard()
    {
        // 1. KPI: Volumen de operaciones del mes (Para medir la carga transaccional)
        $currentMonthMovements = Movement::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 2. KPI de Riesgo: Ajustes Manuales (type_id = 4). Aquí ocurren las mermas y robos.
        $monthlyAdjustments = Movement::where('type_id', 4)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 3. KPI: Salud del Inventario
        $criticalStockCount = Product::whereColumn('stock', '<=', 'stock_minimun')->count();

        // 4. Tabla de Vigilancia: Los últimos ajustes manuales realizados
        $recentAdjustments = Movement::with(['user', 'office'])
            ->where('type_id', 4)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $data = [
            'current_month_movements' => $currentMonthMovements,
            'monthly_adjustments'     => $monthlyAdjustments,
            'critical_stock_count'    => $criticalStockCount,
            'recent_adjustments'      => $recentAdjustments,
        ];

        return view('pages.dashboard.audit', compact('data'));
    }
}
