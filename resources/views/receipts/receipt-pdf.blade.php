<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Comprobante - {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #222;
            background: white;
        }

        body { padding: 15mm 18mm; }

        .receipt-container {
            width: 100%;
            max-width: 160mm;
            margin: 0 auto;
        }

        .divider-orange {
            height: 2px;
            background: #e18018;
            margin-bottom: 12px;
            font-size: 0;
            line-height: 0;
        }

        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #e18018;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .info-cell {
            border: 0.5px solid #e8e8e8;
            padding: 6px 8px;
        }

        .info-label {
            font-size: 8px;
            color: #aaa;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 10px;
            font-weight: bold;
            color: #222;
            word-break: break-word;
        }

        @media print {
            @page {
                margin: 15mm 18mm;
                size: letter;
            }
            body { padding: 0; }
        }
    </style>
</head>
<body>
<div class="receipt-container">

    <!-- HEADER -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-size:20px; font-weight:bold; color:#e18018; letter-spacing:1.5px;">LA COMARCA</div>
                <div style="font-size:9px; color:#888; letter-spacing:1px; margin-top:2px;">GASTROPARK</div>
                <div style="font-size:8px; color:#aaa; margin-top:2px;">Comprobante de Venta</div>
            </td>
            <td style="vertical-align:top; text-align:right; font-size:9px; color:#555; line-height:1.9;">
                <div><span style="color:#aaa;">Orden:</span> {{ $order->order_number }}</div>
                <div><span style="color:#aaa;">Comprobante:</span> {{ $receiptNumber }}</div>
                <div><span style="color:#aaa;">Fecha:</span> {{ $generatedAt->format('d/m/Y') }} &middot; {{ $generatedAt->format('H:i') }}</div>
            </td>
        </tr>
    </table>

    <div class="divider-orange"></div>

    <!-- TÍTULO + REFERENCIA -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
        <tr>
            <td style="font-size:14px; font-weight:bold; color:#e18018; letter-spacing:0.5px;">COMPROBANTE DE VENTA</td>
            <td style="text-align:right; font-size:9px; color:#bbb;">Ref: {{ $reference }}</td>
        </tr>
    </table>

    <!-- CLIENTE -->
    <div class="section-label">Cliente</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
        <tr>
            <td width="49%" style="padding-right:5px; padding-bottom:5px; vertical-align:top;">
                <div class="info-cell">
                    <div class="info-label">Nombre</div>
                    <div class="info-value">{{ $order->user->first()?->full_name ?? 'N/A' }}</div>
                </div>
            </td>
            <td width="49%" style="padding-left:5px; padding-bottom:5px; vertical-align:top;">
                <div class="info-cell">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->user->first()?->email ?? 'N/A' }}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="49%" style="padding-right:5px; vertical-align:top;">
                <div class="info-cell">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">{{ $order->user->first()?->phone ?? 'N/A' }}</div>
                </div>
            </td>
            <td width="49%" style="padding-left:5px; vertical-align:top;">
                <div class="info-cell">
                    <div class="info-label">Local</div>
                    <div class="info-value">{{ $order->local?->name ?? 'N/A' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- PRODUCTOS -->
    <div class="section-label">Detalle de productos</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:10px; margin-bottom:14px;">
        <colgroup>
            <col width="46%"><col width="14%"><col width="20%"><col width="20%">
        </colgroup>
        <thead>
            <tr>
                <th style="padding:6px 4px 6px 0; text-align:left; font-weight:bold; color:#555; font-size:9px; border-bottom:1.5px solid #333;">Producto</th>
                <th style="padding:6px 4px; text-align:center; font-weight:bold; color:#555; font-size:9px; border-bottom:1.5px solid #333;">Cant.</th>
                <th style="padding:6px 4px; text-align:right; font-weight:bold; color:#555; font-size:9px; border-bottom:1.5px solid #333;">Precio</th>
                <th style="padding:6px 0 6px 4px; text-align:right; font-weight:bold; color:#555; font-size:9px; border-bottom:1.5px solid #333;">Subtotal</th>
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
                    <td style="padding:6px 4px 6px 0; border-bottom:0.5px solid #f0f0f0;">{{ $item->product->name }}</td>
                    <td style="padding:6px 4px; text-align:center; color:#888; border-bottom:0.5px solid #f0f0f0;">{{ $item->quantity }}</td>
                    <td style="padding:6px 4px; text-align:right; color:#888; border-bottom:0.5px solid #f0f0f0;">&#8353;{{ number_format($price, 2, '.', ',') }}</td>
                    <td style="padding:6px 0 6px 4px; text-align:right; font-weight:bold; border-bottom:0.5px solid #f0f0f0;">&#8353;{{ number_format($subtotal, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
        <tr>
            <td></td>
            <td width="44%">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="font-size:9px; color:#888; padding-bottom:4px;">Subtotal</td>
                        <td style="font-size:9px; color:#888; text-align:right; padding-bottom:4px;">&#8353;{{ number_format($order->total_amount, 2, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top:0.5px solid #e8e8e8; padding:0; line-height:0; font-size:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="font-size:11px; font-weight:bold; color:#222; padding-top:5px;">Total a pagar</td>
                        <td style="font-size:11px; font-weight:bold; color:#e18018; text-align:right; padding-top:5px;">&#8353;{{ number_format($order->total_amount, 2, '.', ',') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PAGO -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fafafa; border:0.5px solid #e8e8e8; margin-bottom:14px;">
        <tr>
            <td style="padding:7px 10px 3px 10px; font-size:10px; color:#888;">Método de pago</td>
            <td style="padding:7px 10px 3px 10px; font-size:10px; font-weight:bold; color:#222; text-align:right;">{{ $paymentMethod }}</td>
        </tr>
        <tr>
            <td style="padding:3px 10px 7px 10px; font-size:10px; color:#888;">Referencia de pago</td>
            <td style="padding:3px 10px 7px 10px; font-size:10px; font-weight:bold; color:#222; text-align:right;">{{ $receiptReference ?: '&mdash;' }}</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-top:0.5px solid #e8e8e8;">
        <tr>
            <td style="padding-top:10px; font-size:9px; color:#bbb; line-height:1.8;">
                <div>Gracias por su compra en La Comarca Gastropark</div>
                <div>Este comprobante es válido como constancia de pago</div>
            </td>
            <td style="padding-top:10px; text-align:right; font-size:9px; color:#e18018; font-weight:bold; white-space:nowrap; vertical-align:bottom;">
                {{ now()->format('d/m/Y H:i:s') }}
            </td>
        </tr>
    </table>

</div>
</body>
</html>