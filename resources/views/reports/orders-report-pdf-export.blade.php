<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pedidos - {{ $local->name ?? 'Local' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            padding: 0;
            margin: 0;
        }
        
        /* Tamaño carta fijo para PDF */
        .pdf-container {
            width: 100%;
            max-width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            padding: 0.5in;
            background: white;
            box-sizing: border-box;
        }
        
        /* Header centrado */
        .header {
            text-align: center;
            border-bottom: 4px solid #485a1a;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            font-size: 28px;
            color: #181818;
            margin-bottom: 8px;
            font-weight: 800;
        }
        
        .header p {
            font-size: 14px;
            color: #666;
            margin: 4px 0;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 25px;
            padding: 18px;
            background: #f7f7f7;
            border-radius: 6px;
            border-left: 4px solid #b3621b;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #485a1a;
        }
        
        .info-value {
            color: #333;
        }
        
        /* Cards Grid - Centradas */
        .cards-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: center;
        }
        
        .card {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #e0e0e0;
            background: #fafafa;
        }
        
        .card-web {
            border-color: #485a1a;
            background: rgba(72, 90, 26, 0.05);
        }
        
        .card-presential {
            border-color: #b3621b;
            background: rgba(179, 98, 27, 0.05);
        }
        
        .card-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 12px;
        }
        
        .card-value {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        
        .card-web .card-value {
            color: #485a1a;
        }
        
        .card-presential .card-value {
            color: #b3621b;
        }
        
        .card-percentage {
            font-size: 13px;
            font-weight: 600;
        }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 13px;
        }
        
        thead {
            background: #485a1a;
            color: white;
        }
        
        th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 700;
        }
        
        th.center, td.center {
            text-align: center;
        }
        
        th.right, td.right {
            text-align: right;
        }
        
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tbody tr:hover {
            background: #f7f7f7;
        }
        
        tfoot tr {
            background: #f0f0f0;
            font-weight: 700;
        }
        
        tfoot td {
            padding: 10px 12px;
            border-top: 2px solid #485a1a;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-web {
            background: rgba(72, 90, 26, 0.2);
            color: #2d6b4f;
        }
        
        .badge-presential {
            background: rgba(179, 98, 27, 0.2);
            color: #7a4a1b;
        }
        
        /* Section Title */
        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #181818;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #b3621b;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 14px;
            background: #fef3cd;
            border-left: 4px solid #daa520;
            border-radius: 4px;
        }
        
        /* Footer */
        .footer {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        
        /* Top items table */
        .top-items-table thead {
            background: #b3621b;
        }
        
        /* Print styles para PDF */
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            
            .pdf-container {
                padding: 0.5in;
                max-width: 100%;
                min-height: auto;
            }
            
            .card, .info-section, table {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            
            .section-title {
                break-after: avoid;
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Header -->
        <div class="header">
            <h1>📊 Reporte de Pedidos</h1>
            <p><strong>{{ $local->name ?? 'Sin nombre' }}</strong></p>
            <p style="font-size: 12px; margin-top: 8px;">Generado: {{ $exportDate ?? date('d/m/Y H:i') }}</p>
        </div>
        
        @if(isset($hasData) && $hasData)
            <!-- Info Section -->
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">Período:</span>
                    <span class="info-value">{{ $startDate ?? 'N/A' }} a {{ $endDate ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Generación:</span>
                    <span class="info-value">{{ $exportDate ?? date('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Pedidos:</span>
                    <span class="info-value" style="font-weight: bold; color: #485a1a;">{{ $orderStats['total'] ?? 0 }}</span>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="cards-grid">
                <div class="card card-web">
                    <div class="card-title">Pedidos En Línea</div>
                    <div class="card-value">{{ $orderStats['web']['count'] ?? 0 }}</div>
                    <div class="card-percentage">{{ $orderStats['web']['percentage'] ?? 0 }}% del total</div>
                </div>
                <div class="card card-presential">
                    <div class="card-title">Pedidos Presenciales</div>
                    <div class="card-value">{{ $orderStats['presential']['count'] ?? 0 }}</div>
                    <div class="card-percentage">{{ $orderStats['presential']['percentage'] ?? 0 }}% del total</div>
                </div>
            </div>
            
            <!-- Summary Table -->
            <h2 class="section-title">Resumen por Tipo de Pedido</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tipo de Pedido</th>
                        <th class="center">Cantidad</th>
                        <th class="center">Porcentaje</th>
                        <th class="right">Ingresos</th>
                        <th class="right">Promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge badge-web"> En Línea</span></td>
                        <td class="center"><strong>{{ $orderStats['web']['count'] ?? 0 }}</strong></td>
                        <td class="center">{{ $orderStats['web']['percentage'] ?? 0 }}%</td>
                        <td class="right"><strong>₡{{ number_format($revenueStats['web']['revenue'] ?? 0, 2) }}</strong></td>
                        <td class="right">
                            @php
                                $webCount = $orderStats['web']['count'] ?? 0;
                                $webRevenue = $revenueStats['web']['revenue'] ?? 0;
                            @endphp
                            @if($webCount > 0)
                                ₡{{ number_format($webRevenue / $webCount, 2) }}
                            @else
                                ₡0.00
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-presential">Presencial </span></td>
                        <td class="center"><strong>{{ $orderStats['presential']['count'] ?? 0 }}</strong></td>
                        <td class="center">{{ $orderStats['presential']['percentage'] ?? 0 }}%</td>
                        <td class="right"><strong>₡{{ number_format($revenueStats['presential']['revenue'] ?? 0, 2) }}</strong></td>
                        <td class="right">
                            @php
                                $presCount = $orderStats['presential']['count'] ?? 0;
                                $presRevenue = $revenueStats['presential']['revenue'] ?? 0;
                            @endphp
                            @if($presCount > 0)
                                ₡{{ number_format($presRevenue / $presCount, 2) }}
                            @else
                                ₡0.00
                            @endif
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="center">{{ $orderStats['total'] ?? 0 }}</td>
                        <td class="center">100%</td>
                        <td class="right">₡{{ number_format($revenueStats['total'] ?? 0, 2) }}</td>
                        <td class="right">
                            @php
                                $totalCount = $orderStats['total'] ?? 0;
                                $totalRevenue = $revenueStats['total'] ?? 0;
                            @endphp
                            @if($totalCount > 0)
                                ₡{{ number_format($totalRevenue / $totalCount, 2) }}
                            @else
                                ₡0.00
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            @if(isset($topItems) && $topItems && count($topItems) > 0)
                <h2 class="section-title">Productos Más Vendidos</h2>
                <table class="top-items-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="center">Cantidad Vendida</th>
                            <th class="center">Transacciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topItems as $item)
                            <tr>
                                <td><strong>{{ $item->name ?? $item['name'] ?? 'N/A' }}</strong></td>
                                <td class="center">{{ $item->total_quantity ?? $item['total_quantity'] ?? 0 }}</td>
                                <td class="center">{{ $item->order_count ?? $item['order_count'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
        @else
            <div class="empty-state">
                <p>📭 No hay ventas registradas en este período</p>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Este reporte fue generado automáticamente el {{ $exportDate ?? date('d/m/Y H:i') }}</p>
            <p>© {{ date('Y') }} - La Comarca Gastropark</p>
        </div>
    </div>
</body>
</html>