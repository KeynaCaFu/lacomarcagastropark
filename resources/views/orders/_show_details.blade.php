<!-- Detalles de Orden (parcial para AJAX) -->
<div class="order-detail-header">
    <div class="order-detail-title">
        <h2>{{ $order->order_number }}</h2>
        <div class="order-detail-subtitle">
            {{ $order->created_at->format('d/m/Y') }} · {{ $order->created_at->format('H:i') }}
        </div>
    </div>
    <div class="order-detail-status">
        <span class="status-badge {{ $order->getStatusColorClass() }}" style="display: inline-flex; font-size: 13px; font-weight: 600;">
            <i class="{{ $order->getStatusIcon() }}" style="margin-right: 6px;"></i>
            {{ $statuses[$order->status] ?? 'Desconocido' }}
        </span>
    </div>
</div>

<div class="order-detail-body">
    <!-- Grid de información rápida -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 24px;">
        <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px; ">
            <div style="font-size: 11px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Local</div>
            <div style="font-size: 13px; font-weight: 600; color: #111;">{{ $order->local->name ?? 'N/A' }}</div>
        </div>
        <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px; ">
            <div style="font-size: 11px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Tiempo Prep.</div>
            <div style="font-size: 13px; font-weight: 600; color: #111;">{{ $order->preparation_time }} min</div>
        </div>
        <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px; ">
            <div style="font-size: 11px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Total Items</div>
            <div style="font-size: 13px; font-weight: 600; color: #111;">{{ $order->quantity }}</div>
        </div>
        <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px; ">
            <div style="font-size: 11px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Origen</div>
            <div style="font-size: 13px; font-weight: 600; color: #111;">{{ ucfirst($order->origin) }}</div>
        </div>
    </div>

    @if($order->additional_notes)
        <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; padding: 12px; margin-bottom: 24px;">
            <div style="font-size: 11px; color: #b45309; font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">📝 Notas Especiales</div>
            <div style="font-size: 13px; color: #92400e; line-height: 1.5;">{{ $order->additional_notes }}</div>
        </div>
    @endif

    <!-- Razón de Cancelación -->
    @if($order->status === 'Cancelled' && $order->cancellation_reason)
        <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 24px; border-left: 3px solid #ef4444;">
            <div style="font-size: 11px; color: #991b1b; font-weight: 600; text-transform: uppercase; margin-bottom: 6px;">
                <i class="fas fa-ban" style="margin-right: 6px;"></i>Razón de Cancelación
            </div>
            <div style="font-size: 13px; color: #7f1d1d; line-height: 1.5;">{{ $order->cancellation_reason }}</div>
        </div>
    @endif

    <!-- Información del Cliente -->
    @if($order->user && $order->user->count() > 0)
        <div style="margin-bottom: 24px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #999; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user" style="color: #e18018; font-size: 14px;"></i>Cliente
            </div>
            @foreach($order->user as $customer)
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 10px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Nombre</div>
                        <div style="font-size: 13px; font-weight: 600; color: #111;">{{ $customer->full_name }}</div>
                    </div>
                    <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 10px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Email</div>
                        <div style="font-size: 12px; color: #666; word-break: break-all;">{{ $customer->email }}</div>
                    </div>
                    <div style="background: #fafafa; border: 1px solid #f0f0f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 10px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Teléfono</div>
                        <div style="font-size: 13px; font-weight: 600; color: #111;">{{ $customer->phone ?? '—' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Items de la Orden -->
    <div style="margin-bottom: 24px;">
        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #999; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-boxes" style="color: #e18018; font-size: 14px;"></i>Items de la Orden
        </div>

        @if($order->items->count() > 0)
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th width="50" style="text-align: center;">Cant.</th>
                        <th width="70" style="text-align: right;">P. Unit.</th>
                        <th width="70" style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    @if($item->product->photo)
                                        <img src="{{ asset($item->product->photo) }}" alt="{{ $item->product->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">
                                    @else
                                        <div style="width: 40px; height: 40px; background-color: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fas fa-image" style="color: #999; font-size: 16px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong style="font-size: 12px; display: block;">{{ $item->product->name ?? 'N/A' }}</strong>
                                        @if($item->customization)
                                            <div style="font-size: 10px; color: #999; margin-top: 2px;">
                                                {{ $item->customization }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="item-quantity" style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right; color: #666;">
                                @php
                                    $localProduct = $item->product->locals->where('local_id', $order->local_id)->first();
                                    $price = $localProduct ? $localProduct->pivot->price : 0;
                                @endphp
                                ₡{{ number_format($price, 2) }}
                            </td>
                            <td class="item-total" style="text-align: right; font-weight: 600;">
                                @php
                                    $total = $price * $item->quantity;
                                @endphp
                                ₡{{ number_format($total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <!-- Fila de Subtotal -->
                    <tr style="background: #f9fafb;">
                        <td colspan="3" style="text-align: right; padding: 8px 10px; font-weight: 600; font-size: 13px;">Subtotal:</td>
                        <td class="item-total" style="font-weight: 700; text-align: right; color: #e18018;">
                            ₡{{ number_format($order->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <div style="padding: 20px; text-align: center; color: #999; font-size: 13px; background: #fafafa; border-radius: 8px;">
                <i class="fas fa-inbox" style="font-size: 24px; margin-bottom: 8px; opacity: 0.5;"></i>
                <p style="margin: 0;">Sin items en esta orden</p>
            </div>
        @endif
    </div>

    <!-- Resumen de Total -->
    <div style="background: linear-gradient(135deg, #fff7ed 0%, #fffbf0 100%); border: 2px solid #e18018; border-radius: 8px; padding: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px;">
            <span style="font-size: 12px; font-weight: 600; color: #666; text-transform: uppercase;">Subtotal:</span>
            <span style="font-size: 14px; font-weight: 600; color: #111;">
                @php
                    $calculatedTotal = 0;
                    foreach($order->items as $item) {
                        $localProduct = $item->product->locals->where('local_id', $order->local_id)->first();
                        $price = $localProduct ? $localProduct->pivot->price : 0;
                        $calculatedTotal += $price * $item->quantity;
                    }
                @endphp
                ₡{{ number_format($calculatedTotal, 2) }}
            </span>
        </div>
        <div style="border-top: 1px solid #fed7aa; padding-top: 12px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 14px; font-weight: 700; color: #111;">Total:</span>
            <span style="font-size: 18px; font-weight: 700; color: #e18018;">₡{{ number_format($calculatedTotal, 2) }}</span>
        </div>
        @if(abs($calculatedTotal - $order->total_amount) > 0.01)
            <div style="padding: 10px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; margin-top: 12px; font-size: 11px; color: #92400e;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 4px;"></i>
                <strong>Advertencia:</strong> Discrepancia en totales
            </div>
        @endif
    </div>
</div>
