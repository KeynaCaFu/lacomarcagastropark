@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('styles')
    <link href="{{ asset('css/pages/suppliers.css') }}" rel="stylesheet">
    <link href="{{ asset('css/supplier-modals.css') }}" rel="stylesheet">
    <link href="{{ asset('css/supplier-enhancements.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
   
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
               <h1 class="h3 mb-0"><i class="fas fa-truck me-2"></i> Gestión de Proveedores</h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-responsive" onclick="showHelpModal()" title="Ayuda">
                        <i class="fas fa-question-circle me-1"></i> 
                        <span class="d-none d-sm-inline">Ayuda</span>
                    </button>
                    <button type="button" class="btn btn-add btn-responsive" onclick="openCreateProveedorModal()">
                        <i class="fas fa-plus me-1"></i> 
                        <span class="d-none d-sm-inline">Nuevo Proveedor</span>
                        <span class="d-sm-none">Nuevo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light" id="filtrosCardHeader">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h6>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false" aria-controls="filtrosCollapse">
                            <i class="fas fa-chevron-down" id="filtrosIcon"></i>
                        </button>
                    </div>
                    <div class="text-muted">
                    🚚 <strong>{{ $suppliers->count() }}</strong> de <strong>{{ $totals['all'] ?? 0 }}</strong> proveedores
                </div>
                </div>
                <div class="collapse" id="filtrosCollapse">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroEstado" class="form-label">Estado</label>
                                <select class="form-select" id="filtroEstado">
                                    <option value="">Todos los estados</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroInsumos" class="form-label">Insumos</label>
                                <select class="form-select" id="filtroInsumos">
                                    <option value="">Todos</option>
                                    <option value="con-insumos">Con insumos</option>
                                    <option value="sin-insumos">Sin insumos</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="aplicarFiltros()">
                                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </button>
                                    <span class="text-muted small align-self-center ms-2" id="resultadosFiltro">
                                        Mostrando todos los proveedores
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($suppliers->count() > 0)
        <!-- Vista de tabla para pantallas grandes -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table class="table proveedores-table">
                    <thead>
                        <tr>
                            <th hidden>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Total Compras</th>
                            <th>Insumos</th>
                            <th>Estado</th>
                            <th class="accion">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr class="proveedor-row" 
                            data-nombre="{{ strtolower($supplier->name) }}" 
                            data-estado="{{ $supplier->status_in_spanish }}" 
                            data-contacto="{{ strtolower($supplier->phone . ' ' . $supplier->email) }}" 
                            data-supplies="{{ $supplier->supplies->count() }}">
                            <td hidden>{{ $supplier->supplier_id }}</td>
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                            </td>
                            <td class="contacto-info">
                                <i class="fas fa-phone"></i> {{ $supplier->phone }}<br>
                                <i class="fas fa-envelope"></i> {{ Str::limit($supplier->email, 25) }}
                            </td>
                            <td>₡{{ number_format($supplier->total_purchases, 2) }}</td>
                            <td>
                                @if($supplier->supplies->count() > 0)
                                    <span class="badge bg-success">{{ $supplier->supplies->count() }} insumos</span>
                                @else
                                    <span class="text-muted">Sin insumos</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->status_in_spanish == 'Activo')
                                    <span class="estado-activo-badge">{{ $supplier->status_in_spanish }}</span>
                                @else
                                    <span class="estado-inactivo-badge">{{ $supplier->status_in_spanish }}</span>
                                @endif
                            </td>
                            <td class="baction">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" title="Ver" 
                                        onclick="openShowProveedorModal({{ $supplier->supplier_id }})"
                                        onmouseenter="preloadShowProveedorModal({{ $supplier->supplier_id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar" 
                                        onclick="openEditProveedorModal({{ $supplier->supplier_id }})"
                                        onmouseenter="preloadEditProveedorModal({{ $supplier->supplier_id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" class="d-inline" id="deleteForm{{ $supplier->supplier_id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar" 
                                            onclick="handleDeleteSupplier({{ $supplier->supplier_id }}, '{{ addslashes($supplier->name) }}', {{ $supplier->supplies->count() }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vista de cards para pantallas medianas y pequeñas -->
        <div class="d-lg-none">
            <div class="row g-3">
                @foreach($suppliers as $supplier)
                <div class="col-12 col-md-6 proveedor-card-item" 
                     data-nombre="{{ strtolower($supplier->name) }}" 
                     data-estado="{{ $supplier->status_in_spanish }}" 
                     data-contacto="{{ strtolower($supplier->phone . ' ' . $supplier->email) }}" 
                     data-supplies="{{ $supplier->supplies->count() }}">
                    <div class="card proveedor-card-responsive">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">#{{ $supplier->supplier_id }}</span>
                                <h6 class="mb-0 fw-bold">{{ $supplier->name }}</h6>
                            </div>
                            @if($supplier->status_in_spanish == 'Activo')
                                <span class="estado-activo-badge">{{ $supplier->status_in_spanish }}</span>
                            @else
                                <span class="estado-inactivo-badge">{{ $supplier->status_in_spanish }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($supplier->address, 60) }}
                                    </small>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        Actualizado {{ $supplier->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-12 col-sm-6 mb-1">
                                    <small class="contacto-info">
                                        <i class="fas fa-phone me-1"></i> {{ $supplier->phone }}
                                    </small>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <small class="contacto-info">
                                        <i class="fas fa-envelope me-1"></i> {{ Str::limit($supplier->email, 20) }}
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Total Compras</span>
                                        <div class="fw-bold text-success">₡{{ number_format($supplier->total_purchases, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Insumos</span>
                                        <div class="fw-bold">
                                            @if($supplier->supplies->count() > 0)
                                                <span class="badge bg-success">{{ $supplier->supplies->count() }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-info btn-sm flex-fill" 
                                    onclick="openShowProveedorModal({{ $supplier->supplier_id }})"
                                    onmouseenter="preloadShowProveedorModal({{ $supplier->supplier_id }})">
                                    <i class="fas fa-eye me-1"></i>
                                    <span class="d-none d-sm-inline">Ver</span>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm flex-fill" 
                                    onclick="openEditProveedorModal({{ $supplier->supplier_id }})"
                                    onmouseenter="preloadEditProveedorModal({{ $supplier->supplier_id }})">
                                    <i class="fas fa-edit me-1"></i>
                                    <span class="d-none d-sm-inline">Editar</span>
                                </button>
                                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" class="flex-fill" id="deleteForm{{ $supplier->supplier_id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm w-100" 
                                        onclick="handleDeleteSupplier({{ $supplier->supplier_id }}, '{{ addslashes($supplier->name) }}', {{ $supplier->supplies->count() }})">
                                        <i class="fas fa-trash me-1"></i>
                                        <span class="d-none d-sm-inline">Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h4>No hay proveedores registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer proveedor.</p>
                    <button type="button" class="btn btn-primary" onclick="openCreateProveedorModal()">
                        <i class="fas fa-plus me-1"></i> Crear Primer Proveedor
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(method_exists($suppliers, 'links'))
    <div class="row mt-3">
        <div class="col-12 d-flex justify-content-center">
            <div class="pagination-container">
                {{ $suppliers->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal para Ver Detalles de Proveedor -->
<div id="showProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detalles del Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('showProveedorModal')">&times;</span>
        </div>
        <div class="modal-body" id="showProveedorModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para Crear Proveedor -->
<div id="createProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Crear Nuevo Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('createProveedorModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form id="createProveedorForm" action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="create_proveedor_nombre" class="form-label">Nombre del Proveedor *</label>
                    <input type="text" class="form-control" id="create_proveedor_nombre" name="nombre" required placeholder="Ej: Distribuidora">
                </div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_telefono" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="create_proveedor_telefono" name="telefono" required placeholder="Ej: 88888888">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="create_proveedor_correo" name="correo" required placeholder="Ej: contacto@proveedor.com">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="create_proveedor_direccion" class="form-label">Dirección *</label>
                    <textarea class="form-control" id="create_proveedor_direccion" name="direccion" required placeholder="Ej: Calle 123 #45-67, Guápiles"></textarea>
                </div>

                <div class="section-divider"></div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_total_compras" class="form-label">Total de Compras *</label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="create_proveedor_total_compras" name="total_compras" required value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_estado" class="form-label">Estado *</label>
                            <select class="form-select" id="create_proveedor_estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>
                
                <div class="mb-3">
                    <label class="form-label">
                        Insumos que Provee 
                        <span class="info-tooltip" data-bs-toggle="tooltip" data-bs-placement="right" 
                              title="Seleccione los insumos que este proveedor puede suministrar a su negocio">
                            <i class="fas fa-info-circle text-primary"></i>
                        </span>
                    </label>
                    
                    @if($supplies->count() > 5)
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="searchCreateInsumos" 
                               placeholder="🔍 Buscar insumo..." onkeyup="filterInsumos('createProveedorInsumosList', this.value)">
                    </div>
                    @endif
                    
                    <div class="border p-3 rounded insumos-list-container" id="createProveedorInsumosList">
                        @foreach($supplies as $supply)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="insumos[]" value="{{ $supply->supply_id }}" id="create_proveedor_insumo{{ $supply->supply_id }}">
                            <label class="form-check-label" for="create_proveedor_insumo{{ $supply->supply_id }}">
                                <strong>{{ $supply->name }}</strong> - ₡{{ number_format($supply->price, 2) }}
                                <br><small class="text-muted">{{ $supply->unit_of_measure }} | Stock: {{ $supply->current_stock }}</small>
                            </label>
                        </div>
                        @endforeach
                        @if($supplies->count() == 0)
                        <div class="text-center p-3">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay insumos disponibles.</p>
                            <small>Puede crear insumos primero y luego asignarlos a este proveedor.</small>
                        </div>
                        @endif
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> 
                        Puede seleccionar múltiples insumos que este proveedor puede suministrar
                    </small>
                </div>

                <div class="modal-actions d-flex flex-column flex-sm-row gap-2">
                    <button type="button" class="btn btn-secondary flex-sm-fill" onclick="closeProveedorModal('createProveedorModal')">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary flex-sm-fill">
                        <i class="fas fa-save me-1"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Proveedor -->
<div id="editProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('editProveedorModal')">&times;</span>
        </div>
        <div class="modal-body" id="editProveedorModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal de Ayuda -->
<div id="helpModal" class="custom-modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header bg-primary text-white">
            <h3><i class="fas fa-question-circle"></i> Ayuda - Gestión de Proveedores</h3>
            <span class="close text-white" onclick="closeHelpModal()">&times;</span>
        </div>
        <div class="modal-body">
            <ul class="nav nav-tabs" id="helpTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="help-general-tab" data-bs-toggle="tab" data-bs-target="#help-general" type="button">
                        <i class="fas fa-home"></i> General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="help-create-tab" data-bs-toggle="tab" data-bs-target="#help-create" type="button">
                        <i class="fas fa-plus"></i> Crear
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="help-filters-tab" data-bs-toggle="tab" data-bs-target="#help-filters" type="button">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="help-shortcuts-tab" data-bs-toggle="tab" data-bs-target="#help-shortcuts" type="button">
                        <i class="fas fa-keyboard"></i> Atajos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="help-glossary-tab" data-bs-toggle="tab" data-bs-target="#help-glossary" type="button">
                        <i class="fas fa-book"></i> Glosario
                    </button>
                </li>
            </ul>
            
            <div class="tab-content mt-3" id="helpTabContent">
                <!-- Pestaña General -->
                <div class="tab-pane fade show active" id="help-general" role="tabpanel">
                    <h5><i class="fas fa-info-circle text-primary"></i> ¿Qué son los Proveedores?</h5>
                    <p>Los proveedores son las empresas o personas que suministran insumos a su negocio. Aquí puede gestionar toda la información de contacto, compras y los insumos que cada proveedor puede suministrar.</p>
                    
                    <h6 class="mt-3"><i class="fas fa-list-ul"></i> Acciones Disponibles:</h6>
                    <ul>
                        <li><strong><i class="fas fa-eye text-info"></i> Ver:</strong> Visualiza todos los detalles del proveedor y sus insumos asociados</li>
                        <li><strong><i class="fas fa-edit text-warning"></i> Editar:</strong> Modifica la información del proveedor</li>
                        <li><strong><i class="fas fa-trash text-danger"></i> Eliminar:</strong> Elimina permanentemente el proveedor (requiere confirmación)</li>
                    </ul>
                </div>
                
                <!-- Pestaña Crear -->
                <div class="tab-pane fade" id="help-create" role="tabpanel">
                    <h5><i class="fas fa-plus-circle text-success"></i> Cómo Crear un Proveedor</h5>
                    <ol>
                        <li>Haga clic en el botón <strong>"Nuevo Proveedor"</strong> o presione <kbd>Ctrl+N</kbd></li>
                        <li>Complete los campos obligatorios marcados con asterisco (*):
                            <ul>
                                <li><strong>Nombre:</strong> Mínimo 3 caracteres</li>
                                <li><strong>Teléfono:</strong> 8 dígitos para Costa Rica</li>
                                <li><strong>Correo:</strong> Formato válido (usuario@dominio.com)</li>
                                <li><strong>Dirección:</strong> Mínimo 10 caracteres para ser específica</li>
                                <li><strong>Total de Compras:</strong> Puede iniciar con ₡0 para nuevos proveedores</li>
                            </ul>
                        </li>
                        <li>Seleccione los insumos que este proveedor puede suministrar (opcional)</li>
                        <li>Presione <kbd>Enter</kbd> o haga clic en <strong>"Guardar Proveedor"</strong></li>
                    </ol>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Los campos se validan en tiempo real. Si ve un mensaje de error, corrija el campo antes de guardar.
                    </div>
                </div>
                
                <!-- Pestaña Filtros -->
                <div class="tab-pane fade" id="help-filters" role="tabpanel">
                    <h5><i class="fas fa-filter text-primary"></i> Usar los Filtros de Búsqueda</h5>
                    <p>Los filtros le permiten encontrar proveedores específicos rápidamente:</p>
                    
                    <ul>
                        <li><strong>Nombre:</strong> Búsqueda en tiempo real mientras escribe</li>
                        <li><strong>Estado:</strong> Filtra por Activo o Inactivo</li>
                        <li><strong>Insumos:</strong> Muestra solo proveedores con o sin insumos asignados</li>
                    </ul>
                    
                    <h6 class="mt-3">Pasos:</h6>
                    <ol>
                        <li>Expanda el panel de filtros haciendo clic en <i class="fas fa-chevron-down"></i></li>
                        <li>Ingrese sus criterios de búsqueda</li>
                        <li>Los resultados se filtran automáticamente al escribir en "Nombre"</li>
                        <li>Para otros campos, haga clic en "Aplicar Filtros"</li>
                        <li>Use "Limpiar" para resetear todos los filtros</li>
                    </ol>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Los filtros muestran cuántos proveedores coinciden con su búsqueda
                    </div>
                </div>
                
                <!-- Pestaña Atajos -->
                <div class="tab-pane fade" id="help-shortcuts" role="tabpanel">
                    <h5><i class="fas fa-keyboard text-primary"></i> Atajos de Teclado</h5>
                    <p>Use estos atajos para trabajar más rápido:</p>
                    
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Atajo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>N</kbd></td>
                                <td>Abrir formulario de Nuevo Proveedor</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>F</kbd></td>
                                <td>Enfocar en el campo de búsqueda</td>
                            </tr>
                            <tr>
                                <td><kbd>Ctrl</kbd> + <kbd>H</kbd></td>
                                <td>Mostrar esta ayuda</td>
                            </tr>
                            <tr>
                                <td><kbd>Alt</kbd> + <kbd>N</kbd></td>
                                <td>Nuevo Proveedor (alternativo)</td>
                            </tr>
                            <tr>
                                <td><kbd>Enter</kbd></td>
                                <td>Guardar formulario (cuando está en un campo de texto)</td>
                            </tr>
                            <tr>
                                <td><kbd>Esc</kbd></td>
                                <td>Cerrar modal abierto</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Nota:</strong> Los atajos no funcionan cuando está escribiendo en un área de texto (dirección).
                    </div>
                </div>
                
                <!-- Pestaña Glosario -->
                <div class="tab-pane fade" id="help-glossary" role="tabpanel">
                    <h5><i class="fas fa-book text-primary"></i> Glosario de Términos</h5>
                    
                    <dl class="row">
                        <dt class="col-sm-3">Proveedor</dt>
                        <dd class="col-sm-9">Empresa o persona que suministra productos o materias primas al negocio.</dd>
                        
                        <dt class="col-sm-3">Insumos</dt>
                        <dd class="col-sm-9">Productos o materias primas que un proveedor puede suministrar. Un proveedor puede ofrecer múltiples tipos de insumos.</dd>
                        
                        <dt class="col-sm-3">Estado: Activo</dt>
                        <dd class="col-sm-9">Proveedor disponible para realizar nuevas compras. Puede tener insumos asociados.</dd>
                        
                        <dt class="col-sm-3">Estado: Inactivo</dt>
                        <dd class="col-sm-9">Proveedor temporalmente no disponible. <span class="text-warning"><strong>⚠️ Se recomienda no tener insumos asociados a proveedores inactivos.</strong></span></dd>
                        
                        <dt class="col-sm-3">Total Compras</dt>
                        <dd class="col-sm-9">Monto acumulado de todas las compras realizadas a este proveedor.</dd>
                        
                        <dt class="col-sm-3">Deshacer</dt>
                        <dd class="col-sm-9">Al eliminar un proveedor, tiene <strong>10 segundos</strong> para deshacer la eliminación. Después de ese tiempo, los datos se eliminan permanentemente.</dd>
                    </dl>
                    
                    <div class="help-panel">
                        <div class="help-title">
                            <i class="fas fa-lightbulb"></i>
                            <span>Buenas Prácticas</span>
                        </div>
                        <div class="help-content">
                            <ul>
                                <li>Mantenga actualizada la información de contacto de sus proveedores</li>
                                <li>Asocie insumos solo a proveedores activos</li>
                                <li>Revise regularmente el estado de sus proveedores</li>
                                <li>Use los filtros para encontrar rápidamente proveedores específicos</li>
                                <li>Verifique las dependencias antes de eliminar un proveedor</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeHelpModal()">
                <i class="fas fa-times me-1"></i> Cerrar
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/supplier-modals.js') }}"></script>
<script src="{{ asset('js/supplier-validations.js') }}"></script>
<script src="{{ asset('js/supplier-filters.js') }}"></script>
<script>
// Mostrar notificación con opción de deshacer desde el servidor (flash)
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        // Si hay URL de restauración, mostrar botón Deshacer
        @if(session('restore_url'))
            if (window.proveedorModals) {
                window.proveedorModals.showNotification('success', @json(session('success')), {
                    actionText: 'Deshacer',
                    actionUrl: @json(session('restore_url')),
                    timeout: 10000
                });
            }
        @else
            if (window.proveedorModals) {
                window.proveedorModals.showNotification('success', @json(session('success')));
            }
        @endif
    @endif
});

// Función para manejar eliminación con modal personalizado
async function handleDeleteSupplier(supplierId, supplierName, suppliesCount) {
    const confirmed = await confirmDeleteSupplier(supplierId, supplierName, suppliesCount);
    if (confirmed) {
        document.getElementById('deleteForm' + supplierId).submit();
    }
}

// Inicializar tooltips de Bootstrap
function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Funciones para modal de ayuda
function showHelpModal() {
    const modal = document.getElementById('helpModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-open');
    }
}

function closeHelpModal() {
    const modal = document.getElementById('helpModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        document.body.classList.remove('modal-open');
    }
}

// Cerrar modal de ayuda con tecla Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const helpModal = document.getElementById('helpModal');
        if (helpModal && (helpModal.style.display === 'block' || helpModal.style.display === 'flex')) {
            closeHelpModal();
        }
    }
});

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    const helpModal = document.getElementById('helpModal');
    if (event.target === helpModal) {
        closeHelpModal();
    }
});

// Función para filtrar insumos en el formulario
function filterInsumos(containerId, searchText) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const checkboxes = container.querySelectorAll('.form-check');
    const search = searchText.toLowerCase().trim();
    let visibleCount = 0;
    
    checkboxes.forEach(checkbox => {
        const label = checkbox.querySelector('.form-check-label');
        if (!label) return;
        
        const text = label.textContent.toLowerCase();
        
        if (search === '' || text.includes(search)) {
            checkbox.style.display = '';
            visibleCount++;
        } else {
            checkbox.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    let noResultsMsg = container.querySelector('.no-results-message');
    
    if (visibleCount === 0 && search !== '') {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.className = 'no-results-message text-center text-muted p-3';
            noResultsMsg.innerHTML = '<i class="fas fa-search"></i> No se encontraron insumos que coincidan con "' + searchText + '"';
            container.appendChild(noResultsMsg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    initTooltips();
    
    var filtrosCollapse = document.getElementById('filtrosCollapse');
    var cardHeader = document.getElementById('filtrosCardHeader');
    if (filtrosCollapse && cardHeader) {
        filtrosCollapse.addEventListener('show.bs.collapse', function() {
            cardHeader.classList.add('filtros-abiertos');
        });
        filtrosCollapse.addEventListener('hide.bs.collapse', function() {
            cardHeader.classList.remove('filtros-abiertos');
        });
        // Si el filtro inicia abierto, poner la clase
        if (filtrosCollapse.classList.contains('show')) {
            cardHeader.classList.add('filtros-abiertos');
        }
    }
});
</script>

<!-- Scripts de mejoras heurísticas -->
<script src="{{ asset('js/supplier-enhancements.js') }}"></script>

@endpush