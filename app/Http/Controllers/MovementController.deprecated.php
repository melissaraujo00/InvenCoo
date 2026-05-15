<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovementRequest;
use App\Models\Movement;
use App\Models\Office;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    // Calcular total de productos para cada movimiento
    foreach ($movements as $movement) {
        $movement->total_quantity = $movement->details->sum('quantity');
    }

    $types = Type::all();

    return view('pages.movements.index', compact('movements', 'types'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $products = Product::all();

        if ($user->hasRole('Administrador')) {
            $offices = Office::all();
        } else {
            $offices = Office::where('id', $user->office_id)->get();
        }

        $types = Type::all();

        return view('pages.movements.create', compact('products', 'offices', 'types'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request)
    {
        $user = $request->user();

        $validated = $request->validate(); // mismas reglas

         // Validar oficina
    if (!$user->hasRole('Administrador') && $validated['office_id'] != $user->office_id) {
        return back()->withErrors(['office_id' => 'No tienes permiso para registrar movimientos en esta oficina.']);
    }

    // Generar transaction_id automático
    $validated['transaction_id'] = $this->generateTransactionId();

    // Calcular stock
    $currentStock = $this->getCurrentStock($validated['product_id'], $validated['office_id']);
    if ($validated['input_type'] === 'E') {
        $newStock = $currentStock + $validated['amount'];
    } else {
        $newStock = $currentStock - $validated['amount'];
        if ($newStock < 0) {
            return back()->withErrors(['amount' => 'No hay suficiente stock para esta salida.']);
        }
    }

    $validated['stock_total'] = $newStock;
    $validated['user_id'] = $user->id;

    $movement = Movement::create($validated);

    return redirect()->route('movements.index')
        ->with('success', "Movimiento registrado. N° Transacción: {$movement->transaction_id}");
    }

    private function generateTransactionId()
{
    $today = now()->format('Ymd');
    $prefix = "MOV-{$today}-";

    // Buscar el último transaction_id con el prefijo de hoy
    $lastMovement = Movement::where('transaction_id', 'LIKE', $prefix . '%')
        ->orderBy('transaction_id', 'desc')
        ->first();

    if ($lastMovement) {
        $lastNumber = (int) substr($lastMovement->transaction_id, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '0001';
    }

    return $prefix . $newNumber;
}

    private function getCurrentStock($productId, $officeId)
    {
        // Obtener el último movimiento de ese producto en esa oficina
        $lastMovement = Movement::where('product_id', $productId)
            ->where('office_id', $officeId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastMovement ? $lastMovement->stock_total : 0;
    }
    /**
     * Display the specified resource.
     */
    public function show(Movement $movement)
{
    $movement->load(['office', 'type', 'user', 'originatingBranch', 'destinationBranch']);
    $details = $movement->details()->paginate(15); // Paginador separado

    return view('pages.movements.show', compact('movement', 'details'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movement $movement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movement $movement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
