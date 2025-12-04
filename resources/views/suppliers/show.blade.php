@extends('layouts.app')

@section('title', 'Detalles del Proveedor')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Detalles del Proveedor</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $supplier->name }}</h4>
                        <p class="text-muted">ID: {{ $supplier->supplier_id }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-{{ $supplier->status_in_spanish == 'Activo' ? 'success' : 'secondary' }} fs-6">
                            {{ $supplier->status_in_spanish }}
                        </span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Información de Contacto</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th><i class="fas fa-phone"></i> Teléfono:</th>
                                <td>{{ $supplier->phone }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-envelope"></i> Correo:</th>
                                <td>{{ $supplier->email }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i> Dirección:</th>
                                <td>{{ $supplier->address }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Información Comercial</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Total Compras:</th>
                                <td>₡{{ number_format($supplier->total_purchases, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Insumos Proveídos:</th>
                                <td>
                                    <span class="badge bg-success">{{ $supplier->supplies->count() }} insumos</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td>
                                    <span class="badge bg-{{ $supplier->status_in_spanish == 'Activo' ? 'success' : 'secondary' }}">
                                        {{ $supplier->status_in_spanish }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Insumos que Provee</h5>
                    @if($supplier->supplies->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Stock Actual</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->supplies as $supply)
                                <tr>
                                    <td>{{ $supply->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $supply->current_stock > $supply->minimum_stock ? 'success' : 'warning' }}">
                                            {{ $supply->current_stock }}
                                        </span>
                                    </td>
                                    <td>₡{{ number_format($supply->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $supply->status_in_spanish == 'Disponible' ? 'success' : ($supply->status_in_spanish == 'Agotado' ? 'danger' : 'secondary') }}">
                                            {{ $supply->status_in_spanish }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Este proveedor no tiene insumos asignados.
                    </div>
                    @endif
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar Proveedor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection