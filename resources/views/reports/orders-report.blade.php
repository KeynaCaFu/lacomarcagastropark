@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
<style>
    .top-search-bar { display: none !important; }
</style>
@endpush

<div class="rp-container">

    {{-- Header --}}
    <div class="rp-header" style="margin-bottom:1.75rem;">
        <h1>Reportes de Pedidos</h1>
        <p>Análisis de pedidos en línea vs. presenciales — {{ $local->name }}</p>
    </div>

    <!-- Navigation: Local vs Productos -->
    <div class="rp-view-toggle">
        <span class="rp-view-btn active" style="cursor: default; pointer-events: none;">
            <i class="fas fa-store"></i> Reportes del Local
        </span>
        <a href="{{ route('reports.products') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-box"></i> Reportes por Producto
        </a>
        <a href="{{ route('reports.order-history') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-history"></i> Historial de Órdenes
        </a>
    </div>

    {{-- Filters --}}
    <div class="rp-card" style="display:inline-block; min-width: 388px; width:auto;">
        <form id="filterForm" method="GET" action="{{ route('reports.orders') }}">
            <div class="rp-filters">
                @if($userLocals->count() > 1)
                <div>
                    <label for="local_id">Local</label>
                    <select name="local_id" id="local_id" onchange="document.getElementById('filterForm').submit()">
                        @foreach($userLocals as $l)
                        <option value="{{ $l->local_id }}" {{ $local->local_id == $l->local_id ? 'selected' : '' }}>
                            {{ $l->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label for="period">Período</label>
                    <select name="period" id="period" onchange="handlePeriodChange(this.value)">
                        <option value="today"  {{ $period==='today'  ? 'selected' : '' }}>Hoy</option>
                        <option value="week"   {{ $period==='week'   ? 'selected' : '' }}>Esta Semana</option>
                        <option value="month"  {{ $period==='month'  ? 'selected' : '' }}>Este Mes</option>
                        <option value="year"   {{ $period==='year'   ? 'selected' : '' }}>Este Año</option>
                        <option value="custom" {{ $period==='custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                <div id="startDateDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label for="start_date">Desde</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}">
                </div>
                <div id="endDateDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label for="end_date">Hasta</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}">
                </div>
                <div id="filterBtnDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label style="visibility:hidden;">–</label>
                    <button type="submit" class="rp-btn rp-btn--green rp-btn--full">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Mensaje de error de validación de costo -->
    @if($productError)
    <div class="rp-card" style="background: #f8d7da; border-left: 4px solid #dc3545; border-radius: 8px;">
        <p style="color: #721c24; font-size: 1rem; margin: 0; font-weight: 600;">
            ⚠️ {{ $productError }}
        </p>
        <p style="color: #721c24; font-size: 0.9rem; margin: 8px 0 0 0;">
            Por favor selecciona otro producto que tenga costo registrado en el inventario.
        </p>
    </div>
    @endif

    {{-- Empty data message --}}
    @if(!$hasData && !$productError)
    <div class="rp-card" style="background: #fef3cd; border-left: 4px solid #8a6200;">
        <p style="color: #333; font-size: 1rem; margin: 0;">
            <strong>No hay ventas registradas en este período</strong>
        </p>
    </div>
    @endif

    {{-- Stats only shown if there's data --}}
    @if($hasData)
    <div class="rp-stats">
        {{-- Total --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Total de Pedidos</div>
                <div class="rp-stat__value rp-color--dark">{{ $orderStats['total'] }}</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--dark">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
        {{-- En Línea --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">En Línea</div>
                <div class="rp-stat__value rp-color--green">{{ $orderStats['web']['count'] }}</div>
                <div class="rp-stat__sub">{{ $orderStats['web']['percentage'] }}% del total</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999A5.002 5.002 0 003 15z"/></svg>
            </div>
        </div>
        {{-- Presencial --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Presenciales</div>
                <div class="rp-stat__value rp-color--orange">{{ $orderStats['presential']['count'] }}</div>
                <div class="rp-stat__sub">{{ $orderStats['presential']['percentage'] }}% del total</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--orange">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        {{-- Ingresos --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Ingresos Totales</div>
                <div class="rp-stat__value rp-color--gold" style="font-size:1.4rem;">₡{{ number_format($revenueStats['total'], 2) }}</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--gold" style="font-size: 2rem; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                ₡
            </div>
        </div>
    </div>
    @endif

    {{-- Charts --}}
    <div class="rp-charts">
        {{-- Doughnut --}}
        <div class="rp-card rp-card--green">
            <h2>Distribución de Pedidos</h2>
            <div class="rp-chart-canvas-wrap">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="rp-legend">
                <div class="rp-legend__item rp-legend__item--green">
                    <span class="rp-legend__dot rp-legend__dot--green"></span>
                    En Línea: {{ $orderStats['web']['count'] }}
                </div>
                <div class="rp-legend__item rp-legend__item--orange">
                    <span class="rp-legend__dot rp-legend__dot--orange"></span>
                    Presencial: {{ $orderStats['presential']['count'] }}
                </div>
            </div>
        </div>

        {{-- Bar --}}
        <div class="rp-card rp-card--orange">
            <h2>Comparativa de Ingresos</h2>
            <div class="rp-chart-canvas-wrap">
                <canvas id="barChart"></canvas>
            </div>
            <div class="rp-rev-legend">
                <div class="rp-rev-legend__item rp-rev-legend__item--green">
                    <div class="rp-rev-legend__label">En Línea</div>
                    <div class="rp-rev-legend__value rp-color--green">₡{{ number_format($revenueStats['web']['revenue'], 2) }}</div>
                    <div class="rp-rev-legend__pct">{{ $revenueStats['web']['percentage'] }}% del total</div>
                </div>
                <div class="rp-rev-legend__item rp-rev-legend__item--orange">
                    <div class="rp-rev-legend__label">Presencial</div>
                    <div class="rp-rev-legend__value rp-color--orange">₡{{ number_format($revenueStats['presential']['revenue'], 2) }}</div>
                    <div class="rp-rev-legend__pct">{{ $revenueStats['presential']['percentage'] }}% del total</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trend line --}}
    <div class="rp-card rp-card--dark">
        <h2>Tendencia Diaria de Pedidos</h2>
        <div class="rp-chart-canvas-wrap" style="height:360px;">
            <canvas id="trendChart"></canvas>
        </div>
        <div class="rp-note">
            <strong>Nota:</strong> Evolución de pedidos en línea (verde olivo) vs presenciales (naranja) día a día durante el período seleccionado.
        </div>
    </div>

    {{-- Summary table --}}
    <div class="rp-card">
        <h2>Resumen por Tipo</h2>
        <div style="overflow-x:auto;">
            <table class="rp-table">
                <thead>
                    <tr>
                        <th>Tipo de Pedido</th>
                        <th>Cantidad</th>
                        <th>Porcentaje</th>
                        <th>Ingresos</th>
                        <th>Promedio por Pedido</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="rp-dot-label"><span class="rp-dot rp-dot--green"></span> En Línea</span></td>
                        <td><strong>{{ $orderStats['web']['count'] }}</strong></td>
                        <td><span class="rp-badge rp-badge--green">{{ $orderStats['web']['percentage'] }}%</span></td>
                        <td><strong>₡{{ number_format($revenueStats['web']['revenue'], 2) }}</strong></td>
                        <td>
                            @if($orderStats['web']['count'] > 0)
                                ₡{{ number_format($revenueStats['web']['revenue'] / $orderStats['web']['count'], 2) }}
                            @else ₡0.00 @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="rp-dot-label"><span class="rp-dot rp-dot--orange"></span> Presencial</span></td>
                        <td><strong>{{ $orderStats['presential']['count'] }}</strong></td>
                        <td><span class="rp-badge rp-badge--orange">{{ $orderStats['presential']['percentage'] }}%</span></td>
                        <td><strong>₡{{ number_format($revenueStats['presential']['revenue'], 2) }}</strong></td>
                        <td>
                            @if($orderStats['presential']['count'] > 0)
                                ₡{{ number_format($revenueStats['presential']['revenue'] / $orderStats['presential']['count'], 2) }}
                            @else ₡0.00 @endif
                        </td>
                    </tr>
                    <tr class="rp-table__total">
                        <td>TOTAL</td>
                        <td>{{ $orderStats['total'] }}</td>
                        <td><span class="rp-badge rp-badge--dark">100%</span></td>
                        <td>₡{{ number_format($revenueStats['total'], 2) }}</td>
                        <td>
                            @if($orderStats['total'] > 0)
                                ₡{{ number_format($revenueStats['total'] / $orderStats['total'], 2) }}
                            @else ₡0.00 @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    {{-- Top Selling Items (Original Location - Hidden in Local View) --}}
    @if(false)
    <div class="rp-card rp-card--orange" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 0.95rem; margin: 0 0 0.75rem; font-weight: 700; color: var(--dark);">Productos Más Vendidos</h2>
        <div style="overflow-x:auto;">
            <table class="rp-table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Producto</th>
                        <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Cantidad Vendida</th>
                        <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Eventos de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topItems as $item)
                    <tr>
                        <td style="padding: 0.4rem 0.75rem;"><strong>{{ $item->name }}</strong></td>
                        <td style="padding: 0.4rem 0.75rem;">{{ $item->total_quantity }}</td>
                        <td style="padding: 0.4rem 0.75rem;">{{ $item->order_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Action buttons --}}
    <div class="rp-actions">
        <form method="GET" action="{{ route('reports.export-pdf') }}" style="display:inline;">
            <input type="hidden" name="local_id" value="{{ $local->local_id }}">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($period === 'custom')
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            @endif
            <button type="submit" class="rp-btn rp-btn--orange">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Exportar PDF
            </button>
        </form>
        <form method="GET" action="{{ route('reports.export-excel') }}" style="display:inline;">
            <input type="hidden" name="local_id" value="{{ $local->local_id }}">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($period === 'custom')
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            @endif
            <button type="submit" class="rp-btn rp-btn--green">
                📎 Exportar Excel
            </button>
        </form>
    </div>

</div>{{-- /rp-container --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const GREEN   = '#485a1a';
    const ORANGE  = '#b3621b';
    const GREEN_A = 'rgba(72,90,26,0.12)';
    const ORANGE_A= 'rgba(179,98,27,0.12)';

    const orderStats  = @json($orderStats);
    const revenueStats= @json($revenueStats);
    const dailyTrend  = @json($dailyTrend);

    /* ── Doughnut ── */
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: ['En Línea', 'Presencial'],
            datasets: [{
                data: [orderStats.web.percentage, orderStats.presential.percentage],
                backgroundColor: [GREEN, ORANGE],
                borderColor: '#fff',
                borderWidth: 3,
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 13, weight: 'bold' }, color: '#333', padding: 20, usePointStyle: true }
                },
                tooltip: {
                    callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + '%' }
                }
            }
        }
    });

    /* ── Bar (horizontal) ── */
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['En Línea', 'Presencial'],
            datasets: [{
                label: 'Ingresos (₡)',
                data: [revenueStats.web.revenue, revenueStats.presential.revenue],
                backgroundColor: [GREEN, ORANGE],
                borderColor: [GREEN, ORANGE],
                borderWidth: 0,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '₡' + new Intl.NumberFormat('es-CR').format(Math.round(ctx.parsed.x))
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { callback: v => '₡' + new Intl.NumberFormat('es-CR').format(v), font: { size: 11 } }
                },
                y: { ticks: { font: { size: 12, weight: 'bold' }, color: '#333' } }
            }
        }
    });

    /* ── Line trend ── */
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: dailyTrend.map(d => d.date),
            datasets: [
                {
                    label: 'En Línea',
                    data: dailyTrend.map(d => d.web),
                    borderColor: GREEN,
                    backgroundColor: GREEN_A,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: GREEN,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                },
                {
                    label: 'Presencial',
                    data: dailyTrend.map(d => d.presential),
                    borderColor: ORANGE,
                    backgroundColor: ORANGE_A,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: ORANGE,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 13, weight: 'bold' }, color: '#333', padding: 15, usePointStyle: true }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { size: 11 }, color: '#666' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#666', maxRotation: 45 }
                }
            }
        }
    });

    /* ── Period toggle ── */
    function handlePeriodChange(v, submit = true) {
        const isCustom = v === 'custom';
        document.getElementById('startDateDiv').style.display  = isCustom ? 'block' : 'none';
        document.getElementById('endDateDiv').style.display    = isCustom ? 'block' : 'none';
        document.getElementById('filterBtnDiv').style.display  = isCustom ? 'block' : 'none';
        if (!isCustom && submit) document.getElementById('filterForm').submit();
    }
    handlePeriodChange(document.getElementById('period').value, false);
</script>

@endsection