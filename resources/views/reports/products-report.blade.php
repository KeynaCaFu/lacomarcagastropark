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

    /* ── Buttons ── */
    .rp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1.5rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: .9rem;
        font-weight: 600;
        transition: all .2s;
        white-space: nowrap;
    }
    .rp-btn--green {
        background: var(--green);
        color: #fff;
    }
    .rp-btn--green:hover {
        background: var(--green-2);
    }
    .rp-btn--full {
        width: 100%;
    }

    /* ── Stats grid ── */
    .rp-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .rp-stat {
        background: #fff;
        border: 1px solid #e0ddd6;
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }
    .rp-stat__label {
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #666;
        margin-bottom: .35rem;
    }
    .rp-stat__value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        line-height: 1;
    }
    .rp-color--dark   { color: var(--dark); }
    .rp-color--green  { color: var(--green); }
    .rp-color--orange { color: var(--orange); }
    .rp-stat__sub {
        font-size: .8rem;
        color: #888;
        margin-top: .35rem;
    }
    .rp-stat__icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff;
        flex-shrink: 0;
    }
    .rp-stat__icon--dark   { background: var(--dark); }
    .rp-stat__icon--green  { background: var(--green); }
    .rp-stat__icon--orange { background: var(--orange); }
    .rp-stat__icon svg {
        width: 32px;
        height: 32px;
    }

    /* ── Charts grid ── */
    .rp-charts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .rp-chart-canvas-wrap {
        position: relative;
        height: 350px;
    }
    .rp-chart-canvas-wrap canvas {
        position: absolute;
        top: 0;
        left: 0;
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
        text-align: center;
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
        text-align: center;
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

    /* ── View Toggle ── */
    .rp-view-toggle {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid #e0ddd6;
        padding-bottom: 1rem;
    }
    .rp-view-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        color: #999;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        position: relative;
        bottom: -2px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .rp-view-btn.active {
        color: var(--green);
        border-bottom-color: var(--green);
    }
    .rp-view-btn:hover {
        color: var(--dark);
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
</style>
@endpush

<div class="rp-container">

    {{-- Header --}}
    <div class="rp-header" style="margin-bottom:1.75rem;">
        <h1>Reportes por Producto</h1>
        <p>Análisis detallado de ventas por producto — {{ $local->name }}</p>
    </div>

    <!-- Navigation: Local vs Productos -->
    <div class="rp-view-toggle">
        <a href="{{ route('reports.orders') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-store"></i> Reportes del Local
        </a>
        <span class="rp-view-btn active" style="cursor: default; pointer-events: none;">
            <i class="fas fa-box"></i> Reportes por Producto
        </span>
    </div>

    {{-- Filters --}}
    <div class="rp-card">
        <form id="filterForm" method="GET" onsubmit="event.preventDefault(); loadProductData();">
            <div class="rp-filters">
                <div>
                    <label for="period">Período</label>
                    <select name="period" id="period" onchange="handlePeriodChange()">
                        <option value="today">Hoy</option>
                        <option value="week">Esta Semana</option>
                        <option value="month" selected>Este Mes</option>
                        <option value="year">Este Año</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
                <div id="startDateDiv" style="display:none;">
                    <label for="start_date">Desde</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}">
                </div>
                <div id="endDateDiv" style="display:none;">
                    <label for="end_date">Hasta</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}">
                </div>
                <div>
                    <label style="visibility:hidden;">–</label>
                    <button type="button" class="rp-btn rp-btn--green rp-btn--full" onclick="loadProductData()">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Error message for invalid product --}}
    @if($productError)
    <div class="rp-card" style="background: #f8d7da; border-left: 4px solid #dc3545; border-radius: 8px;">
        <p style="color: #721c24; font-size: 1rem; margin: 0; font-weight: 600;">
            ⚠️ {{ $productError }}
        </p>
        <p style="color: #721c24; font-size: 0.9rem; margin: 8px 0 0 0;">
            Por favor selecciona un producto que tenga costo registrado en el inventario.
        </p>
    </div>
    @endif

    {{-- Empty data message --}}
    @if(!$hasData && !$productError)
    <div class="rp-card" style="background: #fef3cd; border-left: 4px solid #8a6200;">
        <p style="color: #333; font-size: 1rem; margin: 0;">
            <strong>No hay ventas registradas para este producto en el período seleccionado</strong>
        </p>
    </div>
    @endif

    {{-- Stats --}}
    @if($hasData)
    <div class="rp-stats">
        {{-- Total --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Total de Pedidos</div>
                <div class="rp-stat__value rp-color--dark" id="stat-total">{{ $orderStats['total'] }}</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--dark">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
        {{-- Web Orders --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">En Línea</div>
                <div class="rp-stat__value rp-color--green" id="stat-web-count">{{ $orderStats['web']['count'] }}</div>
                <div class="rp-stat__sub" id="stat-web-pct">{{ $orderStats['web']['percentage'] }}%</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999A5.002 5.002 0 003 15z"/></svg>
            </div>
        </div>
        {{-- Presential Orders --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Presenciales</div>
                <div class="rp-stat__value rp-color--orange" id="stat-presential-count">{{ $orderStats['presential']['count'] }}</div>
                <div class="rp-stat__sub" id="stat-presential-pct">{{ $orderStats['presential']['percentage'] }}%</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--orange">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        {{-- Revenue --}}
        <div class="rp-stat">
            <div>
                <div class="rp-stat__label">Ingreso Total</div>
                <div class="rp-stat__value rp-color--green" id="stat-revenue">₡{{ number_format($revenueStats['total'], 2) }}</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="rp-charts">
        {{-- Bar Chart (Top Products) --}}
        <div class="rp-card rp-card--green">
            <h2>Top 10 Productos Más Vendidos</h2>
            <div class="rp-chart-canvas-wrap">
                <canvas id="topProductsChart"></canvas>
            </div>
            <div style="margin-top: 1rem; font-size: 0.85rem; color: #666;">
                <p style="margin: 0;">Cantidad de unidades vendidas por producto</p>
            </div>
        </div>

        {{-- Bar Chart (Revenue by Product) --}}
        <div class="rp-card rp-card--orange">
            <h2>Ingresos Generados por Producto</h2>
            <div class="rp-chart-canvas-wrap">
                <canvas id="productRevenueChart"></canvas>
            </div>
            <div style="margin-top: 1rem; font-size: 0.85rem; color: #666;">
                <p style="margin: 0;">Ingresos totales generados por cada producto</p>
            </div>
        </div>
    </div>

    {{-- Trend Chart --}}
    <div class="rp-card rp-card--dark">
        <h2>Tendencia Diaria</h2>
        <div class="rp-chart-canvas-wrap" style="height: 300px;">
            <canvas id="trendChart"></canvas>
        </div>
        <div class="rp-note">
            <strong>Nota:</strong> La gráfica muestra la evolución de pedidos por origen durante el período seleccionado.
        </div>
    </div>

    {{-- Items Summary Table --}}
    <div id="items-table-container"></div>

    {{-- Top Selling Items --}}
    <div id="top-items-container"></div>

    @endif

    {{-- Action buttons --}}
    @if($hasData)
    <div class="rp-actions">
        <form method="GET" action="{{ route('reports.export-pdf') }}" style="display:inline;">
            <input type="hidden" name="local_id" value="{{ $local->local_id }}">
            <input type="hidden" name="product_id" value="{{ $productId }}">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($period === 'custom')
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            @endif
            <button type="submit" class="rp-btn rp-btn--orange">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </form>
        <form method="GET" action="{{ route('reports.export-excel') }}" style="display:inline;">
            <input type="hidden" name="local_id" value="{{ $local->local_id }}">
            <input type="hidden" name="product_id" value="{{ $productId }}">
            <input type="hidden" name="period" value="{{ $period }}">
            @if($period === 'custom')
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            @endif
            <button type="submit" class="rp-btn rp-btn--green">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
        </form>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const GREEN   = '#485a1a';
    const ORANGE  = '#b3621b';
    const GREEN_A = 'rgba(72,90,26,0.12)';
    const ORANGE_A= 'rgba(179,98,27,0.12)';

    let pieChart, barChart, trendChart;

    /* ── Handle period change ── */
    function handlePeriodChange() {
        const period = document.getElementById('period').value;
        const startDiv = document.getElementById('startDateDiv');
        const endDiv = document.getElementById('endDateDiv');

        if (period === 'custom') {
            startDiv.style.display = 'block';
            endDiv.style.display = 'block';
        } else {
            startDiv.style.display = 'none';
            endDiv.style.display = 'none';
        }
        
        // Load data when period changes
        loadProductData();
    }

    /* ── Load Product Data via AJAX ── */
    function loadProductData() {
        const productIdElement = document.getElementById('product_id');
        const productId = productIdElement ? productIdElement.value : '';
        
        const periodElement = document.getElementById('period');
        const period = periodElement ? periodElement.value : 'month';
        
        const startDateElement = document.getElementById('start_date');
        const startDate = startDateElement ? startDateElement.value : '';
        
        const endDateElement = document.getElementById('end_date');
        const endDate = endDateElement ? endDateElement.value : '';

        const params = new URLSearchParams({
            ...(productId && { product_id: productId }),
            period: period,
            ...(period === 'custom' && { start_date: startDate, end_date: endDate })
        });

        console.log('Loading product data with params:', params.toString());

        fetch(`{{ route('reports.api.products') }}?${params}`)
        .then(response => response.json())
        .then(data => {
            console.log('API Response:', data);
            
            if (!data.hasData) {
                console.log('No data available');
                // Show empty state
                document.getElementById('items-table-container').innerHTML = '<div class="rp-card" style="background: #fef3cd; border-left: 4px solid #8a6200;"><p style="color: #333; margin: 0;">No hay datos disponibles para este período</p></div>';
                document.getElementById('top-items-container').innerHTML = '';
                return;
            }

            const o = data.orderStats;
            const r = data.revenueStats;

            // Update stats
            document.getElementById('stat-total').textContent = new Intl.NumberFormat('es-CR').format(o.total);
            document.getElementById('stat-web-count').textContent = new Intl.NumberFormat('es-CR').format(o.web.count);
            document.getElementById('stat-web-pct').textContent = o.web.percentage + '%';
            document.getElementById('stat-presential-count').textContent = new Intl.NumberFormat('es-CR').format(o.presential.count);
            document.getElementById('stat-presential-pct').textContent = o.presential.percentage + '%';
            document.getElementById('stat-revenue').textContent = '₡' + new Intl.NumberFormat('es-CR').format(Math.round(r.total));

            // Destroy existing charts
            if (pieChart) pieChart.destroy();
            if (barChart) barChart.destroy();
            if (trendChart) trendChart.destroy();

            // Top Products Chart (Horizontal Bar)
            pieChart = new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: (data.topItems || []).slice(0, 10).map(p => p.name),
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: (data.topItems || []).slice(0, 10).map(p => p.total_quantity),
                        backgroundColor: GREEN,
                        borderColor: GREEN,
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: ctx => 'Cantidad: ' + ctx.parsed.x + ' unid.' }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 11 } }
                        },
                        y: { ticks: { font: { size: 11, weight: 600 }, color: '#333' } }
                    }
                }
            });

            // Product Revenue Chart (Horizontal Bar)
            barChart = new Chart(document.getElementById('productRevenueChart'), {
                type: 'bar',
                data: {
                    labels: (data.topItems || []).slice(0, 10).map(p => p.name),
                    datasets: [{
                        label: 'Ingresos',
                        data: (data.topItems || []).slice(0, 10).map(p => p.revenue || 0),
                        backgroundColor: ORANGE,
                        borderColor: ORANGE,
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: ctx => '₡' + new Intl.NumberFormat('es-CR').format(Math.round(ctx.parsed.x)) }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { callback: v => '₡' + new Intl.NumberFormat('es-CR').format(v), font: { size: 11 } }
                        },
                        y: { ticks: { font: { size: 11, weight: 600 }, color: '#333' } }
                    }
                }
            });

            // Trend Chart
            trendChart = new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: data.dailyTrend.map(d => d.date),
                    datasets: [
                        {
                            label: 'En Línea',
                            data: data.dailyTrend.map(d => d.web),
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
                            data: data.dailyTrend.map(d => d.presential),
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

            // Update items table
            updateItemsTable(data.items);
            // Update top items
            if (data.topItems) {
                updateTopItems(data.topItems);
            }
        })
        .catch(error => {
            console.error('Error loading data:', error);
            alert('Error al cargar los datos');
        });
    }

    /* ── Update Top Items Table ── */
    function updateTopItems(topItems) {
        const container = document.getElementById('top-items-container');
        
        if (!topItems || topItems.length === 0) {
            container.innerHTML = '';
            return;
        }

        let html = `
        <div class="rp-card rp-card--green" style="padding: 1.25rem;">
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
                    <tbody>`;

        topItems.slice(0, 10).forEach((item, index) => {
            html += `
                        <tr>
                            <td style="padding: 0.4rem 0.75rem;"><strong>${index + 1}. ${item.name}</strong></td>
                            <td style="padding: 0.4rem 0.75rem;">${item.total_quantity}</td>
                            <td style="padding: 0.4rem 0.75rem;">${item.order_count}</td>
                        </tr>`;
        });

        html += `
                    </tbody>
                </table>
            </div>
        </div>`;

        container.innerHTML = html;
    }

    /* ── Update Items Table ── */
    function updateItemsTable(items) {
        const container = document.getElementById('items-table-container');
        
        if (!items || items.length === 0) {
            container.innerHTML = '';
            return;
        }

        let html = `
        <div class="rp-card rp-card--orange" style="padding: 1.25rem;">
            <h2 style="font-size: 0.95rem; margin: 0 0 0.75rem; font-weight: 700; color: var(--dark);">Detalle de Ventas</h2>
            <div style="overflow-x:auto;">
                <table class="rp-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Origen</th>
                            <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Cantidad</th>
                            <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Ingresos</th>
                            <th style="padding: 0.5rem 0.75rem; font-size: 0.75rem;">Prom. Evento</th>
                        </tr>
                    </thead>
                    <tbody>`;

        items.forEach(item => {
            const avgPerOrder = item.order_count > 0 ? (item.total_quantity / item.order_count).toFixed(2) : 0;
            html += `
                        <tr>
                            <td style="padding: 0.4rem 0.75rem;"><strong>${item.type === 'web' ? 'En Línea' : 'Presencial'}</strong></td>
                            <td style="padding: 0.4rem 0.75rem;">${item.total_quantity}</td>
                            <td style="padding: 0.4rem 0.75rem;">₡${new Intl.NumberFormat('es-CR').format(Math.round(item.revenue))}</td>
                            <td style="padding: 0.4rem 0.75rem;">${avgPerOrder} unid.</td>
                        </tr>`;
        });

        html += `
                    </tbody>
                </table>
            </div>
        </div>`;

        container.innerHTML = html;
    }

    // Load data on page load if product is selected
    document.addEventListener('DOMContentLoaded', () => {
        loadProductData();
    });
</script>

@endsection
