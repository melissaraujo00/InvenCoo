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
     * Display the specified resource.
     */
    public function show(Movement $movement)
{
    $movement->load(['office', 'type', 'user', 'originatingBranch', 'destinationBranch']);
    $details = $movement->details()->paginate(15); // Paginador separado

    return view('pages.movements.show', compact('movement', 'details'));
}


}
