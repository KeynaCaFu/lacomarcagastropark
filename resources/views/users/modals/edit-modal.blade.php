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
                <input 
                    type="password" 
                    name="password" 
                    class="form-control"
                    placeholder="Dejar en blanco para mantener la actual"
                >
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Confirmar Nueva Contraseña</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    class="form-control"
                >
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
    // SweetAlert2 CDN guard + bind after load (for AJAX partials)
    function bindEditConfirm() {
        const form = document.getElementById('editUserForm');
        if (form && !form.dataset._editConfirmBound) {
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
        }
    }

    if (typeof Swal === 'undefined') {
        let existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            existing = document.createElement('script');
            existing.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(existing);
        }
        existing.addEventListener('load', bindEditConfirm);
    } else {
        bindEditConfirm();
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
</style>
