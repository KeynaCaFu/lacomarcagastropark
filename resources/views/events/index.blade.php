@extends('layouts.app')

    
@section('title','Gestión de Eventos')


 {{-- Incluir estilos específicos para la gestión de eventos --}} 
@push('styles')
 <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
@endpush

@section('content')
<div style="padding: 0 15px;">
<div class="sage-main">
  <div class="events-bar">
    {{-- <h2 class="section-title">Gestión de Eventos</h2> --}}
    <button type="button" class="btn btn-new btn-lg" id="btnOpenCreate" onclick="openCreateModal()">
      <i class="fas fa-plus"></i> Nuevo Evento
    </button>
  </div>

  <form method="GET" action="{{ route('eventos.index') }}" class="filters-row">
    <div class="filter-input with-icon">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre...">
      <i class="fas fa-search"></i>
    </div>
    <input type="date" name="fecha" value="{{ request('fecha') }}" class="filter-input date">
    <button class="btn btn-search btn-lg"><i class="fas fa-search"></i> Buscar</button>
    <a href="{{ route('eventos.index') }}" class="btn btn-clear btn-lg"><i class="fas fa-times"></i> Limpiar</a>
  </form>

  @php
    use Carbon\Carbon;
    $imgSrc = fn($e) => $e->image_url ? (str_starts_with($e->image_url,'http') ? $e->image_url : asset($e->image_url)) : asset('images/default.jpg');
    $fmtFecha = fn($d) => $d ? Carbon::parse($d)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : '—';
    $fmtHora  = fn($d) => $d ? Carbon::parse($d)->format('H:i') : '—';
  @endphp

  <div class="cards-grid grid-separated">

    @forelse($events as $ev)
      @php
        $txt = $ev->is_active ? 'Activo' : 'Inactivo';
        $bg  = $ev->is_active ? '#eaf4e7' : '#fde2dd';
        $fg  = $ev->is_active ? '#1d3320' : '#7a1d12';
      @endphp

  <article class="event-card fade-in" onmouseenter="preloadShowModal({{ $ev->event_id }})" onclick="openShowModal({{ $ev->event_id }})">
        <div class="event-thumb">
          <img src="{{ $imgSrc($ev) }}" alt="{{ $ev->title }}">
        </div>

        <div class="event-body">
          <h3 class="event-title">{{ $ev->title }}</h3>
          <div class="event-meta">
            <span><i class="far fa-calendar"></i> {{ $fmtFecha($ev->start_at) }}</span>
            <span><i class="far fa-clock"></i> {{ $fmtHora($ev->start_at) }}</span>
          </div>
          <div class="status-badge status-toggler" 
               data-event-id="{{ $ev->event_id }}"
               data-current-status="{{ $ev->is_active ? 'Active' : 'Inactive' }}"
               style="cursor: pointer; transition: all 0.3s ease; background:{{ $bg }}; color:{{ $fg }};"
               title="Haz clic para cambiar el estado"
               data-status-label="{{ $txt }}">
            {{ $txt }}
          </div>
          @if($ev->location)
            <div class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $ev->location }}</div>
          @endif
          @if($ev->description)
            <p class="event-desc">{{ $ev->description }}</p>
          @endif
        </div>

        <div class="event-actions">
          <button type="button" class="btn btn-edit" onclick="event.stopPropagation(); openEditModal({{ $ev->event_id }});" aria-label="Editar evento">
            <i class="fas fa-edit"></i>
          </button>

          {{-- Botón Eliminar separado a la derecha --}}
    <button type="button"
      class="btn btn-danger btn-del push-left"
      data-id="{{ $ev->event_id }}"
      data-name="{{ $ev->title }}"
      onclick="event.stopPropagation();" aria-label="Eliminar evento">
            <i class="fas fa-trash"></i>
          </button>

          <form id="del-{{ $ev->event_id }}" action="{{ route('eventos.eliminar', ['evento' => $ev->event_id]) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </article>
    @empty
      <div class="table-container">
        <p class="empty-row">No hay eventos para mostrar.</p>
      </div>
    @endforelse
  </div>
  </div>

  {{-- Paginación --}}
  <div style="margin-top:18px; display:flex; justify-content:center;">
    {{ $events->links() }}
  </div>

