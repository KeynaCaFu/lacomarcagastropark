<!-- Modal para Crear Usuario -->
<div style="display: flex; flex-direction: column; height: 100%; max-height: calc(100vh - 120px); min-height: 400px;">
    <div class="modal-header" style="background: #faf9f6; padding: 18px 24px; border-bottom: 3px solid #ff9900; flex-shrink: 0;">
        <h3 id="createUserTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: #1f2937;">
            <i class="fas fa-user-plus" style="color: #ff9900; margin-right: 10px;"></i>Crear Nuevo Usuario
        </h3>
        <button type="button" class="close" aria-label="Cerrar" onclick="closeUserModal('userCreateModal')" style="position: absolute; right: 20px; top: 18px; font-size: 24px; border: none; background: none; cursor: pointer; color: #999; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">&times;</button>
    </div>

    <div class="modal-body" style="padding: 24px; background: #ffffff; flex: 1; overflow-y: auto;">
    <form id="createUserForm" method="POST" action="{{ route('users.store') }}" novalidate enctype="multipart/form-data">
        @csrf

        <!-- Fila 1 -->
        <div class="row g-3" style="margin-bottom: 8px;">
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
        <div class="row g-3" style="margin-bottom: 8px; margin-top: 0;">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="phone" class="form-control" placeholder="Ej: 6700-1100">
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
        <div class="row g-3" style="margin-bottom: 8px; margin-top: 0;">
            <div class="col-md-6">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <div class="password-field">
                    <input type="password" id="createPassword" name="password" class="form-control password-input" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required>
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('createPassword')">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="password-hint mt-2">
                    <p style="margin: 0 0 8px; font-size: 12px; color: #666;"><strong>Mínimo 8 caracteres recomendado</strong></p>
                    <p style="margin: 0; font-size: 12px; color: #666;">💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números</p>
                </div>
                <div class="password-strength mt-2">
                    <div class="strength-bar"><div class="strength-fill" id="createStrengthFill"></div></div>
                    <p id="createStrengthText" style="margin: 4px 0 0; font-size: 12px; color: #999;"></p>
                </div>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                <div class="password-field">
                    <input type="password" id="createPasswordConfirm" name="password_confirmation" class="form-control password-input" placeholder="Repite la contraseña" autocomplete="new-password" required>
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('createPasswordConfirm')">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="match-feedback mt-2" id="createMatchFeedback"></div>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Fila 4: Estado y Foto de Perfil -->
        <div class="row g-3" style="margin-bottom: 0; margin-top: 0;">
            <div class="col-md-6">
                <label class="form-label">Estado <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="">Selecciona un estado</option>
                    <option value="Active" selected>Activo</option>
                    <option value="Inactive">Inactivo</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto de Perfil (Avatar)</label>
                <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif,image/jpg">
                <small class="text-muted d-block mt-1">JPG, PNG o GIF. Máximo 2MB</small>
                <div class="invalid-feedback"></div>
            </div>
        </div>

    </form>
    </div>

    <!-- Footer -->
    <div class="modal-actions" style="background: #fafafa; padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end; flex-shrink: 0;">
        <button type="button" class="btn btn-secondary" onclick="closeUserModal('userCreateModal')" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
            <i class="fas fa-times" style="margin-right: 6px;"></i> Cancelar
        </button>

        <button type="submit" form="createUserForm" class="btn btn-primary btn-gradient" style="padding: 10px 24px; background: linear-gradient(135deg, #ff9900, #ff7700); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(255,153,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <i class="fas fa-save" style="margin-right: 6px;"></i> Crear Usuario
        </button>
    </div>
</div>

<style>
/* Responsive para pantallas pequeñas */
@media (max-width: 768px) {
    .col-md-6 {
        width: 100% !important;
    }
    .modal-body {
        padding: 16px !important;
    }
    .modal-header, .modal-actions {
        padding: 12px 16px !important;
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
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    
    const button = event.target.closest('.btn-toggle-password');
    const icon = button.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

function validatePasswordStrength(password) {
    let strength = 0;
    const feedback = [];
    
    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    
    if (strength === 0) return { strength: 0, text: '', color: '' };
    if (strength <= 25) return { strength: 25, text: 'Muy débil', color: '#dc2626' };
    if (strength <= 50) return { strength: 50, text: 'Débil', color: '#f97316' };
    if (strength <= 75) return { strength: 75, text: 'Buena', color: '#eab308' };
    return { strength: 100, text: 'Fuerte', color: '#16a34a' };
}

function setupPasswordValidation(passwordId, confirmId, strengthFillId, strengthTextId, matchFeedbackId) {
    const passwordField = document.getElementById(passwordId);
    const confirmField = document.getElementById(confirmId);
    const strengthFill = document.getElementById(strengthFillId);
    const strengthText = document.getElementById(strengthTextId);
    const matchFeedback = document.getElementById(matchFeedbackId);
    
    if (!passwordField) return;
    
    passwordField.addEventListener('input', function() {
        const value = this.value;
        const result = validatePasswordStrength(value);
        
        if (value.length > 0) {
            document.querySelector('#' + passwordId).closest('.col-md-6').querySelector('.password-strength').classList.add('active');
            strengthFill.style.width = result.strength + '%';
            strengthFill.style.backgroundColor = result.color;
            strengthText.textContent = result.text;
            strengthText.style.color = result.color;
        } else {
            document.querySelector('#' + passwordId).closest('.col-md-6').querySelector('.password-strength').classList.remove('active');
        }
        
        // Validar coincidencia
        if (confirmField && confirmField.value) {
            validatePasswordMatch(passwordId, confirmId, matchFeedbackId);
        }
    });
    
    if (confirmField) {
        confirmField.addEventListener('input', function() {
            if (this.value) {
                validatePasswordMatch(passwordId, confirmId, matchFeedbackId);
            } else {
                matchFeedback.classList.remove('show');
            }
        });
    }
}

function validatePasswordMatch(passwordId, confirmId, feedbackId) {
    const passwordField = document.getElementById(passwordId);
    const confirmField = document.getElementById(confirmId);
    const feedback = document.getElementById(feedbackId);
    
    if (passwordField.value === confirmField.value) {
        feedback.classList.add('show', 'success');
        feedback.classList.remove('error');
        feedback.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
    } else {
        feedback.classList.add('show', 'error');
        feedback.classList.remove('success');
        feedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
    }
}

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

    // Setup password validation
    setupPasswordValidation('createPassword', 'createPasswordConfirm', 'createStrengthFill', 'createStrengthText', 'createMatchFeedback');

    // Note: submit handler is in users/index.blade.php (AJAX-based)
    // Do not add duplicate handler here

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
