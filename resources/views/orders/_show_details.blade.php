<!-- Detalles de Orden (parcial para AJAX) -->
<div class="order-detail-header">
    <div class="order-detail-title">
        <h2>{{ $order->order_number }}</h2>
        <div class="order-detail-subtitle">
            Creada el {{ $order->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="order-detail-status">
        <div class="status-badge {{ $order->getStatusColorClass() }}">
            <i class="{{ $order->getStatusIcon() }}"></i>
            {{ $statuses[$order->status] ?? 'Desconocido' }}
        </div>
    </div>
</div>

<div class="order-detail-body">
    <!-- Información General -->
    <div class="order-section">
        <div class="order-section-title">
            <i class="fas fa-info-circle"></i> Información General
        </div>
        <div class="order-info-grid">
            <div class="order-info-item">
                <div class="order-info-label">Local</div>
                <div class="order-info-value">{{ $order->local->name ?? 'N/A' }}</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Origen</div>
                <div class="order-info-value">
                    {{ ucfirst($order->origin) }}
                </div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Fecha</div>
                <div class="order-info-value">{{ $order->date->format('d/m/Y') }}</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Hora</div>
                <div class="order-info-value">{{ $order->time }}</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Tiempo de Prep.</div>
                <div class="order-info-value">{{ $order->preparation_time }} min</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Total Items</div>
                <div class="order-info-value">{{ $order->quantity }}</div>
            </div>
        </div>

        @if($order->additional_notes)
            <div class="order-address" style="margin-top: 15px;">
                <div class="order-address-label">Notas Especiales</div>
                <div class="order-address-text">{{ $order->additional_notes }}</div>
            </div>
        @endif
    </div>

    <!-- Items de la Orden -->
    <div class="order-section">
        <div class="order-section-title">
            <i class="fas fa-boxes"></i> Items
        </div>

        @if($order->items->count() > 0)
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th width="60">Cant.</th>
                        <th width="80">P. Unit.</th>
                        <th width="80">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                @if($item->customization)
                                    <div style="font-size: 11px; color: #999; margin-top: 3px;">
                                        {{ $item->customization }}
                                    </div>
                                @endif
                            </td>
                            <td class="item-quantity">{{ $item->quantity }}</td>
                            <td>
                                @php
                                    $localProduct = $item->product->locals->where('local_id', $order->local_id)->first();
                                    $price = $localProduct ? $localProduct->pivot->price : 0;
                                @endphp
                                ₡{{ number_format($price, 2) }}
                            </td>
                            <td class="item-total">
                                @php
                                    $total = $price * $item->quantity;
                                @endphp
                                ₡{{ number_format($total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 15px; text-align: center; color: #999; font-size: 13px;">
                Sin items
            </div>
        @endif
    </div>

    <!-- Resumen de Pago -->
    <div class="order-section">
        <div class="order-totals">
            <div class="total-row final-total-row">
                <div class="total-label">Total:</div>
                <div class="total-value">₡{{ number_format($order->total_amount, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Cambio de Estado -->
    @if($order->status === 'Pending')
        <div class="order-section">
            <div class="order-section-title">
                <i class="fas fa-arrow-right-arrow-left"></i> Cambiar Estado
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <button type="button" class="status-option" data-order-id="{{ $order->order_id }}" data-status="En Preparación" style="padding: 12px; border-radius: 6px; border: 2px solid #e5e7eb; cursor: pointer; background: #f9fafb; font-weight: 600; transition: all 0.2s;">
                    <i class="fas fa-fire"></i> Preparación
                </button>
                <button type="button" class="status-option" data-order-id="{{ $order->order_id }}" data-status="Listo" style="padding: 12px; border-radius: 6px; border: 2px solid #e5e7eb; cursor: pointer; background: #f9fafb; font-weight: 600; transition: all 0.2s;">
                    <i class="fas fa-check-circle"></i> Listo
                </button>
                <button type="button" class="status-option" data-order-id="{{ $order->order_id }}" data-status="Delivered" style="padding: 12px; border-radius: 6px; border: 2px solid #e5e7eb; cursor: pointer; background: #f9fafb; font-weight: 600; transition: all 0.2s;">
                    <i class="fas fa-truck"></i> Entregado
                </button>
                <button type="button" class="status-option" data-order-id="{{ $order->order_id }}" data-status="Cancelado" style="padding: 12px; border-radius: 6px; border: 2px solid #e5e7eb; cursor: pointer; background: #f9fafb; font-weight: 600; transition: all 0.2s;">
                    <i class="fas fa-times-circle"></i> Cancelar
                </button>
            </div>
        </div>
    @endif

    <!-- Acciones -->
    <div class="order-actions">
        @if($order->status === 'Pending')
            <a href="{{ route('orders.edit', $order->order_id) }}" class="btn-order primary" style="flex: 1;">
                <i class="fas fa-edit"></i> Editar
            </a>
        @endif

        <a href="{{ route('orders.show', $order->order_id) }}" class="btn-order secondary" style="flex: 1;">
            <i class="fas fa-expand"></i> Ver Completo
        </a>

        @if(in_array($order->status, ['Pending', 'Cancelado']))
            <button type="button" class="btn-order danger" style="flex: 1;" data-order-id="{{ $order->order_id }}" data-delete onclick="deleteOrder({{ $order->order_id }})">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        @endif
    </div>
</div>

<script>
// Eventos de cambio de estado
document.querySelectorAll('[data-status]').forEach(btn => {
    btn.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const status = this.dataset.status;
        changeOrderStatus(orderId, status);
    });
});

function changeOrderStatus(orderId, status) {
    fetch(`/ordenes/${orderId}/cambiar-estado`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success !== false) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo cambiar el estado'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error en la solicitud');
    });
}

function deleteOrder(orderId) {
    if (confirm('¿Estás seguro de que deseas eliminar esta orden?')) {
        fetch(`/ordenes/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(() => location.reload())
        .catch(error => alert('Error: ' + error));
    }
}
</script>
