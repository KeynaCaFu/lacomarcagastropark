@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-6">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $supplier->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h2 mb-2">
                    <i class="fas fa-store me-2" style="color: #059669;"></i>
                    {{ $supplier->name }}
                </h1>
                <p class="text-muted">Detalles del proveedor</p>
            </div>
            <div>
                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?')">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row">
        <!-- Main Info Column -->
        <div class="col-lg-8 mb-4">
            <!-- Información Principal -->
            <div class="card mb-4" style="border: 1px solid #e5e7eb;">
                <div class="card-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2" style="color: #059669;"></i>
                        Información General
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Nombre</p>
                            <p class="h6 mb-0">{{ $supplier->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Teléfono</p>
                            <p class="h6 mb-0">
                                <a href="tel:{{ $supplier->phone }}">{{ $supplier->phone }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Email</p>
                            <p class="h6 mb-0">
                                <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Creado</p>
                            <p class="h6 mb-0">{{ $supplier->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-1">Última actualización</p>
                            <p class="h6 mb-0">{{ $supplier->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galería de Imágenes -->
            @if($supplier->gallery && $supplier->gallery->count() > 0)
            <div class="card" style="border: 1px solid #e5e7eb;">
                <div class="card-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2" style="color: #059669;"></i>
                        Galería de Imágenes ({{ $supplier->gallery->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($supplier->gallery as $image)
                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <img src="{{ $image->image_url }}" alt="{{ $image->description }}" class="img-fluid rounded" style="height: 250px; object-fit: cover; width: 100%;">
                                <div class="mt-2">
                                    @if($image->description)
                                    <p class="small text-muted mb-0">{{ $image->description }}</p>
                                    @else
                                    <p class="small text-muted mb-0">Sin descripción</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Card de Estadísticas -->
            <div class="card mb-4" style="border: 1px solid #e5e7eb;">
                <div class="card-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2" style="color: #059669;"></i>
                        Estadísticas
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <p class="text-muted small mb-1">Imágenes en galería</p>
                        <p class="h3" style="color: #059669;">
                            {{ $supplier->gallery ? $supplier->gallery->count() : 0 }}
                        </p>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <p class="text-muted small mb-1">Estado</p>
                        <span class="badge bg-success">Activo</span>
                    </div>
                </div>
            </div>

            <!-- Card de Acciones -->
            <div class="card" style="border: 1px solid #e5e7eb;">
                <div class="card-header" style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2" style="color: #059669;"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>Editar Proveedor
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm w-100">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/productos.css') }}">
@endsection
