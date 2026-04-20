@extends('layouts.app')

@section('title', 'Historial de QR - Gestión de QR de Validación')

@section('content')
<link href="{{ asset('css/QrValidacion.css') }}" rel="stylesheet">

<div class="qr-admin-container">

    <!-- Breadcrumb -->
    <nav class="qr-breadcrumb" aria-label="breadcrumb">
        <ol class="qr-breadcrumb-list">
            <li class="qr-breadcrumb-item">
                <a href="{{ route('admin.qr.index') }}">
                    <i class="fas fa-qrcode"></i>
                    QR Validación
                </a>
            </li>
            <li class="qr-breadcrumb-item active" aria-current="page">
                <i class="fas fa-history"></i>
                Historial
            </li>
        </ol>
    </nav>

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
@endsection
