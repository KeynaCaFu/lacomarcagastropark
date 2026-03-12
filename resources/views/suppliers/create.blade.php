@extends('layouts.app')

@section('title', 'Crear Proveedor')

@push('styles')
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="product-page-wrapper">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
        <ol class="breadcrumb" style="padding: 0; background: none;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
            <li class="breadcrumb-item active">Crear Proveedor</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="product-page-header">
        <div class="product-page-header-flex">
            <div class="product-page-header-title">
                <h2>
                    <i class="fas fa-user-plus"></i> Registrar Nuevo Proveedor
                </h2>
                <div class="accent-bar"></div>
                <small class="text-muted">Complete los datos del proveedor</small>
            </div>
        </div>
    </div>

    <!-- Formulario de Creación -->
    <div class="product-form-container">
        <div class="card product-card">
            <div class="card-header product-card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Información del Proveedor
                </h5>
            </div>
            <div class="card-body" style="padding:24px;">
                <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm">
                    @csrf

                    <div style="max-width: 600px;">
                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Nombre del Proveedor *</strong></span>
                                <span class="ms-2 text-white-50" title="Nombre completo del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Ej: Juan García Suministros">
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group mb-3">
                            <label for="telefono" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Teléfono *</strong></span>
                                <span class="ms-2 text-white-50" title="Número de contacto del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}"
                                       required
                                       maxlength="20"
                                       placeholder="Ej: +506 8765-4321">
                            </div>
                            @error('telefono')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Email *</strong></span>
                                <span class="ms-2 text-white-50" title="Correo electrónico del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       maxlength="255"
                                       placeholder="Ej: contacto@proveedor.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Botones de Acción -->
                        <div style="display: flex; gap: 12px; margin-top: 32px; justify-content: flex-start;">
                            <button type="submit" class="btn-primary" style="background: #059669; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s;">
                                <i class="fas fa-check"></i> Crear Proveedor
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn-secondary" style="background: #e5e7eb; color: #374151; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
