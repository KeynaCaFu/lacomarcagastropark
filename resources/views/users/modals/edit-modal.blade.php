<!-- Modal para Editar Usuario (parcial para AJAX) -->
<div style="display: flex; flex-direction: column; height: 100%; max-height: calc(100vh - 120px); min-height: 400px;">
    <div class="modal-header" style="background: #faf9f6; padding: 18px 24px; border-bottom: 3px solid #ff9900; flex-shrink: 0;">
        <h3 id="editUserTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">
            <i class="fas fa-user-edit" style="color: #ff9900; margin-right: 10px;"></i>Editar Usuario
        </h3>
        <button type="button" class="close" aria-label="Cerrar" onclick="closeUserModal('userEditModal')" style="position: absolute; right: 20px; top: 18px; font-size: 24px; border: none; background: none; cursor: pointer; color: #999; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">&times;</button>
    </div>

    <div class="modal-body" style="padding: 24px; background: #ffffff; flex: 1; overflow-y: auto;">
    <form id="editUserForm" method="POST" novalidate enctype="multipart/form-data" data-user-id="{{ $user->user_id }}">
        @csrf
        @method('PUT')

        <div class="info-alert mb-3" style="background: #fffaf0; border-left: 3px solid #ff9900; padding: 12px; border-radius: 6px; font-size: 13px; color: #92400e; margin-bottom: 20px;">
            <i class="fas fa-info-circle"></i>  
            Deja en blanco los campos de contraseña si deseas mantener la actual.
        </div>

        <!-- Fila 1 -->
        <div class="row g-3" style="margin-bottom: 8px;">
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
        <div class="row g-3" style="margin-bottom: 8px; margin-top: 0;">
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
        <div class="row g-3" style="margin-bottom: 8px; margin-top: 0;">
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
                        <i class="fas fa-eye-slash"></i>
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
                        placeholder="Repite la nueva contraseña"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('edit_modal_password_confirmation')">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="match-feedback mt-2" id="editModalMatchFeedback"></div>
            </div>
        </div>

        <!-- Fila 4: Estado y Foto de Perfil -->
        <div class="row g-3" style="margin-bottom: 0; margin-top: 0;">
            <div class="col-md-6">
                <label class="form-label">Estado <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Active" {{ $user->status === 'Active' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactive" {{ $user->status === 'Inactive' ? 'selected' : '' }}>Inactivo</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto de Perfil (Avatar)</label>
                <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif,image/jpg" id="avatarInput">
                <small class="text-muted d-block mt-1">JPG, PNG o GIF. Máximo 2MB</small>
                <div class="invalid-feedback"></div>
            </div>
        </div>

    </form>
    </div>

    <!-- Footer -->
    <div class="modal-actions" style="background: #fafafa; padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end; flex-shrink: 0;">
        <button type="button" class="btn btn-secondary" onclick="closeUserModal('userEditModal')" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
            <i class="fas fa-times" style="margin-right: 6px;"></i> Cancelar
        </button>
        <button type="submit" form="editUserForm" class="btn btn-primary btn-gradient" style="padding: 0px 0px; background: linear-gradient(135deg, #ff9900, #ff7700); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(255,153,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <i class="fas fa-save" style="margin-right: 6px;"></i> Guardar Cambios
        </button>
    </div>
</div>

<style>
/* Responsive para pantallas pequeñas */
@media (max-width: 768px) {
    .col-md-6 {
        width: 100% !important;
    }
}

@media (max-width: 480px) {
    .form-label {
        font-size: 12px !important;
    }
    .form-control, .form-select {
        padding: 8px 10px !important;
        font-size: 13px !important;
    }
    .password-hint {
        padding: 10px !important;
    }
}

.password-field {
    position: relative;
    display: flex;
    align-items: center;
}

.password-field .form-control {
    padding-right: 72px;
}

/* Evita que el icono de validacion se monte sobre el boton de mostrar contrasena */
.password-field .form-control.is-invalid,
.password-field .form-control.is-valid {
    padding-right: 72px;
    background-position: right 44px center;
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
}

.btn-toggle-password:hover {
    color: #ff9900;
}

.password-hint {
    background: #fffaf0;
    padding: 12px;
    border-radius: 6px;
    border-left: 3px solid #ff9900;
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

.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 13px;
    margin-bottom: 6px;
    display: block;
}

.form-control, .form-select {
    border: 1.5px solid #e5e7eb;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #ff9900;
    box-shadow: 0 0 0 3px rgba(255,153,0,0.15);
}

.text-danger {
    color: #ef4444;
}
</style>

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
        background: #fffaf0;
        border-left: 3px solid #ff9900;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #92400e;
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

<script>
// Manejar vista previa de avatar
document.addEventListener('DOMContentLoaded', function(){
    const avatarInput = document.getElementById('avatarInput');
    if(avatarInput){
        avatarInput.addEventListener('change', function(e){
            if(this.files && this.files[0]){
                const reader = new FileReader();
                reader.onload = function(event){
                    let preview = document.getElementById('avatarPreview');
                    if(!preview){
                        const container = avatarInput.closest('.col-md-6');
                        preview = document.createElement('img');
                        preview.id = 'avatarPreview';
                        preview.style.cssText = 'width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 2px solid #e5e7eb; margin-top: 10px;';
                        container.appendChild(preview);
                    }
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
