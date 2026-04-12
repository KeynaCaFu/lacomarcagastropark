
<div id="createModal" class="sage-modal" aria-hidden="true" style="z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; padding: 10px;">
    <div class="modal-backdrop" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(3px); z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
        <div class="modal-card" style="box-sizing: border-box; max-width: 740px; width: calc(100% - 20px); z-index:9998; background:#fdfdfc; display: flex; flex-direction: column; max-height: calc(100vh - 20px); min-height: auto; position: relative; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);" role="dialog" aria-modal="true">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Agregar Nuevo Evento</h3>
                <a href="javascript:void(0)" class="modal-close" aria-label="Cerrar" onclick="closeModal('createModal')" style="cursor: pointer; font-size: 28px; color: #4b4545;">×</a>
            </div>

            <form id="formCreate" action="{{ route('eventos.guardar') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                @csrf

                <div class="modal-body" style="overflow-y: auto; overflow-x: hidden; flex: 1; padding: 20px; -webkit-overflow-scrolling: touch;">
                    <div class="form-grid2">
                        <div>
                            <label class="label">Nombre del Evento *</label>
                            <input class="field" name="title" required placeholder="Ej. Noche de Tapas" value="{{ old('title') }}">

                            <label class="label">Fecha *</label>
                            <input type="date" class="field" name="date" required value="{{ old('date') }}" min="{{ date('Y-m-d') }}">

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

                <div class="modal-footer">
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
            
            // Función con retry logic para asegurar que swConfirm está disponible
            const showConfirm = () => {
                if (typeof window.swConfirm === 'undefined') {
                    // Si aún no está disponible, esperar 100ms y reintentar
                    setTimeout(showConfirm, 100);
                    return;
                }
                
                swConfirm({
                    title: '¿Guardar evento?',
                    text: 'Se guardará el evento con los datos ingresados',
                    icon: 'question',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then(r => { if (r.isConfirmed) form.submit(); });
            };
            
            showConfirm();
        });
    }
})();
</script>

