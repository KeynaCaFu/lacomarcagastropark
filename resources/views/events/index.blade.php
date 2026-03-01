@extends('layouts.app')

    
@section('title','Gestión de Eventos')


 {{-- Incluir estilos específicos para la gestión de eventos --}} 
@push('styles')
 <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('css/modals.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div style="padding: 0 15px;">
<div class="sage-main">
  <div class="events-bar" style="justify-content: space-between; align-items: center;">
    <h1 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 12px;">
      <i class="fas fa-calendar-alt" style="color: #c9690f;"></i>
      Gestión de Eventos
    </h1>
    <button type="button" class="btn btn-new btn-lg" id="btnOpenCreate" onclick="openCreateModal()">
      <i class="fas fa-plus"></i> Nuevo Evento
    </button>
  </div>

  <!-- Filtros en acordeón -->
  <div class="filters-accordion">
    <button type="button" id="filtersToggle" class="filters-toggle" aria-expanded="false" aria-controls="filtrosBody">
      <i class="fas fa-chevron-down"></i>
      Filtros de búsqueda
    </button>
    <div id="filtrosBody" class="search-filter-group closed" role="region" aria-labelledby="filtersToggle">
      <input 
        type="text" 
        id="filterFecha"
        placeholder="Selecciona fecha..."
        class="filter-select"
        style="min-width: 150px; cursor: pointer;"
        readonly
      />

      <select id="filterEstado" class="filter-select" style="min-width: 150px;">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>

      <a href="javascript:void(0);" id="btnClear" class="btn-action" style="background: #e5e7eb; color: #374151; padding: 10px 20px; border-radius: 8px; display: none;">
        <i class="fas fa-redo" style="font-size: 13px;"></i> Limpiar
      </a>
    </div>
  </div>

  @php
    use Carbon\Carbon;
  @endphp

  <div class="cards-grid grid-separated" id="eventsContainer">
    @include('events.partials.cards', ['events' => $events])
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
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

  {{-- SweetAlert2 se carga globalmente desde el layout --}}
  <script>
    // ===== DEFINIR reattachEventListeners PRIMERO =====
    function reattachEventListeners() {
      // Re-vincular listeners de eliminación
      document.querySelectorAll('.btn-del').forEach(btn => {
        if (btn.dataset._deleteListener === 'true') return;
        btn.dataset._deleteListener = 'true';
        btn.addEventListener('click', async (e) => {
          const id = btn.dataset.id;
          const name = btn.dataset.name || 'este evento';
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

      // Re-vincular listeners de estado
      document.querySelectorAll('.status-toggler').forEach(badge => {
        if (badge.dataset._statusBound === 'true') return;
        badge.dataset._statusBound = 'true';
        
        badge.addEventListener('click', async (e) => {
          e.stopPropagation();
          
          const eventId = badge.dataset.eventId;
          const currentStatus = badge.dataset.currentStatus;
          const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
          const newStatusLabel = newStatus === 'Active' ? 'Activo' : 'Inactivo';
          const currentStatusLabel = badge.dataset.statusLabel;
          
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
            
            badge.dataset.currentStatus = newStatus;
            badge.dataset.statusLabel = newStatusLabel;
            
            const bgColor = newStatus === 'Active' ? '#eaf4e7' : '#fde2dd';
            const fgColor = newStatus === 'Active' ? '#1d3320' : '#7a1d12';
            badge.style.background = bgColor;
            badge.style.color = fgColor;
            badge.textContent = newStatusLabel === 'Activo' ? 'Activo' : 'Inactivo';
            
            badge.style.opacity = '1';
            badge.style.pointerEvents = 'auto';
            
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
    }

    // ===== MANEJO DE FILTROS =====
    function initFilters() {
      const filtersToggle = document.getElementById('filtersToggle');
      const filtersBody = document.getElementById('filtrosBody');
      const filterFecha = document.getElementById('filterFecha');
      const filterEstado = document.getElementById('filterEstado');
      const btnClear = document.getElementById('btnClear');

      if (!filterFecha || !filterEstado) {
        return;
      }

      // Función para cargar eventos con filtros
      const loadEvents = async () => {
        const fecha = filterFecha.dataset.value || '';
        const estado = filterEstado.value || '';

        try {
          const params = new URLSearchParams();
          if (fecha) params.append('fecha', fecha);
          if (estado) params.append('estado', estado);

          const url = `{{ route('eventos.index') }}?${params.toString()}`;

          const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });

          if (response.ok) {
            let html = await response.text();
            
            // Limpiar scripts y estilos del HTML recibido
            html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
            html = html.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');
            
            const eventsContainer = document.getElementById('eventsContainer');
            if (eventsContainer) {
              eventsContainer.innerHTML = html.trim();
              reattachEventListeners();
            }
          }
        } catch (error) {
          console.error('Error al cargar eventos:', error);
        }
      };

      // Mostrar/ocultar botón Limpiar
      const updateClearButton = () => {
        if (filterFecha.dataset.value || filterEstado.value) {
          btnClear.style.display = 'inline-flex';
        } else {
          btnClear.style.display = 'none';
        }
      };

      // Toggle acordeón
      filtersToggle?.addEventListener('click', () => {
        const isClosed = filtersBody.classList.contains('closed');
        if (isClosed) {
          filtersBody.classList.remove('closed');
          filtersToggle.classList.add('open');
          filtersToggle.setAttribute('aria-expanded', 'true');
        } else {
          filtersBody.classList.add('closed');
          filtersToggle.classList.remove('open');
          filtersToggle.setAttribute('aria-expanded', 'false');
        }
      });

      // Esperar a que flatpickr esté disponible
      let retries = 0;
      const initFlatpickr = () => {
        if (typeof flatpickr === 'undefined') {
          if (retries < 50) {
            retries++;
            setTimeout(initFlatpickr, 100);
          } else {
            console.error('flatpickr no se cargó correctamente');
          }
          return;
        }

        flatpickr(filterFecha, {
          locale: 'es',
          dateFormat: 'd/m/Y',
          allowInput: false,
          onChange: (selectedDates) => {
            if (selectedDates.length > 0) {
              const date = selectedDates[0];
              const year = date.getFullYear();
              const month = String(date.getMonth() + 1).padStart(2, '0');
              const day = String(date.getDate()).padStart(2, '0');
              filterFecha.dataset.value = `${year}-${month}-${day}`;
              updateClearButton();
              loadEvents();
            }
          },
          onClear: () => {
            filterFecha.dataset.value = '';
            updateClearButton();
            loadEvents();
          }
        });
      };

      initFlatpickr();

      // Estado select - filtrar automáticamente
      filterEstado.addEventListener('change', () => {
        updateClearButton();
        loadEvents();
      });

      // Botón Limpiar
      btnClear.addEventListener('click', (e) => {
        e.preventDefault();
        filterFecha.value = '';
        filterFecha.dataset.value = '';
        filterEstado.value = '';
        updateClearButton();
        loadEvents();
      });

      updateClearButton();
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFilters);
    } else {
      initFilters();
    }

    // Hook the "Nuevo Evento" button to the shared EventoModals API
    document.getElementById('btnOpenCreate')?.addEventListener('click', () => {
      if(typeof openCreateModal === 'function') openCreateModal();
    });

    // Inicializar listeners la primera vez
    document.addEventListener('DOMContentLoaded', reattachEventListeners);
  </script>

  {{-- El resto del código JavaScript global (no de filtros) --}}
  <script>
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
  document.addEventListener('DOMContentLoaded', function(){
    reattachEventListeners();
  });

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
    justify-content: space-between;
    align-items: center;
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
