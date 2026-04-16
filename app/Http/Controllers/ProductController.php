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
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('office', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
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
    $validated = $request->validated();

    // Generar código único automáticamente
    $lastProduct = Product::orderBy('id', 'desc')->first();
    $lastCode = $lastProduct ? $lastProduct->code : 'PROD-0000';
    $lastNumber = (int) substr($lastCode, 5);
    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    $validated['code'] = 'PROD-' . $newNumber;

    // Asignar la oficina del usuario autenticado
    $validated['office_id'] = $request->user()->office_id;

    $product = Product::create($validated);

    // Asociar proveedores (sin cambios)
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
