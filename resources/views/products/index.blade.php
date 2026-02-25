@extends('layouts.app')

@section('title', 'Productos')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div style="padding: 0 15px;">

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
            setTimeout(checkAndShowToast, 100);
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
