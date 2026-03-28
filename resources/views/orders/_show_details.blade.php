<!-- Detalles de Orden (parcial para AJAX) -->
<div class="order-detail-header">
    <div class="order-detail-title">
        <h2>{{ $order->order_number }}</h2>
        <div class="order-detail-subtitle">
            Creada el {{ $order->created_at->format('d/m/Y H:i') }}
        </div>
    </div>
    <div class="order-detail-status">
        <div class="status-badge {{ $order->getStatusColorClass() }}" style="display: inline-flex;">
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
            {{-- <div class="order-info-item">
                <div class="order-info-label">Fecha</div>
                <div class="order-info-value">{{ $order->date->format('d/m/Y') }}</div>
            </div>
            <div class="order-info-item">
                <div class="order-info-label">Hora</div>
                <div class="order-info-value">{{ $order->time }}</div>
            </div> --}}
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

        <!-- Razón de Cancelación (Solo si está Cancelado) -->
        @if($order->status === 'Cancelled' && $order->cancellation_reason)
            <div class="order-address" style="margin-top: 15px; background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; border-radius: 4px;">
                <div class="order-address-label" style="color: #991b1b;">
                    <i class="fas fa-ban" style="margin-right: 6px;"></i>Razón de Cancelación
                </div>
                <div class="order-address-text" style="color: #7f1d1d; margin-top: 8px;">
                    {{ $order->cancellation_reason }}
                </div>
            </div>
        @endif
    </div>

    <!-- Información del Usuario/Cliente -->
    @if($order->user && $order->user->count() > 0)
        <div class="order-section">
            <div class="order-section-title">
                <i class="fas fa-user"></i> Información del Cliente
            </div>
            @foreach($order->user as $customer)
                <div class="order-info-grid">
                    <div class="order-info-item">
                        <div class="order-info-label">Nombre</div>
                        <div class="order-info-value">{{ $customer->full_name }}</div>
                    </div>
                    <div class="order-info-item">
                        <div class="order-info-label">Email</div>
                        <div class="order-info-value">{{ $customer->email }}</div>
                    </div>
                    <div class="order-info-item">
                        <div class="order-info-label">Teléfono</div>
                        <div class="order-info-value">{{ $customer->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

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
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($item->product->photo)
                                        <img src="{{ asset($item->product->photo) }}" alt="{{ $item->product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 50px; height: 50px; background-color: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #999; font-size: 24px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                        @if($item->customization)
                                            <div style="font-size: 11px; color: #999; margin-top: 3px;">
                                                {{ $item->customization }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
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
                    <!-- Fila de Subtotal -->
                    <tr style="border-top: 2px solid #e5e7eb; font-weight: 600;">
                        <td colspan="3" style="text-align: right; padding: 12px;">Subtotal:</td>
                        <td class="item-total" style="font-weight: 600;">
                            ₡{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
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
            @php
                $calculatedTotal = 0;
                foreach($order->items as $item) {
                    $localProduct = $item->product->locals->where('local_id', $order->local_id)->first();
                    $price = $localProduct ? $localProduct->pivot->price : 0;
                    $calculatedTotal += $price * $item->quantity;
                }
            @endphp
            <div class="total-row final-total-row">
                <div class="total-label">Total:</div>
                <div class="total-value">₡{{ number_format($calculatedTotal, 2) }}</div>
            </div>
            @if(abs($calculatedTotal - $order->total_amount) > 0.01)
                <div style="padding: 12px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; margin-top: 12px; font-size: 12px; color: #92400e;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>
                    <strong>Advertencia:</strong> El total guardado (₡{{ number_format($order->total_amount, 2) }}) no coincide con la suma de items (₡{{ number_format($calculatedTotal, 2) }})
                </div>
            @endif
        </div>
    </div>
</div>
