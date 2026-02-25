
<div id="createModal" class="sage-modal" aria-hidden="true" style="z-index:9997;">
    <div class="modal-backdrop" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(3px); z-index:9997;">
        <div class="modal-card" style="max-width: 740px; width:90%; z-index:9998; background:#fdfdfc;" role="dialog" aria-modal="true">
            <div class="modal-header" style="background:#faf9f6;">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Agregar Nuevo Evento</h3>
                <a href="javascript:void(0)" class="modal-close" aria-label="Cerrar" onclick="closeModal('createModal')">×</a>
            </div>

            <form id="formCreate" action="{{ route('eventos.guardar') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="form-grid2">
                        <div>
                            <label class="label">Nombre del Evento *</label>
                            <input class="field" name="title" required placeholder="Ej. Noche de Tapas" value="{{ old('title') }}">

                            <label class="label">Fecha *</label>
                            <input type="date" class="field" name="date" required value="{{ old('date') }}">

                            <label class="label">Hora *</label>
                            <input type="time" class="field" name="time" required value="{{ old('time') }}">
                        </div>

                        <div>
                            <label class="label">Imagen (archivo opcional)</label>
                            <input type="file" class="field" name="photo" id="photoInputCreate" accept="image/*">
                            <div style="color:#6f7a71; font-size:14px; margin-top:6px;">Formatos: JPG, PNG. Máx: 2MB</div>

                            <label class="label" style="margin-top:12px;">Estado *</label>
                            <select class="field" name="status" required>
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>Seleccione un estado</option>
                                <option value="activo"   {{ old('status')==='activo' ? 'selected':'' }}>Activo</option>
                                <option value="inactivo" {{ old('status')==='inactivo' ? 'selected':'' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <label class="label" style="margin-top:10px;">Ubicación (opcional)</label>
                    <input class="field" name="location" placeholder="Ej. Plaza central" value="{{ old('location') }}">

                    <label class="label" style="margin-top:10px;">Descripción *</label>
                    <textarea name="description" class="field" style="min-height:100px; resize:vertical;" required placeholder="Describe el evento...">{{ old('description') }}</textarea>
                </div>

                <div class="modal-footer" style="background:#faf9f6;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('createModal')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-modal-save">
                        Guardar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('formCreate');
    if (form && !form.dataset._createConfirmBound) {
        form.dataset._createConfirmBound = 'true';
        form.addEventListener('submit', function(e){
            e.preventDefault();
            if (window.swConfirm) {
                swConfirm({
                    html: '<div class="swal-title-like">¿Estás seguro de guardar el evento?</div>',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then(r => { if (r.isConfirmed) form.submit(); });
            } else if (confirm('¿Estás seguro de guardar el evento?')) {
                form.submit();
            }
        });
    }

    // Session success/error and validation errors
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
                    swAlert({ icon: 'error', title: 'Error', text: errorMsg });
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
                    swAlert({ icon: 'error', title: 'Errores de validación', html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>` });
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
