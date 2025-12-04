<!-- Modal para Crear Usuario -->
<div class="modal-header">
    <h3 id="createUserTitle">
        <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
    </h3>
    <button type="button" class="close" aria-label="Cerrar" onclick="closeUserModal('userCreateModal')">&times;</button>
</div>

<div class="modal-body">
    <form id="createUserForm" method="POST" action="{{ route('users.store') }}" novalidate>
        @csrf

        <!-- Fila 1 -->
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="Ej: Juan Pérez" required>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Fila 2 -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="phone" class="form-control" placeholder="+34 600 000 000">
            </div>

            <div class="col-md-6">
                <label class="form-label">Rol <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}">{{ $role->role_type }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Fila 3 -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Estado -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Estado <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Active" selected>Activo</option>
                    <option value="Inactive">Inactivo</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

    </form>
</div>

<!-- Footer -->
<div class="modal-actions">
    <button type="button" class="btn btn-secondary" onclick="closeUserModal('userCreateModal')">
        <i class="fas fa-times"></i> Cancelar
    </button>

    <button type="submit" form="createUserForm" class="btn btn-primary btn-gradient">
        <i class="fas fa-save"></i> Crear Usuario
    </button>
</div>
