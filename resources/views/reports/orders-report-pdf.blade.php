<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pedidos - {{ $local->name ?? 'Local' }}</title>
    <style>
        @page {
            size: letter;
            margin: 1.5cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        
        .container {
            width: 100%;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #485a1a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 22pt;
            color: #181818;
            margin-bottom: 5px;
        }
        
        .header .local-name {
            font-size: 14pt;
            font-weight: bold;
            color: #485a1a;
            margin: 5px 0;
        }
        
        .header .date {
            font-size: 9pt;
            color: #888;
        }
        
        /* Info box */
        .info-box {
            background: #f5f5f5;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-left: 4px solid #b3621b;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #485a1a;
        }
        
        .info-value {
            display: table-cell;
            width: 60%;
        }
        
        /* Cards */
        .cards {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        
        .card {
            display: table-cell;
            width: 50%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background: #fafafa;
        }
        
        .card-web {
            border-right: 1px solid #485a1a;
            border-top: 3px solid #485a1a;
        }
        
        .card-presential {
            border-left: 1px solid #b3621b;
            border-top: 3px solid #b3621b;
        }
        
        .card-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
        }
        
        .card-value {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .card-web .card-value {
            color: #485a1a;
        }
        
        .card-presential .card-value {
            color: #b3621b;
        }
        
        .card-percentage {
            font-size: 9pt;
            color: #888;
        }
        
        /* Section titles */
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #181818;
            margin: 20px 0 12px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #b3621b;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th {
            background: #485a1a;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-size: 10pt;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: center;
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
            border-top: 2px solid #485a1a;
        }
        
        /* Top items table */
        .top-items-table th {
            background: #b3621b;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 30px;
            background: #fef3cd;
            border-left: 4px solid #daa520;
            margin: 20px 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8pt;
            color: #999;
        }
        
        /* Print optimization */
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
        <!-- Header -->
        <div class="header">
            <h1>REPORTE DE PEDIDOS</h1>
            <div class="local-name">{{ $local->name ?? 'Sin nombre' }}</div>
            <div class="date">Generado: {{ $exportDate ?? date('d/m/Y H:i') }}</div>
        </div>

        @if(isset($hasData) && $hasData)
            <!-- Info -->
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

            <!-- Cards -->
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

            <!-- Summary Table -->
            <div class="section-title">RESUMEN POR TIPO DE PEDIDO</div>
            <table>
                <thead>
                    <tr>
                        <th>TIPO</th>
                        <th>CANTIDAD</th>
                        <th>PORCENTAJE</th>
                        <th>INGRESOS (₡)</th>
                        <th>PROMEDIO (₡)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-left">En Linea</td>
                        <td class="text-center">{{ $orderStats['web']['count'] ?? 0 }}</td>
                        <td class="text-center">{{ number_format($orderStats['web']['percentage'] ?? 0, 2) }}%</td>
                        <td class="text-right">₡{{ number_format($revenueStats['web']['revenue'] ?? 0, 2) }}</td>
                        <td class="text-right">
                            @php
                                $webCount = $orderStats['web']['count'] ?? 0;
                                $webRevenue = $revenueStats['web']['revenue'] ?? 0;
                            @endphp
                            ₡{{ number_format($webCount > 0 ? $webRevenue / $webCount : 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Presencial</td>
                        <td class="text-center">{{ $orderStats['presential']['count'] ?? 0 }}</td>
                        <td class="text-center">{{ number_format($orderStats['presential']['percentage'] ?? 0, 2) }}%</td>
                        <td class="text-right">₡{{ number_format($revenueStats['presential']['revenue'] ?? 0, 2) }}</td>
                        <td class="text-right">
                            @php
                                $presCount = $orderStats['presential']['count'] ?? 0;
                                $presRevenue = $revenueStats['presential']['revenue'] ?? 0;
                            @endphp
                            ₡{{ number_format($presCount > 0 ? $presRevenue / $presCount : 0, 2) }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-left">TOTAL</td>
                        <td class="text-center">{{ $orderStats['total'] ?? 0 }}</td>
                        <td class="text-center">100%</td>
                        <td class="text-right">₡{{ number_format($revenueStats['total'] ?? 0, 2) }}</td>
                        <td class="text-right">
                            @php
                                $totalCount = $orderStats['total'] ?? 0;
                                $totalRevenue = $revenueStats['total'] ?? 0;
                            @endphp
                            ₡{{ number_format($totalCount > 0 ? $totalRevenue / $totalCount : 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            <!-- Top Items -->
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

        <!-- Footer -->
        <div class="footer">
            Reporte generado automaticamente el {{ $exportDate ?? date('d/m/Y H:i') }} | La Comarca Gastropark
        </div>
    </div>
</body>
</html>