<div class="detail-section">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 style="color: #485a1a; margin-bottom: 5px;">{{ $supplier->name }}</h4>
            <p class="text-muted">ID: {{ $supplier->supplier_id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="status-badge {{ $supplier->status_in_spanish == 'Activo' ? 'status-disponible' : 'status-agotado' }}">
                {{ $supplier->status_in_spanish }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Información de Contacto</h5>
            <table class="detail-table">
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
            <table class="detail-table">
                <tr>
                    <th>Total Compras:</th>
                    <td>₡{{ number_format($supplier->total_purchases, 2) }}</td>
                </tr>
                <tr>
                    <th>Insumos Proveídos:</th>
                    <td>
                        <span class="status-badge status-disponible">{{ $supplier->supplies->count() }} insumos</span>
                    </td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td>
                        <span class="status-badge {{ $supplier->status_in_spanish == 'Activo' ? 'status-disponible' : 'status-agotado' }}">
                            {{ $supplier->status_in_spanish }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section-divider"></div>
    
    <div class="mt-4">
        <h5>Insumos que Provee <span class="info-tooltip" data-tooltip="Lista de todos los insumos que este proveedor puede suministrar">ℹ️</span></h5>
        @if($supplier->supplies->count() > 0)
        <div class="table-responsive">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-box"></i> Nombre</th>
                        <th><i class="fas fa-warehouse"></i> Stock</th>
                        <th><i class="fas fa-tag"></i> Precio</th>
                        <th><i class="fas fa-info-circle"></i> Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supplier->supplies as $supply)
                    <tr class="proveedor-card-animation">
                        <td><strong>{{ $supply->name }}</strong><br><small class="text-muted">{{ $supply->unit_of_measure }}</small></td>
                        <td>
                            <span class="status-badge {{ $supply->current_stock > $supply->minimum_stock ? 'status-disponible' : 'status-agotado' }}">
                                {{ $supply->current_stock }} / {{ $supply->minimum_stock }}
                            </span>
                        </td>
                        <td><strong>₡{{ number_format($supply->price, 2) }}</strong></td>
                        <td>
                            <span class="status-badge status-{{ strtolower($supply->status_in_spanish) }}">
                                {{ $supply->status_in_spanish }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="comercial-info mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                <strong>Total de insumos:</strong> {{ $supplier->supplies->count() }} | 
                <strong>Valor promedio:</strong> ₡{{ number_format($supplier->supplies->avg('price'), 2) }}
            </small>
        </div>
        @else
        <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; padding: 15px; border-radius: 8px;">
            <i class="fas fa-exclamation-triangle"></i> Este proveedor no tiene insumos asignados.
            <br><small>Puede asignar insumos editando este proveedor.</small>
        </div>
        @endif
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeProveedorModal('showProveedorModal')">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>
</div>