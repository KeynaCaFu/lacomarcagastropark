@extends('layouts.app')

@section('title', 'Gestión de QR de Validación')

@section('content')
<link href="{{ asset('css/QrValidacion.css') }}" rel="stylesheet">

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
                    <form method="POST" action="{{ route('admin.qr.generate') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sync"></i>
                            Generar Nuevo QR
                        </button>
                    </form>

                    <!-- Descargar PNG -->
                    <a href="{{ route('admin.qr.download') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-download"></i>
                        Descargar PNG
                    </a>

                    <!-- Ver Historial -->
                    <a href="{{ route('admin.qr.logs') }}" class="btn btn-info btn-lg">
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
                    <a href="{{ route('admin.qr.logs') }}" class="btn btn-link">
                        Ver todo el historial
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar notificaciones de sesión usando componentes globales
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            window.swToast && window.swToast.fire({
                icon: 'success',
                title: '✓ Éxito',
                text: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            window.swToast && window.swToast.fire({
                icon: 'error',
                title: '✗ Error',
                text: '{{ session('error') }}'
            });
        @endif
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            window.swToast && window.swToast.fire({
                icon: 'success',
                title: '✓ Copiado',
                text: 'Contenido copiado al portapapeles'
            });
        }).catch(err => {
            console.error('Error al copiar:', err);
            window.swToast && window.swToast.fire({
                icon: 'error',
                title: '✗ Error',
                text: 'Error al copiar al portapapeles'
            });
        });
    }

    // Interceptar el formulario de generación para usar confirmación global
    document.addEventListener('DOMContentLoaded', function() {
        const generateForm = document.querySelector('form[action="{{ route('admin.qr.generate') }}"]');
        if (generateForm) {
            generateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                window.swConfirm({
                    title: '¿Generar nueva clave QR?',
                    text: 'La clave anterior dejará de ser válida. ¿Continuar?',
                    icon: 'warning',
                    confirmButtonText: 'Sí, generar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generateForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
