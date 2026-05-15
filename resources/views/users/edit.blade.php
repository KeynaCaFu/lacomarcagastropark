@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="page-wrapper">
<style>
    .form-container {
        background: white; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px; margin-top: 15px;
        max-width: 600px; margin-left: auto; margin-right: auto;
    }
    .form-title { font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 16px; }
    .form-group { margin-bottom: 14px; }
    .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 4px; font-size: 13px; }
    .form-input, .form-select {
        width: 100%; padding: 8px 10px; border: 2px solid #e5e7eb;
        border-radius: 8px; font-size: 13px;
        transition: border-color .3s, box-shadow .3s;
        font-family: inherit; box-sizing: border-box;
    }
    .form-input:focus, .form-select:focus {
        outline: none; border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22,163,74,.1);
    }
    .error-message { color: #dc2626; font-size: 13px; margin-top: 4px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-actions { display: flex; gap: 10px; margin-top: 20px; }
    .required { color: #dc2626; }
    .form-input.field-error { border-color: #dc2626; }
    .inline-error { color: #dc2626; font-size: 12px; margin-top: 4px; display: none; }
    .inline-error.show { display: block; }

    .info-box {
        background: #f0fdf4; border-left: 4px solid #16a34a;
        padding: 10px; border-radius: 4px; margin-bottom: 15px;
        font-size: 12px; color: #166534;
    }

    /* ══════════════════════════════════════════════
       CAMPO CONTRASEÑA CON OVERLAY
    ══════════════════════════════════════════════ */
    .pw-wrapper { position: relative; display: block; }

    .pw-real {
        position: relative; z-index: 2;
        color: transparent !important;
        caret-color: #374151;
        background: transparent !important;
        width: 100%; padding: 8px 40px 8px 10px;
        border: 2px solid #e5e7eb; border-radius: 8px;
        font-size: 13px; font-family: inherit; box-sizing: border-box;
        transition: border-color .3s, box-shadow .3s;
    }
    .pw-real:focus {
        outline: none; border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22,163,74,.1);
    }
    .pw-real.field-error { border-color: #dc2626; }

    .pw-overlay {
        position: absolute; top: 0; left: 0; right: 40px; bottom: 0;
        z-index: 1; padding: 8px 0 8px 10px;
        font-size: 13px; font-family: inherit; color: #374151;
        white-space: nowrap; overflow: hidden;
        pointer-events: none;
        display: flex; align-items: center;
        letter-spacing: 0.2em;
    }

    .btn-toggle-password {
        position: absolute; top: 50%; right: 10px;
        transform: translateY(-50%); z-index: 3;
        background: none; border: none; color: #9ca3af;
        cursor: pointer; font-size: 15px; padding: 0;
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
        transition: color .2s;
    }
    .btn-toggle-password:hover { color: #374151; }

    .password-hint {
        background: #f9fafb; padding: 8px; border-radius: 6px;
        border-left: 3px solid #16a34a; margin-top: 4px;
    }
    .password-strength { display: none; margin-top: 6px; }
    .password-strength.active { display: block; }
    .strength-bar { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
    .strength-fill { height: 100%; width: 0%; border-radius: 3px; transition: width .3s, background-color .3s; }
    .match-feedback { font-size: 12px; display: none; margin-top: 6px; }
    .match-feedback.show { display: block; }
    .match-feedback.success { color: #16a34a; }
    .match-feedback.error   { color: #dc2626; }

    .btn-submit {
        flex: 1; padding: 12px 20px;
        background: linear-gradient(135deg, #485a1a, #0d5e2a);
        color: white; border: none; border-radius: 8px;
        font-weight: 600; font-size: 15px; cursor: pointer; transition: all .3s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(22,163,74,.25); }
    .btn-cancel {
        flex: 1; padding: 12px 20px; background: #e5e7eb; color: #374151;
        border: none; border-radius: 8px; font-weight: 600; font-size: 15px;
        cursor: pointer; text-decoration: none; transition: all .3s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-cancel:hover { background: #d1d5db; }

    @media (max-width: 991.98px) { .form-container { margin-top: 10px; } }
    @media (max-width: 768px) {
        .form-container { padding: 20px; max-width: 100%; }
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn-submit, .btn-cancel { width: 100%; }
    }
    @media (max-width: 575.98px) {
        .form-container { padding: 12px; border-radius: 8px; }
        .form-title { font-size: 1.2rem; }
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

        <!-- Nombre Completo — solo letras -->
        <div class="form-group">
            <label for="full_name" class="form-label">Nombre Completo <span class="required">*</span></label>
            <input type="text" id="full_name" name="full_name"
                class="form-input {{ $errors->has('full_name') ? 'is-invalid' : '' }}"
                value="{{ old('full_name', $user->full_name) }}"
                placeholder="Ej: Juan Pérez" autocomplete="off" required autofocus />
            <div class="inline-error" id="full_name_error">Solo se permiten letras y espacios.</div>
            @error('full_name')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Correo Electrónico <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email', $user->email) }}"
                placeholder="ejemplo@gmail.com" required />
            <div class="inline-error" id="email_error">El correo debe tener el formato ejemplo@gmail.com</div>
            @error('email')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <!-- Teléfono y Rol -->
        <div class="form-row">
            <div class="form-group">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="tel" id="phone" name="phone"
                    class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                    value="{{ old('phone', $user->phone) }}"
                    placeholder="Ej: 6700-1100" inputmode="numeric" maxlength="9" />
                <div class="inline-error" id="phone_error">El teléfono debe tener el formato 8888-8888.</div>
                @error('phone')<div class="error-message">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="role_id" class="form-label">Rol <span class="required">*</span></label>
                <select id="role_id" name="role_id"
                    class="form-select {{ $errors->has('role_id') ? 'is-invalid' : '' }}" required>
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="error-message">{{ $message }}</div>@enderror
            </div>
        </div>

        <!-- Contraseña con overlay -->
        <div class="form-row">
            <div class="form-group">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <div class="pw-wrapper">
                    <input type="text" id="password" name="password"
                        class="pw-real {{ $errors->has('password') ? 'field-error' : '' }}"
                        placeholder="" autocomplete="new-password"
                        data-lpignore="true" data-form-type="other" />
                    <div class="pw-overlay" id="pw_overlay" data-placeholder="Dejar en blanco para no cambiar"></div>
                    <button type="button" class="btn-toggle-password" data-pw="password" data-overlay="pw_overlay" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-hint">
                    <p style="margin:0 0 4px;font-size:11px;color:#666;"><strong>Mínimo 8 caracteres</strong></p>
                    <p style="margin:0;font-size:11px;color:#666;">💡 Combina mayúsculas, minúsculas y números</p>
                </div>
                <div class="password-strength" id="passwordStrengthEdit">
                    <div class="strength-bar"><div class="strength-fill" id="strengthFillEdit"></div></div>
                    <p id="strengthTextEdit" style="margin:4px 0 0;font-size:12px;color:#999;"></p>
                </div>
                @error('password')<div class="error-message">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                <div class="pw-wrapper">
                    <input type="text" id="password_confirmation" name="password_confirmation"
                        class="pw-real" placeholder=""
                        autocomplete="new-password"
                        data-lpignore="true" data-form-type="other" />
                    <div class="pw-overlay" id="pw_conf_overlay" data-placeholder="Repite la contraseña"></div>
                    <button type="button" class="btn-toggle-password" data-pw="password_confirmation" data-overlay="pw_conf_overlay" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="match-feedback" id="matchFeedbackEdit"></div>
            </div>
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="status" class="form-label">Estado <span class="required">*</span></label>
            <select id="status" name="status"
                class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
                <option value="">Selecciona un estado</option>
                <option value="Active"   {{ old('status', $user->status) == 'Active'   ? 'selected' : '' }}>Activo</option>
                <option value="Inactive" {{ old('status', $user->status) == 'Inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')<div class="error-message">{{ $message }}</div>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Guardar Cambios</button>
            <a href="{{ route('users.index') }}" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </form>
</div>

<script>
/* =================================================================
   SISTEMA DE OVERLAY PARA CONTRASEÑA
   ================================================================= */
(function() {
    const DOT = '●';

    function initPwField(inputId, overlayId) {
        const input   = document.getElementById(inputId);
        const overlay = document.getElementById(overlayId);
        if (!input || !overlay) return;

        let isVisible = false;

        function renderOverlay() {
            const val = input.value;
            if (val.length === 0) {
                overlay.style.color = '#9ca3af';
                overlay.style.letterSpacing = 'normal';
                overlay.textContent = overlay.dataset.placeholder;
            } else if (isVisible) {
                overlay.style.color = '#374151';
                overlay.style.letterSpacing = 'normal';
                overlay.textContent = val;
            } else {
                overlay.style.color = '#374151';
                overlay.style.letterSpacing = '0.2em';
                overlay.textContent = DOT.repeat(val.length);
            }
        }

        input.addEventListener('input', renderOverlay);
        renderOverlay();

        input._pwToggle = function(btn) {
            isVisible = !isVisible;
            renderOverlay();
            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye',      !isVisible);
                icon.classList.toggle('fa-eye-slash',  isVisible);
            }
        };
    }

    initPwField('password',              'pw_overlay');
    initPwField('password_confirmation', 'pw_conf_overlay');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-toggle-password');
        if (!btn) return;
        const input = document.getElementById(btn.dataset.pw);
        if (input && input._pwToggle) input._pwToggle(btn);
    });
})();

/* =================================================================
   NOMBRE — solo letras, acentos y espacios. SIN números.
   ================================================================= */
(function() {
    const field = document.getElementById('full_name');
    const err   = document.getElementById('full_name_error');
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
        err.classList.toggle('show', bad);
        this.classList.toggle('field-error', bad);
    });
})();

/* =================================================================
   EMAIL — solo @gmail.com
   ================================================================= */
(function() {
    const field = document.getElementById('email');
    const err   = document.getElementById('email_error');
    if (!field) return;

    const regex = /^[a-zA-Z0-9._%+\-]+@gmail\.com$/i;

    function validate() {
        const val = field.value.trim();
        if (!val) { err.classList.remove('show'); field.classList.remove('field-error'); return; }
        const ok = regex.test(val);
        err.classList.toggle('show', !ok);
        field.classList.toggle('field-error', !ok);
    }

    field.addEventListener('blur', validate);
    field.addEventListener('input', function() { if (err.classList.contains('show')) validate(); });
})();

/* =================================================================
   TELÉFONO — solo dígitos, máscara 8888-8888
   ================================================================= */
(function() {
    const field = document.getElementById('phone');
    const err   = document.getElementById('phone_error');
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
        err.classList.toggle('show', bad);
        this.classList.toggle('field-error', bad);
    });

    field.addEventListener('blur', function() {
        const raw = this.value.replace(/\D/g, '');
        const bad = raw.length > 0 && raw.length < 8;
        err.classList.toggle('show', bad);
        this.classList.toggle('field-error', bad);
    });
})();

/* =================================================================
   FORTALEZA DE CONTRASEÑA
   ================================================================= */
(function() {
    const pw    = document.getElementById('password');
    const conf  = document.getElementById('password_confirmation');
    const bar   = document.getElementById('strengthFillEdit');
    const txt   = document.getElementById('strengthTextEdit');
    const strEl = document.getElementById('passwordStrengthEdit');
    const fb    = document.getElementById('matchFeedbackEdit');
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

/* =================================================================
   SUBMIT CON CONFIRMACIÓN
   ================================================================= */
(function() {
    if (typeof Swal === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
        document.head.appendChild(s);
    }

    function bindSubmit() {
        const form = document.getElementById('editUserFormPage');
        if (!form || form.dataset._bound) return;
        form.dataset._bound = 'true';

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const emailField = document.getElementById('email');
            if (emailField && emailField.value.trim()) {
                if (!/^[a-zA-Z0-9._%+\-]+@gmail\.com$/i.test(emailField.value.trim())) {
                    document.getElementById('email_error').classList.add('show');
                    emailField.classList.add('field-error');
                    emailField.focus();
                    return;
                }
            }

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

            const formData = new FormData(form);
            const userId   = "{{ $user->user_id }}";

            try {
                const response = await fetch(`/usuarios/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) { throw await response.json(); }

                if (window.swToast) {
                    swToast.fire({
                        icon: 'success', title: 'Usuario actualizado correctamente',
                        timer: 1500, timerProgressBar: true,
                        didClose: () => { window.location.href = '{{ route("users.index") }}'; }
                    });
                } else {
                    window.location.href = '{{ route("users.index") }}';
                }
            } catch (error) {
                if (window.swAlert) {
                    if (error.errors) {
                        const msgs = Object.values(error.errors).flat().map(m => `• ${m}`).join('<br>');
                        swAlert({ icon:'error', title:'Error de validación', html: msgs, confirmButtonColor:'#dc2626' });
                    } else {
                        swAlert({ icon:'error', title:'Error', text: error.message || 'Error al guardar', confirmButtonColor:'#dc2626' });
                    }
                }
            }
        });
    }

    if (typeof Swal !== 'undefined') { bindSubmit(); }
    else { setTimeout(bindSubmit, 300); }
})();

/* =================================================================
   MENSAJES DE SESIÓN
   ================================================================= */
(function() {
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

</div>
@endsection