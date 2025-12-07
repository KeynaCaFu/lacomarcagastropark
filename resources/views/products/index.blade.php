@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container-fluid">
    <!-- Header con título y botón crear -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-box-open mr-2"></i> Gestión de Productos
            </h1>
            <small class="text-muted">Administra el catálogo de productos disponibles</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary">
                <div class="card-body">
                    <h6 class="card-title text-primary">Total de Productos</h6>
                    <h2>{{ $totals['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <h6 class="card-title text-success">Disponibles</h6>
                    <h2>{{ $totals['available'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger">No Disponibles</h6>
                    <h2>{{ $totals['unavailable'] }}</h2>
                </div>
            </div>
        </div>
    </div>

  <!-- Panel de Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">

            <!-- ENCABEZADO -->
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h6>

                    <!-- Botón del Collapse -->
                        <button class="btn btn-sm btn-outline-secondary" 
                            type="button" 
                            id="filtrosToggleBtn" 
                            aria-expanded="false" 
                            aria-controls="filtrosSection">
                        <i class="fas fa-chevron-down" id="filtrosIcon"></i>
                    </button>
                </div>

                <div class="text-muted">
                    <i class="fas fa-box-open"></i> 
                    <strong>{{ $products->count() }}</strong> 
                    de 
                    <strong>{{ $totals['total'] ?? 0 }}</strong> productos
                </div>
            </div>

            <!-- FILTROS  -->
            <div id="filtrosSection" class="filtros-section closed">
                <div class="card-body">

                    <form method="GET" action="{{ route('products.index') }}" id="filtrosForm">
                        <div class="row g-3">

                            <!-- Búsqueda -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="buscar" class="form-label">Búsqueda</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="buscar" 
                                       name="buscar" 
                                       placeholder="Buscar por nombre..." 
                                       value="{{ request('buscar') }}">
                            </div>

                            <!-- Categoría -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" 
                                            {{ request('categoria') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="Disponible" 
                                        {{ request('estado') == 'Disponible' ? 'selected' : '' }}>
                                        Disponible
                                    </option>
                                    <option value="No disponible" 
                                        {{ request('estado') == 'No disponible' ? 'selected' : '' }}>
                                        No disponible
                                    </option>
                                </select>
                            </div>

                        </div>

                        <!-- ACCIONES -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">

                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                                    </button>

                                    <button type="button"
                                            id="clearFiltersBtn"
                                            class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </button>

                                    <span class="text-muted small align-self-center ms-2">
                                        Mostrando {{ $products->count() }} de {{ $totals['total'] }} productos
                                    </span>

                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>


    <!-- Tabla de productos (AJAX actualizable) -->
    <div id="productsTableContainer">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Galería</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->photo)
                                     <img src="{{ $product->photo }}" alt="{{ $product->name }}" 
                                         class="product-thumb" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="badge badge-secondary">Sin foto</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="badge badge-info">{{ $product->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong>₡{{ number_format($product->price, 2, ',', '.') }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('products.gallery', $product->product_id) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Ver galería">
                                    <i class="fas fa-images"></i> {{ $product->gallery_count ?? 0 }}
                                </a>
                            </td>
                            <td>
                                @if($product->status === 'Available')
                                    <span class="badge badge-success">Disponible</span>
                                @else
                                    <span class="badge badge-danger">No disponible</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product->product_id) }}" 
                                   class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->product_id) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product->product_id) }}" 
                                      style="display:inline;" 
                                      onsubmit="return confirm('¿Está seguro de que desea eliminar &quot;{{ $product->name }}&quot;?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay productos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(method_exists($products, 'links'))
    <div class="row mt-4">
        <div class="col-md-12 d-flex justify-content-center">
            <div class="pagination-container" id="productsPagination">
                {{ $products->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtrosIcon = document.getElementById('filtrosIcon');
        const filtrosSection = document.getElementById('filtrosSection');
        const filtrosBtn = document.getElementById('filtrosToggleBtn');
        
        if (!filtrosIcon || !filtrosSection || !filtrosBtn) {
            console.warn('Elementos de filtro no encontrados');
            return;
        }

        // Estado inicial del icono según visibilidad
        const setExpanded = (expanded) => {
            filtrosBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            filtrosIcon.classList.toggle('rotated', expanded);
            filtrosSection.classList.toggle('closed', !expanded);
        };
        // Ocultar por defecto; solo aparece al hacer clic
        setExpanded(false);

        // Toggle manual sin Bootstrap Collapse
        filtrosBtn.addEventListener('click', () => {
            const isOpen = filtrosBtn.getAttribute('aria-expanded') === 'true';
            setExpanded(!isOpen);
        });

        // Si hay filtros activos, abrir el acordeón automáticamente
        const buscar = '{{ request('buscar') }}';
        const categoria = '{{ request('categoria') }}';
        const estado = '{{ request('estado') }}';
        
        // No auto-abrir: solo abrir al hacer clic
        // AJAX: Actualizar solo la tabla y paginación
        const tableContainer = document.getElementById('productsTableContainer');
        const filtrosForm = document.getElementById('filtrosForm');
        const clearBtn = document.getElementById('clearFiltersBtn');

        const fetchAndReplaceTable = async (url) => {
            try {
                // Mostrar estado de carga simple
                const placeholder = document.createElement('div');
                placeholder.className = 'p-3 text-center text-muted';
                placeholder.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando productos...';
                tableContainer.parentNode.insertBefore(placeholder, tableContainer);
                tableContainer.style.opacity = '0.5';

                const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await resp.text();

                // Crear documento temporal para extraer la sección
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('productsTableContainer');

                if (newTable) {
                    tableContainer.innerHTML = newTable.innerHTML;
                } else {
                    console.warn('No se encontró el contenedor de tabla en la respuesta');
                }

                // También actualizar la paginación si está dentro
                const newPagination = doc.getElementById('productsPagination');
                const oldPagination = document.getElementById('productsPagination');
                if (newPagination && oldPagination) {
                    oldPagination.innerHTML = newPagination.innerHTML;
                }
            } catch (err) {
                console.error('Error cargando tabla:', err);
            } finally {
                tableContainer.style.opacity = '1';
                const ph = tableContainer.previousElementSibling;
                if (ph && ph.classList.contains('text-center')) ph.remove();
            }
        };

        // Interceptar envío del formulario de filtros
        if (filtrosForm) {
            filtrosForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(filtrosForm);
                const params = new URLSearchParams(formData);
                const url = `${filtrosForm.action}?${params.toString()}`;
                fetchAndReplaceTable(url);
                // Actualizar URL sin recargar
                window.history.replaceState({}, '', url);
            });
        }

        // Limpiar filtros sin recargar toda la página
        if (clearBtn && filtrosForm) {
            clearBtn.addEventListener('click', () => {
                // Resetear formulario
                filtrosForm.reset();
                // Construir URL base sin parámetros
                const baseUrl = filtrosForm.action;
                // Refrescar solo la tabla
                fetchAndReplaceTable(baseUrl);
                // Actualizar URL sin recargar
                window.history.replaceState({}, '', baseUrl);
            });
        }

        // Delegación de eventos para paginación (AJAX)
        document.addEventListener('click', (e) => {
            const a = e.target.closest('#productsPagination a.page-link, #productsPagination a');
            if (a && a.href) {
                e.preventDefault();
                fetchAndReplaceTable(a.href);
                window.history.replaceState({}, '', a.href);
            }
        });
    });
</script>
<style>
    #filtrosIcon {
        transition: transform 0.3s ease;
    }
    #filtrosIcon.rotated {
        transform: rotate(180deg);
    }
    .filtros-section.closed {
        display: none;
    }
    /* Eliminar fondo/blanco de las miniaturas y ajustar spacing */
    .product-thumb {
        display: block;
        border-radius: 6px;
        border: none;
        padding: 0;
        background: transparent;
    }
    /* Asegurar que no quede espacio extra bajo la tabla */
    #productsTableContainer .table-responsive { margin-bottom: 0; }
    #productsTableContainer table { margin-bottom: 0; }
    /* En algunos temas, .img-thumbnail agrega fondo blanco: neutralizarlo aquí por si aplica */
    .table .img-thumbnail { background: transparent; border: none; padding: 0; }
</style>
@endpush

@endsection
