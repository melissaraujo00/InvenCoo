@props(['user'])

<div x-show="open" @keydown.escape.window="open = false"
     @open-profile-edit-modal.window="open = true"
     x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">

    {{-- Overlay con Blur --}}
    <div x-show="open" 
         x-transition.opacity 
         @click="open = false" 
         class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm"></div>

    {{-- Modal Contenedor --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative w-full max-w-2xl max-h-[90vh] flex flex-col rounded-2xl bg-white shadow-2xl dark:bg-gray-800 z-50 overflow-hidden">

        <form class="flex flex-col h-full overflow-hidden" @submit.prevent="saveProfile">
            
            {{-- HEADER (Fijo) --}}
            <div class="shrink-0 px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    Editar Información Personal
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Actualiza tus datos personales
                </p>
            </div>

            {{-- CUERPO (Scrollable y Fluido) --}}
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    
                    {{-- Nombre --}}
                    <div class="col-span-2 sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.name"
                            :class="{'border-red-500': errors.name}"
                            class="w-full h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
                            placeholder="Tu nombre" />
                        <template x-if="errors.name">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    {{-- Apellido --}}
                    <div class="col-span-2 sm:col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.last_name"
                            :class="{'border-red-500': errors.last_name}"
                            class="w-full h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
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
                            class="w-full h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
                            placeholder="correo@ejemplo.com" />
                        <template x-if="errors.email">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.email[0]"></p>
                        </template>
                    </div>

                    {{-- Teléfono (Bloqueado a 8 dígitos numéricos) --}}
                    <div class="col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                            x-model="form.number"
                            @input="form.number = $event.target.value.replace(/[^0-9]/g, '').slice(0, 8)"
                            maxlength="8"
                            :class="{'border-red-500': errors.number}"
                            class="w-full h-11 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 transition-all"
                            placeholder="Ej: 71505760" />
                        <template x-if="errors.number">
                            <p class="mt-1 text-sm text-red-500" x-text="errors.number[0]"></p>
                        </template>
                    </div>

                    {{-- Cambiar Contraseña (Toggle) --}}
                    <div class="col-span-2 pt-2">
                        <button type="button" @click="showPassword = !showPassword"
                            class="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span x-text="showPassword ? 'Cancelar cambio de contraseña' : 'Cambiar contraseña'"></span>
                        </button>
                    </div>

                    {{-- Campos de Contraseña (Condicionales) --}}
                    <template x-if="showPassword">
                        <div class="col-span-2 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nueva Contraseña</label>
                                <input type="password" x-model="form.password"
                                    :class="{'border-red-500': errors.password}"
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white/90 transition-all"
                                    placeholder="Mínimo 8 caracteres" />
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirmar Contraseña</label>
                                <input type="password" x-model="form.password_confirmation"
                                    class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white/90 transition-all"
                                    placeholder="Repite la contraseña" />
                            </div>

                            <template x-if="errors.password">
                                <div class="col-span-2">
                                    <p class="mt-1 text-sm font-medium text-red-500" x-text="errors.password[0]"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- FOOTER (Fijo) --}}
            <div class="shrink-0 px-6 py-4 border-t border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/80 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button @click="open = false" type="button"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 shadow-sm">
                    Cancelar
                </button>
                <button type="submit" :disabled="loading"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-brand-600 text-sm font-medium text-white hover:bg-brand-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Guardando...' : 'Guardar Cambios'"></span>
                </button>
            </div>
        </form>
    </div>
</div>