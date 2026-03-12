@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Editar Marca" />

    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
                    Editar Marca
                </h2>
            </div>

            <div class="flex gap-3">
                <x-form.button href="{{ route('brands.index') }}" variant="secondary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al listado
                </x-form.button>
            </div>
        </div>

        <form action="{{ route('brands.update', $brand->id) }}" method="POST">
            @csrf
            @method('PUT')

            <x-common.component-card title="" class="mb-1">
                <div class="p-1">

                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="name" label="Nombre" :required="true">
                                <x-form.input
                                    name="name"
                                    placeholder="Ej. Marca 1"
                                    :required="true"
                                    :value="old('name', $brand->name)" />
                            </x-form.group>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <x-form.group name="description" label="Descripción">
                                <x-form.textarea name="description" placeholder="" :value="old('description', $brand->description)" />
                            </x-form.group>
                        </div>
                    </div>
                </div>
            </x-common.component-card>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                <x-form.button type="button" href="{{ route('brands.index') }}" variant="secondary" size="lg">
                    Cancelar
                </x-form.button>

                <x-form.button type="submit" variant="primary" size="lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Editar Marca
                </x-form.button>
            </div>
        </form>
    </div>
@endsection
