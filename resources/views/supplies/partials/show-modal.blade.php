<div class="detail-section">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 style="color: #485a1a; margin-bottom: 5px;">{{ $supply->name }}</h4>
            <p class="text-muted">ID: {{ $supply->supply_id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="status-badge status-{{ strtolower($supply->status_in_spanish) }}">
                {{ $supply->status_in_spanish }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Información General</h5>
            <table class="detail-table">
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
            <table class="detail-table">
                <tr>
                    <th>Stock Actual:</th>
                    <td>
                        <span class="status-badge {{ $supply->current_stock > $supply->minimum_stock ? 'status-disponible' : 'status-agotado' }}">
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
                        <span class="status-badge {{ ($supply->current_stock - $supply->minimum_stock) >= 0 ? 'status-disponible' : 'status-agotado' }}">
                            {{ $supply->current_stock - $supply->minimum_stock }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <h5>Proveedores</h5>
        @if($supply->suppliers->count() > 0)
        <div class="row">
            @foreach($supply->suppliers as $supplier)
            <div class="col-md-6 mb-2">
                <div class="proveedor-card">
                    <h6>{{ $supplier->name }}</h6>
                    <p>
                        <i class="fas fa-phone"></i> {{ $supplier->phone }}<br>
                        <i class="fas fa-envelope"></i> {{ $supplier->email }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; padding: 15px; border-radius: 8px;">
            <i class="fas fa-exclamation-triangle"></i> Este insumo no tiene proveedores asignados.
        </div>
        @endif
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('showModal')">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>
</div>