<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuyRequest;
use App\Http\Requests\UpdateBuyRequest;
use App\Models\Buy;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Office;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuyController extends Controller
{
    public function index(Request $request)
    {
        $buys = Buy::with(['supplier', 'user', 'office'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', fn($q) => $q->where('company_name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('office', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
            })
            ->orderBy('date', 'desc')
            ->paginate(15)->withQueryString();

        return view('pages.buys.index', compact('buys'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('pages.buys.create', compact('suppliers', 'products'));
    }

    public function store(StoreBuyRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = $request->user();
            $officeId = $user->office_id;
            $validated = $request->validated();

            $subtotal = collect($validated['products'])->sum(fn($item) => $item['quantity'] * $item['price']);
            $discount = $validated['discount'] ?? 0;
            
            $ivaRate = ($validated['iva_rate'] ?? 0) / 100;
            $subtotalAfterDiscount = $subtotal - $discount;
            $totalIva = $subtotalAfterDiscount * $ivaRate;
            $total = $subtotalAfterDiscount + $totalIva;

            $buy = Buy::create([
                'supplier_id' => $validated['supplier_id'],
                'date' => $validated['date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_iva' => $totalIva,
                'total' => $total,
                'user_id' => $user->id,
                'office_id' => $officeId,
                'is_cancelled' => false,
            ]);

            $supplierName = Supplier::find($validated['supplier_id'])->company_name;

            $movement = Movement::create([
                'office_id' => $officeId,
                'date_movement' => $validated['date'],
                'type_id' => $this->getPurchaseTypeId(),
                'user_id' => $user->id,
                'transaction_id' => 'COMPRA-' . $buy->id,
                'description' => "Compra a proveedor: $supplierName",
                'input_type' => 'E',
            ]);

            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $item['price'];

                PurchaseDetail::create([
                    'buy_id' => $buy->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $quantity * $price,
                ]);

                $product->increment('stock', $quantity);

                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $quantity * $price,
                    'stock_after' => $product->stock,
                ]);
            }
        });

        return redirect()->route('buys.index')->with('success', 'Compra registrada exitosamente.');
    }

    public function show(Buy $buy)
    {
        $buy->load(['supplier', 'user', 'office', 'details.product']);
        return view('pages.buys.show', compact('buy'));
    }

    public function edit(Buy $buy)
    {
        if (!auth()->user()->hasRole('Administrador')) abort(403);
        if ($buy->is_cancelled) return redirect()->route('buys.index')->with('error', 'No se puede editar una compra cancelada.');
        
        $suppliers = Supplier::all();
        $offices = Office::all();
        $products = Product::all();
        $buy->load('details');
        return view('pages.buys.edit', compact('buy', 'suppliers', 'offices', 'products'));
    }

    public function update(UpdateBuyRequest $request, Buy $buy)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $buy) {
            
            // 1. REVERSIÓN DEL STOCK VIEJO (Blindaje contable)
            foreach ($buy->details as $detail) {
                $product = Product::lockForUpdate()->find($detail->product_id);
                $product->decrement('stock', $detail->quantity);
            }
            $buy->details()->delete();

            // 2. CÁLCULO DE LOS NUEVOS TOTALES
            $subtotal = collect($validated['products'])->sum(fn($item) => $item['quantity'] * $item['price']);
            $discount = $validated['discount'] ?? 0;
            $ivaRate = ($validated['iva_rate'] ?? 0) / 100;
            $subtotalAfterDiscount = $subtotal - $discount;
            $totalIva = $subtotalAfterDiscount * $ivaRate;
            $total = $subtotalAfterDiscount + $totalIva;

            $buy->update([
                'supplier_id' => $validated['supplier_id'],
                'date' => $validated['date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_iva' => $totalIva,
                'total' => $total,
            ]);

            // 3. REGISTRO DEL MOVIMIENTO DE CORRECCIÓN
            $movement = Movement::create([
                'office_id' => $buy->office_id,
                'date_movement' => now(),
                'type_id' => $this->getCorrectionTypeId(),
                'user_id' => auth()->id(),
                'transaction_id' => 'CORRECCION-COMPRA-' . $buy->id . '-' . time(),
                'description' => 'Ajuste por edición de compra #' . $buy->id,
                'input_type' => 'E',
            ]);

            // 4. CREACIÓN DE NUEVOS DETALLES Y NUEVO STOCK
            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                $buy->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);

                $product->increment('stock', $item['quantity']);

                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'stock_after' => $product->stock,
                ]);
            }
        });

        return redirect()->route('buys.show', $buy)->with('success', 'Compra actualizada y stock reajustado correctamente.');
    }

    public function cancel(Buy $buy)
    {
        if (!auth()->user()->hasRole('Administrador')) abort(403, 'No autorizado.');
        if ($buy->is_cancelled) return redirect()->back()->with('error', 'La compra ya está cancelada.');

        $buy->load('details.product');

        DB::transaction(function () use ($buy) {
            foreach ($buy->details as $detail) {
                $detail->product->decrement('stock', $detail->quantity);
            }

            $movement = Movement::create([
                'office_id' => $buy->office_id,
                'date_movement' => now(),
                'type_id' => $this->getCancellationTypeId(),
                'user_id' => auth()->id(),
                'transaction_id' => 'ANULACION-COMPRA-' . $buy->id . '-' . time(),
                'description' => 'Anulación de compra #' . $buy->id,
                'input_type' => 'S',
            ]);

            foreach ($buy->details as $detail) {
                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->price,
                    'subtotal' => $detail->subtotal,
                    'stock_after' => $detail->product->stock,
                ]);
            }

            $buy->update(['is_cancelled' => 1]);
        });

        return redirect()->route('buys.index')->with('success', 'Compra cancelada y stock revertido.');
    }

    public function restore(Buy $buy)
    {
        // Igual que cancel pero incrementando
    }

    private function getPurchaseTypeId() { return Type::firstOrCreate(['name' => 'compra'], ['description' => 'Movimiento por compra'])->id; }
    private function getCancellationTypeId() { return Type::firstOrCreate(['name' => 'anulacion_compra'], ['description' => 'Movimiento por anulación de compra'])->id; }
    private function getRestorationTypeId() { return Type::firstOrCreate(['name' => 'restauracion_compra'], ['description' => 'Movimiento por restauración de compra'])->id; }
    private function getCorrectionTypeId() { return Type::firstOrCreate(['name' => 'correccion_compra'], ['description' => 'Ajuste por edición de compra'])->id; }
}