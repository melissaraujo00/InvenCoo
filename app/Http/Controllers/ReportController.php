<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transfer;
use App\Models\Product;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 1. Muestra la pantalla del Centro de Reportes
    public function index()
    {
        return view('pages.reports.index');
    }

    // 2. Genera el PDF de Movimientos
    public function exportMovements(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();

        $typeId = $request->input('type_id');

        // CAMBIO 1: Filtramos por 'created_at' para asegurar que agarre las fechas reales de creación
        // CAMBIA ESTO:
       $query = Movement::with(['user', 'originatingBranch', 'destinationBranch', 'office', 'type'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();


        // (El resto del código del PDF no se ejecutará hasta que borremos el dd)
        $logoPath = public_path('images/logo.png');
        $base64Logo = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.reports.templates.movements', compact('movements', 'base64Logo', 'startDate', 'endDate'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download("Movimientos_" . $startDate->format('dmY') . "_al_" . $endDate->format('dmY') . ".pdf");
    }

    // 3. Genera el PDF de Productos (Inventario)
    public function exportProducts()
    {
        $products = Product::orderBy('name', 'asc')->get();

        $logoPath = public_path('images/logo.png');
        $base64Logo = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('pages.reports.templates.products', compact('products', 'base64Logo'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Inventario_Global_" . now()->format('dmY') . ".pdf");
    }

    
}
