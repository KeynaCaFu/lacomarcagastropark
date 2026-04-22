@extends('layouts.app')

@section('title', isset($local) && $local ? 'Dashboard — ' . $local->name : 'Dashboard — La Comarca')

@section('content')

<div class="page-wrapper">
<div class="dash-container">
    <!-- KPIs PRINCIPALES -->
    <div class="stats-grid-main">
        <!-- NUEVOS KPIs DE VENTAS -->
        <div class="stat-card-main">
            <div class="stat-icon ic-total"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <div class="stat-title">Ventas (Último Mes)</div>
                <div class="stat-number">₡{{ number_format($salesLastMonth ?? 0, 0) }}</div>
            </div>
        </div>

        <div class="stat-card-main">
            <div class="stat-icon ic-active"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <div class="stat-title">Órdenes Activas</div>
                <div class="stat-number">{{ $activeOrders ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- KPIs SECUNDARIOS -->
    <div class="stats-grid-secondary">
        <div class="stat-card">
            <div class="stat-icon ic-total"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="stat-title">Productos</div>
                <div class="stat-number">{{ $totals['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-active"><i class="fas fa-check"></i></div>
            <div>
                <div class="stat-title">Disponibles</div>
                <div class="stat-number">{{ $totals['available'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-inactive"><i class="fas fa-ban"></i></div>
            <div>
                <div class="stat-title">No Disponibles</div>
                <div class="stat-number">{{ $totals['unavailable'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-upcoming"><i class="fas fa-tags"></i></div>
            <div>
                <div class="stat-title">Categorías</div>
                <div class="stat-number">{{ isset($categories) ? $categories->count() : 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon qi-stores"><i class="fas fa-truck"></i></div>
            <div>
                <div class="stat-title">Proveedores</div>
                <div class="stat-number">{{ $supplierTotals['total'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Accesos rápidos - 2 columnas -->
    <div class="quick-links-2col">
        <a class="quick-link" href="{{ route('products.index') }}" title="Ver productos">
            <div class="quick-icon qi-events"><i class="fas fa-box"></i></div>
            <div class="quick-text">
                <div class="quick-title">Productos</div>
                <div class="quick-hint">Gestiona tu catálogo</div>
            </div>
        </a>

        <a class="quick-link" href="{{ route('products.create') }}" title="Nuevo producto">
            <div class="quick-icon qi-new"><i class="fas fa-plus"></i></div>
            <div class="quick-text">
                <div class="quick-title">Nuevo Producto</div>
                <div class="quick-hint">Agregar al local</div>
            </div>
        </a>

        <a class="quick-link" href="{{ route('suppliers.index') }}" title="Ver proveedores">
            <div class="quick-icon qi-stores"><i class="fas fa-truck"></i></div>
            <div class="quick-text">
                <div class="quick-title">Proveedores</div>
                <div class="quick-hint">Gestiona tus proveedores</div>
            </div>
        </a>

        <a class="quick-link" href="{{ route('suppliers.create') }}" title="Nuevo proveedor">
            <div class="quick-icon qi-new"><i class="fas fa-plus"></i></div>
            <div class="quick-text">
                <div class="quick-title">Nuevo Proveedor</div>
                <div class="quick-hint">Agregar al local</div>
            </div>
        </a>
    </div>

<div class="cards-grid">

    <!-- GRÁFICO DE ÓRDENES POR ESTADO -->
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-chart-pie"></i> Distribución de Órdenes</h4>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 250px; max-width: 100%;">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

</div> <!-- cards-grid -->
</div> <!-- dash-container -->
</div> <!-- page-wrapper -->


@push('styles')
<style>
    .dash-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 8px; margin-top: 20px; }
    .stats-grid-main { display:grid; grid-template-columns: repeat(2, 1fr); gap:6px; margin-bottom: 4px; }
    .stat-card-main { background:#fff; border:2px solid #e5e7eb; border-radius:10px; padding:10px; display:flex; align-items:center; gap:8px; }
    .stats-grid-secondary { display:grid; grid-template-columns: repeat(5, 1fr); gap:6px; margin-bottom: 6px; }
    .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:8px 6px; display:flex; align-items:center; gap:5px; }
    .stat-icon { width:32px; height:32px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size: 14px; }
    .stat-title { font-size:9px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.01em; }
    .stat-number { font-size:16px; font-weight:800; color:#111827; line-height: 1.1; }
    .quick-links-2col { display:grid; grid-template-columns: repeat(2, 1fr); gap:6px; margin: 4px 0 6px; }
    .quick-link { display:flex; align-items:center; gap:6px; border:1px solid #e5e7eb; background:#ffffff; border-radius:8px; padding:8px 10px; text-decoration:none; color:#111827; transition:transform .2s ease, box-shadow .2s ease; font-size: 11px; }
    .quick-link:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
    .quick-icon { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size: 12px; }
    .qi-users { background:#e0f2fe; color:#0369a1; }
    .qi-stores { background:#ede9fe; color:#6d28d9; }
    .qi-events { background:#ffe4e6; color:#be123c; }
    .qi-new { background:#dcfce7; color:#166534; }
    .quick-text { display:flex; flex-direction:column; gap: 1px; }
    .quick-title { font-weight:700; font-size: 11px; }
    .quick-hint { font-size:8px; color:#6b7280; }
    .cards-grid { display:grid; grid-template-columns: 1fr 1fr; gap:8px; }
    @media (max-width: 1200px){ .stats-grid-secondary { grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); } }
    @media (max-width: 992px){ 
        .cards-grid { grid-template-columns: 1fr; } 
        .stats-grid-main { grid-template-columns: 1fr; }
        .quick-links-2col { grid-template-columns: 1fr; }
    }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; }
    .card-header { padding:6px 8px; display:flex; align-items:center; justify-content:space-between; border-bottom: 1px solid #e5e7eb; gap: 8px; }
    .card-title { margin:0; font-weight:700; color:#111827; font-size: 11px; white-space: nowrap; }
    .card-body { padding:6px 8px; }
    .table-scroll { max-height: 280px; overflow-y: scroll; overflow-x: hidden; }
    .table-scroll::-webkit-scrollbar { width: 6px; }
    .table-scroll::-webkit-scrollbar-track { background: transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background: transparent; }
    .table-scroll { scrollbar-width: none; }
    table { width:100%; border-collapse:collapse; font-size: 10px; }
    th, td { padding:5px 6px; border-bottom:1px solid #e5e7eb; text-align:left; }
    th { background:#f9fafb; font-weight:700; color:#374151; font-size: 9px; }
    .badge { display:inline-flex; align-items:center; gap:3px; padding:3px 6px; border-radius:999px; font-size:8px; font-weight:700; }
    .bd-active { background:#dcfce7; color:#166534; }
    .bd-inactive { background:#fee2e2; color:#991b1b; }
    .ic-total { background:#e0f2fe; color:#0369a1; }
    .ic-active { background:#dcfce7; color:#16a34a; }
    .ic-inactive { background:#fee2e2; color:#dc2626; }
    .ic-upcoming { background:#fef3c7; color:#b45309; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('keydown', function(e){
    if (!e.altKey) return;
    const key = (e.key || '').toLowerCase();
    if (key === 'p') { e.preventDefault(); window.location.href = @json(route('products.index')); }
    if (key === 'n') { e.preventDefault(); window.location.href = @json(route('products.create')); }
});

// Gráfico de órdenes por estado
document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('ordersChart');
    if (!chartCanvas) return;
    
    const ordersByStatus = @json($ordersByStatus ?? []);
    
    if (Object.keys(ordersByStatus).length === 0) return;
    
    const statusLabels = Object.keys(ordersByStatus);
    const statusCounts = Object.values(ordersByStatus);
    
    const colors = ['#b45309', '#6d28d9', '#dc2626', '#be123c', '#166534', '#991b1b', '#0369a1'];
    
    new Chart(chartCanvas, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: colors.slice(0, statusLabels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11 },
                        padding: 10,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => ({
                                text: label + ' (' + data.datasets[0].data[i] + ')',
                                fillStyle: data.datasets[0].backgroundColor[i],
                                hidden: false,
                                index: i
                            }));
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection