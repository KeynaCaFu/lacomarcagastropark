<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background: #c9690f;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background: #e8e8e8;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            margin: 20px -20px -20px -20px;
            border-radius: 0 0 5px 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #ddd;
        }
        table th {
            background: #f0f0f0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .button {
            display: inline-block;
            background: #c9690f;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .total {
            font-size: 18px;
            color: #c9690f;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">Comprobante de Orden</h1>
            <p style="margin: 5px 0 0 0;">La Comarca Gastropark</p>
        </div>

        <div class="content">
            <p>¡Hola {{ $customerName }}!</p>

            <p>Te enviamos el comprobante de tu orden <strong>#{{ $order->order_number }}</strong> del <strong>{{ $order->created_at->format('d/m/Y \a \l\a\s H:i') }}</strong>.</p>

            <h2>Resumen de tu Orden</h2>

            <table>
                <tr>
                    <td><strong>Número de Orden:</strong></td>
                    <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td><strong>Comprobante #:</strong></td>
                    <td>{{ $receipt->receipt_number }}</td>
                </tr>
                <tr>
                    <td><strong>Referencia/Factura:</strong></td>
                    <td>{{ $receipt->receipt_reference }}</td>
                </tr>
                <tr>
                    <td><strong>Método de Pago:</strong></td>
                    <td>{{ $receipt->payment_method }}</td>
                </tr>
                <tr>
                    <td><strong>Fecha:</strong></td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            <h2>Ítems Solicitados</h2>

            <table>
                <tr>
                    <th>Producto</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: right;">Precio Unitario</th>
                </tr>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">
                            ₡{{ number_format($item->product->locals->where('local_id', $order->local_id)->first()?->pivot->price ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
            </table>

            <div class="total">
                Total: ₡{{ number_format($order->total_amount, 2) }}
            </div>

            <p>El comprobante en PDF está adjunto a este correo. Puedes descargarlo e imprimirlo cuando lo necesites.</p>

            <p>
                <a href="{{ route('orders.show', ['order' => $order->order_id]) }}" class="button">Ver Orden en el Sistema</a>
            </p>

            <p>Si tienes dudas o problemas con tu orden, no dudes en contactarnos.</p>
        </div>

        <div class="footer">
            <p><strong>La Comarca Gastropark</strong></p>
            <p>Gracias por tu confianza</p>
        </div>
    </div>
</body>
</html>