</div>

  @include('events._create_modal')

  {{-- Contenedores para modales AJAX (show / edit) --}}
  <div id="showModal" class="custom-modal" style="display:none;">
    <div class="modal-content" id="showModalContent"></div>
  </div>

  <div id="editModal" class="custom-modal" style="display:none;">
    <div class="modal-content" id="editModalContent"></div>
  </div>

  <script src="{{ asset('js/event-modals.js') }}"></script>

  {{-- SweetAlert2 se carga globalmente desde el layout --}}
  <script>
    // Hook the "Nuevo Evento" button to the shared EventoModals API. openCreateModal is exposed
    // by public/js/event-modals.js and will show the create modal that's rendered in the page.
    document.getElementById('btnOpenCreate')?.addEventListener('click', () => {
      if(typeof openCreateModal === 'function') openCreateModal();
    });

  async function showConfirmSave(){
    // Prefer using a lightweight confirm if available from supplies module
    if (typeof window.confirmWithUndo === 'function'){
      // confirmWithUndo is intended for deletion/undo flows; for save we use a simple JS confirm-like fallback
      return confirm('¿Estas seguro de Guardar el Evento?');
    }

    const res = await swConfirm({
      title: '',
      html: `<div class="swal-title-like">¿Estas seguro de Guardar el Evento?</div>`,
      confirmButtonText: 'Sí, quiero'
    });
    return res.isConfirmed === true;
  }

  function showSuccessSaved(){
    // If the supplies notification system is available, reuse it for consistent UI
    if (typeof window.showNotification === 'function'){
      try{
        window.showNotification('success', 'Evento registrado exitosamente');
        return Promise.resolve();
      }catch(e){
        // Fall through to Swal if something fails
      }
    }

    // Use retry logic to ensure swToast is available
    return new Promise((resolve) => {
      let retries = 0;
      const checkAndShowToast = () => {
        if (window.swToast) {
          swToast.fire({
            icon: 'success',
            title: 'Evento registrado exitosamente'
          });
          resolve();
        } else if (retries < 50) {
          retries++;
          setTimeout(checkAndShowToast, 100);
        } else {
          resolve(); // Give up after 5 seconds
        }
      };
      setTimeout(checkAndShowToast, 100);
    });
  }

  const formCreate = document.getElementById('formCreate');
  let submitting = false;
  formCreate?.addEventListener('submit', async function(e){
    if (submitting) return;
    e.preventDefault();
    const ok = await showConfirmSave();
    if(ok){
      submitting = true;
      this.submit();
    }
  });

  function confirmDelete(nombre, onConfirmSubmit){
    // If supplies confirmWithUndo is available, use it so the user can undo deletion
    if (typeof window.confirmWithUndo === 'function'){
      // First ask confirmation, then present undo toast
      return swConfirm({
        html: `<div class="swal-title-like">¿Seguro que deseas eliminar <b>${nombre || 'este evento'}</b>?</div>`,
        confirmButtonText: 'Sí, eliminar'
      }).then(r => {
        if (r.isConfirmed) {
          const formSubmit = () => { if (typeof onConfirmSubmit === 'function') onConfirmSubmit(); };
          const undo = () => {};
          window.confirmWithUndo({ message: `Se eliminará: ${nombre || 'este evento'}`, delayMs: 10000, onConfirm: formSubmit, onUndo: undo });
        }
        return r;
      });
    }

      return swConfirm({
      backdrop: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      customClass: { popup: 'sw-rounded' },
      html: `
        <div class="swal-title-like">¿Seguro que deseas eliminar<br><b>${nombre || 'este evento'}</b>?</div>
      `,
       confirmButtonText: 'Sí, eliminar'
    });
  }

  function showDeletedOK(){
    if (typeof window.showNotification === 'function'){
      try{ window.showNotification('success', 'Evento eliminado exitosamente'); return Promise.resolve(); }catch(e){}
    }
    
    // Use retry logic to ensure swAlert is available
    return new Promise((resolve) => {
      let retries = 0;
      const checkAndShowAlert = () => {
        if (window.swAlert) {
          swAlert({
            width: '100%',
            padding: 0,
            backdrop: `rgba(0,0,0,.40)`,
            customClass: { popup: 'sw-success-shell' },
            html: `
              <div style="display:flex; align-items:center; justify-content:center; padding:28px 12px;">
                <div class="sw-success-panel">
                  <div class="sw-success-icon">✔</div>
                  <div class="sw-success-text">Evento eliminado exitosamente</div>
                </div>
              </div>
            `,
            showConfirmButton:false,
            timer: 3000
          });
          resolve();
        } else if (retries < 50) {
          retries++;
          setTimeout(checkAndShowAlert, 100);
        } else {
          resolve();
        }
      };
      setTimeout(checkAndShowAlert, 100);
    });
  }

  // Atar confirmación a cada botón eliminar
  document.querySelectorAll('.btn-del').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.dataset.id;
      const name = btn.dataset.name || 'este evento';
      // If confirmWithUndo exists, it will handle scheduling the action and calling onConfirm
      // confirmDelete handles both undo toast and standard confirm
      if (typeof window.confirmWithUndo === 'function'){
        confirmDelete(name, () => document.getElementById('del-' + id).submit());
        return;
      }

      const res = await confirmDelete(name);
      if (res.isConfirmed) {
        document.getElementById('del-' + id).submit();
      }
    });
  });

  // Toggle event status
  (function(){
    document.querySelectorAll('.status-toggler').forEach(badge => {
      if (badge.dataset._statusBound === 'true') return;
      badge.dataset._statusBound = 'true';
      
      badge.addEventListener('click', async (e) => {
        e.stopPropagation(); // Prevenir que se abra el modal
        
        const eventId = badge.dataset.eventId;
        const currentStatus = badge.dataset.currentStatus;
        const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
        const newStatusLabel = newStatus === 'Active' ? 'Activo' : 'Inactivo';
        const currentStatusLabel = badge.dataset.statusLabel;
        
        // Show confirmation
        if (window.swConfirm) {
          const result = await swConfirm({
            title: 'Cambiar estado',
            html: `¿Cambiar de <b>${currentStatusLabel}</b> a <b>${newStatusLabel}</b>?`,
            icon: 'question',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
          });
          
          if (!result.isConfirmed) return;
        } else {
          const ok = confirm(`¿Cambiar de ${currentStatusLabel} a ${newStatusLabel}?`);
          if (!ok) return;
        }
        
        // Disable badge while updating
        badge.style.opacity = '0.5';
        badge.style.pointerEvents = 'none';
        
        try {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
          const response = await fetch(`/eventos/${eventId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: newStatus === 'Active' })
          });
          
          if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || 'Error al actualizar el estado');
          }
          
          // Update badge visually
          badge.dataset.currentStatus = newStatus;
          badge.dataset.statusLabel = newStatusLabel;
          
          const bgColor = newStatus === 'Active' ? '#eaf4e7' : '#fde2dd';
          const fgColor = newStatus === 'Active' ? '#1d3320' : '#7a1d12';
          badge.style.background = bgColor;
          badge.style.color = fgColor;
          badge.textContent = newStatusLabel === 'Activo' ? 'Activo' : 'Inactivo';
          
          // Restore opacity
          badge.style.opacity = '1';
          badge.style.pointerEvents = 'auto';
          
          // Show success toast
          let retries = 0;
          const checkAndShowSuccess = () => {
            if (window.swToast) {
              swToast.fire({
                icon: 'success',
                title: `Estado actualizado a ${newStatusLabel}`
              });
            } else if (retries < 50) {
              retries++;
              setTimeout(checkAndShowSuccess, 100);
            }
          };
          setTimeout(checkAndShowSuccess, 100);
          
        } catch (error) {
          console.error('Error:', error);
          badge.style.opacity = '1';
          badge.style.pointerEvents = 'auto';
          
          if (window.swAlert) {
            swAlert({
              icon: 'error',
              title: 'Error',
              text: error.message || 'No se pudo actualizar el estado',
              confirmButtonColor: '#dc2626'
            });
          } else {
            alert(error.message || 'No se pudo actualizar el estado');
          }
        }
      });
    });
  })();

</script>

{{-- ========= Mensajes desde el backend ========= --}}
@if(session('ok') === 'saved' || session('success'))
  <script> showSuccessSaved(); </script>
@endif

@if(session('ok') === 'deleted')
  <script> showDeletedOK(); </script>
@endif

@if ($errors->any() && old('title'))
  <script>
    (function(){
      // Mostrar alerta con los errores pero sin abrir el modal automáticamente
          swAlert({
        title: 'Problemas con el formulario',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        icon: 'error',
        customClass: { popup: 'sw-rounded' }
      });
    })();
  </script>
@elseif (session('error'))
  <script>
    (function(){
          swAlert({
        title: 'No se pudo guardar',
        html: @json(session('error')),
        icon: 'error',
        customClass: { popup: 'sw-rounded' }
      });
    })();
  </script>
@endif

<style>
  .event-actions{
    display:flex;
    align-items:center;
    gap:16px !important;          /* más aire si hay varios botones a la izq */
  }
  .event-actions .push-right{
    margin-left:auto !important;  /* empuja Eliminar a la derecha */
  }
  /* Mover el botón "Nuevo Evento" a la derecha */
  .events-bar{
    display:flex;
    justify-content:flex-end;
  }
  /* Colores de botones para igualar a Usuarios */
  .event-actions .btn-edit{
    background: transparent !important;
    color: #3e3d3a !important;
    border: 2px solid #43423f !important;
    padding: 0; /* usar tamaño compacto global */
    border-radius: 6px;
    font-weight: 500;
    width: 36px; height: 36px; font-size: 0; display:inline-flex; align-items:center; justify-content:center;
  }
  .event-actions .btn-edit:hover{
    background: #848380ec !important;
    color: #000 !important;
  }
  .event-actions .btn-del{
    background: transparent !important;
    color: #dc2626 !important;
    border: 2px solid #dc2626 !important;
    padding: 0; /* usar tamaño compacto global */
    border-radius: 6px;
    font-weight: 500;
    width: 36px; height: 36px; font-size: 0; display:inline-flex; align-items:center; justify-content:center;
  }
  .event-actions .btn-del:hover{
    background: #dc2626 !important;
    color: #fff !important;
  }

  /* Estilos para el estado clickeable */
  .status-badge.status-toggler {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    user-select: none;
    transition: all 0.3s ease;
  }

  .status-badge.status-toggler:hover {
    filter: brightness(0.90);
    transform: scale(1.05);
  }

  .status-badge.status-toggler:active {
    transform: scale(0.98);
  }
</style>
</div>
@endsection
