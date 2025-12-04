<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-elevated">

            <!-- Header -->
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="editUserForm" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="info-alert mb-3">
                        <i class="fas fa-info-circle"></i>  
                        Deja en blanco los campos de contraseña si deseas mantener la actual.
                    </div>

                    <!-- Fila 1 -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Fila 2 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" id="edit_phone" name="phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select id="edit_role_id" name="role_id" class="form-select" required>
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Fila 3 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Nueva Contraseña</label>
                            <input 
                                type="password" 
                                id="edit_password" 
                                name="password" 
                                class="form-control"
                                placeholder="Dejar en blanco para mantener la actual"
                            >
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input 
                                type="password" 
                                id="edit_password_confirmation" 
                                name="password_confirmation" 
                                class="form-control"
                            >
                        </div>
                    </div>

                    <!-- Fila 4 -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="">Selecciona un estado</option>
                                <option value="Active">Activo</option>
                                <option value="Inactive">Inactivo</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-gradient">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
    /* Asegurar superposición correcta */
    #editUserModal { z-index: 9999 !important; }
    .modal-backdrop.show { z-index: 9998 !important; }

    /* Card del modal */
    .modal-elevated {
        border-radius: 14px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.25);
    }

    /* Header elegante */
    .modal-header-custom {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-bottom: 1px solid #e5e7eb;
        border-radius: 14px 14px 0 0;
    }

    /* Inputs */
    .form-control,
    .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        transition: 0.25s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22,163,74,0.2);
    }

    /* Botón degradado */
    .btn-gradient {
        background: linear-gradient(135deg, #485a1a, #0d5e2a) !important;
        border: none;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #3e5018, #093e1c) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(10,94,42,0.35);
    }

    /* Alerta */
    .info-alert {
        background: #f0fdf4;
        border-left: 4px solid #16a34a;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #166534;
    }
</style>
