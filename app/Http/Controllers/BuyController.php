<?php

namespace App\Http\Controllers;

use App\Actions\Buys\CancelPurchaseAction;
use App\Actions\Buys\ProcessPurchaseAction;
use App\Actions\Buys\UpdatePurchaseAction;
use App\Http\Requests\StoreBuyRequest;
use App\Http\Requests\UpdateBuyRequest;
use App\Models\Buy;
use App\Models\Office;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Type;
use Illuminate\Http\Request;


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
        $offices = Office::all();
        return view('pages.buys.create', compact('suppliers', 'products', 'offices'));
    }

    public function store(StoreBuyRequest $request)
    {
        app(ProcessPurchaseAction::class)->execute(
            $request->validated(),
            $request->user()
        );

        return to_route('buys.index')->with('success', 'Compra procesada con éxito y Kardex actualizado.');
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
        app(UpdatePurchaseAction::class)->execute($buy, $request->validated());

        return redirect()->route('buys.show', $buy)->with('success', 'Compra actualizada.');
    }

    public function cancel(Buy $buy)
    {
        if (!auth()->user()->hasRole('Administrador')) abort(403);
        if ($buy->is_cancelled) return back()->with('error', 'La compra ya está cancelada.');

        app(CancelPurchaseAction::class)->execute($buy);

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
