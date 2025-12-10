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
          <input type="file" name="photo" accept="image/*" class="form-control mb-2">
          @php
            $imgUrl = null;
            if ($event->image_url) {
                if (str_starts_with($event->image_url, 'http')) {
                    $imgUrl = $event->image_url;
                } elseif (str_starts_with($event->image_url, 'storage/')) {
                    $imgUrl = asset($event->image_url);
                } else {
                    
                    $imgUrl = asset('storage/' . ltrim($event->image_url, '/'));
                }
            }
          @endphp

          @if($imgUrl)
            <div style="border-radius:8px; overflow:hidden; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.06);">
              <img src="{{ $imgUrl }}" alt="{{ $event->title }}" style="width:100%; height:140px; object-fit:cover; display:block;" onerror="this.style.display='none'">
            </div>
            <small class="text-muted d-block mt-2">Actualmente: <a href="{{ $imgUrl }}" target="_blank">Ver imagen</a></small>
          @else
            <div style="height:140px; background:#f8f8f8; border-radius:8px;"></div>
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

    <div class="modal-actions">
      <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancelar</button>
      <button type="submit" class="btn btn-update">Actualizar Evento</button>
    </div>
  </form>
</div>

<script>
// Inicializar confirmación de actualización de evento para el caso server-rendered.
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
      } else if (typeof Swal !== 'undefined'){
        const res = await Swal.fire({
          title: '',
          html: `<div class="swal-title-like">¿Estás seguro de actualizar el Evento?</div>`,
          backdrop: true,
          allowOutsideClick: false,
          allowEscapeKey: false,
          customClass: {
            popup: 'sw-rounded',
            confirmButton: 'sw-btn sw-btn-confirm',
            cancelButton: 'sw-btn sw-btn-cancel'
          },
          buttonsStyling: false,
          showCancelButton: true,
          confirmButtonText: 'Sí, actualizar',
          cancelButtonText: 'Cancelar'
        });
        ok = res.isConfirmed === true;
      } else {
        ok = confirm('¿Estás seguro de actualizar el evento?');
      }
    } catch (err){ ok = confirm('¿Estás seguro de actualizar el evento?'); }

    if (ok === true) formEdit.submit();
  });
})();
</script>
