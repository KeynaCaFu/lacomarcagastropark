@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="profile-container" style="padding: 0 15px;">
    {{-- <div style="margin-bottom: 20px;">
        <h2 style="margin: 0; padding: 0;">Actualiza tu Perfil</h2>
    </div> --}}

    <div style="max-width: 800px;">
        <!-- Formulario de actualización de perfil -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Información del Perfil</h5>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Formulario de cambio de contraseña -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cambiar Contraseña</h5>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Formulario de eliminar cuenta -->
        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="mb-0 text-white">Zona de Peligro</h5>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
