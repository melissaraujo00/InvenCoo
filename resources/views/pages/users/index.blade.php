@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Usuarios" />
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-title-md2 font-bold text-gray-800 dark:text-white/90">
            Gestión de Usuarios
        </h2>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Administra los accesos de tu plataforma
        </p>
    </div>

    <a href="{{ route('users.create') }}"
   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 transition-colors">
    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path d="M10.8333 5V9.16667H15V10.8333H10.8333V15H9.16667V10.8333H5V9.16667H9.16667V5H10.8333Z" />
    </svg>
    Agregar Usuario
</a>
</div>
    <x-tables.table-users :users="$users" />

</div>
@endsection
