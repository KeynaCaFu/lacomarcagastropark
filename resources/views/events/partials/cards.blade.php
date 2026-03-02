@php
use Carbon\Carbon;
$imgSrc = fn($e) => $e->image_url ? (str_starts_with($e->image_url,'http') ? $e->image_url : asset($e->image_url)) : asset('images/default.jpg');
$fmtFecha = fn($d) => $d ? Carbon::parse($d)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : '—';
$fmtHora  = fn($d) => $d ? Carbon::parse($d)->format('h:i A') : '—';
@endphp

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
