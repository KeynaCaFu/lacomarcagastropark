@extends('layouts.app')

@section('title', 'Editar Proveedor')

@push('styles')
    <link href="{{ asset('css/pages/suppliers.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="form-container form-proveedores">
            <div class="d-flex align-items-center mb-4">
                <h3><i class="fas fa-edit"></i> Editar Proveedor: {{ $supplier->name }}</h3>
            </div>
            
            <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Proveedor *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                           value="{{ old('nombre', $supplier->name) }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required 
                                   value="{{ old('telefono', $supplier->phone) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" required 
                                   value="{{ old('correo', $supplier->email) }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección *</label>
                    <textarea class="form-control" id="direccion" name="direccion" required>{{ old('direccion', $supplier->address) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_compras" class="form-label">Total de Compras *</label>
                            <div class="input-group price-input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="total_compras" name="total_compras" required 
                                       value="{{ old('total_compras', $supplier->total_purchases) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="Activo" {{ old('estado', $supplier->status_in_spanish) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado', $supplier->status_in_spanish) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Insumos que Provee</label>
                    <div class="checkbox-insumos">
                        @foreach($supplies as $supply)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="insumos[]" 
                                   value="{{ $supply->supply_id }}" id="insumo{{ $supply->supply_id }}"
                                   {{ in_array($supply->supply_id, old('insumos', $supplier->supplies->pluck('supply_id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="insumo{{ $supply->supply_id }}">
                                {{ $supply->name }} - ₡{{ number_format($supply->price, 2) }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection