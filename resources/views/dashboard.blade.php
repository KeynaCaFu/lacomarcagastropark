@extends('layouts.app')

@section('title', isset($local) && $local ? 'Dashboard — ' . $local->name : 'Dashboard — La Comarca')

@section('content')

<div style="padding: 0 15px;">
<div class="dash-container">
    <!-- KPIs con el mismo diseño del admin -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon ic-total"><i class="fas fa-boxes"></i></div>
            <div>
                <div class="stat-title">Productos (Total)</div>
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
    </div>

    <!-- Accesos rápidos con el mismo estilo -->
    <div class="quick-links">
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
    </div>

    <div class="cards-grid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-store"></i> Mi Local</h4>
            </div>
            <div class="card-body">
                @if(isset($local) && $local)
                    <table>
                        <tbody>
                            <tr>
                                <th style="width:180px;">Nombre</th>
                                <td>{{ $local->name }}</td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    @if(($local->status ?? '') === 'Active')
                                        <span class="badge bd-active"><i class="fas fa-check"></i> Activo</span>
                                    @else
                                        <span class="badge bd-inactive"><i class="fas fa-minus-circle"></i> Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Categorías</th>
                                <td>{{ isset($categories) ? $categories->implode(', ') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div style="color:#6b7280;">Sin local asignado.</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-clock"></i> Productos Recientes</h4>
            </div>
            <div class="card-body">
                @if(isset($recentProducts) && $recentProducts->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th style="width:120px;">Precio</th>
                                <th style="width:140px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProducts as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td>{{ $p->category ?? '-' }}</td>
                                    <td>{{ number_format($p->price ?? 0, 2) }}</td>
                                    <td>
                                        @if(($p->status ?? '') === 'Available')
                                            <span class="badge bd-active"><i class="fas fa-check"></i> Disponible</span>
                                        @else
                                            <span class="badge bd-inactive"><i class="fas fa-minus-circle"></i> No disponible</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="color:#6b7280;">No hay productos recientes para este local.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Reutilizamos el mismo set de estilos del dashboard del admin */
    .dash-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 20px; }
    .stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:16px; margin-bottom: 20px; }
    .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; display:flex; align-items:center; gap:12px; }
    .stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .ic-total { background:#e0f2fe; color:#0369a1; }
    .ic-active { background:#dcfce7; color:#16a34a; }
    .ic-inactive { background:#fee2e2; color:#dc2626; }
    .ic-upcoming { background:#fef3c7; color:#b45309; }
    .stat-title { font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.02em; }
    .stat-number { font-size:28px; font-weight:800; color:#111827; }
    .quick-links { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px; margin: 8px 0 20px; }
    .quick-link { display:flex; align-items:center; gap:10px; border:1px solid #e5e7eb; background:#ffffff; border-radius:12px; padding:14px 16px; text-decoration:none; color:#111827; transition:transform .2s ease, box-shadow .2s ease; }
    .quick-link:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
    .quick-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
    .qi-users { background:#e0f2fe; color:#0369a1; }
    .qi-stores { background:#ede9fe; color:#6d28d9; }
    .qi-events { background:#ffe4e6; color:#be123c; }
    .qi-new { background:#dcfce7; color:#166534; }
    .quick-text { display:flex; flex-direction:column; }
    .quick-title { font-weight:700; }
    .quick-hint { font-size:12px; color:#6b7280; }
    .cards-grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
    @media (max-width: 992px){ .cards-grid{ grid-template-columns: 1fr; } }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; }
    .card-header { padding:12px 16px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
    .card-title { margin:0; font-weight:700; color:#111827; }
    .card-body { padding:12px 16px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
    th { background:#f9fafb; font-weight:700; color:#374151; }
    .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .bd-active { background:#dcfce7; color:#166534; }
    .bd-inactive { background:#fee2e2; color:#991b1b; }
</style>
@endpush

@push('scripts')
<script>
// Atajos opcionales para el gerente
document.addEventListener('keydown', function(e){
    if (!e.altKey) return;
    const key = (e.key || '').toLowerCase();
    if (key === 'p') { e.preventDefault(); window.location.href = @json(route('products.index')); }
    if (key === 'n') { e.preventDefault(); window.location.href = @json(route('products.create')); }
});
</script>
@endpush
</div>
