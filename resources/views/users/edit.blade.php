@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 32px;
        margin-top: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .form-title {
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
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
        gap: 12px;
        margin-top: 32px;
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
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #166534;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 20px;
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
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Mínimo 8 caracteres (dejar en blanco para no cambiar)"
                />
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Confirmar Nueva Contraseña
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input"
                    placeholder="Repite la contraseña"
                />
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
                form.submit();
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
        if (window.swAlert) {
            if (successMsg) {
                swAlert({ icon: 'success', title: 'Éxito', text: successMsg });
            }
            if (errorMsg) {
                swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
            }
            if (hasErrors) {
                swAlert({
                    icon: 'error',
                    title: 'Errores de validación',
                    html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                    confirmButtonColor: '#dc2626'
                });
            }
        }
    } catch(e) { /* noop */ }
})();
</script>
@endsection
