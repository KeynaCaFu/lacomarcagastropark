@extends('layouts.app')

@section('title', 'Detalles del Proveedor')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">

    <style>
        .supplier-detail-page {
            width: 100%;
        }

        .supplier-breadcrumb {
            margin-bottom: 14px;
        }

        .supplier-breadcrumb .breadcrumb {
            padding: 0;
            margin: 0;
            background: none;
        }

        .supplier-breadcrumb .breadcrumb-item,
        .supplier-breadcrumb .breadcrumb-item a {
            font-size: 14px;
            font-weight: 700;
            color: #c9690f;
            text-decoration: none;
        }

        .supplier-title-wrap {
            margin-bottom: 18px;
        }

        .supplier-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 30px;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
        }

        .supplier-title i {
            color: #1f2937;
        }

        .supplier-subtitle {
            margin-top: 6px;
            color: #6b7280;
            font-size: 14px;
        }

        .supplier-main-card,
        .supplier-gallery-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .supplier-card-header {
            background: #f3f4f6;
            padding: 14px 18px;
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .supplier-card-body {
            padding: 22px;
        }

        .supplier-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(240px, 1fr));
            gap: 22px 32px;
        }

        .supplier-info-item .label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .supplier-info-item .value {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            word-break: break-word;
        }

        .supplier-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: #dcfce7;
            color: #15803d;
        }

        .supplier-gallery-card {
            margin-top: 18px;
        }

        .supplier-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .supplier-gallery-item {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            background: #fff;
        }

        .supplier-gallery-item img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            border-radius: 10px;
            background: #f9fafb;
            border: 1px solid #eef2f7;
        }

        .supplier-pdf-box {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #fff7ed;
            color: #c9690f;
            border: 1px solid #fed7aa;
            flex-direction: column;
            gap: 8px;
        }

        .supplier-pdf-box i {
            font-size: 38px;
        }

        .supplier-gallery-name {
            margin-top: 12px;
            font-size: 13px;
            color: #374151;
            word-break: break-word;
        }

        .supplier-file-actions {
            margin-top: 12px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .supplier-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .supplier-btn-add {
            background: #b86c1b;
            color: #fff;
        }

        .supplier-meta-box {
            margin-top: 18px;
            background: #d9f1f8;
            border-radius: 12px;
            padding: 16px 20px;
            color: #0f4c5c;
            font-size: 14px;
        }

        .supplier-meta-box strong {
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .supplier-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="supplier-detail-page">

    <nav aria-label="breadcrumb" class="supplier-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('suppliers.index') }}">
                    <i class="fas fa-truck"></i> Proveedores
                </a>
            </li>
            <li class="breadcrumb-item active" style="color:#6b7280; font-weight:600;">
                {{ $supplier->name }}
            </li>
        </ol>
    </nav>

    <div class="supplier-title-wrap">
        <h1 class="supplier-title">
            <i class="fas fa-store"></i>
            {{ $supplier->name }}
        </h1>
        <div class="supplier-subtitle">Detalles del proveedor</div>
    </div>

    @php
        $gallery = $supplier->gallery ?? collect();
        $created = $supplier->created_at ? $supplier->created_at->format('d/m/Y H:i') : 'N/A';
        $updated = $supplier->updated_at ? $supplier->updated_at->format('d/m/Y H:i') : 'N/A';
    @endphp

    {{-- Información General --}}
    <div class="supplier-main-card">
        <div class="supplier-card-header">Información General</div>
        <div class="supplier-card-body">
            <div class="supplier-info-grid">
                <div class="supplier-info-item">
                    <div class="label">Nombre</div>
                    <div class="value">{{ $supplier->name }}</div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Teléfono</div>
                    <div class="value">{{ $supplier->phone }}</div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Correo electrónico</div>
                    <div class="value">{{ $supplier->email }}</div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Estado</div>
                    <div class="value">
                        <span class="supplier-status-badge">Activo</span>
                    </div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Creado</div>
                    <div class="value">{{ $created }}</div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Última actualización</div>
                    <div class="value">{{ $updated }}</div>
                </div>

                <div class="supplier-info-item">
                    <div class="label">Cantidad de archivos</div>
                    <div class="value">{{ $gallery->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Galería --}}
    <div class="supplier-gallery-card">
        <div class="supplier-card-header">
            <span>Galería de Facturas ({{ $gallery->count() }})</span>

            <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="supplier-btn supplier-btn-add">
                <i class="fas fa-plus"></i>
                Agregar facturas
            </a>
        </div>

        <div class="supplier-card-body">
            @if($gallery->count() > 0)
                <div class="supplier-gallery-grid">
                    @foreach($gallery as $item)
                        @php
                            $path = $item->image_url ?? asset($item->image_path);
                            $fileName = $item->description ?? ($item->image_path ?? 'Archivo');
                            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isPdf = $ext === 'pdf';
                        @endphp

                        <div class="supplier-gallery-item">
                            @if($isPdf)
                                <div class="supplier-pdf-box">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PDF</span>
                                </div>
                            @else
                                <img src="{{ $path }}" alt="Factura">
                            @endif

                            <div class="supplier-gallery-name">{{ $fileName }}</div>

                            <div class="supplier-file-actions">
                                <span style="font-size: 12px; color: #6b7280;">Factura registrada</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="color:#6b7280;">No hay facturas registradas para este proveedor.</div>
            @endif
        </div>
    </div>

    <div class="supplier-meta-box">
        <div><strong>Creado:</strong> {{ $created }}</div>
        <div style="margin-top:6px;"><strong>Actualizado:</strong> {{ $updated }}</div>
    </div>
</div>
@endsection