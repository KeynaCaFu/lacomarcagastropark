<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante - {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            color: #333;
            padding: 5mm;
        }

        .receipt-container {
            width: 100%;
            max-width: 210mm;
            margin: 0;
            background: white;
            padding: 5mm;
            font-size: 8px;
        }

        /* Header */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #c9690f;
            padding-bottom: 4px;
            margin-bottom: 6px;
            gap: 5px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 1 auto;
        }

        .company-info {
            flex: 0 1 auto;
        }

        .company-info h1 {
            font-size: 12px;
            color: #c9690f;
            margin: 0;
            line-height: 1;
        }

        .company-info p {
            font-size: 8px;
            color: #666;
            margin: 0;
        }

        .receipt-meta {
            text-align: right;
            flex: 1;
            min-width: 150px;
        }

        .receipt-meta-item {
            font-size: 7px;
            margin: 1px 0;
            white-space: nowrap;
        }

        .receipt-meta-item strong {
            color: #c9690f;
        }

        /* Título */
        .receipt-title {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #c9690f;
            margin: 3px 0 2px 0;
        }

        .receipt-reference {
            text-align: center;
            font-size: 7px;
            color: #999;
            margin-bottom: 5px;
        }

        /* Secciones */
        .section {
            margin-bottom: 6px;
        }

        .section-title {
            background: #f9f9f9;
            border-left: 3px solid #c9690f;
            padding: 3px 5px;
            font-weight: 600;
            font-size: 8px;
            color: #333;
            margin-bottom: 3px;
        }

        /* Customer Info */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px;
            font-size: 7px;
            margin-bottom: 4px;
        }

        .info-item {
            background: #fafafa;
            padding: 2px 3px;
            border-radius: 2px;
            page-break-inside: avoid;
        }

        .info-label {
            font-size: 6px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 1px;
        }

        .info-value {
            font-weight: 600;
            color: #333;
            word-break: break-word;
            font-size: 7px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
        }

        .items-table thead {
            background: #f0f0f0;
        }

        .items-table th {
            padding: 2px 2px;
            text-align: left;
            font-size: 7px;
            font-weight: 700;
            color: #333;
            border-bottom: 1px solid #c9690f;
        }

        .items-table td {
            padding: 2px 2px;
            font-size: 7px;
            border-bottom: 0.5px solid #f0f0f0;
        }

        .item-name {
            font-weight: 600;
            color: #333;
        }

        .item-quantity {
            text-align: center;
        }

        .item-price {
            text-align: right;
        }

        .item-subtotal {
            text-align: right;
            font-weight: 600;
            color: #c9690f;
        }

        /* Totals Section */
        .totals-section {
            margin-top: 4px;
            border-top: 1px solid #f0f0f0;
            padding-top: 3px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            font-size: 7px;
            margin-bottom: 1px;
        }

        .total-label {
            width: 100px;
            text-align: left;
            color: #666;
        }

        .total-value {
            width: 60px;
            text-align: right;
        }

        .final-total {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            font-size: 8px;
            font-weight: 700;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px solid #c9690f;
        }

        .final-total .total-label {
            color: #c9690f;
        }

        .final-total .total-value {
            color: #c9690f;
        }

        /* Payment Info */
        .payment-info {
            background: #fef5ed;
            border-left: 3px solid #f0a060;
            padding: 3px 4px;
            border-radius: 2px;
            font-size: 7px;
            margin: 4px 0;
        }

        .payment-method {
            margin-bottom: 1px;
        }

        .payment-method strong {
            color: #c9690f;
        }

        /* Footer */
        .receipt-footer {
            margin-top: 4px;
            padding-top: 3px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
            font-size: 6px;
            color: #999;
        }

        .footer-text {
            margin: 1px 0;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 3mm;
            }
            .receipt-container {
                box-shadow: none;
                padding: 3mm;
            }
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="logo-section">
                <div class="company-info">
                    <h1>LA COMARCA</h1>
                    <p>Gastropark - Comprobante de Venta</p>
                </div>
            </div>

            <div class="receipt-meta">
                <div class="receipt-meta-item">
                    <strong>Orden #:</strong> {{ $order->order_number }}
                </div>
                <div class="receipt-meta-item">
                    <strong>Comprobante #:</strong> {{ $receiptNumber }}
                </div>
                <div class="receipt-meta-item">
                    <strong>Fecha:</strong> {{ $generatedAt->format('d/m/Y') }}
                </div>
                <div class="receipt-meta-item">
                    <strong>Hora:</strong> {{ $generatedAt->format('H:i') }}
                </div>
            </div>
        </div>

        <div class="receipt-title">COMPROBANTE DE VENTA</div>
        <div class="receipt-reference">
            Referencia: <strong>{{ $reference }}</strong>
        </div>

        <!-- Cliente -->
        <div class="section">
            <div class="section-title">Información de Cliente</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nombre</div>
                    <div class="info-value">{{ $order->user->first()?->full_name ?? 'No especificado' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->user->first()?->email ?? 'No especificado' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">{{ $order->user->first()?->phone ?? 'No especificado' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Local</div>
                    <div class="info-value">{{ $order->local?->name ?? 'No especificado' }}</div>
                </div>
            </div>
        </div>

        <!-- Detalle de ítems -->
        <div class="section">
            <div class="section-title">Detalle de Productos</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%">Producto</th>
                        <th style="width: 15%; text-align: center;">Cantidad</th>
                        <th style="width: 17.5%; text-align: right;">Precio Unit.</th>
                        <th style="width: 17.5%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        @php
                            $localProduct = $item->product->locals->where('local_id', $order->local_id)->first();
                            $price = $localProduct ? $localProduct->pivot->price : 0;
                            $subtotal = $price * $item->quantity;
                        @endphp
                        <tr>
                            <td class="item-name">{{ $item->product->name }}</td>
                            <td class="item-quantity">{{ $item->quantity }}</td>
                            <td class="item-price">₡{{ number_format($price, 2, '.', ',') }}</td>
                            <td class="item-subtotal">₡{{ number_format($subtotal, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">₡{{ number_format($order->total_amount, 2, '.', ',') }}</span>
            </div>
            <div class="final-total">
                <span class="total-label">TOTAL A PAGAR:</span>
                <span class="total-value">₡{{ number_format($order->total_amount, 2, '.', ',') }}</span>
            </div>
        </div>

        <!-- Información de pago -->
        <div class="payment-info">
            <div class="payment-method">
                <strong>Método de Pago:</strong> {{ $paymentMethod }}
            </div>
            <div class="payment-method">
                <strong>Número de Referencia:</strong> {{ $receiptReference }}
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="footer-text">
                Gracias por su compra en La Comarca Gastropark
            </div>
            <div class="footer-text">
                Este comprobante es válido como constancia de pago
            </div>
            <div class="footer-text" style="margin-top: 15px; color: #c9690f;">
                {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
