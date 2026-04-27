@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .perimeter-container {
        width: 100%;
        padding: 1.5rem;
    }
    
    .info-panel {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
         height: 100%;
        margin-right: 253px;
        margin-left: -599px;
        margin-top: 42px;
    }
    
    .panel-header {
        border-bottom: 2px solid #f0f2f5;
        margin-bottom: 1.5rem;
        padding-bottom: 0.8rem;
    }

    .panel-header h3 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    #map { 
        height: 500px; 
        width: 100%; 
        z-index: 1; 
        background: #e9ecef;
    }

    .form-label-custom {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .map-controls {
        padding: 1rem;
        background: white;
        border-top: 1px solid #e9ecef;
        text-align: center;
    }

        .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-item a:hover {
        color: #e18018 !important; /* Color naranja */
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #adb5bd;
    }

    /* Si quieres también cambiar el color del ícono de separación */
    .breadcrumb-item + .breadcrumb-item::before {
        color: #ced4da;
    }
</style>
@endpush

@section('content')
<div class="container-fluid perimeter-container">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="background: transparent; padding: 0; margin: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.plaza-config.index') }}">Perímetro de Seguridad</a></li>
                    <li class="breadcrumb-item active">Editar Perímetro</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda: Formulario -->
        <div class="col-lg-4">
            <div class="info-panel">
                <div class="panel-header">
                    <h3><i class="fas fa-edit text-warning"></i> Editar Configuración</h3>
                </div>
                
                <form action="{{ route('admin.plaza-config.update') }}" method="POST" id="perimeterForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label-custom">Latitud Centro</label>
                        <input type="text" name="latitude" id="latitude" class="form-control form-control-lg" value="{{ $config->latitude }}" required style="border-radius: 10px; font-size: 0.9rem;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Longitud Centro</label>
                        <input type="text" name="longitude" id="longitude" class="form-control form-control-lg" value="{{ $config->longitude }}" required style="border-radius: 10px; font-size: 0.9rem;">
                    </div>

                    <div class="mb-4 text-center">
                        <button type="button" id="btnGetLocation" class="btn btn-outline-info w-100 py-2 shadow-sm" style="border-radius: 10px; font-weight: 600; border-width: 2px;">
                            <i class="fas fa-location-arrow me-2"></i> Usar mi ubicación actual
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Radio Permitido (Metros)</label>
                        <div class="input-group">
                            <input type="number" name="radius_meters" id="radius_meters" class="form-control form-control-lg" value="{{ $config->radius_meters }}" required style="border-radius: 10px 0 0 10px; font-size: 0.9rem;">
                            <span class="input-group-text bg-light" style="border-radius: 0 10px 10px 0; font-weight: 600;">m</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('admin.plaza-config.index') }}" class="btn btn-outline-secondary w-100 py-2 shadow-sm" style="border-radius: 10px; font-weight: 600;">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm" style="border-radius: 10px; font-weight: 700; background: #e18018; border: none;">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                        <div class="p-3 bg-light border-top mt-3">
                        <div class="alert alert-warning mb-0" style="font-size: 0.8rem; border-radius: 10px; border: none; background: #fff8e7; color: #856404;">
                            <i class="fas fa-info-circle me-2"></i> <strong>Tip:</strong> Puedes buscar las coordenadas exactas en 
                            <a href="https://www.google.com/maps" target="_blank" class="text-dark font-weight-bold">Google Maps</a> 
                            y pegarlas, o usar el botón de ubicación si estás en el sitio.
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Mapa Interactivo -->
        <div class="col-lg-8">
            <div class="map-wrapper">
                <div class="map-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-map-marked-alt me-2 text-warning"></i> Ajuste Visual del Perímetro</h4>
                    <div class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.7rem; border-radius: 50px;">
                        <i class="fas fa-mouse-pointer me-1"></i> Arrastra el marcador o haz clic en el mapa
                    </div>
                </div>
                <div id="map"></div>
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
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker, circle;

// Función para mostrar notificaciones estilo toast
function mostrarToast(mensaje, tipo = 'success') {
    // Usar SweetAlert2 para notificaciones pequeñas
    Swal.fire({
        text: mensaje,
        icon: tipo,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || 0;
    const lng = parseFloat(document.getElementById('longitude').value) || 0;
    const radius = parseInt(document.getElementById('radius_meters').value) || 100;

    // Inicializar el mapa
    map = L.map('map').setView([lat, lng], 17);

    // Capa de mapa (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Marcador central (arrastrable)
    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    // Círculo del perímetro
    circle = L.circle([lat, lng], {
        color: '#e18018',
        fillColor: '#e18018',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);

    // Actualizar coordenadas al arrastrar el marcador
    marker.on('dragend', function (e) {
        const pos = marker.getLatLng();
        document.getElementById('latitude').value = pos.lat.toFixed(8);
        document.getElementById('longitude').value = pos.lng.toFixed(8);
        updateMapElements();
    });

    // Mover marcador al hacer click en el mapa
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
        updateMapElements();
    });
}

function updateMapElements() {
    const lat = parseFloat(document.getElementById('latitude').value) || 0;
    const lng = parseFloat(document.getElementById('longitude').value) || 0;
    const radius = parseInt(document.getElementById('radius_meters').value) || 100;

    const newLatLng = new L.LatLng(lat, lng);
    marker.setLatLng(newLatLng);
    circle.setLatLng(newLatLng);
    circle.setRadius(radius);
    map.panTo(newLatLng);
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();

    // Escuchar cambios manuales en los inputs
    document.getElementById('latitude').addEventListener('input', updateMapElements);
    document.getElementById('longitude').addEventListener('input', updateMapElements);
    document.getElementById('radius_meters').addEventListener('input', updateMapElements);

    document.getElementById('btnGetLocation').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Tu navegador no soporta geolocalización.', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Capturando ubicación...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(8);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(8);
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                updateMapElements();
                Swal.fire('¡Éxito!', 'Coordenadas capturadas correctamente desde tu GPS.', 'success');
            },
            function(error) {
                btn.disabled = false;
                btn.innerHTML = originalText;
                let msg = "No pudimos obtener tu ubicación.";
                if (error.code === error.PERMISSION_DENIED) {
                    msg = "Debes permitir el acceso a tu ubicación en el navegador para usar esta función.";
                }
                Swal.fire('Atención', msg, 'warning');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    // Botón Re-centrar Mapa
    document.getElementById('btnActualizar').addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('latitude').value) || 0;
        const lng = parseFloat(document.getElementById('longitude').value) || 0;
        const radius = parseInt(document.getElementById('radius_meters').value) || 100;
        
        // Actualizar el círculo y marcador por si acaso
        const newLatLng = new L.LatLng(lat, lng);
        marker.setLatLng(newLatLng);
        circle.setLatLng(newLatLng);
        circle.setRadius(radius);
        
        // Centrar el mapa en el marcador y ajustar zoom para ver todo el círculo
        map.setView([lat, lng], 17);
        map.fitBounds(circle.getBounds().pad(0.2));
        
        //mostrarToast('Mapa re-centrado correctamente', 'info');
    });
});
</script>
@endpush