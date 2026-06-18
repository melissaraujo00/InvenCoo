@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                    <div>
                        <div class="mb-5 sm:mb-8">
                            <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                                Iniciar Sesión
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Ingrese sus credenciales para acceder al sistema.
                            </p>
                        </div>
                        
                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            @if ($errors->any())
                                <div class="mb-6 rounded-lg bg-red-500/10 p-4 text-sm text-red-500 border border-red-500/20">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="space-y-5">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Correo Electrónico<span class="text-error-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="usuario@cooperativa.com" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-red-500 @enderror" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Contraseña<span class="text-error-500">*</span>
                                    </label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                            name="password"
                                            placeholder="Ingrese su contraseña" required
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />

                                        <span @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPassword" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div x-data="{ checkboxToggle: false }">
                                        <label for="checkboxLabelOne"
                                            class="flex cursor-pointer items-center text-sm font-normal text-gray-700 select-none dark:text-gray-400">
                                            <div class="relative">
                                                <input type="checkbox" name="remember" id="checkboxLabelOne" class="sr-only" @change="checkboxToggle = !checkboxToggle" />
                                                <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                                                    class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px] transition-colors">
                                                    <span :class="checkboxToggle ? '' : 'opacity-0'">
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            Mantener sesión iniciada
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit"
                                        class="bg-brand-600 shadow-theme-xs hover:bg-brand-700 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                        Acceder al Sistema
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-gray-800/50 border-l border-gray-100 dark:border-gray-800">
                <div class="z-1 flex flex-col items-center justify-center px-12 text-center">
                    <x-common.common-grid-shape/>
                    <div class="mb-8 p-4 bg-white/10 rounded-2xl backdrop-blur-sm">
                        <svg class="w-10 h-16 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-3">InvenCoo</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                        Plataforma centralizada para el control de inventarios, compras y distribución operativa.
                    </p>
                </div>
            </div>

            <div class="fixed right-6 bottom-6 z-50">
                <button
                    class="bg-brand-100 dark:bg-gray-800 hover:bg-brand-200 dark:hover:bg-gray-700 border border-brand-200 dark:border-gray-700 shadow-lg inline-flex size-12 items-center justify-center rounded-full text-brand-600 dark:text-gray-300 transition-colors"
                    @click.prevent="$store.theme.toggle()" title="Cambiar tema">
                    <svg class="hidden dark:block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg class="block dark:hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>
            </div>
        </div>
    </div>
@endsection