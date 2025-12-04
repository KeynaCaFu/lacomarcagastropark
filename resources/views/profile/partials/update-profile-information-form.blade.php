<div class="mb-4">
    <h5>Información del Perfil</h5>
    <p class="text-muted">Actualiza la información de tu perfil y dirección de correo electrónico.</p>
</div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="mt-4">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted">
                    Tu dirección de correo no ha sido verificada.
                    <button form="send-verification" class="btn btn-link p-0 text-decoration-none">
                        Haz clic aquí para reenviar el correo de verificación.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success alert-sm mt-2" role="alert">
                        Se ha enviado un nuevo enlace de verificación a tu dirección de correo.
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>

        @if (session('status') === 'profile-updated')
            <p class="text-success small mb-0">Cambios guardados correctamente.</p>
        @endif
    </div>
</form>
