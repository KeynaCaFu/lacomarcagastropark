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

    .search-filter-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
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
        text-align: left;
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
        background: #dbeafe;
        color: #0c4a6e;
    }

    .btn-edit:hover {
        background: #bfdbfe;
    }

    .btn-delete {
        background: #fecaca;
        color: #7f1d1d;
    }

    .btn-delete:hover {
        background: #fca5a5;
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
        <button type="button" class="btn-create" onclick="openUserCreateModal()">
            <i class="fas fa-plus"></i> Agregar Usuario
        </button>
    </div>

    <!-- Búsqueda y filtros -->
    <form method="GET" action="{{ route('users.index') }}" class="search-filter-group">
        <input 
            type="text" 
            name="q" 
            class="search-input" 
            placeholder="Buscar por nombre o email..." 
            value="{{ request('q') }}"
            style="flex: 1; min-width: 200px;"
        />

        <select name="role" class="filter-select">
            <option value="">Todos los roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->role_id }}" {{ request('role') == $role->role_id ? 'selected' : '' }}>
                    {{ $role->role_type }}
                </option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="">Todos los estados</option>
            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Activo</option>
            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactivo</option>
        </select>

        <select name="per_page" class="filter-select" onchange="this.form.submit()">
            <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5 por página</option>
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 por página</option>
            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 por página</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 por página</option>
        </select>

        <button type="submit" class="btn-action" style="background: #16a34a; color: white; border: none; padding: 10px 20px;">
            <i class="fas fa-search"></i> Buscar
        </button>

        @if(request('q') || request('role') || request('status'))
            <a href="{{ route('users.index') }}" class="btn-action" style="background: #e5e7eb; color: #374151; padding: 10px 20px;">
                <i class="fas fa-redo"></i> Limpiar
            </a>
        @endif
    </form>

    <!-- Tabla de usuarios -->
    <div class="table-wrapper">
        @if($users->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->full_name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                <span class="role-badge">
                                    {{ $user->role->role_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($user->status) }}">
                                    {{ $user->status === 'Active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div class="actions" style="justify-content: center;">
                                    <button 
                                        type="button" 
                                        class="btn-action btn-edit"
                                        onclick="openUserEditModal({{ $user->user_id }})"
                                        title="Editar usuario"
                                    >
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')" title="Eliminar usuario">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación -->
            @if($users->hasPages())
                <div class="row mt-4">
                    <div class="col-md-12 d-flex justify-content-between align-items-center">
                        <div style="color: #6b7280; font-size: 14px;">
                            Mostrando <strong>{{ $users->firstItem() }}</strong> a <strong>{{ $users->lastItem() }}</strong> de <strong>{{ $users->total() }}</strong> usuarios
                        </div>
                        <div class="pagination-container">
                            {{ $users->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
                <p>No hay usuarios que mostrar</p>
            </div>
        @endif
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

@endsection

@push('scripts')
<script src="{{ asset('js/user-modals.js') }}"></script>
<script>
    // Manejo de envío de formulario de creación de usuario
    document.addEventListener('DOMContentLoaded', function() {
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
                    window.location.reload();
                } catch(error) {
                    window.userModals.handleValidationErrors(error, this);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
</script>
@endpush
