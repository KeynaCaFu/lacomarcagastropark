@extends('layouts.app')

@section('title', 'Gestión de QR de Validación')

@section('content')
<div class="qr-admin-container">
    <!-- Header -->
    <div class="qr-header">
        <div class="qr-header-content">
            <div class="qr-title-group">
                <h1 class="qr-title">
                    <i class="fas fa-qrcode"></i>
                    Gestión de QR de Validación
                </h1>
                <p class="qr-subtitle">Administra el código QR estático para validación de órdenes en el local</p>
            </div>
        </div>
    </div>

    <!-- Mensajes de Éxito/Error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Contenido Principal -->
    <div class="qr-content-wrapper">
        <!-- Tarjeta del QR -->
        <div class="qr-card">
            <div class="qr-card-header">
                <h2 class="qr-card-title">
                    <i class="fas fa-barcode"></i>
                    Código QR Activo
                </h2>
                <span class="qr-status-badge active">
                    <i class="fas fa-circle"></i>
                    Activo
                </span>
            </div>

            <div class="qr-card-body">
                <!-- Área de Visualización del QR -->
                <div class="qr-display-area">
                    @if ($qrImage)
                        <div class="qr-image-wrapper">
                            {!! $qrImage !!}
                        </div>
                    @else
                        <div class="qr-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <p>No se pudo generar la imagen del QR</p>
                        </div>
                    @endif
                </div>

                <!-- Información del QR -->
                <div class="qr-info-section">
                    <div class="qr-info-item">
                        <label class="qr-info-label">Clave de Validación:</label>
                        <div class="qr-info-value">
                            <code class="qr-key">{{ $qrSetting->qr_key }}</code>
                            <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $qrSetting->qr_key }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="qr-info-item">
                        <label class="qr-info-label">URL del QR:</label>
                        <div class="qr-info-value">
                            <code class="qr-url">{{ $qrSetting->qr_url }}</code>
                            <button type="button" class="btn-copy" onclick="copyToClipboard('{{ $qrSetting->qr_url }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="qr-info-item">
                        <label class="qr-info-label">Última Actualización:</label>
                        <div class="qr-info-value">
                            {{ $qrSetting->updated_at->format('d/m/Y H:i:s') }}
                            <span class="qr-timezone">({{ $qrSetting->updated_at->diffForHumans() }})</span>
                        </div>
                    </div>

                    <div class="qr-info-item">
                        <label class="qr-info-label">Generado por:</label>
                        <div class="qr-info-value">
                            {{ $qrSetting->generatedBy->full_name ?? $qrSetting->generatedBy->name }}
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="qr-actions">
                    <!-- Generar Nuevo -->
                    <form method="POST" action="{{ route('qr.generate') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('¿Estás seguro de que deseas generar una nueva clave QR? La anterior dejará de ser válida.')">
                            <i class="fas fa-sync"></i>
                            Generar Nuevo QR
                        </button>
                    </form>

                    <!-- Descargar PNG -->
                    <a href="{{ route('qr.download') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i>
                        Descargar PNG
                    </a>

                    <!-- Ver Historial -->
                    <a href="{{ route('qr.logs') }}" class="btn btn-info btn-lg">
                        <i class="fas fa-history"></i>
                        Ver Historial
                    </a>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Actividad Reciente -->
        <div class="qr-card">
            <div class="qr-card-header">
                <h2 class="qr-card-title">
                    <i class="fas fa-list"></i>
                    Actividad Reciente
                </h2>
            </div>

            <div class="qr-card-body">
                @if ($logs->count() > 0)
                    <div class="qr-logs-list">
                        @foreach ($logs as $log)
                            <div class="qr-log-item">
                                <div class="qr-log-action">
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
                                </div>

                                <div class="qr-log-details">
                                    <p class="qr-log-description">
                                        {{ $log->admin->full_name ?? $log->admin->name }}
                                        <span class="qr-log-time">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            <em>({{ $log->created_at->diffForHumans() }})</em>
                                        </span>
                                    </p>
                                    <p class="qr-log-meta">
                                        <small>IP: {{ $log->admin_ip ?? 'N/A' }}</small>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="qr-empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No hay actividad registrada</p>
                    </div>
                @endif
            </div>

            @if ($logs->count() > 0)
                <div class="qr-card-footer">
                    <a href="{{ route('qr.logs') }}" class="btn btn-link">
                        Ver todo el historial
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .qr-admin-container {
        max-width: 1000px;
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

    .qr-content-wrapper {
        display: grid;
        gap: 25px;
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

    .qr-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .qr-status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .qr-card-body {
        padding: 30px;
    }

    .qr-display-area {
        text-align: center;
        margin-bottom: 30px;
        padding: 30px;
        background: #f8f9fa;
        border-radius: 10px;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qr-image-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .qr-image-wrapper svg {
        max-width: 400px;
        height: auto;
    }

    .qr-placeholder {
        text-align: center;
        color: #999;
    }

    .qr-placeholder i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 15px;
    }

    .qr-info-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .qr-info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .qr-info-label {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .qr-info-value {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #333;
    }

    .qr-key,
    .qr-url {
        background: white;
        padding: 10px 12px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #D47744;
        word-break: break-all;
        flex: 1;
    }

    .btn-copy {
        background: #D47744;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .btn-copy:hover {
        background: #915016;
        transform: scale(1.05);
    }

    .qr-timezone {
        color: #999;
        font-size: 12px;
    }

    .qr-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
        padding-top: 20px;
        border-top: 1px solid #e8e8e8;
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

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
    }

    .btn-lg {
        padding: 12px 24px;
        font-size: 15px;
    }

    .qr-logs-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .qr-log-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #D47744;
    }

    .qr-log-action {
        flex-shrink: 0;
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

    .qr-log-details {
        flex: 1;
    }

    .qr-log-description {
        margin: 0 0 5px;
        font-size: 14px;
        color: #333;
    }

    .qr-log-time {
        color: #999;
        font-size: 12px;
        margin-left: 10px;
    }

    .qr-log-meta {
        margin: 0;
        color: #999;
        font-size: 12px;
    }

    .qr-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }

    .qr-empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .qr-card-footer {
        padding: 15px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e8e8e8;
    }

    .btn-link {
        background: none;
        color: #D47744;
        text-decoration: none;
        padding: 0;
        display: inline-flex;
        gap: 6px;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        margin-left: auto;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .qr-header {
            padding: 20px;
        }

        .qr-header-content {
            flex-direction: column;
            gap: 15px;
        }

        .qr-title-group h1 {
            font-size: 24px;
        }

        .qr-info-section {
            grid-template-columns: 1fr;
        }

        .qr-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('✓ Copiado al portapapeles');
        }).catch(err => {
            console.error('Error al copiar:', err);
            alert('Error al copiar al portapapeles');
        });
    }
</script>
@endpush
@endsection
