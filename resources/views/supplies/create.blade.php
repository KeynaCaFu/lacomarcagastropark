@extends('layouts.app')

@section('title', 'Crear Nuevo Insumo')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-plus"></i> Crear Nuevo Insumo</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('supplies.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Insumo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       value="{{ old('nombre') }}" placeholder="Ej: Harina de Trigo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unidad_medida" class="form-label">Unidad de Medida *</label>
                                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" required 
                                       value="{{ old('unidad_medida') }}" placeholder="Ej: kg, litro, unidad">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_actual" class="form-label">Stock Actual *</label>
                                <input type="number" class="form-control" id="stock_actual" name="stock_actual" required 
                                       value="{{ old('stock_actual', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_minimo" class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required 
                                       value="{{ old('stock_minimo', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required 
                                       value="{{ old('cantidad', 1) }}" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₡</span>
                                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" required 
                                           value="{{ old('precio') }}" min="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Disponible" {{ old('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Agotado" {{ old('estado') == 'Agotado' ? 'selected' : '' }}>Agotado</option>
                            <option value="Vencido" {{ old('estado') == 'Vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proveedores</label>
                        <div class="border p-3 rounded">
                            @foreach($suppliers as $supplier)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="proveedores[]" 
                                       value="{{ $supplier->supplier_id }}" id="proveedor{{ $supplier->supplier_id }}"
                                       {{ in_array($supplier->supplier_id, old('proveedores', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="proveedor{{ $supplier->supplier_id }}">
                                    {{ $supplier->name }} - {{ $supplier->phone }}
                                </label>
                            </div>
                            @endforeach
                            @if($suppliers->count() == 0)
                            <p class="text-muted">No hay proveedores activos. <a href="{{ route('suppliers.create') }}">Crear proveedor</a></p>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('supplies.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Insumo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection