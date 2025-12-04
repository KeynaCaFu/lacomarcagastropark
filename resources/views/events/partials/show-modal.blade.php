<div class="p-3">
  <h4 class="mb-2">{{ $event->title }}</h4>
  <div class="mb-2 text-muted small">
    <i class="far fa-calendar"></i>
    {{ optional($event->start_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') ?? '—' }}
    &nbsp;&middot;&nbsp;
    <i class="far fa-clock"></i>
    {{ optional($event->start_at)->format('H:i') ?? '—' }}
  </div>

  @if($event->image_url)
    <div class="mb-3 text-center">
      <img src="{{ str_starts_with($event->image_url,'http') ? $event->image_url : asset($event->image_url) }}" alt="{{ $event->title }}" style="max-width:100%; height:auto; border-radius:6px;">
    </div>
  @endif

  @if($event->location)
    <p><strong>Ubicación:</strong> {{ $event->location }}</p>
  @endif

  @if($event->description)
    <div class="mb-3"><strong>Descripción:</strong>
      <div class="mt-1 text-muted">{!! nl2br(e($event->description)) !!}</div>
    </div>
  @endif

  <div class="d-flex justify-content-end gap-2">
    <button class="btn btn-secondary btn-sm" onclick="closeModal('showModal')">Cerrar</button>
    <button class="btn btn-primary btn-sm" onclick="openEditModal({{ $event->event_id }})">Editar</button>
  </div>
</div>
