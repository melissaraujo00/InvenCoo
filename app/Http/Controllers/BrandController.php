<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
         $brands = Brand::select('id', 'name', 'description')
                    ->when($request->filled('search'), fn($query) =>
                        $query->where('name', 'LIKE', "%{$request->search}%")
                    )
                    ->paginate(20)->withQueryString();

        return view('pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('pages.brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        Brand::query()->create($request->validated());

        return to_route('brands.index')
            ->with('success', 'Marca creada exitosamente.');
    }

    public function show(Brand $brand)
    {
        //
    }

    public function edit(Brand $brand)
    {
        return view('pages.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());

        return to_route('brands.index')
            ->with('success', 'Marca editada exitosamente.');
    }

    public function destroy(Brand $brand)
    {
        // 1. Verificas el estado antes de actuar
        if ($brand->products()->exists()) {
            return to_route('brands.index')
                ->with('error', 'No puedes eliminar esta marca porque tiene productos vinculados.');
        }

        // 2. Ejecutas la acción de forma segura
        $brand->delete();

        return to_route('brands.index')
            ->with('success', 'Marca eliminada exitosamente.');
        }
    }
