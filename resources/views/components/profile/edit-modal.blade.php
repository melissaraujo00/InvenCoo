@props(['user'])

<div x-show="open" @keydown.escape.window="open = false"
     @open-profile-edit-modal.window="open = true"
     x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="open = false" class="fixed inset-0 bg-black/50"></div>

    {{-- Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11 z-50">

        <div class="px-2 pr-14">
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                Editar Información Personal
            </h4>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                Actualiza tus datos personales
            </p>
        </div>

        <form class="flex flex-col" @submit.prevent="saveProfile">
            <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                    {{-- Nombre --}}
                    <div class="col-span-2 lg:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.name"
                            :class="{'border-red-500': errors.name}"
                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="Tu nombre" />
                        <template x-if="errors.name">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    {{-- Apellido --}}
                    <div class="col-span-2 lg:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.last_name"
                            :class="{'border-red-500': errors.last_name}"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="Tu apellido" />
                        <template x-if="errors.last_name">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.last_name[0]"></p>
                        </template>
                    </div>

                    {{-- Email --}}
                    <div class="col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" x-model="form.email"
                            :class="{'border-red-500': errors.email}"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="correo@ejemplo.com" />
                        <template x-if="errors.email">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.email[0]"></p>
                        </template>
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.number"
                            :class="{'border-red-500': errors.number}"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            placeholder="00000000" />
                        <template x-if="errors.number">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.number[0]"></p>
                        </template>
                    </div>

                    {{-- Cambiar Contraseña --}}
                    <div class="col-span-2">
                        <button type="button" @click="showPassword = !showPassword"
                            class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 font-medium">
                            <span x-text="showPassword ? 'Cancelar cambio de contraseña' : 'Cambiar contraseña'"></span>
                        </button>
                    </div>

                    <template x-if="showPassword">
                        <>
                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nueva Contraseña
                                </label>
                                <input type="password" x-model="form.password"
                                    :class="{'border-red-500': errors.password}"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    placeholder="Mínimo 8 caracteres" />
                            </div>

                            <div class="col-span-2 lg:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Confirmar Contraseña
                                </label>
                                <input type="password" x-model="form.password_confirmation"
                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    placeholder="Repite la contraseña" />
                            </div>

                            <template x-if="errors.password">
                                <div class="col-span-2">
                                    <p class="mt-1 text-sm text-red-500" x-text="errors.password[0]"></p>
                                </div>
                            </template>
                        </>
                    </template>
                </div>
            </div>

            <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                    Cancelar
                </button>
                <button type="submit" :disabled="loading"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto">
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Guardando...' : 'Guardar Cambios'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
