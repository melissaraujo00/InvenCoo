<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Office;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'office', 'suppliers'])
                    ->when($request->filled('search'), function($query) use ($request) {
                        $query->where('code', 'LIKE', "%{$request->search}%")
                              ->orWhere('name', 'LIKE', "%{$request->search}%");
                    })
                    ->paginate(15);

        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        $brands = Brand::all();

        return view('pages.products.create', compact('suppliers', 'categories', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        // Obtener datos validados
        $validated = $request->validated();

        // Asignar la oficina del usuario autenticado
        $validated['office_id'] = $request->user()->office_id;

        // Crear el producto
        $product = Product::create($validated);

        // Asociar proveedores con sus precios
        if ($request->has('suppliers') && !empty($request->suppliers)) {
            $suppliersData = [];
            foreach ($request->suppliers as $supplier) {
                $suppliersData[$supplier['id']] = ['price' => $supplier['price']];
            }
            $product->suppliers()->attach($suppliersData);
        }

        return to_route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
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
        // Obtener datos validados
        $validated = $request->validated();

        // Asegurar que la oficina sea la del usuario (por si acaso)
        $validated['office_id'] = $request->user()->office_id;

        // Actualizar producto
        $product->update($validated);

        // Sincronizar proveedores
        if ($request->has('suppliers')) {
            $suppliersData = [];
            foreach ($request->suppliers as $supplier) {
                $suppliersData[$supplier['id']] = ['price' => $supplier['price']];
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
        $product->suppliers()->detach();
        $product->delete();

        return to_route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
