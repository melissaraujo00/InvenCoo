@props(['user'])

<div x-data="profileData()" x-init="init('{{ csrf_token() }}', '{{ route("profile.update") }}')">

    {{-- Tarjeta de Perfil con Avatar --}}
    <x-profile.avatar :user="$user" />

    {{-- Grid de Información --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Información Personal --}}
        <x-profile.info-card :user="$user" />

        {{-- Información Laboral --}}
        <x-profile.work-info :user="$user" />
    </div>

    {{-- Modal de Edición --}}
    <x-profile.edit-modal :user="$user" />
</div>

@push('scripts')
<script>
function profileData() {
    return {
        form: {
            name: '{{ $user->name }}',
            last_name: '{{ $user->last_name }}',
            email: '{{ $user->email }}',
            number: '{{ $user->number }}',
            password: '',
            password_confirmation: ''
        },
        showPassword: false,
        loading: false,
        errors: {},
        open: false,
        csrfToken: '',
        updateRoute: '',

        init(token, route) {
            this.csrfToken = token;
            this.updateRoute = route;
        },

        dispatchToast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('notify', { 
                detail: { message: message, type: type } 
            }));
        },

        async saveProfile() {
            this.loading = true;
            this.errors = {};

            let payload = { ...this.form };
            if (!this.showPassword || !payload.password) {
                delete payload.password;
                delete payload.password_confirmation;
            }

            try {
                const response = await fetch(this.updateRoute, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    redirect: 'manual'
                });

                // Si todo sale bien, cerramos el modal y recargamos.
                // Laravel se encargará automáticamente de mostrar el Toast nativo.
                if (response.type === 'opaqueredirect' || response.ok) {
                    this.open = false;
                    window.location.reload(); 
                    return;
                }

                // Manejo de errores de validación (Estos SÍ usan el Toast de JS)
                const data = await response.json();
                
                if (response.status === 422) {
                    this.errors = data.errors;
                    this.dispatchToast('Por favor, revisa los campos marcados en rojo.', 'error');
                } else {
                    throw new Error(data.message || 'Error del servidor.');
                }

            } catch (error) {
                console.error('Error de Fetch:', error);
                this.dispatchToast('Ocurrió un error en la red al intentar guardar.', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush