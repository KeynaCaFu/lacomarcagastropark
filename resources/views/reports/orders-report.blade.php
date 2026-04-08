@extends('layouts.app')

@section('content')

@push('styles')
<style>
    :root {
        --dark:     #181818;
        --green:    #485a1a;
        --green-2:  #5a6e20;
        --orange:   #b3621b;
        --orange-2: #9a5214;
        --primary:  #181818;
        --secondary:#ff9900;
        --light:    #f7f7f7;
        --gray:     #b0b0b0;
    }

    /* ── Layout ── */
    .rp-container {
        max-width: 100%;
        width: 100%;
        margin: 0;
        padding: 2rem 1.5rem;
        font-family: system-ui, sans-serif;
        color: var(--dark);
    }

    /* ── Header ── */
    .rp-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 .25rem;
    }
    .rp-header p {
        color: #555;
        margin: 0;
        font-size: .95rem;
    }

    /* ── Card base ── */
    .rp-card {
        background: #fff;
        border: 1px solid #e0ddd6;
        border-radius: 10px;
        padding: 1.75rem;
        margin-bottom: 2rem;
    }
    .rp-card h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 1rem;
    }

    /* ── Accent top borders ── */
    .rp-card--green  { border-top: 4px solid var(--green); }
    .rp-card--orange { border-top: 4px solid var(--orange); }
    .rp-card--dark   { border-top: 4px solid var(--dark); }

    /* ── Filter form ── */
    .rp-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1.2rem;
        align-items: end;
    }
    @media (max-width: 768px) {
        .rp-filters { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .rp-filters { grid-template-columns: 1fr; }
    }
    .rp-filters label {
        display: block;
        font-size: .8rem;
        font-weight: 600;
        color: #444;
        margin-bottom: .35rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .rp-filters select,
    .rp-filters input[type="date"] {
        width: 100%;
        padding: .5rem .75rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: .9rem;
        background: #fafafa;
        color: var(--dark);
    }
    .rp-filters select:focus,
    .rp-filters input:focus {
        outline: 2px solid var(--green);
        outline-offset: 1px;
    }
    .rp-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.25rem;
        border: none;
        border-radius: 6px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s;
    }
    .rp-btn--green  { background: var(--green);  color: #fff; }
    .rp-btn--green:hover  { background: var(--green-2); }
    .rp-btn--orange { background: var(--orange); color: #fff; }
    .rp-btn--orange:hover { background: var(--orange-2); }
    .rp-btn--dark   { background: var(--dark);   color: #fff; }
    .rp-btn--dark:hover   { background: #2d2d2d; }
    .rp-btn--full { width: 100%; justify-content: center; }

    /* ── Stat cards grid ── */
    .rp-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.2rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1200px) {
        .rp-stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .rp-stats { grid-template-columns: 1fr; }
    }
    .rp-stat {
        background: #fff;
        border: 1px solid #e0ddd6;
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .rp-stat__label {
        font-size: .8rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .25rem;
    }
    .rp-stat__value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
    }
    .rp-stat__sub {
        font-size: .75rem;
        color: #888;
        margin-top: .2rem;
    }
    .rp-stat__icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .rp-stat__icon svg { width: 20px; height: 20px; }

    /* icon color variants */
    .rp-stat__icon--dark   { background: #e8e8e8; color: var(--dark); }
    .rp-stat__icon--green  { background: #e8edda; color: var(--green); }
    .rp-stat__icon--orange { background: #f5e8db; color: var(--orange); }
    .rp-stat__icon--gold   { background: #fef3cd; color: #8a6200; }

    /* value colors */
    .rp-color--dark   { color: var(--dark); }
    .rp-color--green  { color: var(--green); }
    .rp-color--orange { color: var(--orange); }
    .rp-color--gold   { color: #8a6200; }

    /* ── Charts grid ── */
    .rp-charts {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1024px) {
        .rp-charts { grid-template-columns: 1fr; }
    }
    .rp-chart-canvas-wrap {
        position: relative;
        height: 340px;
        margin-bottom: 1rem;
    }

    /* ── Chart legend pills ── */
    .rp-legend {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
    }
    .rp-legend__item {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .75rem;
        border-radius: 6px;
        font-size: .85rem;
        font-weight: 600;
        color: var(--dark);
    }
    .rp-legend__item--green  { background: #e8edda; }
    .rp-legend__item--orange { background: #f5e8db; }
    .rp-legend__dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .rp-legend__dot--green  { background: var(--green); }
    .rp-legend__dot--orange { background: var(--orange); }

    /* revenue legend (taller) */
    .rp-rev-legend {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .75rem;
    }
    .rp-rev-legend__item {
        padding: .75rem 1rem;
        border-radius: 6px;
        border-left: 4px solid;
    }
    .rp-rev-legend__item--green  { background: #e8edda; border-color: var(--green); }
    .rp-rev-legend__item--orange { background: #f5e8db; border-color: var(--orange); }
    .rp-rev-legend__label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #666;
    }
    .rp-rev-legend__value { font-size: 1.05rem; font-weight: 800; margin: .2rem 0 .1rem; }
    .rp-rev-legend__pct   { font-size: .75rem; color: #888; }

    /* ── Info note ── */
    .rp-note {
        background: #f0f4e8;
        border-left: 4px solid var(--green);
        padding: .75rem 1rem;
        border-radius: 0 6px 6px 0;
        font-size: .82rem;
        color: #333;
        margin-top: 1rem;
    }

    /* ── Summary table ── */
    .rp-table { width: 100%; border-collapse: collapse; }
    .rp-table thead tr {
        background: #f5f3ee;
        border-bottom: 2px solid #ddd;
    }
    .rp-table th {
        padding: .75rem 1rem;
        text-align: left;
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #555;
    }
    .rp-table td {
        padding: .75rem 1rem;
        font-size: .9rem;
        border-bottom: 1px solid #eee;
        color: var(--dark);
    }
    .rp-table tr.rp-table__total {
        background: #f5f3ee;
        font-weight: 800;
    }
    .rp-table tr:hover:not(.rp-table__total) { background: #fafaf7; }
    .rp-dot-label {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }
    .rp-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    .rp-dot--green  { background: var(--green); }
    .rp-dot--orange { background: var(--orange); }
    .rp-badge {
        display: inline-block;
        padding: .2rem .6rem;
        border-radius: 4px;
        font-size: .8rem;
        font-weight: 700;
    }
    .rp-badge--green  { background: #e8edda; color: var(--green); }
    .rp-badge--orange { background: #f5e8db; color: #7a3f0e; }
    .rp-badge--dark   { background: #e8e8e8; color: var(--dark); }

    /* ── Action buttons row ── */
    .rp-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    /* ── Responsive ── */
    @media (max-width: 1200px) {
        .rp-stat { padding: 1rem 1.25rem; }
        .rp-stat__value { font-size: 1.5rem; }
    }

    @media (max-width: 768px) {
        .rp-container { padding: 1.5rem 1rem; }
        .rp-card { padding: 1.25rem; }
        .rp-stat { padding: .9rem 1rem; }
        .rp-stat__value { font-size: 1.2rem; }
        .rp-stat__icon { width: 38px; height: 38px; }
        .rp-chart-canvas-wrap { height: 280px; }
        .rp-table { font-size: 0.75rem; }
        .rp-table th, .rp-table td { padding: 0.5rem 0.75rem; }
        .rp-actions { justify-content: stretch; }
        .rp-actions .rp-btn { flex: 1; justify-content: center; }
    }

    @media (max-width: 480px) {
        .rp-container { padding: 1rem; }
        .rp-card { padding: 1rem; margin-bottom: 1.5rem; }
        .rp-header h1 { font-size: 1.5rem; }
        .rp-stat { flex-direction: column; text-align: center; }
        .rp-stat__value { font-size: 1.1rem; }
        .rp-charts { grid-template-columns: 1fr; gap: 1rem; }
        .rp-chart-canvas-wrap { height: 250px; }
        .rp-legend { grid-template-columns: 1fr; }
        .rp-rev-legend { grid-template-columns: 1fr; }
    }

    /* ── Print ── */
    @media print {
        .rp-filters, .rp-actions { display: none !important; }
        .rp-card { page-break-inside: avoid; box-shadow: none; border: 1px solid #ccc; }
        .rp-container { padding: 1rem; }
    }
</style>
@endpush

<div class="rp-container">

    {{-- Header --}}
    <div class="rp-header" style="margin-bottom:1.75rem;">
        <h1>Reportes de Pedidos</h1>
        <p>Análisis de pedidos en línea vs. presenciales — {{ $local->name }}</p>
    </div>

    {{-- Filters --}}
    <div class="rp-card">
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
                    <select name="period" id="period" onchange="handlePeriodChange()">
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
                <div>
                    <label style="visibility:hidden;">–</label>
                    <button type="submit" class="rp-btn rp-btn--green rp-btn--full">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Empty data message --}}
    @if(!$hasData)
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
            <div class="rp-stat__icon rp-stat__icon--gold">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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

    {{-- Top Selling Items --}}
    @if($hasData && $topItems->count() > 0)
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
    function handlePeriodChange() {
        const v = document.getElementById('period').value;
        document.getElementById('startDateDiv').style.display = v === 'custom' ? 'block' : 'none';
        document.getElementById('endDateDiv').style.display   = v === 'custom' ? 'block' : 'none';
    }
    handlePeriodChange();
</script>

@endsection