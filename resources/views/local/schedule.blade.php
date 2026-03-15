@extends('layouts.app')

@section('title', 'Horario del Local - ' . ($local->name ?? 'La comarca'))

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            @include('local.partials.breadcrumb', ['crumbs' => [
                ['label' => 'Horario', 'url' => null]
            ]])

            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1" style="color: #111827; font-weight: 700; margin-top: 14px;">
                    <i class="fas fa-clock" style="color: #e18018; margin-right: 8px;"></i>Horario de {{ $local->name }}
                </h1>
                <p class="text-muted mb-0">Visualiza los horarios de atención configurados para tu local</p>
            </div>

            <!-- Estado Actual del Local -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p style="color: #6b7280; font-size: 14px; margin-bottom: 8px; font-weight: 500;">Estado Actual</p>
                                <h4 style="font-weight: 700; color: #111827; margin-bottom: 0;">
                                    @if($isOpen)
                                        <span style="color: #10b981;">
                                            <i class="fas fa-circle" style="font-size: 12px; margin-right: 6px;"></i>Abierto
                                        </span>
                                    @else
                                        <span style="color: #ef4444;">
                                            <i class="fas fa-circle" style="font-size: 12px; margin-right: 6px;"></i>Cerrado
                                        </span>
                                    @endif
                                </h4>
                            </div>
                            <div style="font-size: 48px; color: {{ $isOpen ? '#10b981' : '#ef4444' }}; opacity: 0.3;">
                                <i class="fas {{ $isOpen ? 'fa-lock-open' : 'fa-lock' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px;">
                        <div>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 8px; font-weight: 500;">
                                <i class="fas fa-globe" style="color: #e18018; margin-right: 6px;"></i>Hora Actual (Costa Rica)
                            </p>
                            <h4 style="font-weight: 700; color: #111827; margin-bottom: 6px;" id="current-time">
                                {{ now()->format('H:i:s') }}
                            </h4>
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                <i class="fas fa-calendar-alt" style="margin-right: 4px;"></i><span id="current-date">{{ now()->format('l, d \\d\\e F') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horarios por Día -->
            <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px;">
                <h5 style="color: #111827; font-weight: 700; margin-bottom: 24px;">
                    <i class="fas fa-calendar-days" style="color: #e18018; margin-right: 8px;"></i>Horarios de Atención
                </h5>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
                    @foreach($schedules as $schedule)
                        <div class="schedule-card {{ $schedule->status ? 'schedule-card-open' : 'schedule-card-closed' }}" 
                             style="border-radius: 12px; padding: 20px; border: 2px solid #e5e7eb; background: white; transition: all 0.3s ease; overflow: hidden;">
                            
                            <!-- Encabezado con Día y Estado -->
                            <div style="margin-bottom: 16px;">
                                <h6 style="color: #111827; font-weight: 800; margin: 0 0 8px 0; font-size: 18px; text-transform: uppercase;">
                                    {{ $schedule->day_of_week }}
                                </h6>
                                @if($schedule->status)
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                        <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                                        Abierto
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                        <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
                                        Cerrado
                                    </span>
                                @endif
                            </div>

                            @if($schedule->status)
                                <!-- Horarios - Formato Compacto -->
                                <div style="background: #f9fafb; border-radius: 8px; padding: 16px;">
                                    <p style="color: #9ca3af; font-size: 11px; font-weight: 700; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">Horario</p>
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                        <div>
                                            <p style="color: #6b7280; font-size: 11px; margin: 0 0 4px 0;">Abre</p>
                                            <p style="color: #111827; font-size: 18px; font-weight: 800; margin: 0;">
                                                {{ $schedule->opening_time ? \Carbon\Carbon::parse($schedule->opening_time)->format('h:i A') : '--:--' }}
                                            </p>
                                        </div>
                                        <div style="color: #d1d5db; font-size: 14px;">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                        <div style="text-align: right;">
                                            <p style="color: #6b7280; font-size: 11px; margin: 0 0 4px 0;">Cierra</p>
                                            <p style="color: #111827; font-size: 18px; font-weight: 800; margin: 0;">
                                                {{ $schedule->closing_time ? \Carbon\Carbon::parse($schedule->closing_time)->format('h:i A') : '--:--' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Día Cerrado -->
                                <div style="background: #fef2f2; border-radius: 8px; padding: 24px; text-align: center;">
                                    <i class="fas fa-lock" style="font-size: 24px; color: #ef4444; margin-bottom: 8px; display: block;"></i>
                                    <p style="color: #7f1d1d; font-size: 13px; font-weight: 600; margin: 0;">
                                        No abre este día
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .schedule-card {
        cursor: pointer;
    }

    .schedule-card-open:hover {
        border-color: #e18018 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px);
    }

    .schedule-card-closed:hover {
        border-color: #ef4444 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px);
    }

    .alert-info {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
    }

    .local-breadcrumb {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .local-breadcrumb ol {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0;
    }

    .local-breadcrumb li {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .local-breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.2s ease;
    }

    .local-breadcrumb a.breadcrumb-home {
        color: #e18018;
        font-weight: 600;
    }

    .local-breadcrumb a.breadcrumb-home:hover {
        color: #c9690f;
    }

    .local-breadcrumb a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .local-breadcrumb .breadcrumb-separator {
        color: #d1d5db;
        margin: 0 4px;
        font-size: 12px;
    }

    .local-breadcrumb .current {
        color: #6b7280;
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar la hora cada segundo con zona horaria de Costa Rica
    function updateTime() {
        const now = new Date();
        const formatter = new Intl.DateTimeFormat('es-CR', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true,
            timeZone: 'America/Costa_Rica'
        });
        
        const dateFormatter = new Intl.DateTimeFormat('es-CR', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            timeZone: 'America/Costa_Rica'
        });
        
        document.getElementById('current-time').textContent = formatter.format(now);
        const dateStr = dateFormatter.format(now);
        // Capitalizar la primera letra
        document.getElementById('current-date').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
    }

    updateTime();
    setInterval(updateTime, 1000);

    // Mostrar mensajes de éxito con SweetAlert Toast
    const successMsg = @json(session('success'));
    if (successMsg && window.swToast) {
        swToast.fire({
            icon: 'success',
            title: successMsg
        });
    }

    // Mostrar mensajes de error con SweetAlert
    const errorMsg = @json(session('error'));
    if (errorMsg && window.swAlert) {
        swAlert({ 
            icon: 'error', 
            title: 'Error', 
            text: errorMsg, 
            confirmButtonColor: '#dc2626' 
        });
    }
});
</script>

@endsection