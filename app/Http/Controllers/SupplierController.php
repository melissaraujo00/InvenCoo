<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        // Se agregó number_phone al select
        $suppliers = Supplier::select('id', 'company_name', 'contact_name', 'number_phone', 'description')
            ->when(
                $request->filled('search'),
                fn($query) =>
                $query->where('company_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('contact_name', 'LIKE', "%{$request->search}%")
            )
            ->paginate(20)->withQueryString();

        return view('pages.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('pages.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::query()->create($request->validated());

        return to_route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Supplier $supplier)
    {
        //
    }

    public function edit(Supplier $supplier)
    {
        return view('pages.suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return to_route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return to_route('suppliers.index')
                ->with('success', 'Proveedor eliminado exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return to_route('suppliers.index')
                    ->with('error', 'No puedes eliminar este proveedor porque ya tiene productos vinculados.');
            }
            throw $e;
        }
    }
}