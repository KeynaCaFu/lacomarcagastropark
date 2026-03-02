<!-- Modal para Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 600px;">
        <div class="modal-content modal-elevated" style="display: flex; flex-direction: column; max-height: calc(100vh - 60px); height: auto;">

            <!-- Header -->
            <div class="modal-header modal-header-custom" style="background: #faf9f6; border-bottom: 3px solid #ff9900; flex-shrink: 0;">
                <h5 class="modal-title" style="font-weight: 600; color: #1f2937; margin: 0; font-size: 18px;">
                    <i class="fas fa-user-edit" style="color: #ff9900; margin-right: 10px;"></i>Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: opacity(0.6); transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'"></button>
            </div>

            <!-- Form -->
            <form id="editUserForm" method="POST" novalidate style="display: flex; flex-direction: column; height: 100%; min-height: 0;">
                @csrf
                @method('PUT')

                <div class="modal-body" style="padding: 20px; flex: 1; overflow-y: auto; min-height: 0;">

                    <div class="info-alert mb-3" style="background: #fffaf0; border-left: 3px solid #ff9900; padding: 10px; border-radius: 6px; font-size: 12px; color: #92400e; margin-bottom: 15px;">
                        <i class="fas fa-info-circle" style="color: #ff9900; margin-right: 8px;\"></i>  
                        Deja en blanco los campos de contraseña si deseas mantener la actual.
                    </div>

                    <!-- Fila 1 -->
                    <div class="row g-2" style="margin-bottom: 6px;">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" id="edit_full_name" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" id="edit_email" name="email" class="form-control" value="{{ $user->email }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Fila 2 -->
                    <div class="row g-2" style="margin-bottom: 6px; margin-top: 0;">
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" id="edit_phone" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Rol <span class="text-danger">*</span></label>
                            <select id="edit_role_id" name="role_id" class="form-select" required>
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ $user->role_id == $role->role_id ? 'selected' : '' }}>{{ $role->role_type }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Fila 3 -->
                    <div class="row g-2" style="margin-bottom: 6px; margin-top: 0;">
                        <div class="col-md-6">
                            <label class="form-label">Nueva Contraseña</label>
                            <div class="password-field">
                                <input 
                                    type="password" 
                                    id="edit_password" 
                                    name="password" 
                                    class="form-control password-input"
                                    placeholder="Dejar en blanco para mantener la actual"
                                    autocomplete="new-password"
                                >
                                <button type="button" class="btn-toggle-password" onclick="toggleModalPasswordVisibility('edit_password')">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="password-hint mt-1">
                                <p style="margin: 0 0 4px; font-size: 11px; color: #666;"><strong>Mínimo 8 caracteres recomendado</strong></p>
                                <p style="margin: 0; font-size: 11px; color: #666;">💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números</p>
                            </div>
                            <div class="password-strength mt-1">
                                <div class="strength-bar"><div class="strength-fill" id="editStrengthFill"></div></div>
                                <p id="editStrengthText" style="margin: 4px 0 0; font-size: 12px; color: #999;"></p>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <div class="password-field">
                                <input 
                                    type="password" 
                                    id="edit_password_confirmation" 
                                    name="password_confirmation" 
                                    class="form-control password-input"
                                    placeholder="Repite la nueva contraseña"
                                    autocomplete="new-password"
                                >
                                <button type="button" class="btn-toggle-password" onclick="toggleModalPasswordVisibility('edit_password_confirmation')">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <div class="match-feedback mt-1" id="editMatchFeedback"></div>
                        </div>
                    </div>

                    <!-- Fila 4: Estado y Foto de Perfil -->
                    <div class="row g-3" style="margin-bottom: 0; margin-top: 0;">
                        <div class="col-md-6">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="">Selecciona un estado</option>
                                <option value="Active" {{ $user->status === 'Active' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactive" {{ $user->status === 'Inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Foto de Perfil (Avatar)</label>
                            <input type="file" id="edit_avatar" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif,image/jpg">
                            <small class="text-muted d-block mt-1">JPG, PNG o GIF. Máximo 2MB</small>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer" style="background: #fafafa; border-top: 1px solid #e5e7eb; padding: 12px 20px; flex-shrink: 0; gap: 10px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: #e5e7eb; color: #374151; border: none; border-radius: 6px; padding: 8px 16px; transition: background 0.2s; font-size: 14px;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                        <i class="fas fa-times" style="margin-right: 6px;"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-gradient" style="padding: 0px 0px; background: linear-gradient(135deg, #ff9900, #ff7700); color: white; border: none; border-radius: 6px; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(255,153,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                        <i class="fas fa-save" style="margin-right: 6px;"></i> Guardar Cambios
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
            // SweetAlert2 CDN guard
            (function(){
                const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
                if (!existing) {
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
                    document.head.appendChild(s);
                }
            })();

            function toggleModalPasswordVisibility(fieldId) {
                const field = document.getElementById(fieldId);
                if (!field) return;
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                
                const button = event.target.closest('.btn-toggle-password');
                if (button) {
                    const icon = button.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
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
                const passwordField = document.getElementById('edit_password');
                const confirmField = document.getElementById('edit_password_confirmation');
                
                if (!passwordField) return;
                
                const strengthFill = document.getElementById('editStrengthFill');
                const strengthText = document.getElementById('editStrengthText');
                const matchFeedback = document.getElementById('editMatchFeedback');
                
                passwordField.addEventListener('input', function() {
                    const value = this.value;
                    const result = validateEditPasswordStrength(value);
                    const strengthContainer = this.closest('.col-md-6').querySelector('.password-strength');
                    
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
                const passwordField = document.getElementById('edit_password');
                const confirmField = document.getElementById('edit_password_confirmation');
                const matchFeedback = document.getElementById('editMatchFeedback');
                
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

            // Setup password validation cuando se muestre el modal
            document.addEventListener('shown.bs.modal', function(e) {
                if (e.target.id === 'editUserModal') {
                    setupEditPasswordValidation();
                }
            });

            // Confirmación antes de enviar edición
            (function(){
                const form = document.getElementById('editUserForm');
                if (!form) return;
                if (form.dataset._editConfirmBound === 'true') return;
                form.dataset._editConfirmBound = 'true';
                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if (window.Swal) {
                        const res = await (window.swConfirm ? swConfirm({
                            title: 'Editar usuario',
                            text: '¿Desea guardar los cambios de este usuario?',
                            icon: 'question',
                            confirmButtonText: 'Sí, actualizar',
                            cancelButtonText: 'Cancelar'
                        }) : Promise.resolve({ isConfirmed: true }));
                        if (!res.isConfirmed) return;
                    }
                    form.submit();
                });
            })();
        </script>

<style>
    /* Responsive para pantallas pequeñas */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px !important;
        }
        .modal-content {
            max-height: calc(100vh - 20px) !important;
        }
        .col-md-6 {
            width: 100% !important;
        }
        .modal-body {
            padding: 16px !important;
        }
        .modal-header, .modal-footer {
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
        .modal-title {
            font-size: 16px !important;
        }
    }

    /* Asegurar superposición correcta */
    #editUserModal { z-index: 9999 !important; }
    .modal-backdrop.show { z-index: 9998 !important; }

    /* Card del modal */
    .modal-elevated {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    }

    /* Header elegante */
    .modal-header-custom {
        border-radius: 12px 12px 0 0;
    }

    /* Inputs */
    .form-control,
    .form-select {
        border: 1.5px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 10px;
        transition: all 0.25s ease;
        font-size: 13px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #ff9900;
        box-shadow: 0 0 0 3px rgba(255,153,0,0.15);
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        font-size: 13px;
        margin-bottom: 4px;
        display: block;
    }

    .text-danger {
        color: #ef4444;
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
        color: #ff9900;
    }

    .password-hint {
        background: #fffaf0;
        padding: 8px;
        border-radius: 6px;
        border-left: 3px solid #ff9900;
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

    /* Botón degradado */
    .btn-gradient {
        background: linear-gradient(135deg, #ff9900, #ff7700) !important;
        border: none;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #ff8800, #ff6600) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(255,153,0,0.35);
    }

    /* Alerta */
    .info-alert {
        background: #fffaf0;
        border-left: 3px solid #ff9900;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #92400e;
    }
</style>
