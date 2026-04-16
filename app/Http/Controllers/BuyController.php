<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuyRequest;
use App\Http\Requests\UpdateBuyRequest;
use App\Models\Buy;
use App\Models\Movement;
use App\Models\MovementDetail; // Corregido: antes era MovementDatail
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
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('company_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('office', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            })
            ->orderBy('date', 'desc')
            ->paginate(15);

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

            $subtotal = 0;
            foreach ($request->products as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }
            $discount = $request->discount ?? 0;
            $total = $subtotal - $discount;

            $buy = Buy::create([
                'supplier_id' => $request->supplier_id ?? null,
                'date' => $request->date,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'total_iva' => 0,
                'user_id' => $user->id,
                'office_id' => $officeId,
                'is_cancelled' => false,
            ]);

            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
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
            }

            $supplierName = $request->supplier_id
                ? Supplier::find($request->supplier_id)->company_name
                : 'Proveedor no especificado';

            $movement = Movement::create([
                'office_id' => $officeId,
                'date_movement' => $request->date,
                'type_id' => $this->getPurchaseTypeId(),
                'user_id' => $user->id,
                'transaction_id' => 'COMPRA-' . $buy->id,
                'description' => "Compra" . ($request->supplier_id ? " a proveedor: $supplierName" : " sin proveedor registrado"),
                'input_type' => 'E',
                'origin_office_id' => null,
                'destination_office_id' => null,
            ]);

            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $item['price'];

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
        if (!auth()->user()->hasRole('Administrador')) {
            abort(403);
        }
        if ($buy->is_cancelled) {
            return redirect()->route('buys.index')->with('error', 'No se puede editar una compra cancelada.');
        }
        $suppliers = Supplier::all();
        $offices = Office::all();
        $products = Product::all();
        $buy->load('details'); // Importante para tener los detalles
        return view('pages.buys.edit', compact('buy', 'suppliers', 'offices', 'products'));
    }

    public function update(UpdateBuyRequest $request, Buy $buy)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $buy) {
            $subtotal = 0;
            foreach ($validated['products'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $discount = $validated['discount'] ?? 0;
            $totalIva = $subtotal * 0.16;
            $total = $subtotal - $discount + $totalIva;

            $buy->update([
                'supplier_id' => $validated['supplier_id'],
                'office_id' => $validated['office_id'],
                'date' => $validated['date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_iva' => $totalIva,
                'total' => $total,
            ]);

            $buy->details()->delete();
            foreach ($validated['products'] as $item) {
                $buy->details()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('buys.show', $buy)->with('success', 'Compra actualizada correctamente.');
    }

    public function cancel(Buy $buy)
{
    if (!auth()->user()->hasRole('Administrador')) {
        abort(403, 'No autorizado.');
    }

    if ($buy->is_cancelled) {
        return redirect()->back()->with('error', 'La compra ya está cancelada.');
    }

    $buy->load('details.product');

    DB::transaction(function () use ($buy) {
        // Revertir stock
        foreach ($buy->details as $detail) {
            $detail->product->decrement('stock', $detail->quantity);
        }

        // Generar transaction_id único con timestamp
        $uniqueSuffix = now()->format('YmdHis') . '_' . rand(100, 999);
        $transactionId = 'ANULACION-COMPRA-' . $buy->id . '-' . $uniqueSuffix;

        $movement = Movement::create([
            'office_id' => $buy->office_id,
            'date_movement' => now(),
            'type_id' => $this->getCancellationTypeId(),
            'user_id' => auth()->id(),
            'transaction_id' => $transactionId,
            'description' => 'Anulación de compra #' . $buy->id,
            'input_type' => 'S',
            'origin_office_id' => null,
            'destination_office_id' => null,
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
    if (!auth()->user()->hasRole('Administrador')) {
        abort(403, 'No autorizado.');
    }

    if (!$buy->is_cancelled) {
        return redirect()->back()->with('error', 'La compra no está cancelada.');
    }

    $buy->load('details.product');

    DB::transaction(function () use ($buy) {
        foreach ($buy->details as $detail) {
            $detail->product->increment('stock', $detail->quantity);
        }

        $uniqueSuffix = now()->format('YmdHis') . '_' . rand(100, 999);
        $transactionId = 'RESTAURACION-COMPRA-' . $buy->id . '-' . $uniqueSuffix;

        $movement = Movement::create([
            'office_id' => $buy->office_id,
            'date_movement' => now(),
            'type_id' => $this->getRestorationTypeId(),
            'user_id' => auth()->id(),
            'transaction_id' => $transactionId,
            'description' => 'Restauración de compra #' . $buy->id,
            'input_type' => 'E',
            'origin_office_id' => null,
            'destination_office_id' => null,
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

        $buy->update(['is_cancelled' => 0]);
    });

    return redirect()->route('buys.index')->with('success', 'Compra restaurada correctamente.');
}
    private function getPurchaseTypeId()
    {
        return Type::firstOrCreate(['name' => 'compra'], ['description' => 'Movimiento por compra'])->id;
    }

    private function getCancellationTypeId()
    {
        return Type::firstOrCreate(['name' => 'anulacion_compra'], ['description' => 'Movimiento por anulación de compra'])->id;
    }

    private function getRestorationTypeId()
    {
        return Type::firstOrCreate(['name' => 'restauracion_compra'], ['description' => 'Movimiento por restauración de compra cancelada'])->id;
    }
}
