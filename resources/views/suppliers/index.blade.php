@extends('layouts.app')

@section('title', 'Proveedores')

@push('styles')
<link href="{{ asset('css/modals.css') }}" rel="stylesheet">
<link href="{{ asset('css/productos.css') }}" rel="stylesheet">

<style>
    .suppliers-table {
        width: 100%;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        overflow: hidden;
        border-radius: 12px;
        border: none !important;
    }

    .suppliers-table thead,
    .suppliers-table thead tr,
    .suppliers-table thead th {
        background: #c9ccc4 !important;
        border: none !important;
        border-top: none !important;
        border-bottom: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .suppliers-table thead::before,
    .suppliers-table thead::after,
    .suppliers-table thead tr::before,
    .suppliers-table thead tr::after {
        display: none !important;
        content: none !important;
    }

    .suppliers-table thead th {
        color: #374151 !important;
        font-weight: 700 !important;
        padding: 16px !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .suppliers-table tbody tr {
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .suppliers-table tbody td {
        padding: 16px !important;
        vertical-align: middle !important;
        color: #6b7280 !important;
        text-align: center !important;
        background: #ffffff !important;
        border-top: none !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .suppliers-table tbody tr:first-child td {
        border-top: none !important;
    }

    .suppliers-name {
        font-weight: 700;
        color: #1f2937 !important;
    }

    .suppliers-link {
        color: #6b7280 !important;
        text-decoration: none;
        font-weight: 500;
    }

    .suppliers-link:hover {
        color: #374151 !important;
    }

    .suppliers-table td:last-child,
    .suppliers-table th:last-child {
        text-align: center !important;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    #filtrosBody.closed {
        display: none;
    }

    .filters-toggle.open i {
        transform: rotate(180deg);
    }

    .single-date-filter {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .single-date-filter label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .single-date-filter input[type="date"] {
        min-width: 220px;
    }

    .date-input-wrapper {
    position: relative;
    display: inline-block;
}

.date-input-wrapper input[type="date"] {
    padding-right: 35px !important;
}

.clear-date-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: #9ca3af;
    cursor: pointer;
    display: none;
    line-height: 1;
}

.clear-date-icon:hover {
    color: #ef4444;
}
</style>
@endpush

@section('content')
<div class="product-page-wrapper">
    <div class="products-container">
        
        <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
            <ol class="breadcrumb" style="padding: 0; background: none;">
                <li class="breadcrumb-item active">Proveedores</li>
            </ol>
        </nav>

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

        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Total de Proveedores</div>
                    <div class="stat-number">{{ $totals['total'] }}</div>
                </div>
            </div>
        </div>

        <div class="filters-accordion">
            <button type="button" id="filtersToggle" class="filters-toggle" aria-expanded="false">
                <i class="fas fa-chevron-down"></i>
                Filtros de búsqueda
            </button>

            <div id="filtrosBody" class="search-filter-group closed">
                <form id="filtrosFormSuppliers" style="display:flex; gap:12px; flex-wrap:wrap; width:100%;">
                    
                    <div class="single-date-filter">
    <label for="fecha">Fecha de registro</label>

    <div class="date-input-wrapper">
        <input
            type="date"
            id="fecha"
            name="fecha"
            class="filter-select"
            value="{{ request('fecha') }}"
            title="Día/Mes/Año">

        <span id="clearDateIcon" class="clear-date-icon">&times;</span>
    </div>
</div>

<div style="display:flex; align-items:flex-end; gap:12px;">
    <button type="button" id="searchDateBtnSuppliers" class="btn-action">
        <i class="fas fa-search"></i> Buscar
    </button>

    
</div>

                    <div style="display:flex; align-items:flex-end;">
                        <button type="button" id="clearBtnSuppliers" class="btn-action" style="display:none;">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="suppliersTableContainer" class="table-wrapper" style="margin-top: 24px;">
            @include('suppliers.table', ['suppliers' => $suppliers])
        </div>

    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filtersToggle = document.getElementById('filtersToggle');
    const filtersBody = document.getElementById('filtrosBody');

    const searchDateBtn = document.getElementById('searchDateBtnSuppliers');
    const clearDateIcon = document.getElementById('clearDateIcon');
    const fecha = document.getElementById('fecha');

    const topSearchInput = document.getElementById('topSearchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    const isSuppliersPage = window.location.pathname.includes('/proveedores');

    if (filtersToggle && filtersBody) {
        filtersToggle.addEventListener('click', function () {
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

    const updateClearIcon = () => {
        if (!clearDateIcon || !fecha) return;
        clearDateIcon.style.display = fecha.value ? 'block' : 'none';
    };

    const attachDeleteEvents = () => {
        document.querySelectorAll('form').forEach(function(form) {
            const deleteMethod = form.querySelector('input[name="_method"][value="DELETE"]');

            if (deleteMethod && !form.dataset.deleteBound) {
                form.dataset.deleteBound = 'true';

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const ejecutarEliminacion = () => {
                        if (window.confirmWithUndo) {
                            confirmWithUndo({
                                message: 'El proveedor se eliminará',
                                delayMs: 8000,
                                onConfirm: () => {
                                    form.dataset.deleteBound = 'done';
                                    form.submit();
                                }
                            });
                        } else {
                            form.dataset.deleteBound = 'done';
                            form.submit();
                        }
                    };

                    if (window.swConfirm) {
                        swConfirm({
                            title: 'Eliminar proveedor',
                            text: '¿Está seguro de eliminar este proveedor?',
                            icon: 'warning',
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Sí, eliminar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                ejecutarEliminacion();
                            }
                        });
                    } else {
                        if (confirm('¿Está seguro de eliminar este proveedor?')) {
                            ejecutarEliminacion();
                        }
                    }
                });
            }
        });
    };

    const loadFilteredSuppliers = async () => {
        try {
            const params = new URLSearchParams();

            if (topSearchInput && topSearchInput.value.trim() !== '') {
                params.append('buscar', topSearchInput.value.trim());
            }

            if (fecha && fecha.value !== '') {
                params.append('fecha', fecha.value);
            }

            const url = `{{ route('suppliers.index') }}${params.toString() ? '?' + params.toString() : ''}`;

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;

            const html = await response.text();
            const currentTable = document.getElementById('suppliersTableContainer');

                if (currentTable) {
                    currentTable.innerHTML = html;
                     attachDeleteEvents();
                    }
        } catch (error) {
            console.error('Error al filtrar proveedores:', error);
        }
    };

    if (fecha) {
        fecha.addEventListener('change', function () {
            updateClearIcon();
        });
    }

    if (clearDateIcon) {
    clearDateIcon.addEventListener('click', function () {
        if (fecha) fecha.value = '';
        updateClearIcon();
        loadFilteredSuppliers();
    });
}

    if (searchDateBtn) {
        searchDateBtn.addEventListener('click', function () {
            loadFilteredSuppliers();
        });
    }

    if (isSuppliersPage && topSearchInput) {
        topSearchInput.addEventListener('input', function () {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = this.value.trim() ? 'inline-block' : 'none';
            }
        });

        topSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadFilteredSuppliers();
            }
        });
    }

    if (isSuppliersPage && searchBtn) {
        searchBtn.onclick = function(e) {
            e.preventDefault();
            loadFilteredSuppliers();
        };
    }

    if (isSuppliersPage && clearSearchBtn) {
        clearSearchBtn.onclick = function(e) {
            e.preventDefault();
            if (topSearchInput) topSearchInput.value = '';
            clearSearchBtn.style.display = 'none';
            loadFilteredSuppliers();
        };
    }

    updateClearIcon();
    attachDeleteEvents();
});
</script>
@endpush