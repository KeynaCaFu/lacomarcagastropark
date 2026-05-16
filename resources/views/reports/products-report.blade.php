@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
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
        <a href="{{ route('reports.order-history') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-history"></i> Historial de Órdenes
        </a>
    </div>

    {{-- Filters --}}
    <div class="rp-card">
        <form id="filterForm" method="GET" onsubmit="event.preventDefault(); loadProductData();">
            <div class="rp-filters">
                <div>
                    <label for="period">Período</label>
                    <select name="period" id="period" onchange="handlePeriodChange()">
                        <option value="today" selected>Hoy</option>
                        <option value="week">Esta Semana</option>
                        <option value="month">Este Mes</option>
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
                <div id="filterBtnDiv" style="display:none;">
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
                <div class="rp-stat__value rp-color--gold" id="stat-revenue">₡{{ number_format($revenueStats['total'], 2) }}</div>
            </div>
            <div class="rp-stat__icon rp-stat__icon--gold" style="font-size: 2rem; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                ₡
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
        const isCustom = period === 'custom';
        document.getElementById('filterBtnDiv').style.display = isCustom ? 'block' : 'none';

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
