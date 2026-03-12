@extends('layouts.app')

@section('title', 'Mi Local - ' . ($local->name ?? 'La comarca'))

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1" style="color: #111827; font-weight: 700; margin-top: 14px;">
                    <i class="fas fa-store" style="color: #e18018; margin-right: 8px;"></i>Mi Local
                </h1>
                <p class="text-muted mb-0">Gestiona la información de {{ $local->name }}</p>
            </div>

            <!-- Información del Local -->
            <div class="card border-0 mb-4" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 18px;">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        @if($local && $local->image_logo)
                            <img src="{{ asset($local->image_logo) }}" 
                                 alt="{{ $local->name }}" 
                                 style="width: 100%; max-width: 150px; height: auto; border-radius: 8px; border: 1px solid #e5e7eb;">
                        @else
                            <div style="width: 150px; height: 150px; background: #f3f4f6; border-radius: 8px; border: 2px dashed #d1d5db; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 40px; color: #9ca3af;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h4 style="font-weight: 700; color: #111827; margin-bottom: 8px;">{{ $local->name }}</h4>
                        <p style="color: #6b7280; margin-bottom: 8px; font-size: 14px;">
                            <i class="fas fa-map-marker-alt" style="color: #e18018; margin-right: 6px;"></i>
                            {{ $local->description ?? 'Sin descripción' }}
                        </p>
                        <p style="color: #6b7280; margin-bottom: 8px; font-size: 14px;">
                            <i class="fas fa-phone" style="color: #e18018; margin-right: 6px;"></i>
                            {{ $local->contact ?? 'Sin contacto' }}
                        </p>
                        <p style="font-size: 14px;">
                            <span class="badge" style="background-color: {{ $local->status === 'Active' ? '#10b981' : '#ef4444' }}; padding: 6px 12px; border-radius: 6px; color: white; font-weight: 500;">
                                {{ $local->status === 'Active' ? '✓ Activo' : '✕ Inactivo' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Opciones de Menú -->
            <div class="row g-4">
                <!-- Opción: Editar Local -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('local.edit') }}" style="text-decoration: none;">
                        <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; height: 100%; transition: all 0.3s ease; cursor: pointer;" 
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                            <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: linear-gradient(135deg, #e18018, #c9690f); border-radius: 12px; margin-bottom: 16px;">
                                <i class="fas fa-edit" style="font-size: 28px; color: white;"></i>
                            </div>
                            <h5 style="color: #111827; font-weight: 700; margin-bottom: 8px;">Editar Local</h5>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 0;">Actualiza la información general de tu local como nombre, descripción, contacto e imagen.</p>
                        </div>
                    </a>
                </div>

                <!-- Opción: Ver Galería -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('local.gallery') }}" style="text-decoration: none;">
                        <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; height: 100%; transition: all 0.3s ease; cursor: pointer;" 
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                            <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 12px; margin-bottom: 16px;">
                                <i class="fas fa-images" style="font-size: 28px; color: white;"></i>
                            </div>
                            <h5 style="color: #111827; font-weight: 700; margin-bottom: 8px;">Ver Galería</h5>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 0;">Gestiona la galería de fotos de tu local. Sube, organiza y elimina imágenes.</p>
                        </div>
                    </a>
                </div>

                <!-- Opción: Ver Horario -->
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('local.schedule') }}" style="text-decoration: none;">
                        <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; height: 100%; transition: all 0.3s ease; cursor: pointer;" 
                             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" 
                             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                            <div style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6, #6d28d9); border-radius: 12px; margin-bottom: 16px;">
                                <i class="fas fa-clock" style="font-size: 28px; color: white;"></i>
                            </div>
                            <h5 style="color: #111827; font-weight: 700; margin-bottom: 8px;">Ver Horario</h5>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 0;">Configura el horario de atención y días de cierre de tu local.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    a {
        text-decoration: none;
        color: inherit;
    }
    
    .btn-secondary:hover {
        background: #f3f4f6 !important;
        border-color: #9ca3af;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar mensajes de éxito con SweetAlert Toast
    const successMsg = @json(session('success'));
    if (successMsg) {
        let retries = 0;
        const checkAndShowToast = () => {
            if (window.swToast) {
                swToast.fire({
                    icon: 'success',
                    title: successMsg
                });
            } else if (retries < 50) {
                retries++;
                setTimeout(checkAndShowToast, 100);
            }
        };
        checkAndShowToast();
    }

    // Mostrar mensajes de error con SweetAlert
    const errorMsg = @json(session('error'));
    if (errorMsg && window.swAlert) {
        swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
    }
    @if ($errors->any())
    if (window.swAlert) {
        swAlert({
            icon: 'error',
            title: 'Errores de validación',
            html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            confirmButtonColor: '#dc2626'
        });
    }
    @endif
});
</script>

@endsection
