@extends('layouts.app')

@section('title', 'Gestionar Usuarios')

@push('styles')
    <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
@endpush

@section('content')
<style>
   
    .users-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-top: 20px;
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
    /* Contenedor para ubicar el botón de Ayuda fuera del card */
    .top-help {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding: 0;
        margin: 0;
        position: relative;
        z-index: 1100;
    }

    .search-filter-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    /* Accordion simple para filtros */
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
    #filtersBody.closed {
        display: none;
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
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(13, 94, 42, 0.3);
    }

    /* Botón Ayuda colocado arriba del botón verde */
    .btn-help {
       background: linear-gradient(135deg, #4e6657, #3d5144);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    .btn-help:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(13, 94, 42, 0.3);
    }
    .btn-help:active {
        transform: translateY(0);
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

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        background: #ede9fe;
        color: #6d28d9;
    }

    .actions {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-edit {
        background: transparent;
        color: #3e3d3a;
        border: 2px solid #43423f !important;
    }

    .btn-edit:hover {
        background: #848380ec;
        color: #000;
    }

    .btn-delete {
        background: transparent;
        color: #dc2626;
        border: 2px solid #dc2626 !important;
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
        text-align: center;           /* centrar número */
        min-width: 36px;              /* ancho mínimo para centrar bien */
        display: inline-flex;         /* centrar vertical y horizontal */
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

<div class="users-container">
    <div class="header-section">
        <h2 style="margin: 0; color: #1f2937; font-weight: 700;">Usuarios del Sistema</h2>
        <div class="header-actions">
            <button type="button" class="btn-create" onclick="openUserCreateModal()">
                <i class="fas fa-plus"></i> Agregar Usuario
            </button>

          
        </div>
         
    </div>

   

    <!-- Filtros en acordeón -->
    <div class="filters-accordion">
        <button type="button" id="filtersToggle" class="filters-toggle" aria-expanded="false" aria-controls="filtersBody">
            <i class="fas fa-chevron-down"></i>
            Filtros de usuarios
        </button>
        <div id="filtersBody" class="search-filter-group closed" role="region" aria-labelledby="filtersToggle">
        <input 
            type="text" 
            id="searchInput"
            class="search-input" 
            placeholder="Buscar por nombre o email..." 
            value="{{ request('q') }}"
            style="flex: 1; min-width: 200px;"
        />

        <select id="roleFilter" class="filter-select">
            <option value="">Todos los roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->role_id }}" {{ request('role') == $role->role_id ? 'selected' : '' }}>
                    {{ $role->role_type }}
                </option>
            @endforeach
        </select>

        <select id="statusFilter" class="filter-select">
            <option value="">Todos los estados</option>
            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Activo</option>
            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactivo</option>
        </select>

        <select id="perPageFilter" class="filter-select">
            <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5 por página</option>
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 por página</option>
            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 por página</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 por página</option>
        </select>

        <button type="button" id="searchBtn" class="btn-action" style="background: #16a34a; color: white; border: none; padding: 10px 20px;">
            <i class="fas fa-search"></i> Buscar Usuarios
        </button>

        <a href="javascript:void(0);" id="clearBtn" class="btn-action" style="background: #e5e7eb; color: #374151; padding: 10px 20px; display: none;">
            <i class="fas fa-redo"></i> Limpiar
        </a>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div id="usersTableContainer" class="table-wrapper">
        @include('users.table', ['users' => $users])
    </div>
</div>

<!-- Contenedores para modales custom -->
<div id="userCreateModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createUserTitle">
        @include('users.modals.create')
    </div>
</div>

<div id="userEditModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editUserTitle">
        <div id="editUserContent"></div>
    </div>
</div>

<!-- Modal de Ayuda -->
<div id="helpModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpTitle">
        <div class="modal-header">
            <h3 id="helpTitle"><i class="fas fa-question-circle"></i> Ayuda de Usuarios</h3>
            <button type="button" class="close" aria-label="Cerrar" onclick="closeHelpModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <h5>¿Cómo buscar usuarios?</h5>
                <p>
                    Usa el acordeón de filtros para buscar por nombre o email, filtrar por rol y estado.
                    Pulsa "Buscar Usuarios" para actualizar la tabla sin recargar la página.
                </p>
            </div>
            <div class="detail-section">
                <h5>¿Cómo crear, editar y eliminar?</h5>
                <p>
                    - "Agregar Usuario" abre el formulario de creación.<br>
                    - El ícono de lápiz permite editar un usuario.<br>
                    - El ícono de papelera elimina el usuario tras confirmar.
                </p>
            </div>
            <div class="detail-section">
                <h5>Paginación</h5>
                <p>
                    Los números de página están centrados bajo la tabla. Al hacer clic se actualiza la lista vía AJAX.
                </p>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeHelpModal()">Cerrar</button>
        </div>
    </div>
    </div>

@endsection

<!-- Botón de Ayuda -->
<div id="helpButtonContainer" style="display: none;">
    <button id="helpButtonTop" type="button" class="btn-help">
        <i class="fas fa-question-circle"></i> Ayuda
    </button>
</div>

@push('scripts')
<script>
    // Mover el botón de ayuda al header
    document.addEventListener('DOMContentLoaded', function() {
        const helpContainer = document.getElementById('topHelpContainer');
        const helpButtonContainer = document.getElementById('helpButtonContainer');
        const helpButton = document.getElementById('helpButtonTop');
        
        if (helpContainer && helpButtonContainer && helpButton) {
            helpContainer.appendChild(helpButton);
            helpButtonContainer.style.display = 'none';
        }
    });
</script>
@endpush

@push('scripts')
<script src="{{ asset('js/user-modals.js') }}"></script>
<script>
    let currentPage = 1;

    function loadUsers(page = 1) {
        const q = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        const per_page = document.getElementById('perPageFilter').value;

        const params = new URLSearchParams();
        if(q) params.append('q', q);
        if(role) params.append('role', role);
        if(status) params.append('status', status);
        params.append('per_page', per_page);
        params.append('page', page);

        // Mostrar botón limpiar si hay filtros activos
        const clearBtn = document.getElementById('clearBtn');
        if(q || role || status) {
            clearBtn.style.display = 'inline-flex';
        } else {
            clearBtn.style.display = 'none';
        }

        fetch(`{{ route('users.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('usersTableContainer').innerHTML = html;
            currentPage = page;
            
            // Re-bind pagination links
            bindPaginationLinks();
        })
        .catch(error => console.error('Error:', error));
    }

    function bindPaginationLinks() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                if(page) loadUsers(page);
            });
        });
    }

    // Funciones para manejar el modal de ayuda
    function openHelpModal() {
        const helpModal = document.getElementById('helpModal');
        if (helpModal) {
            helpModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeHelpModal() {
        const helpModal = document.getElementById('helpModal');
        if (helpModal) {
            helpModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const filtersToggle = document.getElementById('filtersToggle');
        const filtersBody = document.getElementById('filtersBody');
        const clearBtn = document.getElementById('clearBtn');
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        const perPageFilter = document.getElementById('perPageFilter');
        const helpButtonTop = document.getElementById('helpButtonTop');

        // Toggle acordeón
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

        // Al hacer clic en Buscar: si los filtros están ocultos, primero abrir acordeón
        searchBtn.addEventListener('click', () => {
            if (filtersBody && filtersBody.classList.contains('closed')) {
                filtersBody.classList.remove('closed');
                filtersToggle.classList.add('open');
                filtersToggle.setAttribute('aria-expanded', 'true');
                return; // no buscar hasta que el usuario vea los filtros
            }
            loadUsers(1);
        });
        
        searchInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') {
                loadUsers(1);
            }
        });

        roleFilter.addEventListener('change', () => loadUsers(1));
        statusFilter.addEventListener('change', () => loadUsers(1));
        perPageFilter.addEventListener('change', () => loadUsers(1));

        clearBtn.addEventListener('click', () => {
            document.getElementById('searchInput').value = '';
            document.getElementById('roleFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('perPageFilter').value = '5';
            loadUsers(1);
        });

        // Manejo de envío de formulario de creación de usuario
        const createForm = document.getElementById('createUserForm');
        if(createForm && !createForm.dataset._createBound) {
            createForm.dataset._createBound = 'true';
            
            createForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('button[type="submit"]');
                if(submitBtn.disabled) return;
                
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
                
                const formData = new FormData(this);
                
                try {
                    const res = await fetch("{{ route('users.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    if(!res.ok) {
                        const data = await res.json();
                        throw data;
                    }
                    
                    window.userModals.closeModal('userCreateModal');
                    loadUsers(1);
                } catch(error) {
                    window.userModals.handleValidationErrors(error, this);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        bindPaginationLinks();

        // Event listener para el botón de ayuda
        if (helpButtonTop) {
            helpButtonTop.addEventListener('click', function() {
                openHelpModal();
            });
        }
    });
</script>
@endpush
