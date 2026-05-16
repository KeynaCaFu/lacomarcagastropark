<div class="modal-header">
  <h3 class="modal-title"><i class="fas fa-edit"></i> Editar Evento</h3>
  <button type="button" class="close" aria-label="Cerrar" onclick="closeModal('editModal')">&times;</button>
</div>

<style>
  .field-error-edit {
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .field-error-edit i { font-size: 11px; }
  .form-control.input-error, .form-select.input-error {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 2px rgba(220,38,38,0.12) !important;
  }
</style>

<div class="modal-body">
  <form id="editForm" action="{{ route('eventos.actualizar', ['evento' => $event->event_id]) }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="row">
      <div class="col-lg-8">
        <div class="mb-3">
          <label class="form-label">Nombre del Evento <span style="color:#dc2626;">*</span></label>
          <input type="text" id="editTitle" name="title" class="form-control" value="{{ $event->title }}" maxlength="255">
          <div class="field-error-edit" id="editTitleError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Fecha <span style="color:#dc2626;">*</span></label>
            <input type="date" id="editDate" name="date" class="form-control" value="{{ optional($event->start_at)->format('Y-m-d') }}" min="{{ date('Y-m-d') }}">
            <div class="field-error-edit" id="editDateError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Hora <span style="color:#dc2626;">*</span></label>
            <input type="time" id="editTime" name="time" class="form-control" value="{{ optional($event->start_at)->format('H:i') }}">
            <div class="field-error-edit" id="editTimeError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Ubicación <span style="color:#ef4444;">*</span></label>
          <input type="text" id="editLocation" name="location" class="form-control" value="{{ $event->location }}" maxlength="255" placeholder="Ej. Plaza central">
          <div class="field-error-edit" id="editLocationError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción <span style="color:#dc2626;">*</span></label>
          <textarea id="editDescription" name="description" class="form-control" rows="5">{{ $event->description }}</textarea>
          <div class="field-error-edit" id="editDescriptionError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="mb-3">
          <label class="form-label">Imagen (archivo opcional)</label>
          <input type="file" name="photo" accept="image/*" class="form-control mb-2" id="eventPhotoInput">
          @php
            $imgUrl = $event->image_url;
          @endphp

          @if($imgUrl)
            <div style="border-radius:8px; overflow:hidden; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.06);">
              <img id="eventImagePreview" src="{{ $imgUrl }}" alt="{{ $event->title }}" style="width:100%; height:140px; object-fit:cover; display:block;" onerror="this.style.display='none'">
            </div>
            <small class="text-muted d-block mt-2">Actualmente: <a href="{{ $imgUrl }}" target="_blank">Ver imagen</a></small>
          @else
            <div id="eventImagePlaceholder" style="height:140px; background:#f8f8f8; border-radius:8px;"></div>
          @endif
        </div>

        <div class="mb-3">
          <label class="form-label">Estado <span style="color:#dc2626;">*</span></label>
          <select id="editStatus" name="status" class="form-select">
            <option value="">Seleccione un estado</option>
            <option value="activo" {{ $event->is_active ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ !$event->is_active ? 'selected' : '' }}>Inactivo</option>
          </select>
          <div class="field-error-edit" id="editStatusError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
        </div>

      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn-modal-cancel" onclick="closeModal('editModal')">
        Cancelar
      </button>
      <button type="submit" class="btn-modal-save">
        Actualizar Evento
      </button>
    </div>

  </form>
</div>

<script>
// Manejar vista previa de foto de evento
document.addEventListener('DOMContentLoaded', function(){
    const photoInput = document.getElementById('eventPhotoInput');
    if(photoInput){
        photoInput.addEventListener('change', function(e){
            if(this.files && this.files[0]){
                const reader = new FileReader();
                reader.onload = function(event){
                    let preview = document.getElementById('eventImagePreview');
                    if(!preview){
                        const placeholder = document.getElementById('eventImagePlaceholder');
                        if(placeholder) placeholder.remove();
                        const container = photoInput.closest('.mb-3');
                        preview = document.createElement('img');
                        preview.id = 'eventImagePreview';
                        preview.style.cssText = 'width: 100%; height: 140px; object-fit: cover; display: block; border-radius: 8px;';
                        preview.alt = 'Vista previa';
                        const div = document.createElement('div');
                        div.style.cssText = 'border-radius: 8px; overflow: hidden; background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,0.06); margin-top: 8px;';
                        div.appendChild(preview);
                        container.appendChild(div);
                    }
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>

<script>
// Script para confirmación de actualización
(function(){
  const formEdit = document.getElementById('editForm');
  if (!formEdit) return;
  if (formEdit.dataset._editConfirmBound === 'true') return;
  formEdit.dataset._editConfirmBound = 'true';

  formEdit.addEventListener('submit', async function(e){
    e.preventDefault();
    
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

    // Validate required fields
    const titleField = document.getElementById('editTitle');
    const dateField = document.getElementById('editDate');
    const timeField = document.getElementById('editTime');
    const descriptionField = document.getElementById('editDescription');
    const statusField = document.getElementById('editStatus');

    let isValid = true;

    if (!titleField.value.trim()) {
        showFieldError('editTitle', 'El nombre del evento es obligatorio');
        isValid = false;
    } else {
        clearFieldError('editTitle');
    }

    if (!dateField.value) {
        showFieldError('editDate', 'La fecha es obligatoria');
        isValid = false;
    } else {
        clearFieldError('editDate');
    }

    if (!timeField.value) {
        showFieldError('editTime', 'La hora es obligatoria');
        isValid = false;
    } else {
        clearFieldError('editTime');
    }

    if (!descriptionField.value.trim()) {
        showFieldError('editDescription', 'La descripción es obligatoria');
        isValid = false;
    } else {
        clearFieldError('editDescription');
    }

    if (!statusField.value) {
        showFieldError('editStatus', 'Debe seleccionar un estado');
        isValid = false;
    } else {
        clearFieldError('editStatus');
    }

    if (!isValid) return;
    
    // Retry logic para esperar a que swConfirm esté disponible
    let maxRetries = 50;
    while (typeof window.swConfirm === 'undefined' && maxRetries > 0) {
      await new Promise(resolve => setTimeout(resolve, 100));
      maxRetries--;
    }
    
    let ok = false;
    try{
      if (window.swConfirm){
        const res = await swConfirm({
          title: '¿Actualizar evento?',
          text: 'Se actualizarán los datos del evento',
          icon: 'question',
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar'
        });
        ok = !!res?.isConfirmed;
      } else {
        ok = confirm('¿Estás seguro de actualizar el evento?');
      }
    } catch (err){ ok = confirm('¿Estás seguro de actualizar el evento?'); }

    if (ok === true) formEdit.submit();
  });

  // Real-time validation
  ['editTitle', 'editDate', 'editTime', 'editDescription', 'editStatus'].forEach(fieldId => {
      const field = document.getElementById(fieldId);
      if (field) {
          field.addEventListener('input', function() {
              if (this.value.trim()) {
                  const errorSpan = document.getElementById(fieldId + 'Error');
                  if (errorSpan) {
                      this.classList.remove('input-error');
                      errorSpan.style.display = 'none';
                  }
              }
          });
      }
  });
})();
</script>
