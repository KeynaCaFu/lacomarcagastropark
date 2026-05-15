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
                <input type="text" id="editFullName" name="full_name" class="form-control" value="{{ $user->full_name }}" required autocomplete="off">
                <div class="invalid-feedback"></div>
                <div id="editFullNameInline" style="color:#dc2626;font-size:12px;margin-top:4px;display:none;">
                    Solo se permiten letras y espacios.
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                <input type="email" id="editEmail" name="email" class="form-control" value="{{ $user->email }}" required>
                <div class="invalid-feedback"></div>
                <div id="editEmailInline" style="color:#dc2626;font-size:12px;margin-top:4px;display:none;">
                    El correo debe tener el formato ejemplo@gmail.com
                </div>
            </div>
        </div>

        <!-- Fila 2 -->
        <div class="row g-3" style="margin-bottom: 8px; margin-top: 0;">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" id="editPhone" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" inputmode="numeric" maxlength="9">
                <div id="editPhoneInline" style="color:#dc2626;font-size:12px;margin-top:4px;display:none;">
                    El teléfono debe tener el formato 8888-8888.
                </div>
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
                    <input type="password" id="edit_modal_password" name="password"
                        class="form-control password-input"
                        placeholder="Dejar en blanco para mantener la actual"
                        autocomplete="new-password">
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
                    <input type="password" id="edit_modal_password_confirmation" name="password_confirmation"
                        class="form-control password-input"
                        placeholder="Repite la nueva contraseña"
                        autocomplete="new-password">
                    <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('edit_modal_password_confirmation')">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                <div class="match-feedback mt-2" id="editModalMatchFeedback"></div>
            </div>
        </div>

        <!-- Fila 4: Estado -->
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
        </div>

    </form>
    </div>

    <!-- Footer -->
    <div class="modal-actions" style="background: #fafafa; padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end; flex-shrink: 0;">
        <button type="button" class="btn btn-secondary" onclick="closeUserModal('userEditModal')" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
            <i class="fas fa-times" style="margin-right: 6px;"></i> Cancelar
        </button>
        <button type="submit" form="editUserForm" class="btn btn-primary btn-gradient" style="padding: 10px 24px; background: linear-gradient(135deg, #ff9900, #ff7700); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(255,153,0,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
            <i class="fas fa-save" style="margin-right: 6px;"></i> Guardar Cambios
        </button>
    </div>
</div>

<style>
@media (max-width: 768px) { .col-md-6 { width: 100% !important; } }
@media (max-width: 480px) {
    .form-label { font-size: 12px !important; }
    .form-control, .form-select { padding: 8px 10px !important; font-size: 13px !important; }
    .password-hint { padding: 10px !important; }
}

