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
use Illuminate\Support\Facades\DB;

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
        $validated['office_id'] = $request->user()->office_id;

        DB::transaction(function () use ($validated, $request) {

            // A. Bloqueo Pesimista: Nadie más lee el último ID hasta que YO termine de insertar.
            $lastProduct = Product::lockForUpdate()->orderBy('id', 'desc')->first();
            $lastNumber = $lastProduct ? (int) substr($lastProduct->code, 5) : 0;
            $validated['code'] = 'PROD-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // B. Creación: Insertamos el producto (El candado SIGUE ACTIVO)
            $product = Product::create($validated);

            // C. Relaciones: Insertamos la data de la tabla pivote
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

        });

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
        if ($product->details()->exists() ||
        $product->movementDetails()->exists() ||
        $product->transferDetails()->exists()) {

        return to_route('products.index')
            ->with('error', 'No puedes eliminar este producto porque tiene compras, movimientos o transferencias asociadas.');
        }


        DB::transaction(function () use ($product) {
            $product->suppliers()->detach();
            $product->delete();
        });

        return to_route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

}
