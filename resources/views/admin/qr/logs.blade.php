@extends('layouts.app')

@section('title', 'Historial de QR - Gestión de QR de Validación')

@section('content')
<div class="qr-admin-container">
    <!-- Header -->
    <div class="qr-header">
        <div class="qr-header-content">
            <div class="qr-title-group">
                <h1 class="qr-title">
                    <i class="fas fa-history"></i>
                    Historial de Actividad del QR
                </h1>
                <p class="qr-subtitle">Registro completo de cambios y descargas del código QR</p>
            </div>
            <a href="{{ route('qr.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Tarjeta de Historial -->
    <div class="qr-card">
        <div class="qr-card-header">
            <h2 class="qr-card-title">
                <i class="fas fa-list"></i>
                Registro Completo
            </h2>
            <span class="qr-log-count">{{ $logs->total() }} registros</span>
        </div>

        <div class="qr-card-body">
            @if ($logs->count() > 0)
                <div class="qr-logs-table-wrapper">
                    <table class="qr-logs-table">
                        <thead>
                            <tr>
                                <th>Acción</th>
                                <th>Administrador</th>
                                <th>Fecha y Hora</th>
                                <th>IP Address</th>
                                <th>Clave Nueva</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr class="qr-log-row">
                                    <td class="qr-action-cell">
                                        @if ($log->action === 'generate')
                                            <span class="badge badge-generate">
                                                <i class="fas fa-plus"></i> Generado
                                            </span>
                                        @elseif ($log->action === 'update')
                                            <span class="badge badge-update">
                                                <i class="fas fa-sync"></i> Actualizado
                                            </span>
                                        @elseif ($log->action === 'download')
                                            <span class="badge badge-download">
                                                <i class="fas fa-download"></i> Descargado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="qr-admin-cell">
                                        {{ $log->admin->full_name ?? $log->admin->name }}
                                    </td>
                                    <td class="qr-date-cell">
                                        <span class="qr-date">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                        <span class="qr-relative">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="qr-ip-cell">
                                        <code>{{ $log->admin_ip ?? 'N/A' }}</code>
                                    </td>
                                    <td class="qr-key-cell">
                                        @if ($log->new_key)
                                            <code class="qr-key-badge">{{ substr($log->new_key, 0, 8) }}...</code>
                                        @else
                                            <span class="qr-na">N/A</span>
                                        @endif
                                    </td>
                                    <td class="qr-details-cell">
                                        @if ($log->old_key && $log->new_key)
                                            <button type="button" class="btn-details" onclick="showDetails('{{ $log->old_key }}', '{{ $log->new_key }}')">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @else
                                            <span class="qr-na">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if ($logs->hasPages())
                    <div class="qr-pagination-wrapper">
                        {{ $logs->links() }}
                    </div>
                @endif
            @else
                <div class="qr-empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No hay registros de actividad</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div id="detailsModal" class="modal-overlay" onclick="closeDetails(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 class="modal-title">Detalles del Cambio</h3>
            <button type="button" class="modal-close" onclick="closeDetails()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="modal-detail-row">
                <label>Clave Anterior:</label>
                <code id="oldKeyDisplay"></code>
            </div>
            <div class="modal-detail-row">
                <label>Nueva Clave:</label>
                <code id="newKeyDisplay"></code>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .qr-admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .qr-header {
        background: linear-gradient(135deg, #D47744 0%, #915016 100%);
        color: white;
        border-radius: 12px;
        padding: 30px 40px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(212, 119, 68, 0.3);
    }

    .qr-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
    }

    .qr-title-group {
        flex: 1;
    }

    .qr-title-group h1 {
        margin: 0 0 10px;
        font-size: 32px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .qr-subtitle {
        margin: 0;
        opacity: 0.95;
        font-size: 14px;
    }

    .qr-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e8e8e8;
        overflow: hidden;
    }

    .qr-card-header {
        background: #f8f9fa;
        padding: 20px 25px;
        border-bottom: 1px solid #e8e8e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .qr-card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .qr-log-count {
        background: #D47744;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .qr-card-body {
        padding: 0;
    }

    .qr-logs-table-wrapper {
        overflow-x: auto;
    }

    .qr-logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .qr-logs-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e8e8e8;
    }

    .qr-logs-table thead th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .qr-logs-table tbody tr {
        border-bottom: 1px solid #e8e8e8;
        transition: all 0.3s;
    }

    .qr-logs-table tbody tr:hover {
        background: #f8f9fa;
    }

    .qr-logs-table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    .qr-action-cell {
        width: 120px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-generate {
        background: #d4edda;
        color: #155724;
    }

    .badge-update {
        background: #cfe2ff;
        color: #084298;
    }

    .badge-download {
        background: #d1ecf1;
        color: #0c5460;
    }

    .qr-admin-cell {
        font-weight: 500;
        color: #333;
    }

    .qr-date-cell {
        width: 200px;
    }

    .qr-date {
        display: block;
        color: #333;
        font-weight: 500;
    }

    .qr-relative {
        display: block;
        color: #999;
        font-size: 12px;
        margin-top: 3px;
    }

    .qr-ip-cell code,
    .qr-key-cell code {
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #D47744;
    }

    .qr-key-badge {
        background: #fff3e0;
        color: #e65100;
    }

    .qr-na {
        color: #999;
        font-style: italic;
    }

    .btn-details {
        background: #D47744;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s;
    }

    .btn-details:hover {
        background: #915016;
        transform: scale(1.1);
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-primary {
        background: #D47744;
        color: white;
    }

    .btn-primary:hover {
        background: #915016;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(212, 119, 68, 0.3);
    }

    .qr-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .qr-empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .qr-pagination-wrapper {
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #e8e8e8;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlide 0.3s ease;
    }

    @keyframes modalSlide {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e8e8e8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        transition: all 0.3s;
        padding: 0;
    }

    .modal-close:hover {
        color: #333;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-detail-row {
        margin-bottom: 20px;
    }

    .modal-detail-row:last-child {
        margin-bottom: 0;
    }

    .modal-detail-row label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .modal-detail-row code {
        display: block;
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #D47744;
        word-break: break-all;
    }

    @media (max-width: 768px) {
        .qr-header {
            padding: 20px;
        }

        .qr-header-content {
            flex-direction: column;
        }

        .qr-title-group h1 {
            font-size: 24px;
        }

        .qr-logs-table {
            font-size: 12px;
        }

        .qr-logs-table thead th,
        .qr-logs-table tbody td {
            padding: 10px;
        }

        .btn-details {
            padding: 4px 8px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function showDetails(oldKey, newKey) {
        document.getElementById('oldKeyDisplay').textContent = oldKey;
        document.getElementById('newKeyDisplay').textContent = newKey;
        document.getElementById('detailsModal').classList.add('active');
    }

    function closeDetails(event) {
        if (!event || event.target.id === 'detailsModal') {
            document.getElementById('detailsModal').classList.remove('active');
        }
    }

    // Cerrar modal al presionar ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDetails();
        }
    });
</script>
@endpush
@endsection
