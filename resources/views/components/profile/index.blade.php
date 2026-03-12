@props(['user'])

<div x-data="profileData()" x-init="init('{{ csrf_token() }}', '{{ route("profile.update") }}')">
    {{-- Mensaje de éxito --}}
    @if(session('status'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700 dark:bg-green-800/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

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

        async saveProfile() {
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch(this.updateRoute, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Error al guardar');
                    }
                    return;
                }

                alert('Perfil actualizado correctamente');
                setTimeout(() => { this.open = false; }, 1500);

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al guardar los cambios');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
