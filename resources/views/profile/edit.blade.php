@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Editar Perfil</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
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
</div>
@endsection
