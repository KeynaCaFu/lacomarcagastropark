@extends('layouts.app')

@section('title', 'Dashboard — Administrador Principal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endpush

@push('styles')
    <style>
        .dash-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 12px; margin-top: 30px; }
        .stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:10px; margin-bottom: 12px; }
        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px; display:flex; align-items:center; gap:8px; }
        .stat-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size: 18px; }
        .ic-total { background:#e0f2fe; color:#0369a1; }
        .ic-active { background:#dcfce7; color:#16a34a; }
        .ic-inactive { background:#fee2e2; color:#dc2626; }
        .ic-upcoming { background:#fef3c7; color:#b45309; }
        .stat-title { font-size:10px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.01em; }
        .stat-number { font-size:20px; font-weight:800; color:#111827; }
        .live-widget { animation: pulse 1s infinite; }
        .pulse { animation: pulse 0.5s; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .quick-links { display:grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap:8px; margin: 6px 0 12px; }
        .quick-link { display:flex; align-items:center; gap:8px; border:1px solid #e5e7eb; background:#ffffff; border-radius:10px; padding:10px 12px; text-decoration:none; color:#111827; transition:transform .2s ease, box-shadow .2s ease; font-size: 13px; }
        .quick-link:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
        .quick-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size: 16px; }
        .qi-users { background:#e0f2fe; color:#0369a1; }
        .qi-stores { background:#ede9fe; color:#6d28d9; }
        .qi-events { background:#ffe4e6; color:#be123c; }
        .qi-new { background:#dcfce7; color:#166534; }
        .quick-text { display:flex; flex-direction:column; }
        .quick-title { font-weight:700; font-size: 13px; }
        .quick-hint { font-size:10px; color:#6b7280; }
        .cards-grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
        @media (max-width: 992px){ .cards-grid{ grid-template-columns: 1fr; } }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; }
        .card-header { padding:10px 12px; display:flex; align-items:center; justify-content:space-between; }
        .card-title { margin:0; font-weight:700; color:#111827; font-size: 14px; }
        .card-body { padding:10px 12px; }
        .table-scroll { max-height: 280px; overflow-y: scroll; overflow-x: hidden; }
        .table-scroll::-webkit-scrollbar { width: 6px; }
        .table-scroll::-webkit-scrollbar-track { background: transparent; }
        .table-scroll::-webkit-scrollbar-thumb { background: transparent; }
        .table-scroll { scrollbar-width: none; }
        table { width:100%; border-collapse:collapse; font-size: 12px; }
        th, td { padding:6px 8px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { background:#f9fafb; font-weight:700; color:#374151; font-size: 11px; }
        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 8px; border-radius:999px; font-size:10px; font-weight:700; }
        .bd-active { background:#dcfce7; color:#166534; }
        .bd-inactive { background:#fee2e2; color:#991b1b; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .dash-container { margin-top: 10px; padding: 10px; }
            .stat-number { font-size: 18px; }
        }
        @media (max-width: 767.98px) {
            .dash-container { padding: 8px; border-radius: 8px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 10px; }
            .stat-title { font-size: 9px; }
            .stat-number { font-size: 18px; }
            .quick-links { grid-template-columns: 1fr 1fr; gap: 6px; }
            .quick-link { padding: 8px 10px; font-size: 12px; }
            .quick-title { font-size: 12px; }
            .quick-hint { font-size: 9px; }
            .card-header { padding: 8px 10px; }
            .card-title { font-size: 13px; }
            .card-body { padding: 8px 10px; }
            th, td { padding: 6px 6px; font-size: 11px; line-height: 1.3; }
        }
        @media (max-width: 575.98px) {
            .dash-container { margin-top: 4px; padding: 6px; border-radius: 6px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 6px; }
            .stat-card { padding: 8px; }
            .stat-title { font-size: 8px; }
            .stat-number { font-size: 16px; }
            .quick-links { grid-template-columns: 1fr; gap: 6px; }
            .quick-title { font-size: 11px; }
            .quick-hint { font-size: 8px; }
            .card-title { font-size: 12px; }
            th, td { padding: 4px 4px; font-size: 10px; line-height: 1.2; }
            .badge { font-size: 9px; padding: 3px 6px; }
        }
    </style>
@endpush

@section('content')
<div class="page-wrapper">
<div class="dash-container">
    <div class="stats-grid">

        <!-- KPIs: Usuarios -->
        <div class="stat-card">
            <div class="stat-icon ic-total"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-title">Usuarios (Total)</div>
                <div class="stat-number">{{ $totalUsuarios }}</div>
            </div>
        </div>
       

        <!-- KPIs: Eventos -->
        <div class="stat-card">
            <div class="stat-icon ic-total"><i class="fas fa-calendar-days"></i></div>
            <div>
                <div class="stat-title">Eventos (Total)</div>
                <div class="stat-number">{{ $totalEventos }}</div>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon ic-upcoming"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="stat-title">Eventos Próximos</div>
                <div class="stat-number">{{ $eventosProximos }}</div>
            </div>
        </div>
    </div>
    </div>

    <div class="cards-grid">
        <!-- Ranking de Locales Más Activos -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-star"></i> 🏆 Ranking Locales Más Activos (Último Mes)</h4>
            </div>
            <div class="card-body">
                @if($rankingStores->isNotEmpty())
                    <div class="table-scroll">
                    <table id="ranking-stores">
                        <thead>
                            <tr>
                                <th style="width:40px; text-align:center;">#</th>
                                <th>Local</th>
                                <th style="width:80px; text-align:right;">Órdenes</th>
                                <th style="width:100px; text-align:center;">% Total</th>
                                <th style="width:100px; text-align:center;">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rankingStores as $idx => $store)
                                <tr>
                                    <td style="text-align:center; font-weight:700;">{{ $idx + 1 }}</td>
                                    <td>{{ $store['name'] }}</td>
                                    <td style="text-align:right;">{{ $store['orders_count'] }}</td>
                                    <td style="text-align:center; font-weight:700; color:#0369a1;">{{ $store['percentage'] }}%</td>
                                    <td style="text-align:center;">
                                        @php
                                            $rating = $store['rating'];
                                            $fullStars = floor($rating);
                                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        @endphp
                                        @for($i = 0; $i < $fullStars; $i++)
                                            <i class="fas fa-star" style="color:#fbbf24;"></i>
                                        @endfor
                                        @if($hasHalfStar)
                                            <i class="fas fa-star-half-alt" style="color:#fbbf24;"></i>
                                        @endif
                                        @for($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++)
                                            <i class="far fa-star" style="color:#d1d5db;"></i>
                                        @endfor
                                        <span style="font-size:12px; margin-left:4px; color:#6b7280;">{{ number_format($rating, 1) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <div style="color:#6b7280; text-align:center; padding:20px;">
                        <i class="fas fa-info-circle"></i> No hay datos registrados en el último mes
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Gerentes -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-user-tie"></i> Gerentes - Porcentaje de Ventas (Último Mes)</h4>
            </div>
            <div class="card-body">
                @if($topManagers->count())
                    <canvas id="managersChart" height="180"></canvas>
                @else
                    <div style="color:#6b7280;">No hay gerentes con locales asignados.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Atajos de teclado en el dashboard del Administrador Principal
document.addEventListener('keydown', function(e){
    if (!e.altKey) return;
    const key = (e.key || '').toLowerCase();
    if (key === 'u') { e.preventDefault(); window.location.href = @json(route('users.index')); }
    if (key === 'l') { e.preventDefault(); window.location.href = @json(route('locales.index')); }
    if (key === 'e') { e.preventDefault(); window.location.href = @json(route('eventos.index')); }
    if (key === 'n') { e.preventDefault(); window.location.href = @json(route('eventos.create')); }
});

// Actualizar datos del dashboard en tiempo real cada 10 segundos
async function updateDashboardMetrics() {
    try {
        // Obtener ranking de locales
        let response = await fetch('{{ route("api.admin.stores.ranking") }}');
        if (response.ok) {
            let data = await response.json();
            if (data.ranking && data.ranking.length > 0) {
                updateRankingTable(data.ranking);
            }
        }
    } catch (error) {
        console.error('Error actualizando ranking:', error);
    }
}

// Actualizar tabla de ranking
function updateRankingTable(ranking) {
    const tbody = document.querySelector('#ranking-stores tbody');
    if (!tbody) return;
    
    tbody.innerHTML = ranking.map((store, index) => {
        const rating = parseFloat(store.rating);
        const fullStars = Math.floor(rating);
        const hasHalfStar = (rating - fullStars) >= 0.5;
        
        let stars = '';
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star" style="color:#fbbf24;"></i>';
        }
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt" style="color:#fbbf24;"></i>';
        }
        for (let i = fullStars + (hasHalfStar ? 1 : 0); i < 5; i++) {
            stars += '<i class="far fa-star" style="color:#d1d5db;"></i>';
        }
        stars += `<span style="font-size:12px; margin-left:4px; color:#6b7280;">${rating.toFixed(1)}</span>`;
        
        return `
            <tr>
                <td style="text-align:center; font-weight:700;">${index + 1}</td>
                <td>${store.name}</td>
                <td style="text-align:right;">${store.orders_count}</td>
                <td style="text-align:center; font-weight:700; color:#0369a1;">${parseFloat(store.percentage).toFixed(2)}%</td>
                <td style="text-align:center;">${stars}</td>
            </tr>
        `;
    }).join('');
}

// Inicializar actualización automática cuando se carga el documento
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Gerentes - Porcentaje de Ventas del Último Mes
    const managersChart = document.getElementById('managersChart');
    if (managersChart) {
        const managers = @json($topManagers);
        const labels = managers.map(m => m.full_name);
        const data = managers.map(m => parseFloat(m.percentage));
        
        // Generar colores dinámicamente para cada gerente
        const colors = [
            '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981',
            '#06b6d4', '#ef4444', '#8b7355', '#14b8a6', '#f97316',
            '#6366f1', '#ec4899', '#a855f7', '#0ea5e9', '#22c55e'
        ];
        const borderColors = [
            '#1e40af', '#6d28d9', '#be123c', '#b45309', '#047857',
            '#0891b2', '#dc2626', '#5c4033', '#0d9488', '#c2410c',
            '#4f46e5', '#be123c', '#7c3aed', '#0369a1', '#15803d'
        ];
        
        const backgroundColor = managers.map((_, idx) => colors[idx % colors.length]);
        const borderColor = managers.map((_, idx) => borderColors[idx % borderColors.length]);
        
        new Chart(managersChart, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Porcentaje de Ventas (%)',
                    data: data,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    updateDashboardMetrics(); // Actualizar ranking al cargar
    setInterval(updateDashboardMetrics, 15000); // Cada 15 segundos
});
</script>
@endpush
</div>
@endsection
