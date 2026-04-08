@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Pedidos - {{ $local->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .pdf-container {
            background-color: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        header h1 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 28px;
        }
        
        header p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .card-web {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }
        
        .card-presential {
            border-color: #a855f7;
            background: rgba(168, 85, 247, 0.05);
        }
        
        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .card-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .card-web .card-value {
            color: #10b981;
        }
        
        .card-presential .card-value {
            color: #a855f7;
        }
        
        .card-percentage {
            font-size: 12px;
            color: #6b7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 14px;
        }
        
        thead {
            background-color: #f3f4f6;
            border-bottom: 2px solid #d1d5db;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
        }
        
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tfoot tr {
            background-color: #f3f4f6;
            font-weight: 600;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-web {
            background: rgba(16, 185, 129, 0.2);
            color: #047857;
        }
        
        .badge-presential {
            background: rgba(168, 85, 247, 0.2);
            color: #6d28d9;
        }
        
        .validation-box {
            margin-top: 30px;
            padding: 20px;
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            border-radius: 4px;
        }
        
        .validation-box h3 {
            color: #047857;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .validation-box ul {
            margin: 0;
            padding-left: 20px;
            color: #047857;
            font-size: 12px;
        }
        
        .validation-box li {
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
        
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            
            .pdf-container {
                box-shadow: none;
                max-width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Header -->
        <header>
            <h1>📊 Reporte de Pedidos</h1>
            <p>{{ $local->name }}</p>
            <p>Período del {{ $orderStats['period']['startFormatted'] }} al {{ $orderStats['period']['endFormatted'] }}</p>
        </header>

        <!-- Info -->
        <div class="info-grid">
            <div class="info-item">
                <span><strong>Local:</strong></span>
                <span>{{ $local->name }}</span>
            </div>
            <div class="info-item">
                <span><strong>Período:</strong></span>
                <span>{{ $orderStats['period']['startFormatted'] }} - {{ $orderStats['period']['endFormatted'] }}</span>
            </div>
            <div class="info-item">
                <span><strong>Generado:</strong></span>
                <span>{{ $exportDate }}</span>
            </div>
            <div class="info-item">
                <span><strong>Total Pedidos:</strong></span>
                <span style="font-weight: bold; font-size: 16px;">{{ $orderStats['total'] }}</span>
            </div>
        </div>

        <!-- Summary Cards -->
        @if(!$hasData)
            <div class="validation-box" style="background-color: #fef3cd; border-color: #8a6200; margin: 30px 0;">
                <h3 style="color: #333; margin: 0;">⚠️ No hay ventas registradas en este período</h3>
                <p style="color: #333; margin: 10px 0 0 0;">No se encontraron datos de ventas para el período seleccionado.</p>
            </div>
        @else
        <div class="cards-grid">
            <div class="card card-web">
                <p class="card-title">Pedidos En Línea</p>
                <p class="card-value">{{ $orderStats['web']['count'] }}</p>
                <p class="card-percentage">{{ $orderStats['web']['percentage'] }}% del total</p>
            </div>
            <div class="card card-presential">
                <p class="card-title">Pedidos Presenciales</p>
                <p class="card-value">{{ $orderStats['presential']['count'] }}</p>
                <p class="card-percentage">{{ $orderStats['presential']['percentage'] }}% del total</p>
            </div>
        </div>
        @endif

        <!-- Table -->
        @if($hasData)
        <table>
            <thead>
                <tr>
                    <th>Tipo de Pedido</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                    <th>Ingresos</th>
                    <th>Promedio</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-web">En Línea</span></td>
                    <td>{{ $orderStats['web']['count'] }}</td>
                    <td>{{ $orderStats['web']['percentage'] }}%</td>
                    <td>₡{{ number_format($revenueStats['web']['revenue'], 2) }}</td>
                    <td>
                        @if ($orderStats['web']['count'] > 0)
                            ₡{{ number_format($revenueStats['web']['revenue'] / $orderStats['web']['count'], 2) }}
                        @else
                            ₡0.00
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span class="badge badge-presential">Presencial</span></td>
                    <td>{{ $orderStats['presential']['count'] }}</td>
                    <td>{{ $orderStats['presential']['percentage'] }}%</td>
                    <td>₡{{ number_format($revenueStats['presential']['revenue'], 2) }}</td>
                    <td>
                        @if ($orderStats['presential']['count'] > 0)
                            ₡{{ number_format($revenueStats['presential']['revenue'] / $orderStats['presential']['count'], 2) }}
                        @else
                            ₡0.00
                        @endif
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td><strong>{{ $orderStats['total'] }}</strong></td>
                    <td><strong>100%</strong></td>
                    <td><strong>₡{{ number_format($revenueStats['total'], 2) }}</strong></td>
                    <td>
                        <strong>
                            @if ($orderStats['total'] > 0)
                                ₡{{ number_format($revenueStats['total'] / $orderStats['total'], 2) }}
                            @else
                                ₡0.00
                            @endif
                        </strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Top Selling Items --}}
        @if($topItems->count() > 0)
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #1f2937; font-size: 14px; font-weight: 600;">Productos Más Vendidos</h3>
        <table style="font-size: 13px;">
            <thead>
                <tr>
                    <th style="padding: 8px 12px; font-size: 12px;">Producto</th>
                    <th style="padding: 8px 12px; font-size: 12px;">Cantidad Vendida</th>
                    <th style="padding: 8px 12px; font-size: 12px;">Eventos de Compra</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topItems as $item)
                <tr>
                    <td style="padding: 6px 12px;">{{ $item->name }}</td>
                    <td style="padding: 6px 12px;">{{ $item->total_quantity }}</td>
                    <td style="padding: 6px 12px;">{{ $item->order_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Reporte generado automáticamente el {{ $exportDate }}</p>
            <p>La Comarca Gastro Park - Sistema de Reportes de Pedidos</p>
        </div>
    </div>
</body>
</html>
@endsection
