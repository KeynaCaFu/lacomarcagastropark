@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="page-wrapper">
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-top: 15px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .form-title {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 8px 10px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-submit {
        flex: 1;
        padding: 12px 20px;
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(22, 163, 74, 0.25);
    }

    .btn-cancel {
        flex: 1;
        padding: 12px 20px;
        background: #e5e7eb;
        color: #374151;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: #d1d5db;
    }

    .required {
        color: #dc2626;
    }

    .info-box {
        background: #f0fdf4;
        border-left: 4px solid #16a34a;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 12px;
        color: #166534;
    }

    .password-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-field .form-input {
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
        padding: 8px;
        border-radius: 6px;
        border-left: 3px solid #16a34a;
        margin-top: 4px !important;
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

    @media (max-width: 991.98px) {
        .form-container {
            margin-top: 10px;
        }
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 20px;
            max-width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit, .btn-cancel {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .form-container {
            padding: 12px;
            border-radius: 8px;
        }

        .form-title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="form-container">
    <h1 class="form-title">Editar Usuario</h1>

    <div class="info-box">
        <i class="fas fa-info-circle"></i> Deja en blanco los campos de contraseña si deseas mantener la actual.
    </div>

    <form id="editUserFormPage" method="POST" action="{{ route('users.update', $user) }}" novalidate>
        @csrf
        @method('PUT')

        <!-- Nombre Completo -->
        <div class="form-group">
            <label for="full_name" class="form-label">
                Nombre Completo <span class="required">*</span>
            </label>
            <input 
                type="text" 
                id="full_name" 
                name="full_name" 
                class="form-input {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                value="{{ old('full_name', $user->full_name) }}"
                placeholder="Ej: Juan Pérez"
                required
                autofocus
            />
            @error('full_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">
                Correo Electrónico <span class="required">*</span>
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email', $user->email) }}"
                placeholder="correo@ejemplo.com"
                required
            />
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Teléfono y Rol -->
        <div class="form-row">
            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form-input"
                    value="{{ old('phone', $user->phone) }}"
                    placeholder="+34 600 000 000"
                />
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role_id" class="form-label">
                    Rol <span class="required">*</span>
                </label>
                <select 
                    id="role_id" 
                    name="role_id" 
                    class="form-select {{ $errors->has('role_id') ? 'is-invalid' : '' }}"
                    required
                >
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Contraseña y Confirmar -->
        <div class="form-row">
            <div class="form-group">
                <label for="password" class="form-label">
                    Nueva Contraseña
                </label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Mínimo 8 caracteres (dejar en blanco para no cambiar)"
                        autocomplete="new-password"
                    />
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityEdit('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-hint mt-1">
                    <p style="margin: 0 0 4px; font-size: 11px; color: #666;"><strong>Mínimo 8 caracteres recomendado</strong></p>
                    <p style="margin: 0; font-size: 11px; color: #666;">💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números</p>
                </div>
                <div class="password-strength mt-1">
                    <div class="strength-bar"><div class="strength-fill" id="strengthFillEdit"></div></div>
                    <p id="strengthTextEdit" style="margin: 4px 0 0; font-size: 12px; color: #999;"></p>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Confirmar Nueva Contraseña
                </label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input"
                        placeholder="Repite la contraseña"
                        autocomplete="new-password"
                    />
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityEdit('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="match-feedback mt-1" id="matchFeedbackEdit"></div>
            </div>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="status" class="form-label">
                Estado <span class="required">*</span>
            </label>
            <select 
                id="status" 
                name="status" 
                class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}"
                required
            >
                <option value="">Selecciona un estado</option>
                <option value="Active" {{ old('status', $user->status) == 'Active' ? 'selected' : '' }}>Activo</option>
                <option value="Inactive" {{ old('status', $user->status) == 'Inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Acciones -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="{{ route('users.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
<script>
// SweetAlert2 CDN guard
if (typeof Swal === 'undefined') {
    const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
    if (!existing) {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
        document.head.appendChild(s);
    }
}

function togglePasswordVisibilityEdit(fieldId) {
    const field = document.getElementById(fieldId);
    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    
    const button = event.target.closest('.btn-toggle-password');
    const icon = button.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

function validateEditPasswordStrength(password) {
    let strength = 0;
    
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

function setupEditPasswordValidation() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const strengthFill = document.getElementById('strengthFillEdit');
    const strengthText = document.getElementById('strengthTextEdit');
    const matchFeedback = document.getElementById('matchFeedbackEdit');
    
    if (!passwordField || !confirmField || !strengthFill || !strengthText || !matchFeedback) {
        return;
    }
    
    // Prevenir duplicar listeners
    if (passwordField.dataset._editValidationBound === 'true') return;
    passwordField.dataset._editValidationBound = 'true';
    
    passwordField.addEventListener('input', function() {
        const value = this.value;
        const result = validateEditPasswordStrength(value);
        const strengthContainer = this.closest('.form-group').querySelector('.password-strength');
        
        if (value.length > 0) {
            strengthContainer.classList.add('active');
            strengthFill.style.width = result.strength + '%';
            strengthFill.style.backgroundColor = result.color;
            strengthText.textContent = result.text;
            strengthText.style.color = result.color;
        } else {
            strengthContainer.classList.remove('active');
        }
        
        // Validar coincidencia
        if (confirmField && confirmField.value) {
            validateEditPasswordMatch();
        }
    });
    
    if (confirmField) {
        confirmField.addEventListener('input', function() {
            if (this.value || passwordField.value) {
                validateEditPasswordMatch();
            } else {
                matchFeedback.classList.remove('show');
            }
        });
    }
}

function validateEditPasswordMatch() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const matchFeedback = document.getElementById('matchFeedbackEdit');
    
    if (passwordField.value === confirmField.value && confirmField.value) {
        matchFeedback.classList.add('show', 'success');
        matchFeedback.classList.remove('error');
        matchFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
    } else if (confirmField.value) {
        matchFeedback.classList.add('show', 'error');
        matchFeedback.classList.remove('success');
        matchFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
    } else {
        matchFeedback.classList.remove('show');
    }
}

(function(){
    // SweetAlert2 CDN guard
    if (typeof Swal === 'undefined') {
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    }

    // Setup password validation
    setTimeout(() => setupEditPasswordValidation(), 100);

    function bindEditConfirm() {
        const form = document.getElementById('editUserFormPage');
        if (form && !form.dataset._editConfirmBound) {
            form.dataset._editConfirmBound = 'true';
            form.addEventListener('submit', async function(e){
                e.preventDefault();
                if (window.swConfirm) {
                    const res = await swConfirm({
                        title: 'Editar usuario',
                        text: '¿Desea guardar los cambios de este usuario?',
                        icon: 'question',
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    });
                    if (!res.isConfirmed) return;
                }
                
                // Mostrar notificación de guardando
                if (window.swAlert) {
                    swAlert({
                        title: 'Guardando cambios...',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            swAlert.showLoading();
                        }
                    });
                }
                
                // Enviar por AJAX
                const formData = new FormData(form);
                const userId = "{{ $user->user_id }}";
                
                try {
                    const response = await fetch(`/usuarios/${userId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    if (!response.ok) {
                        const data = await response.json();
                        throw data;
                    }
                    
                    // Éxito
                    if (window.swToast) {
                        swToast.fire({
                            icon: 'success',
                            title: 'Usuario actualizado correctamente',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                    
                    // Limpiar el formulario
                    form.reset();
                    
                    // Volver atrás sin recargar (solo actualiza la tabla)
                    setTimeout(() => {
                        window.history.back();
                    }, 2200);
                    
                } catch (error) {
                    // Cerrar el loading
                    swAlert.close();
                    
                    // Mostrar errores de validación
                    if (error.errors) {
                        const messages = Object.values(error.errors)
                            .flat()
                            .map(msg => `• ${msg}`)
                            .join('<br>');
                        
                        if (window.swAlert) {
                            swAlert({
                                icon: 'error',
                                title: 'Error de validación',
                                html: messages,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    } else {
                        if (window.swAlert) {
                            swAlert({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Ocurrió un error al guardar',
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    }
                }
            });
        }
    }

    const scriptEl = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
    if (typeof Swal === 'undefined' && scriptEl) {
        scriptEl.addEventListener('load', bindEditConfirm);
    } else {
        bindEditConfirm();
    }

    // Session success/error and validation SweetAlerts
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

</div>
@endsection
