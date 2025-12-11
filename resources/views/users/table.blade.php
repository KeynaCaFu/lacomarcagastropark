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
