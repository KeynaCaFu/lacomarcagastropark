@extends('layouts.app')

@section('title', 'Proveedores')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">

    <style>
        .suppliers-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 24px;
            margin-top: 50px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .suppliers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .suppliers-table thead {
            background: #c9ccc4;
        }

        .suppliers-table thead th {
            background: #c9ccc4;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .suppliers-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
        }

        .suppliers-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            background-color: #f4f4f4;
        }

        .suppliers-table tbody tr:hover {
            background: #e8e8e8;
        }

        .suppliers-name {
            font-weight: 700;
            color: #1f2937;
        }

        .suppliers-link {
            color: #374151 !important;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
        }

        .suppliers-muted {
            color: #9ca3af;
        }

        .suppliers-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-view {
            background: transparent;
            color: #0ea5e9;
            border-color: #0ea5e9;
        }

        .btn-view:hover {
            background: #0ea5e9;
            color: white;
        }

        .btn-edit {
            background: transparent;
            color: #3e3d3a;
            border: 2px solid #43423f;
        }

        .btn-edit:hover {
            background: #848380;
            color: white;
        }

        .btn-delete {
            background: transparent;
            color: #dc2626;
            border-color: #dc2626;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }

        .btn-action-small {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .filters-accordion {
            margin-bottom: 12px;
        }

        .filters-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #111827;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filters-toggle i {
            transition: transform 0.2s ease;
        }

        .filters-toggle.open i {
            transform: rotate(180deg);
        }

        .filters-toggle:hover {
            background: #f1f5f9;
        }

        #filtrosBody.closed {
            display: none;
        }

        #filtrosBody.open {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-filter-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 12px;
            padding: 12px;
            border-radius: 8px;
        }

        .search-filter-group form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            width: 100%;
        }

        .filter-select {
            padding: 6px 10px;
            border: 1px solid #db8a21;
            border-radius: 6px;
            font-size: 13px;
            transition: border-color 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #dc860e;
            background: #f0f9ff;
        }

        .search-filter-group .btn-action {
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 6px;
        }

        .search-filter-group .btn-action:hover {
            transform: translateY(-1px);
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
            Filtrar por fecha
        </button>

        <div id="filtrosBody" class="search-filter-group closed">
            <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="fecha_desde" style="font-size: 12px; font-weight: 500; color: #374151; white-space: nowrap;">Desde:</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" class="filter-select" lang="es-ES" value="{{ request('fecha_desde') }}">
                </div>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="fecha_hasta" style="font-size: 12px; font-weight: 500; color: #374151; white-space: nowrap;">Hasta:</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" class="filter-select" lang="es-ES" value="{{ request('fecha_hasta') }}">
                </div>
                
                <button type="button" id="clearFilterBtn" class="btn-action" style="color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px; font-size: 12px; border: none; cursor: pointer;">
                    <i class="fas fa-redo"></i> Limpiar
                </button>
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
                        <th style="text-align: center;">Acciones</th>
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
                                   class="btn-action-small btn-view"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}"
                                   class="btn-action-small btn-edit"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button" 
                                        class="btn-action-small btn-delete"
                                        onclick="deleteSupplier({{ $supplier->supplier_id }}, '{{ $supplier->name }}')"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>

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

@push('scripts')
<script>
async function deleteSupplier(supplierId, supplierName) {
    if (window.swConfirm) {
        const result = await swConfirm({
            title: 'Eliminar proveedor',
            text: `¿Está seguro de que desea eliminar a ${supplierName}?`,
            icon: 'warning',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626'
        });
        
        if (result.isConfirmed) {
            // Crear un formulario temporal para enviar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proveedores/${supplierId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    } else {
        const ok = confirm(`¿Está seguro de que desea eliminar a ${supplierName}?`);
        if (ok) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/proveedores/${supplierId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

// Manejar el acordeón de filtros
(function() {
    const toggleBtn = document.getElementById('filtersToggle');
    const filtrosBody = document.getElementById('filtrosBody');

    if (toggleBtn && filtrosBody) {
        toggleBtn.addEventListener('click', function() {
            filtrosBody.classList.toggle('closed');
            filtrosBody.classList.toggle('open');
            toggleBtn.classList.toggle('open');
        });
    }
})();

// Manejar el formulario de filtros con AJAX
(function() {
    const filterForm = document.getElementById('filterForm');
    const fechaDesde = document.getElementById('fecha_desde');
    const fechaHasta = document.getElementById('fecha_hasta');
    const clearBtn = document.getElementById('clearFilterBtn');
    
    function loadSuppliers(url) {
        // Mostrar indicador de carga
        const tableWrapper = document.querySelector('.table-wrapper');
        if (tableWrapper) {
            tableWrapper.style.opacity = '0.6';
            tableWrapper.style.pointerEvents = 'none';
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            // Crear un elemento temporal para parsear el HTML
            const temp = document.createElement('div');
            temp.innerHTML = html;
            
            // Extraer la tabla nueva
            const newTable = temp.querySelector('.table-wrapper');
            
            if (newTable && tableWrapper) {
                // Reemplazar la tabla con la nueva
                tableWrapper.innerHTML = newTable.innerHTML;
                tableWrapper.style.opacity = '1';
                tableWrapper.style.pointerEvents = 'auto';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (tableWrapper) {
                tableWrapper.style.opacity = '1';
                tableWrapper.style.pointerEvents = 'auto';
            }
            if (window.swAlert) {
                swAlert({ icon: 'error', title: 'Error', text: 'Hubo un error al filtrar los proveedores' });
            }
        });
    }
    
    function applyFilter() {
        const formData = new FormData(filterForm);
        const queryString = new URLSearchParams(formData).toString();
        loadSuppliers(`{{ route('suppliers.index') }}?${queryString}`);
    }
    
    // Aplicar filtro automáticamente cuando cambien las fechas
    if (fechaDesde) {
        fechaDesde.addEventListener('change', applyFilter);
    }
    
    if (fechaHasta) {
        fechaHasta.addEventListener('change', applyFilter);
    }
    
    // Limpiar filtros
    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (fechaDesde) fechaDesde.value = '';
            if (fechaHasta) fechaHasta.value = '';
            loadSuppliers('{{ route('suppliers.index') }}');
        });
    }
})();
</script>
@endpush
