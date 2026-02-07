@extends('layouts.app')

@section('title', 'Productos')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
@endpush

@section('content')
<div style="padding: 0 15px;">
<style>
    .products-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-top: 0;
    }

    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .header-section h2 {
        font-size: 28px !important;
        font-weight: 800 !important;
        color: #1f2937 !important;
        margin: 0 !important;
        flex: 1;
    }

    .header-actions {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    /* Estadísticas Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        position: relative;
        border-radius: 12px;
        padding: 18px 20px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        transition: transform .2s ease, box-shadow .2s ease;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        flex-shrink: 0;
    }

    .stat-card.total .stat-icon {
        background: #e0f2fe; /* sky-100 */
        color: #0369a1;      /* sky-700 */
    }
    .stat-card.available .stat-icon {
        background: #dcfce7; /* green-100 */
        color: #16a34a;      /* green-600 */
    }
    .stat-card.unavailable .stat-icon {
        background: #fee2e2; /* red-100 */
        color: #dc2626;      /* red-600 */
    }

    .stat-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.1;
    }

    .btn-create {
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }

    .btn-create:hover {
        background-color: #54625a;
        color: white;
        
    }

    .search-filter-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
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
    }

    .filters-toggle i {
        transition: transform .2s ease;
    }

    .filters-toggle.open i {
        transform: rotate(180deg);
    }

    #filtrosBody.closed {
        display: none;
    }

    .search-input, .filter-select {
        padding: 10px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .search-input:focus, .filter-select:focus {
        outline: none;
        border-color: #16a34a;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f8f9fa;
    }

    th {
        padding: 12px;
        text-align: center;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }

    td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
    }

    tr:hover {
        background: #f9fafb;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-available {
        background: #dcfce7;
        color: #166534;
    }

    .status-unavailable {
        background: #fee2e2;
        color: #991b1b;
    }

    .category-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        background: #ede9fe;
        color: #6d28d9;
    }

    .product-thumb {
        border-radius: 6px;
        border: none;
        padding: 0;
        background: transparent;
    }

    .actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        border: 2px solid;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: transparent;
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
        border-color: #43423f;
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

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
    }

    .page-item {
        border-radius: 6px;
        overflow: hidden;
    }

    .page-link {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        color: #374151;
        text-decoration: none;
        transition: all 0.3s ease;
        text-align: center;
        min-width: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .page-link:hover {
        background: #f3f4f6;
    }

    .page-item.active .page-link {
        background: #16a34a;
        color: white;
        border-color: #16a34a;
    }

    @media (max-width: 768px) {
        .header-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-filter-group {
            flex-direction: column;
        }

        .search-input, .filter-select, .btn-create {
            width: 100%;
        }

        table {
            font-size: 13px;
        }

        th, td {
            padding: 8px;
        }

        .actions {
            flex-direction: column;
        }

        .btn-action {
            justify-content: center;
        }
    }
</style>

<div class="products-container">
    <div class="header-section">
        <h2>Gestión de Productos</h2>
        <div class="header-actions">
            <a href="{{ route('products.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon"><i class="fas fa-boxes"></i></div>
            <div class="stat-content">
                <div class="stat-label">Total de Productos</div>
                <div class="stat-number">{{ $totals['total'] }}</div>
            </div>
        </div>
        <div class="stat-card available">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-label">Disponibles</div>
                <div class="stat-number">{{ $totals['available'] }}</div>
            </div>
        </div>
        <div class="stat-card unavailable">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-content">
                <div class="stat-label">No Disponibles</div>
                <div class="stat-number">{{ $totals['unavailable'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filtros en acordeón -->
    <div class="filters-accordion">
        <button type="button" id="filtersToggle" class="filters-toggle" aria-expanded="false" aria-controls="filtrosBody">
            <i class="fas fa-chevron-down"></i>
            Filtros de búsqueda
        </button>
        <div id="filtrosBody" class="search-filter-group closed" role="region" aria-labelledby="filtersToggle">
            <form method="GET" action="{{ route('products.index') }}" id="filtrosForm" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%;">
                <input 
                    type="text" 
                    id="buscar"
                    class="search-input" 
                    name="buscar"
                    placeholder="Buscar por nombre..." 
                    value="{{ request('buscar') }}"
                    style="flex: 1; min-width: 200px;"
                />

                <select id="categoria" name="categoria" class="filter-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('categoria') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                <select id="estado" name="estado" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="No disponible" {{ request('estado') == 'No disponible' ? 'selected' : '' }}>No disponible</option>
                </select>

                <button type="submit" id="searchBtn" class="btn-action" style="background: #16a34a; color: white; border: none; padding: 10px 20px;">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <a href="javascript:void(0);" id="clearBtn" class="btn-action" style="background: #e5e7eb; color: #374151; padding: 10px 20px; display: none;">
                    <i class="fas fa-redo"></i> Limpiar
                </a>
            </form>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div id="productsTableContainer" class="table-wrapper">
        @include('products.table', ['products' => $products])
    </div>

    <!-- Paginación -->
    @if(method_exists($products, 'links'))
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        <div id="productsPagination">
            {{ $products->onEachSide(1)->links() }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // SweetAlert2 CDN
    (function(){
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const filtersToggle = document.getElementById('filtersToggle');
        const filtersBody = document.getElementById('filtrosBody');
        const clearBtn = document.getElementById('clearBtn');
        const searchBtn = document.getElementById('searchBtn');
        const filtrosForm = document.getElementById('filtrosForm');
        
        // Toggle acordeón de filtros
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

        // Mostrar botón limpiar si hay filtros activos
        const updateClearButton = () => {
            const buscar = document.getElementById('buscar').value;
            const categoria = document.getElementById('categoria').value;
            const estado = document.getElementById('estado').value;
            
            if (buscar || categoria || estado) {
                clearBtn.style.display = 'inline-flex';
            } else {
                clearBtn.style.display = 'none';
            }
        };

        // Limpiar filtros
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                document.getElementById('buscar').value = '';
                document.getElementById('categoria').value = '';
                document.getElementById('estado').value = '';
                window.location.href = '{{ route('products.index') }}';
            });
        }

        // Cambios en tiempo real
        document.getElementById('buscar').addEventListener('keypress', (e) => {
            if(e.key === 'Enter') {
                filtrosForm.submit();
            }
        });

        document.getElementById('categoria').addEventListener('change', () => {
            updateClearButton();
        });

        document.getElementById('estado').addEventListener('change', () => {
            updateClearButton();
        });

        updateClearButton();

        // Mover botón de ayuda al header
        const helpContainer = document.getElementById('topHelpContainer');
        const helpButtonContainer = document.getElementById('helpButtonContainerIndex');
        const helpButton = document.getElementById('helpButtonIndex');
        
        if (helpContainer && helpButtonContainer && helpButton) {
            helpContainer.appendChild(helpButton);
            helpButtonContainer.style.display = 'none';
        }

        // Interceptar formularios de eliminación de producto en la tabla
        document.querySelectorAll('form[action*="products/"][method="POST"]').forEach(function(form){
            const deleteMethod = form.querySelector('input[name="_method"][value="DELETE"]');
            if (deleteMethod) {
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    const productName = form.closest('tr')?.querySelector('td:nth-child(2) strong')?.textContent || 'este producto';
                    if (window.swConfirm) {
                        swConfirm({
                            title: 'Eliminar producto',
                            text: `¿Desea eliminar "${productName}"?`,
                            icon: 'warning',
                            confirmButtonColor: '#dc2626',
                            confirmButtonText: 'Sí, eliminar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else {
                        const ok = confirm(`¿Desea eliminar "${productName}"?`);
                        if (ok) form.submit();
                    }
                });
            }
        });

        // Mostrar alertas de éxito desde sesión (si existen)
        const successMsg = @json(session('success'));
        if (successMsg && window.swAlert) {
            swAlert({ icon: 'success', title: 'Éxito', text: successMsg });
        }

        // Mostrar alertas de error desde sesión (si existen)
        const errorMsg = @json(session('error'));
        if (errorMsg && window.swAlert) {
            swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
        }

        // Mostrar errores de validación (si existen)
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

    // Abrir modal de ayuda
    function openProductsIndexHelpModal() {
        const modal = document.getElementById('productsIndexHelpModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    // Cerrar modal de ayuda
    function closeProductsIndexHelpModal() {
        const modal = document.getElementById('productsIndexHelpModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('productsIndexHelpModal');
        if (e.target === modal) {
            closeProductsIndexHelpModal();
        }
    });
</script>
@endpush

<!-- Botón de Ayuda para Índice de Productos -->
<div id="helpButtonContainerIndex" style="display: none;">
    <button id="helpButtonIndex" type="button" class="btn btn-help" onclick="openProductsIndexHelpModal()">
        <i class="fas fa-question-circle"></i> Ayuda
    </button>
</div>

<!-- Modal de Ayuda para Índice de Productos -->
<div id="productsIndexHelpModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpTitle">
        <div class="modal-header">
            <h3 id="helpTitle"><i class="fas fa-lightbulb"></i> Consejos para Gestionar Productos</h3>
            <button type="button" class="close" aria-label="Cerrar" onclick="closeProductsIndexHelpModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <h5><i class="fas fa-plus-circle"></i> Crear un Nuevo Producto</h5>
                <p>
                    Haz clic en el botón "Nuevo Producto" para agregar un nuevo artículo al catálogo.<br>
                    <small class="text-muted">Rellena todos los campos requeridos (nombre, precio y estado).</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-chart-bar"></i> Estadísticas</h5>
                <p>
                    Las tarjetas superiores muestran un resumen rápido de tus productos.<br>
                    <small class="text-muted">Total: todos los productos. Disponibles: productos en stock. No disponibles: productos sin stock.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-search"></i> Buscar y Filtrar</h5>
                <p>
                    Usa los filtros para encontrar productos rápidamente por nombre, categoría o estado.<br>
                    <small class="text-muted">Abre los filtros haciendo clic en "Filtros de búsqueda" para ver todas las opciones.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-eye"></i> Ver Detalles</h5>
                <p>
                    Haz clic en el icono de ojo para ver toda la información del producto.<br>
                    <small class="text-muted">Desde aquí puedes acceder a la galería de imágenes del producto.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-edit"></i> Editar Producto</h5>
                <p>
                    Haz clic en el icono de lápiz para modificar la información del producto.<br>
                    <small class="text-muted">Puedes cambiar nombre, precio, disponibilidad y otros detalles.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-images"></i> Galería de Imágenes</h5>
                <p>
                    Haz clic en el número de imágenes para agregar más fotos al producto.<br>
                    <small class="text-muted">Cada producto puede tener múltiples imágenes para mostrar diferentes ángulos.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-trash"></i> Eliminar Producto</h5>
                <p>
                    Haz clic en el icono de basura para eliminar un producto.<br>
                    <small class="text-muted">Esta acción es irreversible y también eliminará la galería del producto.</small>
                </p>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeProductsIndexHelpModal()">Cerrar</button>
        </div>
    </div>
</div>

</div>
@endsection
