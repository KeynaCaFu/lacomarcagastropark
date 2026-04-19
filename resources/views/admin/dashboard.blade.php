@extends('layouts.app')

@section('title', 'Dashboard — Administrador Principal')

@push('styles')
    <style>
        .dash-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.08); padding: 20px; margin-top: 50px; }
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
        .card-header { padding:12px 16px; display:flex; align-items:center; justify-content:space-between; }
        .card-title { margin:0; font-weight:700; color:#111827; }
        .card-body { padding:12px 16px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px; border-bottom:1px solid #e5e7eb; text-align:left; }
        th { background:#f9fafb; font-weight:700; color:#374151; }
        .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:700; }
        .bd-active { background:#dcfce7; color:#166534; }
        .bd-inactive { background:#fee2e2; color:#991b1b; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            .dash-container { margin-top: 10px; padding: 14px; }
            .stat-number { font-size: 22px; }
        }
        @media (max-width: 767.98px) {
            .dash-container { padding: 12px; border-radius: 8px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card { padding: 14px; }
            .stat-title { font-size: 13px; }
            .stat-number { font-size: 22px; }
            .quick-links { grid-template-columns: 1fr 1fr; gap: 10px; }
            .quick-link { padding: 12px 14px; }
            .quick-title { font-size: 15px; }
            .quick-hint { font-size: 13px; }
            .card-header { padding: 12px 14px; }
            .card-title { font-size: 15px; }
            .card-body { padding: 12px 14px; }
            th, td { padding: 10px 8px; font-size: 14px; line-height: 1.5; }
        }
        @media (max-width: 575.98px) {
            .dash-container { margin-top: 4px; padding: 10px; border-radius: 6px; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 12px; }
            .stat-title { font-size: 12px; }
            .stat-number { font-size: 20px; }
            .quick-links { grid-template-columns: 1fr; gap: 8px; }
            .quick-title { font-size: 14px; }
            .quick-hint { font-size: 12px; }
            .card-title { font-size: 14px; }
            th, td { padding: 8px 6px; font-size: 13px; line-height: 1.4; }
            .badge { font-size: 11px; padding: 5px 8px; }
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

    <!-- Accesos rápidos -->
    <div class="quick-links">
        <a class="quick-link" href="{{ route('users.index') }}" title="Gestionar usuarios (Alt+U)">
            <div class="quick-icon qi-users"><i class="fas fa-users"></i></div>
            <div class="quick-text">
                <div class="quick-title">Usuarios</div>
                <div class="quick-hint">Alt+U</div>
            </div>
        </a>
        <a class="quick-link" href="{{ route('locales.index') }}" title="Gestionar locales (Alt+L)">
            <div class="quick-icon qi-stores"><i class="fas fa-store"></i></div>
            <div class="quick-text">
                <div class="quick-title">Locales</div>
                <div class="quick-hint">Alt+L</div>
            </div>
        </a>
        <a class="quick-link" href="{{ route('eventos.index') }}" title="Gestionar eventos (Alt+E)">
            <div class="quick-icon qi-events"><i class="fas fa-calendar-days"></i></div>
            <div class="quick-text">
                <div class="quick-title">Eventos</div>
                <div class="quick-hint">Alt+E</div>
            </div>
        </a>
        <a class="quick-link" href="{{ route('qr.index') }}" title="Gestionar QR de validación (Alt+Q)">
            <div class="quick-icon qi-new" style="background:#f0f9ff; color:#0369a1;"><i class="fas fa-qrcode"></i></div>
            <div class="quick-text">
                <div class="quick-title">QR Validación</div>
                <div class="quick-hint">Alt+Q</div>
            </div>
        </a>
    </div>

    <div class="cards-grid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-user-tie"></i> Top 5 Gerentes por locales</h4>
            </div>
            <div class="card-body">
                @if($topManagers->count())
                    <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Gerente</th>
                                <th style="width:120px; text-align:right;"># Locales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topManagers as $m)
                                <tr>
                                    <td>{{ $m->full_name }}</td>
                                    <td style="text-align:right; font-weight:700;">{{ $m->locals_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
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
</script>
@endpush
</div>
@endsection
