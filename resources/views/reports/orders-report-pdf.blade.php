<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos - {{ $local->name ?? 'Local' }}</title>
    <style>
        @page {
            size: letter;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
            margin: 1.5cm;
            padding: 0;
        }
        
        .container {
            width: 100%;
        }
        
        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #485a1a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 15pt;
            color: #181818;
            margin-bottom: 2px;
        }
        
        .header .local-name {
            font-size: 11pt;
            font-weight: bold;
            color: #485a1a;
            margin: 2px 0;
        }
        
        .header .date {
            font-size: 8pt;
            color: #888;
        }
        
        /* INFO BOX */
        .info-box {
            background: #f5f5f5;
            padding: 6px 10px;
            margin-bottom: 10px;
            border-left: 3px solid #b3621b;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin: 2px 0;
        }
        
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #485a1a;
            font-size: 8.5pt;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
            font-size: 8.5pt;
        }
        
        /* CARDS */
        .cards {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        
        .card {
            display: table-cell;
            width: 50%;
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #ddd;
            background: #fafafa;
        }
        
        .card-web {
            border-right: 1px solid #485a1a;
            border-top: 2px solid #485a1a;
        }
        
        .card-presential {
            border-left: 1px solid #b3621b;
            border-top: 2px solid #b3621b;
        }
        
        .card-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
        }
        
        .card-value {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .card-web .card-value {
            color: #485a1a;
        }
        
        .card-presential .card-value {
            color: #b3621b;
        }
        
        .card-percentage {
            font-size: 7.5pt;
            color: #888;
        }
        
        /* SECTION TITLE */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #181818;
            margin: 10px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #b3621b;
        }
        
        /* TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        
        th {
            background: #485a1a;
            color: white;
            padding: 5px 6px;
            text-align: center;
            font-size: 8pt;
        }
        
        td {
            padding: 5px 6px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 8.5pt;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        tfoot td {
            background: #f0f0f0;
            font-weight: bold;
            border-top: 1.5px solid #485a1a;
        }
        
        .top-items-table th {
            background: #b3621b;
        }
        
        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 15px;
            background: #fef3cd;
            border-left: 3px solid #daa520;
            margin: 10px 0;
            font-size: 9pt;
        }
        
        /* FOOTER */
        .footer {
            margin-top: 15px;
            padding-top: 6px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #999;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>REPORTE DE PEDIDOS</h1>
            <div class="local-name">{{ $local->name ?? 'Sin nombre' }}</div>
            <div class="date">Generado: {{ $exportDate ?? date('d/m/Y H:i') }}</div>
        </div>

        @if(isset($hasData) && $hasData)
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Periodo:</span>
                    <span class="info-value">{{ $startDate ?? 'N/A' }} al {{ $endDate ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Pedidos:</span>
                    <span class="info-value">{{ $orderStats['total'] ?? 0 }}</span>
                </div>
            </div>

            <div class="cards">
                <div class="card card-web">
                    <div class="card-title">PEDIDOS EN LINEA</div>
                    <div class="card-value">{{ $orderStats['web']['count'] ?? 0 }}</div>
                    <div class="card-percentage">{{ number_format($orderStats['web']['percentage'] ?? 0, 2) }}% del total</div>
                </div>
                <div class="card card-presential">
                    <div class="card-title">PEDIDOS PRESENCIALES</div>
                    <div class="card-value">{{ $orderStats['presential']['count'] ?? 0 }}</div>
                    <div class="card-percentage">{{ number_format($orderStats['presential']['percentage'] ?? 0, 2) }}% del total</div>
                </div>
            </div>

            <div class="section-title">RESUMEN POR TIPO DE PEDIDO</div>
            <table>
                <thead>
                    <tr>
                        <th>TIPO</th>
                        <th>CANTIDAD</th>
                        <th>PORCENTAJE</th>
                        <th>INGRESOS (&#x20A1;)</th>
                        <th>PROMEDIO (&#x20A1;)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">En Linea</td>
                        <td class="text-center">{{ $orderStats['web']['count'] ?? 0 }}</td>
                        <td class="text-center">{{ number_format($orderStats['web']['percentage'] ?? 0, 2) }}%</td>
                        <td class="text-center">&#x20A1;{{ number_format($revenueStats['web']['revenue'] ?? 0, 2) }}</td>
                        <td class="text-center">
                            @php
                                $webCount = $orderStats['web']['count'] ?? 0;
                                $webRevenue = $revenueStats['web']['revenue'] ?? 0;
                            @endphp
                            &#x20A1;{{ number_format($webCount > 0 ? $webRevenue / $webCount : 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Presencial</td>
                        <td class="text-center">{{ $orderStats['presential']['count'] ?? 0 }}</td>
                        <td class="text-center">{{ number_format($orderStats['presential']['percentage'] ?? 0, 2) }}%</td>
                        <td class="text-center">&#x20A1;{{ number_format($revenueStats['presential']['revenue'] ?? 0, 2) }}</td>
                        <td class="text-center">
                            @php
                                $presCount = $orderStats['presential']['count'] ?? 0;
                                $presRevenue = $revenueStats['presential']['revenue'] ?? 0;
                            @endphp
                            &#x20A1;{{ number_format($presCount > 0 ? $presRevenue / $presCount : 0, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-center">TOTAL</td>
                        <td class="text-center">{{ $orderStats['total'] ?? 0 }}</td>
                        <td class="text-center">100%</td>
                        <td class="text-center">&#x20A1;{{ number_format($revenueStats['total'] ?? 0, 2) }}</td>
                        <td class="text-center">
                            @php
                                $totalCount = $orderStats['total'] ?? 0;
                                $totalRevenue = $revenueStats['total'] ?? 0;
                            @endphp
                            &#x20A1;{{ number_format($totalCount > 0 ? $totalRevenue / $totalCount : 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if(isset($topItems) && count($topItems) > 0)
                <div class="section-title">PRODUCTOS MAS VENDIDOS</div>
                <table class="top-items-table">
                    <thead>
                        <tr>
                            <th class="text-left">PRODUCTO</th>
                            <th>CANTIDAD VENDIDA</th>
                            <th>TRANSACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topItems as $item)
                        <tr>
                            <td class="text-left">{{ $item->name ?? $item['name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $item->total_quantity ?? $item['total_quantity'] ?? 0 }}</td>
                            <td class="text-center">{{ $item->order_count ?? $item['order_count'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        @else
            <div class="empty-state">
                <strong>No hay ventas registradas en este periodo</strong>
            </div>
        @endif

        <div class="footer">
            Reporte generado automaticamente el {{ $exportDate ?? date('d/m/Y H:i') }} | La Comarca Gastropark
        </div>
    </div>
</body>
</html>