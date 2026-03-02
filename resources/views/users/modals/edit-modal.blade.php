<!-- Modal para Editar Usuario (parcial para AJAX) -->
<div class="modal-header">
    <h3 id="editUserTitle">
        <i class="fas fa-user-edit"></i> Editar Usuario
    </h3>
    <button type="button" class="close" aria-label="Cerrar" onclick="closeUserModal('userEditModal')">&times;</button>
</div>

<div class="modal-body">
    <form id="editUserForm" method="POST" novalidate data-user-id="{{ $user->user_id }}">
        @csrf
        @method('PUT')

        <div class="info-alert mb-3">
            <i class="fas fa-info-circle"></i>  
            Deja en blanco los campos de contraseña si deseas mantener la actual.
        </div>

        <!-- Fila 1 -->
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Fila 2 -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Rol <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}" {{ $user->role_id == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_type }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Fila 3 -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Nueva Contraseña</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="edit_modal_password" 
                        name="password" 
                        class="form-control password-input"
                        placeholder="Dejar en blanco para mantener la actual"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('edit_modal_password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-hint mt-2">
                    <p style="margin: 0 0 8px; font-size: 12px; color: #666;"><strong>Mínimo 8 caracteres recomendado</strong></p>
                    <p style="margin: 0; font-size: 12px; color: #666;">💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números</p>
                </div>
                <div class="password-strength mt-2">
                    <div class="strength-bar"><div class="strength-fill" id="editModalStrengthFill"></div></div>
                    <p id="editModalStrengthText" style="margin: 4px 0 0; font-size: 12px; color: #999;"></p>
                </div>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar Nueva Contraseña</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="edit_modal_password_confirmation" 
                        name="password_confirmation" 
                        class="form-control password-input"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('edit_modal_password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="match-feedback mt-2" id="editModalMatchFeedback"></div>
            </div>
        </div>

        <!-- Fila 4 -->
        <div class="row g-3 mt-1">
            <div class="col-md-6">
                <label class="form-label">Estado <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Active" {{ $user->status === 'Active' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactive" {{ $user->status === 'Inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>

    </form>
</div>

<!-- Footer -->
<div class="modal-actions">
    <button type="button" class="btn btn-secondary" onclick="closeUserModal('userEditModal')">
        <i class="fas fa-times"></i> Cancelar
    </button>
    <button type="submit" form="editUserForm" class="btn btn-primary btn-gradient">
        <i class="fas fa-save"></i> Guardar Cambios
    </button>
</div>

<script>
(function(){
    // SweetAlert2 CDN guard (partial may be loaded via AJAX)
    if (typeof Swal === 'undefined') {
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    }

    // Session success/error and validation SweetAlerts (in case rendered server-side)
    try {
        const successMsg = @json(session('success'));
        const errorMsg = @json(session('error'));
        const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
        
        // Handle success messages with retry logic
        if (successMsg) {
            let retries = 0;
            const checkAndShowToast = () => {
                if (window.swToast) {
                    swToast.fire({ 
                        icon: 'success', 
                        title: successMsg
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(checkAndShowToast, 100);
                }
            };
            setTimeout(checkAndShowToast, 100);
        }
        
        // Handle error messages with retry logic
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
        
        // Handle validation errors with retry logic
        if (hasErrors) {
            let retries = 0;
            const checkAndShowErrors = () => {
                if (window.swAlert) {
                    swAlert({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                        confirmButtonColor: '#dc2626'
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(checkAndShowErrors, 100);
                }
            };
            setTimeout(checkAndShowErrors, 100);
        }
    } catch(e) { /* noop */ }
})();
</script>

<style>
    /* Alerta */
    .info-alert {
        background: #f0fdf4;
        border-left: 4px solid #16a34a;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #166534;
    }

    .password-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-field .form-control {
        padding-right: 40px;
    }

    .btn-toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        transition: color 0.2s ease;
        width: auto;
    }

    .btn-toggle-password:hover {
        color: #666;
    }

    .password-hint {
        background: #f9fafb;
        padding: 10px;
        border-radius: 6px;
        border-left: 3px solid #16a34a;
    }

    .password-strength {
        display: none;
    }

    .password-strength.active {
        display: block;
    }

    .strength-bar {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    .match-feedback {
        font-size: 12px;
        display: none;
    }

    .match-feedback.show {
        display: block;
    }

    .match-feedback.success {
        color: #16a34a;
    }

    .match-feedback.error {
        color: #dc2626;
    }
</style>
