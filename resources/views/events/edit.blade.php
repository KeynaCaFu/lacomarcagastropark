@extends('layouts.app')
@section('title', 'Editar Evento')

@section('content')
<div id="modalEdit" class="sage-modal open" aria-hidden="false" style="z-index:9997;">
  <div class="modal-backdrop" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(3px); z-index:9997;">
    <div class="modal-card" style="max-width: 740px; width:90%; z-index:9998; background:#fdfdfc;" role="dialog" aria-modal="true">
      <div class="modal-header" style="background:#faf9f6;">
        <h3 class="modal-title">Editar Evento</h3>
        <a href="{{ route('eventos.index') }}" class="modal-close" aria-label="Cerrar">×</a>
      </div>

      @php
        $fecha   = optional($event->start_at)->format('Y-m-d');
        $hora    = optional($event->start_at)->format('H:i');
        $estado  = $event->is_active ? 'activo' : 'inactivo';
        $imgActual = $event->image_url
          ? (str_starts_with($event->image_url,'http') ? $event->image_url : asset($event->image_url))
          : asset('images/default.jpg');
      @endphp

      <form id="formEdit" action="{{ route('eventos.actualizar', ['evento' => $event->event_id]) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="modal-body">
          <div class="form-grid2">
            <div>
              <label class="label">Nombre del Evento *</label>
              <input type="text" class="field" name="title" required value="{{ old('title', $event->title) }}">

              <label class="label">Fecha *</label>
              <input type="date" class="field" name="date" required value="{{ old('date', $fecha) }}">

              <label class="label">Hora *</label>
              <input type="time" class="field" name="time" required value="{{ old('time', $hora) }}">
            </div>

            <div>
              <label class="label">Imagen (archivo opcional)</label>
              <input type="file" class="field" name="photo" id="photoInput" accept="image/*">
              <div style="color:#6f7a71; font-size:14px; margin-top:6px;">Formatos: JPG, PNG. Máx: 2MB</div>

              <div style="margin-top:10px; border:1px solid #d8d8d8; border-radius:10px; overflow:hidden;">
                <img id="previewEdit"
                     src="{{ $imgActual }}"
                     alt="Vista previa"
                     style="display:block; width:100%; max-height:160px; object-fit:cover; background:#eef2ef;">
              </div>

              <label class="label" style="margin-top:12px;">Estado *</label>
              <select class="field" name="status" required>
                <option value="activo"   {{ old('status',$estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('status',$estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
              </select>
            </div>
          </div>

          <label class="label" style="margin-top:10px;">Ubicación (opcional)</label>
          <input class="field" name="location" value="{{ old('location', $event->location) }}" placeholder="Ej. Plaza central">

          <label class="label" style="margin-top:10px;">Descripción *</label>
          <textarea name="description" class="field textarea" required placeholder="Describe el evento...">{{ old('description', $event->description) }}</textarea>
        </div>

        <!-- ESTANDARIZADO: mismo orden que Crear -->
        <div class="modal-footer" style="background:#faf9f6;">
          <button type="button" class="btn-modal-cancel" onclick="window.location='{{ route('eventos.index') }}'">
            Cancelar
          </button>
          <button type="submit" class="btn-modal-save">
            Actualizar Evento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Preview imagen
  const fileInput = document.getElementById('photoInput');
  const imgPrev   = document.getElementById('previewEdit');
  fileInput?.addEventListener('change', () => {
    const f = fileInput.files?.[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = e => imgPrev.src = e.target.result;
    reader.readAsDataURL(f);
  });

  // Confirmación SweetAlert (orden estandarizado: Cancelar -> Confirmar)
  const formEdit = document.getElementById('formEdit');
  formEdit?.addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
      backdrop: true,
      background:'#f8f6f2',
      allowOutsideClick: false,
      allowEscapeKey: false,
      customClass: { popup: 'sw-rounded' },
      html: `
        <div class="swal-title-like">¿Deseas actualizar este evento?</div>
        <div class="sw-btns" style="display:flex; gap:12px; justify-content:center;">
          <button type="button" class="sw-btn sw-btn-cancel" id="btnCancel">Cancelar</button>
          <button type="button" class="sw-btn sw-btn-confirm" id="btnOk">Sí, actualizar</button>
        </div>
      `,
      didOpen: el => {
        el.querySelector('#btnCancel').addEventListener('click', ()=>Swal.close());
        el.querySelector('#btnOk').addEventListener('click', ()=>Swal.close({isConfirmed:true}));
      },
      showConfirmButton:false,
      showCancelButton:false
    }).then(r => { if(r.isConfirmed) this.submit(); });
  });

  // Éxito tras actualizar (igual que lo tenías)
  @if(session('ok') === 'saved')
  Swal.fire({
    width: '100%',
    padding: 0,
    backdrop: `rgba(0,0,0,.40)`,
    customClass: { popup: 'sw-success-shell' },
    html: `
      <div style="display:flex; align-items:center; justify-content:center; padding:28px 12px;">
        <div class="sw-success-panel">
          <div class="sw-success-icon">✔</div>
          <div class="sw-success-text">Evento actualizado con éxito</div>
        </div>
      </div>
    `,
    showConfirmButton:false,
    timer:1700
  });
  @endif
</script>
@endsection
