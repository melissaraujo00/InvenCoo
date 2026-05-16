<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = Movement::with(['office', 'type', 'user', 'originatingBranch', 'destinationBranch', 'details'])
            ->when($request->filled('search'), function($query) use ($request) {
                $query->where('transaction_id', 'LIKE', "%{$request->search}%")
                      ->orWhere('description', 'LIKE', "%{$request->search}%");
            })
            ->when($request->filled('type_id'), function($query) use ($request) {
                $query->where('type_id', $request->type_id);
            })
            ->when($request->filled('input_type'), function($query) use ($request) {
                $query->where('input_type', $request->input_type);
            })
            ->orderBy('date_movement', 'desc')
            ->paginate(15);

        foreach ($movements as $movement) {
            $movement->total_quantity = $movement->details->sum('quantity');
        }

        $types = Type::all();

        return view('pages.movements.index', compact('movements', 'types'));
    }

    public function create()
    {
        $types = Type::all();
        $products = Product::all();
        
        return view('pages.movements.create', compact('types', 'products'));
    }

    public function store(StoreMovementRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $user) {
                $movement = Movement::create([
                    'office_id' => $user->office_id, 
                    'date_movement' => $validated['date_movement'],
                    'type_id' => $validated['type_id'],
                    'user_id' => $user->id,
                    'transaction_id' => $this->generateTransactionId($validated['input_type']),
                    'description' => $validated['description'],
                    'input_type' => $validated['input_type'],
                ]);

                foreach ($validated['products'] as $item) {
                    $qty = $item['quantity'];
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($validated['input_type'] === 'S') {
                        if ($qty > $product->stock) {
                            throw new \Exception("Stock insuficiente. Intentaste retirar {$qty} de '{$product->name}', pero solo hay {$product->stock} disponibles.");
                        }
                        $product->decrement('stock', $qty);
                    } else {
                        $product->increment('stock', $qty);
                    }

                    // Arrastre del costo promedio exacto
                    $lastInputDetail = MovementDetail::where('product_id', $product->id)
                        ->whereHas('movement', fn($q) => $q->where('input_type', 'E'))
                        ->orderBy('id', 'desc')->first();
                        
                    $cost = $lastInputDetail ? $lastInputDetail->unit_price : 0;

                    MovementDetail::create([
                        'movement_id' => $movement->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $cost,
                        'subtotal' => $cost * $qty,
                        'stock_after' => $product->stock,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('movements.index')->with('success', 'Ajuste de inventario procesado y guardado en el Kardex correctamente.');
    }

    public function show(Movement $movement)
    {
        $movement->load(['office', 'type', 'user', 'originatingBranch', 'destinationBranch', 'details.product']);
        return view('pages.movements.show', compact('movement'));
    }

    private function generateTransactionId($inputType)
    {
        $today = now()->format('Ymd');
        $prefix = "AJU-{$inputType}-{$today}-"; 

        $lastMovement = Movement::where('transaction_id', 'LIKE', $prefix . '%')
            ->orderBy('transaction_id', 'desc')
            ->first();

        $newNumber = $lastMovement ? str_pad(((int) substr($lastMovement->transaction_id, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';

        return $prefix . $newNumber;
    }
}