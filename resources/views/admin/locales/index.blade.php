@extends('layouts.app')

@section('title', 'Gestionar Locales — Administrador')

@push('styles')
    <style>
        .locales-container { 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 2px 8px rgba(0,0,0,.08); 
            padding: 24px; 
            margin-top: 50px; 
        }
        
        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .header-left h1 {
            color: #111827;
            font-weight: 700;
            font-size: 28px;
            margin: 0;
            margin-bottom: 4px;
        }
        
        .header-left p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .stat-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .stat-item-value {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }
        
        .stat-item-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .locales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .local-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .local-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
            border-color: #e18018;
        }
        
        .local-card-header {
            position: relative;
            height: 160px;
            background: linear-gradient(135deg, #e18018, #c9690f);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .local-card-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .local-card-header-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e18018, #c9690f);
            color: rgba(255,255,255,0.6);
            font-size: 48px;
        }
        
        .local-card-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        
        .local-card-status:hover {
            transform: scale(1.05);
        }
        
        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }
        
        .local-card-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .local-card-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }

        .local-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .local-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .icon-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            border: 2px solid #d1d5db;
            background: transparent;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .icon-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.06);
        }

        .icon-action-btn.delete {
            color: #dc2626;
            border-color: #dc2626;
            background: transparent;
        }

        .icon-action-btn.delete:hover {
            background: #dc2626;
            color: #ffffff;
        }

        .icon-action-btn.edit {
            color: #3e3d3a;
            border-color: #43423f;
            background: transparent;
        }

        .icon-action-btn.edit:hover {
            background: #848380ec;
            color: #000000;
        }
        
        .local-card-description {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 12px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .local-card-contact {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .local-card-managers {
            font-size: 12px;
            color: #6b7280;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }
        
        .managers-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .manager-badge {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-right: 6px;
            margin-bottom: 4px;
        }
        
        .no-locales {
            text-align: center;
            padding: 48px 24px;
            color: #6b7280;
        }
        
        .no-locales i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
            display: block;
        }
        
        .no-locales h4 {
            color: #374151;
            margin-bottom: 8px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .locales-container {
                margin-top: 10px;
                padding: 16px;
            }
            
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .locales-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }
        }
        
        @media (max-width: 767px) {
            .locales-container {
                padding: 12px;
                border-radius: 8px;
            }
            
            .header-left h1 {
                font-size: 22px;
            }
            
            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .locales-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .local-card-header {
                height: 120px;
            }
        }
    </style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="locales-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-left">
                <h1><i class="fas fa-store" style="color: #e18018; margin-right: 10px;"></i>Gestionar Locales</h1>
                <p>Visualiza y administra todos los locales registrados en el sistema</p>
            </div>
            <button type="button" id="btnOpenCreateLocal"
                style="display:inline-flex;align-items:center;gap:8px;background:#e18018;color:#fff;border:none;border-radius:10px;padding:10px 20px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s;">
                <i class="fas fa-plus"></i> Nuevo Local
            </button>
        </div>
        
        <!-- Statistics Summary -->
        <div class="stats-summary">
            <div class="stat-item">
                <div>
                    <div class="stat-item-value">{{ $locales->count() }}</div>
                    <div class="stat-item-label">Total de Locales</div>
                </div>
            </div>
            <div class="stat-item">
                <div>
                    <div class="stat-item-value">{{ $locales->where('status', 'Active')->count() }}</div>
                    <div class="stat-item-label">Activos</div>
                </div>
            </div>
            <div class="stat-item">
                <div>
                    <div class="stat-item-value">{{ $locales->where('status', 'Inactive')->count() }}</div>
                    <div class="stat-item-label">Inactivos</div>
                </div>
            </div>
        </div>
        
        <!-- Locales Grid -->
        @if($locales->count() > 0)
            <div class="locales-grid">
                @foreach($locales as $local)
                    <div class="local-card">
                        <!-- Card Header with Image -->
                        <div class="local-card-header">
                            @if($local->image_logo)
                                <img src="{{ asset($local->image_logo) }}" alt="{{ $local->name }}">
                            @else
                                <div class="local-card-header-placeholder">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Card Body -->
                        <div class="local-card-body">
                            <div class="local-card-head">
                                <h3 class="local-card-name" title="{{ $local->name }}">{{ $local->name }}</h3>
                                <div class="local-actions">
                                    <button type="button"
                                            class="icon-action-btn edit btn-edit-local"
                                            data-id="{{ $local->local_id }}"
                                            data-name="{{ $local->name }}"
                                            data-manager-id="{{ optional($local->users->first())->user_id }}"
                                            data-update-url="{{ route('locales.update', $local->local_id) }}"
                                            title="Editar local">
                                        <i class="fas fa-pen-to-square"></i>
                                    </button>
                                    <form id="del-local-{{ $local->local_id }}" method="POST" action="{{ route('locales.destroy', $local->local_id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="icon-action-btn delete btn-delete" data-id="{{ $local->local_id }}" data-name="{{ $local->name }}" title="Eliminar local">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="local-card-status status-toggler {{ $local->status === 'Active' ? 'status-active' : 'status-inactive' }}" 
                                 data-local-id="{{ $local->local_id }}"
                                 data-current-status="{{ $local->status }}"
                                 data-status-label="{{ $local->status === 'Active' ? 'Activo' : 'Inactivo' }}"
                                 title="Haz clic para cambiar el estado">
                                <i class="fas fa-{{ $local->status === 'Active' ? 'check-circle' : 'times-circle' }}"></i>
                                <span>{{ $local->status === 'Active' ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                            
                            @if($local->description)
                                <p class="local-card-description">{{ $local->description }}</p>
                            @endif
                            
                            @if($local->contact)
                                <p class="local-card-contact">
                                    <i class="fas fa-phone" style="color: #e18018;"></i>
                                    {{ $local->contact }}
                                </p>
                            @endif
                            
                            <!-- Managers Section -->
                            @if($local->users->count() > 0)
                                <div class="local-card-managers">
                                    <div class="managers-label">
                                        <i class="fas fa-users" style="color: #e18018; margin-right: 6px;"></i>
                                        Gerentes
                                    </div>
                                    <div>
                                        @foreach($local->users as $manager)
                                            <span class="manager-badge">{{ $manager->full_name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="local-card-managers">
                                    <div class="managers-label">
                                        <i class="fas fa-users" style="color: #d1d5db; margin-right: 6px;"></i>
                                    </div>
                                    <div style="color: #d1d5db; font-size: 12px;">
                                        Sin gerentes asignados
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="no-locales">
                <i class="fas fa-inbox"></i>
                <h4>No hay locales registrados</h4>
                <p style="margin: 0;">Aún no hay locales en el sistema. Los locales aparecerán aquí cuando sean creados.</p>
            </div>
        @endif
    </div>
</div>

@include('admin.locales.modal.modal')
@include('admin.locales.modal.edit-modal')

<script>
// ===== MODAL CREAR LOCAL =====
document.getElementById('btnOpenCreateLocal').addEventListener('click', function(){
    document.getElementById('modalCrearLocal').style.display = 'flex';
});
document.getElementById('btnCloseCreateLocal').addEventListener('click', function(){
    document.getElementById('modalCrearLocal').style.display = 'none';
});
document.getElementById('btnCloseCreateLocal2').addEventListener('click', function(){
    document.getElementById('modalCrearLocal').style.display = 'none';
});
document.getElementById('modalCrearLocal').addEventListener('click', function(e){
    if (e.target === this) this.style.display = 'none';
});

// Confirmar antes de crear local
(function(){
    const formCrearLocal = document.querySelector('#modalCrearLocal form');
    if (formCrearLocal && !formCrearLocal.dataset._createBound) {
        formCrearLocal.dataset._createBound = 'true';
        formCrearLocal.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (window.swConfirm) {
                const res = await swConfirm({
                    title: 'Crear local',
                    text: '¿Desea guardar este nuevo local?',
                    icon: 'question',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                });
                if (!res.isConfirmed) return;
            }
            this.submit();
        });
    }
})();

// ===== MODAL EDITAR LOCAL =====
const modalEditarLocal = document.getElementById('modalEditarLocal');
const formEditarLocal = document.getElementById('formEditarLocal');
const inputEditName = document.getElementById('edit_local_name');
const inputEditManager = document.getElementById('edit_manager_id');

document.querySelectorAll('.btn-edit-local').forEach(btn => {
    btn.addEventListener('click', function() {
        const localName = btn.dataset.name || '';
        const managerId = btn.dataset.managerId || '';
        const updateUrl = btn.dataset.updateUrl || '#';

        formEditarLocal.action = updateUrl;
        inputEditName.value = localName;
        inputEditManager.value = managerId;
        modalEditarLocal.style.display = 'flex';
    });
});

document.getElementById('btnCloseEditLocal').addEventListener('click', function(){
    modalEditarLocal.style.display = 'none';
});
document.getElementById('btnCloseEditLocal2').addEventListener('click', function(){
    modalEditarLocal.style.display = 'none';
});
modalEditarLocal.addEventListener('click', function(e){
    if (e.target === modalEditarLocal) modalEditarLocal.style.display = 'none';
});

// Confirmar antes de guardar cambios del local
formEditarLocal.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (window.Swal) {
        const res = await Swal.fire({
            title: 'Editar local',
            text: '¿Desea guardar los cambios de este local?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        });
        if (!res.isConfirmed) return;
    }
    this.submit();
});

// ===== MANEJADOR DE ELIMINAR LOCAL =====
(function(){
    document.querySelectorAll('.btn-delete[data-id]').forEach(btn => {
        if (btn.dataset._undoBound === 'true') return;
        btn.dataset._undoBound = 'true';
        btn.addEventListener('click', (e) => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || 'este local';
            const formId = 'del-local-' + id;
            const submitAction = () => { const f = document.getElementById(formId); if (f) f.submit(); };
            if (typeof window.confirmWithUndo === 'function') {
                const ask = window.swConfirm ? swConfirm({ title: 'Eliminar local', text: `¿Desea eliminar "${name}"?`, icon: 'warning', confirmButtonColor: '#dc2626', confirmButtonText: 'Sí, eliminar' }) : Promise.resolve({ isConfirmed: confirm(`¿Desea eliminar "${name}"?`) });
                ask.then(r => { if (r.isConfirmed) window.confirmWithUndo({ message: `Se eliminará: ${name}`, delayMs: 10000, onConfirm: submitAction, onUndo: function(){} }); });
            } else if (window.swConfirm) {
                swConfirm({ title: 'Eliminar local', text: `¿Desea eliminar "${name}"?`, icon: 'warning', confirmButtonColor: '#dc2626', confirmButtonText: 'Sí, eliminar' }).then(r => { if (r.isConfirmed) submitAction(); });
            } else if (confirm('¿Seguro que deseas eliminar este local?')) {
                submitAction();
            }
        });
    });
})();

// ===== MANEJADOR DE CAMBIO DE ESTADO =====
function reattachEventListeners() {
    document.querySelectorAll('.status-toggler').forEach(badge => {
        if (badge.dataset._statusBound === 'true') return;
        badge.dataset._statusBound = 'true';
        
        badge.addEventListener('click', async (e) => {
            e.stopPropagation();
            
            const localId = badge.dataset.localId;
            const currentStatus = badge.dataset.currentStatus;
            const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
            const newStatusLabel = newStatus === 'Active' ? 'Activo' : 'Inactivo';
            const currentStatusLabel = badge.dataset.statusLabel;
            
            if (window.swConfirm) {
                const result = await swConfirm({
                    title: 'Cambiar estado',
                    html: `¿Cambiar de <b>${currentStatusLabel}</b> a <b>${newStatusLabel}</b>?`,
                    icon: 'question',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                });
                
                if (!result.isConfirmed) return;
            } else {
                const ok = confirm(`¿Cambiar de ${currentStatusLabel} a ${newStatusLabel}?`);
                if (!ok) return;
            }
            
            badge.style.opacity = '0.5';
            badge.style.pointerEvents = 'none';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/locales/${localId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Error al actualizar el estado');
                }
                
                badge.dataset.currentStatus = newStatus;
                badge.dataset.statusLabel = newStatusLabel;
                
                if (newStatus === 'Active') {
                    badge.classList.remove('status-inactive');
                    badge.classList.add('status-active');
                    badge.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    badge.classList.remove('status-active');
                    badge.classList.add('status-inactive');
                    badge.querySelector('i').className = 'fas fa-times-circle';
                }
                
                badge.querySelector('span').textContent = newStatusLabel;
                
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                
                let retries = 0;
                const checkAndShowSuccess = () => {
                    if (window.swToast) {
                        swToast.fire({
                            icon: 'success',
                            title: `Estado actualizado a ${newStatusLabel}`
                        });
                    } else if (retries < 50) {
                        retries++;
                        setTimeout(checkAndShowSuccess, 100);
                    }
                };
                setTimeout(checkAndShowSuccess, 100);
                
            } catch (error) {
                console.error('Error:', error);
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                
                if (window.swAlert) {
                    swAlert({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo actualizar el estado',
                        confirmButtonColor: '#dc2626'
                    });
                } else {
                    alert(error.message || 'No se pudo actualizar el estado');
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    reattachEventListeners();
    
    const successMsg = @json(session('success'));
    if (successMsg) {
        let retries = 0;
        const checkAndShowSuccess = () => {
            if (window.swToast) {
                swToast.fire({ icon: 'success', title: successMsg });
            } else if (retries < 50) {
                retries++;
                setTimeout(checkAndShowSuccess, 100);
            }
        };
        setTimeout(checkAndShowSuccess, 100);
    }

    const errorMsg = @json(session('error'));
    if (errorMsg) {
        let retries = 0;
        const checkAndShowError = () => {
            if (window.swAlert) {
                swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
            } else if (retries < 50) {
                retries++;
                setTimeout(checkAndShowError, 100);
            }
        };
        setTimeout(checkAndShowError, 100);
    }
});
</script>
@endsection
