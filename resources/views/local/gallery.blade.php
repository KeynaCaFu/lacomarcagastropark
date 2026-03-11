@extends('layouts.app')

@section('title', 'Galería del Local - ' . ($local->name ?? 'La Comarca'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2" style="color: #111827; font-weight: 700;">
                        <i class="fas fa-image" style="color: #e18018; margin-right: 8px;"></i>Galería del Local
                    </h1>
                    <p class="text-muted mb-0">Administra las imágenes de {{ $local ? $local->name : 'tu local' }}</p>
                </div>
                <a href="{{ route('local.edit') }}" class="btn btn-secondary" style="padding: 10px 16px; border-radius: 8px; font-weight: 600; border: 1px solid #d1d5db; background: #f9fafb; color: #374151; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Placeholder -->
            <div class="alert alert-info" style="border-radius: 8px; border-left: 4px solid #3b82f6; background: #eff6ff; color: #1e40af;">
                <i class="fas fa-wrench"></i> 
                <strong>En desarrollo:</strong> Esta funcionalidad se implementará más adelante.
            </div>
        </div>
    </div>
</div>
@endsection
