
<div id="createModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="createTitle">
        <div class="modal-header">
            <h3 id="createTitle">Agregar Nuevo Evento</h3>
            <button type="button" class="close" aria-label="Cerrar" onclick="closeModal('createModal')">&times;</button>
        </div>

        <div class="modal-body">
            <form id="formCreate" action="{{ route('eventos.guardar') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                        <input type="file" class="field" name="photo" accept="image/*">
                        <div style="color:#6f7a71; font-size:14px; margin-top:6px;">Formatos: JPG, PNG. Máx: 2MB</div>

                        <label class="label">Estado *</label>
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
                <textarea name="description" class="field textarea" required placeholder="Describe el evento...">{{ old('description') }}</textarea>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Evento</button>
                </div>
            </form>
        </div>
    </div>
</div>
