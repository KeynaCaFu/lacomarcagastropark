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
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')" title="Eliminar usuario">
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
