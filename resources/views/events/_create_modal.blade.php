
<div id="createModal" class="sage-modal" aria-hidden="true" style="z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; padding: 10px;">
    <style>
        .field-error-event {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .field-error-event i { font-size: 11px; }
        .field.input-error {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 2px rgba(220,38,38,0.12) !important;
        }
    </style>
    <div class="modal-backdrop" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(3px); z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
        <div class="modal-card" style="box-sizing: border-box; max-width: 740px; width: calc(100% - 20px); z-index:9998; background:#fdfdfc; display: flex; flex-direction: column; max-height: calc(100vh - 20px); min-height: auto; position: relative; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);" role="dialog" aria-modal="true">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Agregar Nuevo Evento</h3>
                <a href="javascript:void(0)" class="modal-close" aria-label="Cerrar" onclick="closeModal('createModal')" style="cursor: pointer; font-size: 28px; color: #4b4545;">×</a>
            </div>

            <form id="formCreate" action="{{ route('eventos.guardar') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;" novalidate>
                @csrf

                <div class="modal-body" style="overflow-y: auto; overflow-x: hidden; flex: 1; padding: 20px; -webkit-overflow-scrolling: touch;">
                    <div class="form-grid2">
                        <div>
                            <label class="label">Nombre del Evento <span style="color:#dc2626;">*</span></label>
                            <input class="field" id="eventTitle" name="title" placeholder="Ej. Noche de Tapas" value="{{ old('title') }}">
                            <div class="field-error-event" id="eventTitleError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>

                            <label class="label">Fecha <span style="color:#dc2626;">*</span></label>
                            <input type="date" class="field" id="eventDate" name="date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}">
                            <div class="field-error-event" id="eventDateError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>

                            <label class="label">Hora <span style="color:#dc2626;">*</span></label>
                            <input type="time" class="field" id="eventTime" name="time" value="{{ old('time') }}">
                            <div class="field-error-event" id="eventTimeError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                        </div>

                        <div>
                            <label class="label">Imagen <span style="color:#dc2626;">*</span></label>
                            <input type="file" class="field" id="photoInputCreate" name="photo" accept="image/*">
                            <div class="field-error-event" id="photoInputCreateError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                            <div style="color:#6f7a71; font-size:14px; margin-top:6px;">Formatos: JPG, PNG. Máx: 4MB</div>

                            <label class="label" style="margin-top:12px;">Estado <span style="color:#dc2626;">*</span></label>
                            <select class="field" id="eventStatus" name="status">
                                <option value="">Seleccione un estado</option>
                                <option value="activo"   {{ old('status')==='activo' ? 'selected':'' }}>Activo</option>
                                <option value="inactivo" {{ old('status')==='inactivo' ? 'selected':'' }}>Inactivo</option>
                            </select>
                            <div class="field-error-event" id="eventStatusError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                        </div>
                    </div>

                    <label class="label" style="margin-top:10px;">Ubicación <span style="color:#dc2626;">*</span></label>
                    <input class="field" id="eventLocation" name="location" placeholder="Ej. Plaza central" value="{{ old('location') }}">
                    <div class="field-error-event" id="eventLocationError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>

                    <label class="label" style="margin-top:10px;">Descripción <span style="color:#dc2626;">*</span></label>
                    <textarea id="eventDescription" name="description" class="field" style="min-height:100px; resize:vertical;" placeholder="Describe el evento...">{{ old('description') }}</textarea>
                    <div class="field-error-event" id="eventDescriptionError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
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

        // Helper functions
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + 'Error');
            if (field && errorSpan) {
                field.classList.add('input-error');
                errorSpan.style.display = 'flex';
                const span = errorSpan.querySelector('span');
                if (span) span.textContent = message;
            }
        }

        function clearFieldError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + 'Error');
            if (field && errorSpan) {
                field.classList.remove('input-error');
                errorSpan.style.display = 'none';
            }
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();
            
            // Get fields
            const titleField = document.getElementById('eventTitle');
            const dateField = document.getElementById('eventDate');
            const timeField = document.getElementById('eventTime');
            const photoField = document.getElementById('photoInputCreate');
            const statusField = document.getElementById('eventStatus');
            const descriptionField = document.getElementById('eventDescription');

            let isValid = true;

            // Validate title
            if (!titleField.value.trim()) {
                showFieldError('eventTitle', 'El nombre del evento es obligatorio');
                isValid = false;
            } else {
                clearFieldError('eventTitle');
            }

            // Validate date
            if (!dateField.value) {
                showFieldError('eventDate', 'La fecha es obligatoria');
                isValid = false;
            } else {
                clearFieldError('eventDate');
            }

            // Validate time
            if (!timeField.value) {
                showFieldError('eventTime', 'La hora es obligatoria');
                isValid = false;
            } else {
                clearFieldError('eventTime');
            }

            // Validate photo
            if (!photoField.files || photoField.files.length === 0) {
                showFieldError('photoInputCreate', 'La imagen es obligatoria');
                isValid = false;
            } else {
                clearFieldError('photoInputCreate');
            }

            // Validate status
            if (!statusField.value) {
                showFieldError('eventStatus', 'Debe seleccionar un estado');
                isValid = false;
            } else {
                clearFieldError('eventStatus');
            }

            // Validate location
            const locationField = document.getElementById('eventLocation');
            if (!locationField || !locationField.value.trim()) {
                showFieldError('eventLocation', 'La ubicación es obligatoria');
                isValid = false;
            } else {
                clearFieldError('eventLocation');
            }

            // Validate description
            if (!descriptionField.value.trim()) {
                showFieldError('eventDescription', 'La descripción es obligatoria');
                isValid = false;
            } else {
                clearFieldError('eventDescription');
            }

            if (!isValid) return;

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

        // Real-time validation
        ['eventTitle', 'eventDate', 'eventTime', 'eventStatus', 'eventLocation', 'eventDescription'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', function() {
                    if (this.value.trim()) clearFieldError(fieldId);
                });
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) showFieldError(fieldId, 'Este campo es obligatorio');
                });
            }
        });

        // Photo field validation
        const photoField = document.getElementById('photoInputCreate');
        if (photoField) {
            photoField.addEventListener('change', function() {
                if (this.files && this.files.length > 0) clearFieldError('photoInputCreate');
            });
        }
    }
})();
</script>