.info-alert { background: #fffaf0; border-left: 3px solid #ff9900; padding: 12px; border-radius: 6px; font-size: 13px; color: #92400e; }

.password-field { position: relative; display: flex; align-items: center; }
.password-field .form-control { padding-right: 72px; }
.password-field .form-control.is-invalid,
.password-field .form-control.is-valid { padding-right: 72px; background-position: right 44px center; }

.btn-toggle-password {
    position: absolute; right: 12px;
    background: none; border: none; color: #999;
    cursor: pointer; font-size: 16px; padding: 5px;
    transition: color 0.2s ease; width: auto;
}
.btn-toggle-password:hover { color: #ff9900; }

.password-hint { background: #fffaf0; padding: 12px; border-radius: 6px; border-left: 3px solid #ff9900; }

.form-label { font-weight: 600; color: #374151; font-size: 13px; margin-bottom: 6px; display: block; }
.form-control, .form-select {
    border: 1.5px solid #e5e7eb; border-radius: 6px;
    padding: 10px 12px; font-size: 14px; transition: all 0.2s ease;
}
.form-control:focus, .form-select:focus {
    outline: none; border-color: #ff9900;
    box-shadow: 0 0 0 3px rgba(255,153,0,0.15);
}
.form-control.field-error { border-color: #dc2626 !important; }
.text-danger { color: #ef4444; }

.password-strength { display: none; }
.password-strength.active { display: block; }
.strength-bar { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
.strength-fill { height: 100%; width: 0%; border-radius: 3px; transition: width 0.3s, background-color 0.3s; }

.match-feedback { font-size: 12px; display: none; }
.match-feedback.show { display: block; }
.match-feedback.success { color: #16a34a; }
.match-feedback.error   { color: #dc2626; }
</style>

<script>
/* ── Toggle contraseña ── */
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    const button = event.target.closest('.btn-toggle-password');
    const icon = button.querySelector('i');
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

/* ══════════════════════════════════════════
   NOMBRE — solo letras, acentos y espacios
   ══════════════════════════════════════════ */
(function() {
    const field = document.getElementById('editFullName');
    const err   = document.getElementById('editFullNameInline');
    if (!field) return;

    field.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        if (e.key.length > 1) return;
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]$/.test(e.key)) e.preventDefault();
    });

    field.addEventListener('input', function() {
        const clean = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '');
        if (this.value !== clean) this.value = clean;
        const bad = this.value.length > 0 && this.value.trim() === '';
        err.style.display = bad ? 'block' : 'none';
        this.classList.toggle('field-error', bad);
    });
})();

/* ══════════════════════════════════════════
   EMAIL — solo @gmail.com
   ══════════════════════════════════════════ */
(function() {
    const field = document.getElementById('editEmail');
    const err   = document.getElementById('editEmailInline');
    if (!field) return;

    const regex = /^[a-zA-Z0-9._%+\-]+@gmail\.com$/i;

    function validate() {
        const val = field.value.trim();
        if (!val) { err.style.display = 'none'; field.classList.remove('field-error'); return; }
        const ok = regex.test(val);
        err.style.display = ok ? 'none' : 'block';
        field.classList.toggle('field-error', !ok);
    }

    field.addEventListener('blur', validate);
    field.addEventListener('input', function() {
        if (err.style.display === 'block') validate();
    });
})();

/* ══════════════════════════════════════════
   TELÉFONO — solo dígitos, máscara 8888-8888
   ══════════════════════════════════════════ */
(function() {
    const field = document.getElementById('editPhone');
    const err   = document.getElementById('editPhoneInline');
    if (!field) return;

    field.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey || e.altKey) return;
        if (e.key.length > 1) return;
        if (!/[0-9]/.test(e.key)) e.preventDefault();
    });

    field.addEventListener('input', function() {
        let d = this.value.replace(/\D/g, '').slice(0, 8);
        this.value = d.length > 4 ? d.slice(0,4) + '-' + d.slice(4) : d;
        const raw = this.value.replace(/\D/g, '');
        const bad = raw.length > 0 && raw.length < 8;
        err.style.display = bad ? 'block' : 'none';
        this.classList.toggle('field-error', bad);
    });

    field.addEventListener('blur', function() {
        const raw = this.value.replace(/\D/g, '');
        const bad = raw.length > 0 && raw.length < 8;
        err.style.display = bad ? 'block' : 'none';
        this.classList.toggle('field-error', bad);
    });
})();

/* ── Fortaleza de contraseña ── */
(function() {
    const pw    = document.getElementById('edit_modal_password');
    const conf  = document.getElementById('edit_modal_password_confirmation');
    const bar   = document.getElementById('editModalStrengthFill');
    const txt   = document.getElementById('editModalStrengthText');
    const fb    = document.getElementById('editModalMatchFeedback');
    if (!pw) return;

    function strength(v) {
        let s = 0;
        if (v.length >= 8)   s += 25;
        if (/[a-z]/.test(v)) s += 25;
        if (/[A-Z]/.test(v)) s += 25;
        if (/[0-9]/.test(v)) s += 25;
        return {
            pct:   s,
            text:  ['','Muy débil','Débil','Buena','Fuerte'][s/25] || '',
            color: ['','#dc2626','#f97316','#eab308','#16a34a'][s/25] || ''
        };
    }

    function checkMatch() {
        if (!conf || !conf.value) { fb.classList.remove('show'); return; }
        const ok = pw.value === conf.value;
        fb.className = 'match-feedback show ' + (ok ? 'success' : 'error');
        fb.innerHTML = ok
            ? '<i class="fas fa-check-circle"></i> Las contraseñas coinciden'
            : '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
    }

    pw.addEventListener('input', function() {
        const strEl = this.closest('.col-md-6').querySelector('.password-strength');
        if (this.value.length > 0) {
            const r = strength(this.value);
            strEl.classList.add('active');
            bar.style.width = r.pct + '%';
            bar.style.backgroundColor = r.color;
            txt.textContent = r.text;
            txt.style.color = r.color;
        } else {
            strEl.classList.remove('active');
        }
        if (conf && conf.value) checkMatch();
    });

    if (conf) conf.addEventListener('input', checkMatch);
})();

/* ── Init ── */
(function(){
    if (typeof Swal === 'undefined') {
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    }

    try {
        const successMsg = @json(session('success'));
        const errorMsg   = @json(session('error'));
        const hasErrors  = {{ $errors->any() ? 'true' : 'false' }};

        function waitFor(key, cb, n=0) {
            if (window[key]) cb();
            else if (n < 50) setTimeout(() => waitFor(key, cb, n+1), 100);
        }

        if (successMsg) waitFor('swToast', () => swToast.fire({ icon:'success', title: successMsg }));
        if (errorMsg)   waitFor('swAlert',  () => swAlert({ icon:'error', title:'Error', text: errorMsg, confirmButtonColor:'#dc2626' }));
        if (hasErrors)  waitFor('swAlert',  () => swAlert({
            icon: 'error', title: 'Errores de validación',
            html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            confirmButtonColor: '#dc2626'
        }));
    } catch(e) {}
})();
</script>