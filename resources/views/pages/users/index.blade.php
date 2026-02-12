@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Usuarios" />

    <div class="flex flex-col gap-10">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="min-w-[220px] py-4 px-4 font-medium text-black dark:text-white xl:pl-11">
                                Nombre
                            </th>
                            <th class="min-w-[150px] py-4 px-4 font-medium text-black dark:text-white">
                                Correo Electrónico
                            </th>
                            <th class="min-w-[120px] py-4 px-4 font-medium text-black dark:text-white">
                                Fecha de Registro
                            </th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-center">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 pl-9 dark:border-strokedark xl:pl-11">
                                <h5 class="font-medium text-black dark:text-white">{{ $user->name }}</h5>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                <p class="text-black dark:text-white">{{ $user->email }}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                <p class="text-black dark:text-white">{{ $user->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                <div class="flex items-center justify-center space-x-3.5">
                                    <button class="hover:text-primary">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            </svg>
                                    </button>
                                    <button class="hover:text-danger">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
