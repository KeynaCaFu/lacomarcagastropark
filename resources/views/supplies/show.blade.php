@extends('layouts.app')

@section('title', 'Detalles del Insumo')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-info-circle"></i> Detalles del Insumo</h1>
    <div>
        <a href="{{ route('supplies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="{{ url('insumos/'.$supply->supply_id.'/edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Detalles del Insumo</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $supply->name }}</h4>
                        <p class="text-muted">ID: {{ $supply->supply_id }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-{{ $supply->status_in_spanish == 'Disponible' ? 'success' : ($supply->status_in_spanish == 'Agotado' ? 'danger' : 'secondary') }} fs-6">
                            {{ $supply->status_in_spanish }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Unidad de Medida:</th>
                                <td>{{ $supply->unit_of_measure }}</td>
                            </tr>
                            <tr>
                                <th>Cantidad:</th>
                                <td>{{ $supply->quantity }}</td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>₡{{ number_format($supply->price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Vencimiento:</th>
                                <td>{{ $supply->expiration_date ? $supply->expiration_date->format('d/m/Y') : 'No especificada' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Inventario</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Stock Actual:</th>
                                <td>
                                    <span class="badge bg-{{ $supply->current_stock > $supply->minimum_stock ? 'success' : 'warning' }}">
                                        {{ $supply->current_stock }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Stock Mínimo:</th>
                                <td>{{ $supply->minimum_stock }}</td>
                            </tr>
                            <tr>
                                <th>Diferencia:</th>
                                <td>
                                    <span class="badge bg-{{ ($supply->current_stock - $supply->minimum_stock) >= 0 ? 'success' : 'danger' }}">
                                        {{ $supply->current_stock - $supply->minimum_stock }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Proveedores</h5>
                    @if($supply->suppliers->count() > 0)
                    <div class="row">
                        @foreach($supply->suppliers as $supplier)
                        <div class="col-md-6 mb-2">
                            <div class="card">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1">{{ $supplier->name }}</h6>
                                    <p class="card-text small mb-1">
                                        <i class="fas fa-phone"></i> {{ $supplier->phone }}<br>
                                        <i class="fas fa-envelope"></i> {{ $supplier->email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Este insumo no tiene proveedores asignados.
                    </div>
                    @endif
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('supplies.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                    <a href="{{ url('insumos/'.$supply->supply_id.'/edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar Insumo
                    </a>
                    <form action="{{ route('supplies.destroy', $supply->supply_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este insumo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection