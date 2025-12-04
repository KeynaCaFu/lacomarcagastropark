<?php
// resources/views/insumos/partials/edit-modal.blade.php
?>
<div id="editErrors" class="alert alert-danger d-none">
    <ul class="mb-0" id="editErrorsList"></ul>
</div>

<form id="editForm" action="{{ route('supplies.update', $supply->supply_id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_nombre" class="form-label">
                    Nombre del Insumo *
                </label>
                <input type="text" class="form-control" id="edit_nombre" name="nombre" 
                       value="{{ $supply->name }}" required 
                       placeholder="Ej: Harina de Trigo" 
                       pattern="^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\.]+$"
                       title="Solo se permiten letras, espacios, guiones y puntos"
                       maxlength="255">
                <div class="invalid-feedback"></div>
                {{-- <small class="form-text text-muted">Solo letras, espacios, guiones y puntos</small> --}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_unidad_medida" class="form-label">
                    Unidad de Medida *
                </label>
                <select class="form-select" id="edit_unidad_medida" name="unidad_medida" required>
                    <?php
                        // Normalizar unidad seleccionada para evitar diferencias de mayúsculas, espacios o variantes
                        $selectedUnit = old('unidad_medida', $supply->unit_of_measure ?? '');
                        $selectedUnitNorm = trim(strtolower($selectedUnit));
                        $predefined = ['kg','gramos','litros','ml','metros','cm','unidades','cajas','bolsas','botellas','latas','paquetes'];
                    ?>
                    <option value="">Seleccionar unidad...</option>
                    {{-- Si la unidad guardada no está en la lista predefinida, mostrarla como opción seleccionada --}}
                    @if($selectedUnitNorm !== '' && !in_array($selectedUnitNorm, $predefined))
                        <option value="{{ $selectedUnit }}" selected>{{ $selectedUnit }}</option>
                    @endif
                    <optgroup label="Peso">
                        <option value="kg" {{ $selectedUnitNorm == 'kg' ? 'selected' : '' }}>Kilogramos (kg)</option>
                        <option value="gramos" {{ $selectedUnitNorm == 'gramos' ? 'selected' : '' }}>Gramos (g)</option>
                    </optgroup>
                    <optgroup label="Volumen">
                        <option value="litros" {{ $selectedUnitNorm == 'litros' ? 'selected' : '' }}>Litros (L)</option>
                        <option value="ml" {{ $selectedUnitNorm == 'ml' ? 'selected' : '' }}>Mililitros (ml)</option>
                    </optgroup>
                    <optgroup label="Longitud">
                        <option value="metros" {{ $selectedUnitNorm == 'metros' ? 'selected' : '' }}>Metros (m)</option>
                        <option value="cm" {{ $selectedUnitNorm == 'cm' ? 'selected' : '' }}>Centímetros (cm)</option>
                    </optgroup>
                    <optgroup label="Cantidad">
                        <option value="unidades" {{ $selectedUnitNorm == 'unidades' ? 'selected' : '' }}>Unidades</option>
                        <option value="cajas" {{ $selectedUnitNorm == 'cajas' ? 'selected' : '' }}>Cajas</option>
                        <option value="bolsas" {{ $selectedUnitNorm == 'bolsas' ? 'selected' : '' }}>Bolsas</option>
                        <option value="botellas" {{ $selectedUnitNorm == 'botellas' ? 'selected' : '' }}>Botellas</option>
                        <option value="latas" {{ $selectedUnitNorm == 'latas' ? 'selected' : '' }}>Latas</option>
                        <option value="paquetes" {{ $selectedUnitNorm == 'paquetes' ? 'selected' : '' }}>Paquetes</option>
                    </optgroup>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="edit_stock_actual" class="form-label">
                    Stock Actual *
                    <i class="fas fa-info-circle text-info ms-1" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Cantidad actual disponible en inventario de este insumo."></i>
                </label>
                <input type="number" class="form-control" id="edit_stock_actual" name="stock_actual" 
                       value="{{ $supply->current_stock }}" required min="0" max="999999" step="1"
                       title="Solo números enteros del 0 al 999,999">
                <div class="invalid-feedback"></div>
                {{-- <small class="form-text text-muted">Números enteros del 0 al 999,999</small> --}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="edit_stock_minimo" class="form-label">
                    Stock Mínimo *
                </label>
                <input type="number" class="form-control" id="edit_stock_minimo" name="stock_minimo" 
                       value="{{ $supply->minimum_stock }}" required min="0" max="999999" step="1"
                       title="Solo números enteros del 0 al 999,999">
                <div class="invalid-feedback"></div>
                {{-- <small class="form-text text-muted">Números enteros del 0 al 999,999</small> --}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="edit_cantidad" class="form-label">
                    Cantidad *
                </label>
                <input type="number" class="form-control" id="edit_cantidad" name="cantidad" 
                       value="{{ $supply->quantity }}" required min="1" max="999999" step="1"
                       title="Solo números enteros del 1 al 999,999">
                <div class="invalid-feedback"></div>
                {{-- <small class="form-text text-muted">Números enteros del 1 al 999,999</small> --}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_precio" class="form-label">
                    Precio *
                </label>
                <div class="input-group">
                    <span class="input-group-text">₡</span>
                    <input type="number" step="0.01" class="form-control" id="edit_precio" name="precio" 
                           value="{{ $supply->price }}" required min="0.01" max="999999.99"
                           title="Precio válido entre ₡0.01 y ₡999,999.99">
                </div>
                <div class="invalid-feedback"></div>
                {{-- <small class="form-text text-muted">Precio entre ₡0.01 y ₡999,999.99</small> --}}
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_fecha_vencimiento" class="form-label">
                    Fecha de Vencimiento
                    <i class="fas fa-info-circle text-info ms-1" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="right" 
                       title="Fecha en la que el insumo expirará. Recibirá alertas 30 días antes del vencimiento."></i>
                </label>
          <input type="date" class="form-control" id="edit_fecha_vencimiento" name="fecha_vencimiento"
              value="{{ old('fecha_vencimiento', $supply->expiration_date ? $supply->expiration_date->format('Y-m-d') : '') }}"
              min="{{ date('Y-m-d', strtotime('+1 day')) }}"
              title="La fecha debe ser posterior a hoy">
                <div class="invalid-feedback"></div>
                <small class="form-text text-muted">Opcional - debe ser posterior a hoy</small>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="edit_estado" class="form-label">
            Estado *
            <i class="fas fa-info-circle text-info ms-1" 
               data-bs-toggle="tooltip" 
               data-bs-placement="right" 
               title="Estado actual del insumo. Disponible: listo para usar, Agotado: sin stock, Vencido: expirado."></i>
        </label>
        <select class="form-select" id="edit_estado" name="estado" required>
            <option value="">Seleccionar estado...</option>
            <option value="Disponible" {{ $supply->status_in_spanish == 'Disponible' ? 'selected' : '' }}>✅ Disponible</option>
            <option value="Agotado" {{ $supply->status_in_spanish == 'Agotado' ? 'selected' : '' }}>❌ Agotado</option>
            <option value="Vencido" {{ $supply->status_in_spanish == 'Vencido' ? 'selected' : '' }}>💀 Vencido</option>
        </select>
        <div class="invalid-feedback"></div>
    </div>

    <div class="mb-3">
        <label class="form-label">
            Proveedores
            <i class="fas fa-info-circle text-info ms-1" 
               data-bs-toggle="tooltip" 
               data-bs-placement="right" 
               title="Seleccione uno o más proveedores que suministran este insumo. Puede buscar por nombre o teléfono."></i>
        </label>
        <!-- Buscador de proveedores -->
        <div class="mb-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       id="edit_buscarProveedor" 
                       placeholder="Buscar proveedor por nombre o teléfono..."
                       autocomplete="off">
            </div>
        </div>
        <div class="border p-3 rounded" style="max-height: 250px; overflow-y: auto;" id="edit_proveedoresList">
            @foreach($suppliers as $supplier)
            <div class="form-check proveedor-item" data-nombre="{{ strtolower($supplier->name) }}" data-telefono="{{ $supplier->phone }}">
                <input class="form-check-input" type="checkbox" name="proveedores[]" 
                       value="{{ $supplier->supplier_id }}" 
                       id="edit_proveedor{{ $supplier->supplier_id }}"
                       {{ $supply->suppliers->contains('supplier_id', $supplier->supplier_id) ? 'checked' : '' }}>
                <label class="form-check-label" for="edit_proveedor{{ $supplier->supplier_id }}">
                    {{ $supplier->name }} - {{ $supplier->phone }}
                </label>
            </div>
            @endforeach
            @if($suppliers->count() == 0)
            <p class="text-muted">No hay proveedores activos.</p>
            @endif
        </div>
        <small class="form-text text-muted">Selecciona uno o más proveedores (opcional)</small>
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Actualizar Insumo
        </button>
    </div>
</form>

<script>
// Inicializar tooltips de Bootstrap cuando se cargue el modal
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Re-inicializar tooltips cuando se abra el modal de editar
setTimeout(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}, 500);
</script>