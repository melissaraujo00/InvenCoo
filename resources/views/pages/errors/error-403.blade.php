@extends('layouts.app')

@section('content')
<div class="flex h-screen items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-800 dark:text-white">403</h1>
        <h2 class="mb-4 text-2xl font-semibold text-gray-700 dark:text-gray-300">
            Acceso Restringido
        </h2>
        <p class="mb-6 text-gray-500">
            Tu rol actual no tiene los privilegios necesarios para ver esta pantalla o ejecutar esta acción.
        </p>
        <a href="{{ route('dashboard') }}" class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">
            Volver al Inicio
        </a>
    </div>
</div>
@endsection
