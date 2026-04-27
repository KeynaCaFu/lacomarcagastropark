@extends('layouts.app')

@section('title', 'Perímetro de Seguridad - La Comarca')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Estilos para que se vea como container, no como card */
    .perimeter-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        padding: 1.5rem;
    }
    
    .info-panel {
        background: white;
        border-radius: 16px;
        padding: 1.2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        height: 100%;
        margin-right: 253px;
        margin-left: -599px;
        margin-top: 42px;
    }
    
    /* Encabezado plano */
    .panel-header {
        border-bottom: 2px solid #f0f2f5;
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
    }
    .panel-header h3 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Metricas mas horizontales y compactas */
    .metric-row {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.9rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 3px solid #e18018;
    }
    .metric-row.radius-metric {
        border-left-color: #0d6efd;
    }
    .metric-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: 0.5px;
    }
    .metric-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a2c3e;
    }
    .metric-value small {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6c757d;
    }
    
    .badge-active {
        background: #d1e7dd;
        color: #0a5e2e;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .info-note-plain {
        background: #fff8e7;
        border-radius: 12px;
        padding: 0.75rem;
        font-size: 0.75rem;
        margin-top: 1rem;
        border: 1px solid #ffe8c7;
        color: #856404;
    }
    
    .btn-edit-plain {
        background: #e18018;
        border: none;
        border-radius: 10px;
        padding: 0.5rem;
        font-weight: 600;
        color: white;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }
    .btn-edit-plain:hover {
        background: #c96f12;
    }
    
    .map-wrapper {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        height: 99%;
        margin-right: -1px;
        margin-left: -240px;
        margin-top: 42px;
    }
    .map-header {
        padding: 0.8rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .map-header h4 {
        font-size: 0.9rem;
        margin: 0;
        font-weight: 600;
        color: #495057;
    }
    #map-preview {
        height: 450px;
        width: 100%;
        z-index: 1;
        background: #e9ecef;
    }
    
    @media (max-width: 768px) {
        .metric-value { font-size: 1rem; }
        #map-preview { height: 350px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4 perimeter-container">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Perímetro de Seguridad</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Layout horizontal: izquierda como container plano, derecha mapa -->
    <div class="row g-4">
        <!-- Columna Izquierda - estilo container (sin card, mas plano) -->
        <div class="col-lg-4 col-md-12">
            <div class="info-panel">
                <div class="panel-header">
                    <h3>
                        <i class="fas fa-satellite-dish text-warning"></i> 
                        Configuración GPS
                        <span class="badge-active ms-auto"><i class="fas fa-circle" style="font-size: 0.4rem;"></i> Activo</span>
                    </h3>
                </div>
                
                <!-- Latitud -->
                <div class="metric-row">
                    <div class="metric-label">LATITUD CENTRO</div>
                    <div class="metric-value">{{ $config->latitude }}</div>
                </div>
                
                <!-- Longitud -->
                <div class="metric-row">
                    <div class="metric-label">LONGITUD CENTRO</div>
                    <div class="metric-value">{{ $config->longitude }}</div>
                </div>
                
                <!-- Radio -->
                <div class="metric-row radius-metric">
                    <div class="metric-label">RADIO PERMITIDO</div>
                    <div class="metric-value">{{ $config->radius_meters }} <small>metros</small></div>
                </div>
                
                <!-- Nota -->
                <div class="info-note-plain">
                    <i class="fas fa-info-circle text-warning me-1"></i>
                    Los pedidos solo se permiten si el cliente se encuentra dentro del círculo naranja.
                </div>
                
                <!-- Boton editar -->
                <div class="mt-3">
                    <a href="{{ route('admin.plaza-config.edit') }}" class="btn-edit-plain">
                        <i class="fas fa-edit"></i> Editar Perímetro
                    </a>
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Mapa -->
        <div class="col-lg-8 col-md-12">
            <div class="map-wrapper">
                <div class="map-header">
                    <h4><i class="fas fa-map-marked-alt me-2 text-warning"></i> Visualización del Área | Círculo de Seguridad</h4>
                </div>
                <div id="map-preview"></div>
                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-globe-americas"></i> Leaflet | © OpenStreetMap
                    </div>
                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3" id="btnActualizar">
                        <i class="fas fa-sync-alt me-1"></i> Re-centrar Mapa
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos desde el backend
        const lat = {{ $config->latitude }};
        const lng = {{ $config->longitude }};
        const radius = {{ $config->radius_meters }};

        // Inicializar mapa
        const map = L.map('map-preview').setView([lat, lng], 17);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Marcador central
        L.marker([lat, lng]).addTo(map);
        
        // Círculo naranja (perímetro)
        const circle = L.circle([lat, lng], {
            color: '#e18018',
            fillColor: '#e18018',
            fillOpacity: 0.2,
            radius: radius
        }).addTo(map);

        // Ajustar vista para que el círculo se vea completamente
        map.fitBounds(circle.getBounds().pad(0.2));

        // Botón Actualizar: re-centrar el mapa y mostrar mensaje
        document.getElementById('btnActualizar').addEventListener('click', function() {
            map.setView([lat, lng], 17);
            map.fitBounds(circle.getBounds().pad(0.2));
            mostrarToast('Mapa actualizado', 'info');
        });


        // Función para toast simple
        function mostrarToast(mensaje, tipo = 'success') {
            const toastDiv = document.createElement('div');
            toastDiv.style.position = 'fixed';
            toastDiv.style.bottom = '20px';
            toastDiv.style.right = '20px';
            toastDiv.style.backgroundColor = '#2c3e50';
            toastDiv.style.color = 'white';
            toastDiv.style.padding = '8px 16px';
            toastDiv.style.borderRadius = '30px';
            toastDiv.style.fontSize = '0.8rem';
            toastDiv.style.zIndex = '9999';
            toastDiv.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
            toastDiv.innerHTML = `<i class="fas fa-info-circle me-2"></i>${mensaje}`;
            document.body.appendChild(toastDiv);
            setTimeout(() => {
                toastDiv.style.opacity = '0';
                setTimeout(() => toastDiv.remove(), 300);
            }, 2000);
        }
    });
</script>
@endpush