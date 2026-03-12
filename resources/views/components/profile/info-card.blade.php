@props(['user'])

<div class="rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">
        Información Personal
    </h3>

    <div class="space-y-4">
        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Nombre Completo</label>
            <p class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ $user->name }} {{ $user->last_name }}
            </p>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Correo Electrónico</label>
            <p class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ $user->email }}
            </p>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Teléfono</label>
            <p class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ $user->number ?? 'No registrado' }}
            </p>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Estado de la Cuenta</label>
            <p class="text-base font-medium">
                @if($user->status)
                    <span class="text-green-600 dark:text-green-400">Activo</span>
                @else
                    <span class="text-red-600 dark:text-red-400">Inactivo</span>
                @endif
            </p>
        </div>
    </div>
</div>
