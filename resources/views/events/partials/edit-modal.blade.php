<div class="modal-header">
  <h3 class="modal-title"><i class="fas fa-edit"></i> Editar Evento</h3>
  <button type="button" class="close" aria-label="Cerrar" onclick="closeModal('editModal')">&times;</button>
</div>

<div class="modal-body">
  <form id="editForm" action="{{ route('eventos.actualizar', ['evento' => $event->event_id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
      <div class="col-lg-8">
        <div class="mb-3">
          <label class="form-label">Nombre del Evento *</label>
          <input type="text" name="title" class="form-control" value="{{ $event->title }}" required maxlength="255">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Fecha *</label>
            <input type="date" name="date" class="form-control" value="{{ optional($event->start_at)->format('Y-m-d') }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Hora *</label>
            <input type="time" name="time" class="form-control" value="{{ optional($event->start_at)->format('H:i') }}" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Ubicación (opcional)</label>
          <input type="text" name="location" class="form-control" value="{{ $event->location }}" maxlength="255" placeholder="Ej. Plaza central">
        </div>

        <div class="mb-3">
          <label class="form-label">Descripción *</label>
          <textarea name="description" class="form-control" rows="5" required>{{ $event->description }}</textarea>
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
          <label class="form-label">Estado *</label>
          <select name="status" class="form-select" required>
            <option value="activo" {{ $event->is_active ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ !$event->is_active ? 'selected' : '' }}>Inactivo</option>
          </select>
        </div>

      </div>
    </div>

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
// Script original para confirmación y validación
(function(){
// Si el partial se carga vía AJAX, la función initEditForm de event-modals.js ya se encargará.
(function(){
  const formEdit = document.getElementById('editForm');
  if (!formEdit) return;
  if (formEdit.dataset._editConfirmBound === 'true') return;
  formEdit.dataset._editConfirmBound = 'true';

  formEdit.addEventListener('submit', async function(e){
    e.preventDefault();
    let ok = false;
    try{
      if (window.insumoModals && typeof window.insumoModals.showConfirmDialog === 'function'){
        ok = await window.insumoModals.showConfirmDialog(
          '¿Estás seguro de actualizar el Evento?',
          'Los cambios realizados se guardarán permanentemente.',
          'Sí, actualizar',
          'Cancelar'
        );
      } else if (window.swConfirm){
        const res = await swConfirm({
          html: '<div class="swal-title-like">¿Estás seguro de actualizar el Evento?</div>',
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

  // Mostrar SweetAlerts para errores/success cuando el partial se renderiza desde servidor
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
  } catch (e) { /* noop */ }
})();
</script>
