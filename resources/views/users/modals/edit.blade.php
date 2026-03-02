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
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-hint mt-2">
                                <p style="margin: 0 0 8px; font-size: 12px; color: #666;"><strong>Mínimo 8 caracteres recomendado</strong></p>
                                <p style="margin: 0; font-size: 12px; color: #666;">💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números</p>
                            </div>
                            <div class="password-strength mt-2">
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
                                    autocomplete="new-password"
                                >
                                <button type="button" class="btn-toggle-password" onclick="toggleModalPasswordVisibility('edit_password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="match-feedback mt-2" id="editMatchFeedback"></div>
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
