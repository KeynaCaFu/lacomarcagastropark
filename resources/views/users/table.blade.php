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
                        <span class="status-badge status-toggler {{ $user->status === 'Active' ? 'status-active' : 'status-inactive' }}" 
                              data-user-id="{{ $user->user_id }}"
                              data-current-status="{{ $user->status }}"
                              style="cursor: pointer; transition: all 0.3s ease;"
                              title="Haz clic para cambiar el estado"
                              @if($user->status === 'Active')
                                  data-status-label="Activo"
                              @else
                                  data-status-label="Inactivo"
                              @endif>
                            @if($user->status === 'Active')
                                <span class="status-text" style="margin-right: 6px;">Activo</span>
                                <i class="fas fa-check-circle" style="opacity: 0.8;"></i>
                            @else
                                <span class="status-text" style="margin-right: 6px;">Inactivo</span>
                                <i class="fas fa-times-circle" style="opacity: 0.8;"></i>
                            @endif
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
                                <i class="fas fa-edit"></i>
                            </button>
                            <form id="del-user-{{ $user->user_id }}" action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-delete" data-id="{{ $user->user_id }}" data-name="{{ $user->full_name }}" title="Eliminar usuario">
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
    @if($users->hasPages())
        <div class="pagination-wrapper" style="margin-top: 16px; display: flex; justify-content: center;">
            <div class="pagination-container">
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
        <div style="text-align:center; color:#6b7280; font-size: 13px; margin-top:8px;">
            Mostrando <strong>{{ $users->firstItem() }}</strong> a <strong>{{ $users->lastItem() }}</strong> de <strong>{{ $users->total() }}</strong> usuarios
        </div>
    @endif
@else
    <div class="empty-state">
        <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
        <p>No hay usuarios que mostrar</p>
    </div>
@endif

<script>
// Toggle user status
(function(){
    document.querySelectorAll('.status-toggler').forEach(badge => {
        if (badge.dataset._statusBound === 'true') return;
        badge.dataset._statusBound = 'true';
        
        badge.addEventListener('click', async (e) => {
            const userId = badge.dataset.userId;
            const currentStatus = badge.dataset.currentStatus;
            const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
            const newStatusLabel = newStatus === 'Active' ? 'Activo' : 'Inactivo';
            const currentStatusLabel = badge.dataset.statusLabel;
            
            // Show confirmation
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
            
            // Disable badge while updating
            badge.style.opacity = '0.5';
            badge.style.pointerEvents = 'none';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/usuarios/${userId}`, {
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
                
                // Update badge visually
                badge.dataset.currentStatus = newStatus;
                badge.dataset.statusLabel = newStatusLabel;
                
                if (newStatus === 'Active') {
                    badge.classList.remove('status-inactive');
                    badge.classList.add('status-active');
                    badge.innerHTML = `<span class="status-text" style="margin-right: 6px;">Activo</span><i class="fas fa-check-circle" style="opacity: 0.8;"></i>`;
                } else {
                    badge.classList.remove('status-active');
                    badge.classList.add('status-inactive');
                    badge.innerHTML = `<span class="status-text" style="margin-right: 6px;">Inactivo</span><i class="fas fa-times-circle" style="opacity: 0.8;"></i>`;
                }
                
                // Restore opacity
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                
                // Show success toast
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
})();

// Bind delete with Undo for Users table
(function(){
    document.querySelectorAll('.btn-delete[data-id]').forEach(btn => {
        if (btn.dataset._undoBound === 'true') return;
        btn.dataset._undoBound = 'true';
        btn.addEventListener('click', (e) => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || 'este usuario';
            const formId = 'del-user-' + id;
            const submitAction = () => { const f = document.getElementById(formId); if (f) f.submit(); };
            if (typeof window.confirmWithUndo === 'function') {
                const ask = window.swConfirm ? swConfirm({ html: `<div class='swal-title-like'>¿Seguro que deseas eliminar <b>${name}</b>?</div>`, confirmButtonText: 'Sí, eliminar' }) : Promise.resolve({ isConfirmed: confirm('¿Seguro que deseas eliminar este usuario?') });
                ask.then(r => { if (r.isConfirmed) window.confirmWithUndo({ message: `Se eliminará: ${name}`, delayMs: 10000, onConfirm: submitAction, onUndo: function(){} }); });
            } else if (window.swConfirm) {
                swConfirm({ html: `<div class='swal-title-like'>¿Seguro que deseas eliminar <b>${name}</b>?</div>`, confirmButtonText: 'Sí, eliminar' }).then(r => { if (r.isConfirmed) submitAction(); });
            } else if (confirm('¿Seguro que deseas eliminar este usuario?')) {
                submitAction();
            }
        });
    });
})();
</script>
