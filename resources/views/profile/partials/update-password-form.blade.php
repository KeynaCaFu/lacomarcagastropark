<div class="mb-4">
    <h5>Actualizar Contraseña</h5>
    <p class="text-muted">Asegúrate de que tu cuenta está usando una contraseña larga y aleatoria para mantenerte seguro.</p>
</div>

<form method="post" action="{{ route('password.update') }}" class="mt-4">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="current_password" class="form-label">Contraseña Actual</label>
        <div style="position:relative;">
            <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" style="padding-right:42px;" />
            <button type="button" onclick="togglePwd('current_password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                <i class="fas fa-eye-slash"></i>
            </button>
        </div>
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Nueva Contraseña</label>
        <div style="position:relative;">
            <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" style="padding-right:42px;" />
            <button type="button" onclick="togglePwd('password', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                <i class="fas fa-eye-slash"></i>
            </button>
        </div>
        <!-- Barra de fortaleza -->
        <div id="pwd-strength-bar" style="display:none; margin-top:8px;">
            <div style="height:5px; background:#e5e7eb; border-radius:4px; overflow:hidden;">
                <div id="pwd-strength-fill" style="height:100%; width:0%; border-radius:4px; transition:width .3s,background .3s;"></div>
            </div>
            <small id="pwd-strength-text" style="font-size:11px; margin-top:3px; display:block;"></small>
        </div>
        @error('password', 'updatePassword')
            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <div style="position:relative;">
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" style="padding-right:42px;" />
            <button type="button" onclick="togglePwd('password_confirmation', this)" style="position:absolute;top:50%;right:10px;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                <i class="fas fa-eye-slash"></i>
            </button>
        </div>
        <!-- Feedback de coincidencia -->
        <small id="pwd-match-text" style="font-size:11px; margin-top:4px; display:none;"></small>
        @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
        @enderror
    </div>

    <script>
    function togglePwd(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye', isHidden);
        icon.classList.toggle('fa-eye-slash', !isHidden);
        btn.style.color = isHidden ? '#e18018' : '#9ca3af';
    }

    (function () {
        const pwdInput    = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const bar         = document.getElementById('pwd-strength-bar');
        const fill        = document.getElementById('pwd-strength-fill');
        const text        = document.getElementById('pwd-strength-text');
        const matchText   = document.getElementById('pwd-match-text');

        const levels = [
            { min: 0,  max: 0,   label: '',           color: '',        width: '0%'   },
            { min: 1,  max: 25,  label: 'Muy débil',  color: '#dc2626', width: '25%'  },
            { min: 26, max: 50,  label: 'Débil',      color: '#f97316', width: '50%'  },
            { min: 51, max: 75,  label: 'Buena',      color: '#eab308', width: '75%'  },
            { min: 76, max: 100, label: 'Fuerte',      color: '#16a34a', width: '100%' },
        ];

        function getStrength(val) {
            let score = 0;
            if (val.length >= 8)          score += 25;
            if (/[a-z]/.test(val))        score += 25;
            if (/[A-Z]/.test(val))        score += 25;
            if (/[0-9!@#$%^&*]/.test(val)) score += 25;
            return score;
        }

        function updateStrength() {
            const val = pwdInput.value;
            if (!val) { bar.style.display = 'none'; return; }
            const score = getStrength(val);
            const level = levels.find(l => score >= l.min && score <= l.max) || levels[0];
            bar.style.display = 'block';
            fill.style.width = level.width;
            fill.style.background = level.color;
            text.textContent = level.label;
            text.style.color = level.color;
        }

        function updateMatch() {
            const pwd  = pwdInput.value;
            const conf = confirmInput.value;
            if (!conf) { matchText.style.display = 'none'; return; }
            matchText.style.display = 'block';
            if (pwd === conf) {
                matchText.textContent = 'Las contraseñas coinciden';
                matchText.style.color = '#16a34a';
            } else {
                matchText.textContent = 'Las contraseñas no coinciden';
                matchText.style.color = '#dc2626';
            }
        }

        pwdInput.addEventListener('input', () => { updateStrength(); updateMatch(); });
        confirmInput.addEventListener('input', updateMatch);
    })();
    </script>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>

        @if (session('status') === 'password-updated')
            <p class="text-success small mb-0">Contraseña actualizada correctamente.</p>
        @endif
    </div>
</form>
