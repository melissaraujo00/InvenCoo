@props(['user'])

<div class="rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">
        Información Laboral
    </h3>

    <div class="space-y-4">
        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Oficina</label>
            <p class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ $user->office->name ?? 'Sin oficina asignada' }}
            </p>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Roles</label>
            <div class="flex flex-wrap gap-2 mt-1">
                @forelse($user->roles as $role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400">
                        {{ $role->name }}
                    </span>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin roles asignados</p>
                @endforelse
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Verificación de Email</label>
            <p class="text-base font-medium">
                @if($user->email_verified_at)
                    <span class="text-green-600 dark:text-green-400">
                        Verificado ({{ $user->email_verified_at->format('d/m/Y') }})
                    </span>
                @else
                    <span class="text-yellow-600 dark:text-yellow-400">No verificado</span>
                @endif
            </p>
        </div>

        <div>
            <label class="text-sm text-gray-500 dark:text-gray-400">Miembro desde</label>
            <p class="text-base font-medium text-gray-800 dark:text-white/90">
                {{ $user->created_at->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>
