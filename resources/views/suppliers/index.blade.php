@extends('layouts.app')

@section('title', 'Proveedores')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">

    <style>
        .suppliers-table {
            width: 100%;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .suppliers-table thead tr {
            background: #c9ccc4 !important;
        }

        .suppliers-table thead th {
            background: #c9ccc4 !important;
            color: #374151 !important;
            font-weight: 700 !important;
            padding: 16px !important;
            border-bottom: 1px solid #9ca3af !important;
        }

        .suppliers-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .suppliers-table tbody td {
            padding: 16px !important;
            vertical-align: middle !important;
            color: #374151;
        }

        .suppliers-name {
            font-weight: 700;
            color: #1f2937;
        }

        .suppliers-link {
            color: #374151 !important;
            text-decoration: none;
            font-weight: 500;
        }

        .suppliers-muted {
            color: #9ca3af;
        }

        .suppliers-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .btn-view {
            background: #e0f2fe;
            color: #0284c7;
        }

        .btn-edit {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-action-small {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
<div class="product-page-wrapper">

<div class="products-container">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
        <ol class="breadcrumb" style="padding: 0; background: none;">
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="header-section">
        <h2 style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-users" style="color: #c9690f;"></i> Gestión de Proveedores
        </h2>
        <div class="header-actions">
            <a href="{{ route('suppliers.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <div class="stat-label">Total de Proveedores</div>
                <div class="stat-number">{{ $totals['total'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-accordion">
        <button type="button" id="filtersToggle" class="filters-toggle">
            <i class="fas fa-chevron-down"></i>
            Buscar Proveedores
        </button>

        <div id="filtrosBody" class="search-filter-group closed">
            <form method="GET" action="{{ route('suppliers.index') }}">
                <input type="text" name="buscar" class="filter-select" placeholder="Buscar..." value="{{ request('buscar') }}">
                <button type="submit" class="btn-action">Buscar</button>
            </form>
        </div>
    </div>

    <!-- TABLA -->
    <div class="table-wrapper" style="margin-top: 24px;">
        @if($suppliers && count($suppliers) > 0)
            <table class="table table-hover suppliers-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Registrado</th>
                        <th>Última Compra</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>
                            <strong class="suppliers-name">{{ $supplier->name }}</strong>
                        </td>
                        <td>
                            <a href="tel:{{ $supplier->phone }}" class="suppliers-link">
                                {{ $supplier->phone }}
                            </a>
                        </td>
                        <td>
                            <a href="mailto:{{ $supplier->email }}" class="suppliers-link">
                                {{ $supplier->email }}
                            </a>
                        </td>
                        <td>
                            {{ $supplier->created_at ? $supplier->created_at->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td>
                            <span class="suppliers-muted">Sin compras registradas</span>
                        </td>

                        <td>
                            <div class="suppliers-actions">

                                <a href="{{ route('suppliers.show', $supplier->supplier_id) }}"
                                   class="btn-action-small btn-view">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}"
                                   class="btn-action-small btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-small btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
</div>
@endsection