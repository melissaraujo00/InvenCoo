<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'office', 'suppliers'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('brand', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('office', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
            })
            ->paginate(15)->withQueryString();

        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $units = Unit::orderBy('sort_order')->get();
        $brands = Brand::all();

        return view('pages.products.create', compact('suppliers', 'categories','units', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $lastProduct = Product::orderBy('id', 'desc')->first();
        $lastCode = $lastProduct ? $lastProduct->code : 'PROD-0000';
        $lastNumber = (int) substr($lastCode, 5);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $validated['code'] = 'PROD-' . $newNumber;

        $validated['office_id'] = $request->user()->office_id;

        $product = Product::create($validated);

        if ($request->has('suppliers') && !empty($request->suppliers)) {
            $suppliersData = [];
            foreach ($request->suppliers as $supplier) {
                if (!empty($supplier['id'])) {
                    $suppliersData[$supplier['id']] = ['price' => $supplier['price']];
                }
            }
            if (!empty($suppliersData)) {
                $product->suppliers()->attach($suppliersData);
            }
        }

        return to_route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        // Se mantiene para el futuro o si decides usar una vista dedicada
        $product->load(['category', 'brand', 'office', 'suppliers']);
        return view('pages.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $brands = Brand::all();
        $product->load('suppliers');

        return view('pages.products.edit', compact('product', 'suppliers', 'categories', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // ELIMINADA la reasignación de office_id. Un producto no cambia de oficina solo por ser editado.

        $product->update($validated);

        if ($request->has('suppliers')) {
            $suppliersData = [];
            foreach ($request->suppliers as $supplier) {
                if (!empty($supplier['id'])) {
                    $suppliersData[$supplier['id']] = ['price' => $supplier['price']];
                }
            }
            $product->suppliers()->sync($suppliersData);
        } else {
            $product->suppliers()->detach();
        }

        return to_route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        try {
            $product->suppliers()->detach();
            $product->delete();
            return to_route('products.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return to_route('products.index')
                    ->with('error', 'No puedes eliminar este producto porque tiene movimientos de inventario o ventas asociadas.');
            }
            throw $e;
        }
    }
}
