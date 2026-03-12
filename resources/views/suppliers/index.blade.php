@extends('layouts.app')

@section('title', 'Proveedores')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="product-page-wrapper">

<div class="products-container">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
        <ol class="breadcrumb" style="padding: 0; background: none;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>
    </nav>

    <!-- Header Section -->
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

    <!-- Tarjeta de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <div class="stat-label">Total de Proveedores</div>
                <div class="stat-number">{{ $totals['total'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filtros en acordeón -->
    <div class="filters-accordion">
        <button type="button" id="filtersToggle" class="filters-toggle" aria-expanded="false" aria-controls="filtrosBody">
            <i class="fas fa-chevron-down"></i>
            Buscar Proveedores
        </button>
        <div id="filtrosBody" class="search-filter-group closed" role="region" aria-labelledby="filtersToggle">
            <form method="GET" action="{{ route('suppliers.index') }}" id="filtrosForm" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%;">
                <input type="text" 
                       name="buscar" 
                       class="filter-select" 
                       placeholder="Buscar por nombre, teléfono o email..."
                       value="{{ request('buscar') }}"
                       style="flex: 1; min-width: 250px;">
                
                <button type="submit" class="btn-action" style="background: #0ea5e9; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-search"></i> Buscar
                </button>
                
                @if(request('buscar'))
                <a href="{{ route('suppliers.index') }}" class="btn-action" style="background: #e5e7eb; color: #374151; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-redo"></i> Limpiar
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla de Proveedores -->
    <div class="table-wrapper" style="margin-top: 24px;">
        @if($suppliers && count($suppliers) > 0)
            <table class="table table-hover" style="margin: 0;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 16px; font-weight: 600; color: #475569;">Nombre</th>
                        <th style="padding: 16px; font-weight: 600; color: #475569;">Teléfono</th>
                        <th style="padding: 16px; font-weight: 600; color: #475569;">Email</th>
                        <th style="padding: 16px; font-weight: 600; color: #475569;">Registrado</th>
                        <th style="padding: 16px; font-weight: 600; color: #475569; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 16px;">
                            <strong>{{ $supplier->name }}</strong>
                        </td>
                        <td style="padding: 16px;">
                            <a href="tel:{{ $supplier->phone }}" style="color: #0ea5e9; text-decoration: none;">
                                {{ $supplier->phone }}
                            </a>
                        </td>
                        <td style="padding: 16px;">
                            <a href="mailto:{{ $supplier->email }}" style="color: #0ea5e9; text-decoration: none;">
                                {{ $supplier->email }}
                            </a>
                        </td>
                        <td style="padding: 16px; font-size: 12px; color: #64748b;">
                            {{ $supplier->created_at ? $supplier->created_at->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" title="Ver detalles" class="btn-action-small" style="background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" title="Editar" class="btn-action-small" style="background: #fef3c7; color: #a16207; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar" class="btn-action-small" style="background: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 12px;" onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación -->
            @if(method_exists($suppliers, 'links'))
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $suppliers->onEachSide(1)->links() }}
            </div>
            @endif
        @else
            <div style="padding: 40px; text-align: center; color: #94a3b8;">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="font-size: 16px; margin: 0;">No hay proveedores registrados</p>
                <a href="{{ route('suppliers.create') }}" class="btn-create" style="margin-top: 16px; display: inline-block;">
                    <i class="fas fa-plus"></i> Crear el primer proveedor
                </a>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    // Toggle para filtros
    document.addEventListener('DOMContentLoaded', function() {
        const filtersToggle = document.getElementById('filtersToggle');
        const filtersBody = document.getElementById('filtrosBody');
        
        if (filtersToggle && filtersBody) {
            filtersToggle.addEventListener('click', () => {
                const isClosed = filtersBody.classList.contains('closed');
                if (isClosed) {
                    filtersBody.classList.remove('closed');
                    filtersToggle.classList.add('open');
                    filtersToggle.setAttribute('aria-expanded', 'true');
                } else {
                    filtersBody.classList.add('closed');
                    filtersToggle.classList.remove('open');
                    filtersToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>
@endpush

@endsection
