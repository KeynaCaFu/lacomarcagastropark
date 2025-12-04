@extends('layouts.app')

    
@section('title','Gestión de Eventos')


 {{-- Incluir estilos específicos para la gestión de eventos --}} 
@push('styles')
 <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="sage-main">
  <div class="events-bar">
    <h2 class="section-title">Gestión de Eventos</h2>
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
          <div class="status-badge" style="background:{{ $bg }}; color:{{ $fg }};">{{ $txt }}</div>
          @if($ev->location)
            <div class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $ev->location }}</div>
          @endif
          @if($ev->description)
            <p class="event-desc">{{ $ev->description }}</p>
          @endif
        </div>

        <div class="event-actions">
          <button type="button" class="btn btn-edit" onclick="event.stopPropagation(); openEditModal({{ $ev->event_id }});">
            <i class="fas fa-edit"></i> Editar
          </button>

          {{-- Botón Eliminar separado a la derecha --}}
    <button type="button"
      class="btn btn-danger btn-del push-right"
      data-id="{{ $ev->event_id }}"
      data-name="{{ $ev->title }}"
      onclick="event.stopPropagation();">
            <i class="fas fa-trash"></i> Eliminar
          </button>

          <form id="del-{{ $ev->event_id }}" action="{{ route('eventos.eliminar', $ev->event_id) }}" method="POST" style="display:none;">
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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    const res = await Swal.fire({
      title: '',
      html: `<div class="swal-title-like">¿Estas seguro de Guardar el Evento?</div>`,
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
      confirmButtonText: 'Sí, quiero',
      cancelButtonText: 'Cancelar'
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

    return Swal.fire({
      width: '100%',
      padding: 0,
      backdrop: `rgba(0,0,0,.40)`,
      customClass: { popup: 'sw-success-shell' },
      html: `
        <div style="display:flex; align-items:center; justify-content:center; padding:28px 12px;">
          <div class="sw-success-panel">
            <div class="sw-success-icon">✔</div>
            <div class="sw-success-text">Evento registrado exitosamente</div>
          </div>
        </div>
      `,
      showConfirmButton:false,
      timer: 1600
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
      // schedule the actual submission in confirmWithUndo and mark the row visually
      const formSubmit = () => { if (typeof onConfirmSubmit === 'function') onConfirmSubmit(); };
      const undo = () => { /* no-op here; original form will not be submitted until timer expires */ };
      // Show the warn notification with Undo button
      window.confirmWithUndo({ message: `Se eliminará: ${nombre || 'este evento'}`, delayMs: 5000, onConfirm: formSubmit, onUndo: undo });
      return Promise.resolve({ isConfirmed: true });
    }

    return Swal.fire({
      backdrop: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      customClass: { popup: 'sw-rounded' },
      html: `
        <div class="swal-title-like">¿Seguro que deseas eliminar<br><b>${nombre || 'este evento'}</b>?</div>
      `,
      showCancelButton: true,
      buttonsStyling: false,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: {
        popup: 'sw-rounded',
        confirmButton: 'sw-btn sw-btn-confirm',
        cancelButton: 'sw-btn sw-btn-cancel'
      }
    });
  }

  function showDeletedOK(){
    if (typeof window.showNotification === 'function'){
      try{ window.showNotification('success', 'Evento eliminado exitosamente'); return Promise.resolve(); }catch(e){}
    }
    return Swal.fire({
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
      timer: 1600
    });
  }

  // Atar confirmación a cada botón eliminar
  document.querySelectorAll('.btn-del').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.dataset.id;
      const name = btn.dataset.name || 'este evento';
      // If confirmWithUndo exists, it will handle scheduling the action and calling onConfirm
      if (typeof window.confirmWithUndo === 'function'){
        // Prevent immediate form submit; pass a callback that submits the form when the confirmWithUndo timer expires
        confirmDelete(name, () => document.getElementById('del-' + id).submit());
        return;
      }

      const res = await confirmDelete(name);
      if (res.isConfirmed) {
        document.getElementById('del-' + id).submit();
      }
    });
  });
</script>

{{-- ========= Mensajes desde el backend ========= --}}
@if(session('ok') === 'saved' || session('success'))
  <script> showSuccessSaved(); </script>
@endif

@if(session('ok') === 'deleted')
  <script> showDeletedOK(); </script>
@endif

@if (session('ok') === 'saved' || session('success'))
  <script> showSuccessSaved(); </script>
@endif

@if(session('ok') === 'deleted')
  <script> showDeletedOK(); </script>
@endif

{{-- Nota: no abrimos el modal de creación automáticamente; el usuario debe hacer click en "Nuevo Evento" --}}
{{-- Si quieres, podemos mostrar los errores en la parte superior en vez de abrir el modal. --}}
@if ($errors->any() && old('title'))
  <script>
    (function(){
      // Mostrar alerta con los errores pero sin abrir el modal automáticamente
      Swal.fire({
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
      Swal.fire({
        title: 'No se pudo guardar',
        html: @json(session('error')),
        icon: 'error',
        customClass: { popup: 'sw-rounded' }
      });
    })();
  </script>
@endif

{{-- ======= Parche de separación de acciones (solo aquí) ======= --}}
<style>
  .event-actions{
    display:flex;
    align-items:center;
    gap:16px !important;          /* más aire si hay varios botones a la izq */
  }
  .event-actions .push-right{
    margin-left:auto !important;  /* empuja Eliminar a la derecha */
  }
</styl>
@endsection
