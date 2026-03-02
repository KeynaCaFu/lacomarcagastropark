<div class="modal-header" style="position: relative;">
  <h4><i class="fas fa-info-circle" style="color: #c9690f; margin-right: 10px;"></i>{{ $event->title }}</h4>
  <button type="button" onclick="closeModal('showModal')" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 28px; color: #999; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">&times;</button>
</div>

<div class="modal-body">
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; max-width: 742px; padding: 4px;">
    <!-- Foto izquierda -->
    @if($event->image_url)
      <div style="text-align: center;">
        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" style="width: 100%; max-width: 320px; max-height: 400px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); object-fit: cover;">
      </div>
    @endif

    <!-- Fecha, ubicación y descripción derecha -->
    <div>
      <div style="margin-bottom: 16px;">
        <p style="margin: 0 0 8px; font-weight: 600; color: #374151; font-size: 14px;"><i class="far fa-calendar" style="color: #c9690f; margin-right: 8px;"></i>Fecha</p>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">{{ optional($event->start_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') ?? '—' }}</p>
      </div>

      <div style="margin-bottom: 20px;">
        <p style="margin: 0 0 8px; font-weight: 600; color: #374151; font-size: 14px;"><i class="far fa-clock" style="color: #c9690f; margin-right: 8px;"></i>Hora de inicio</p>
        <p style="margin: 0; color: #6b7280; font-size: 14px;">{{ optional($event->start_at)->format('h:i A') ?? '—' }}</p>
      </div>

      @if($event->location)
        <div style="margin-bottom: 16px;">
          <p style="margin: 0 0 8px; font-weight: 600; color: #374151; font-size: 14px;"><i class="fas fa-map-marker-alt" style="color: #c9690f; margin-right: 8px;"></i>Ubicación</p>
          <p style="margin: 0; color: #6b7280; font-size: 14px;">{{ $event->location }}</p>
        </div>
      @endif

      @if($event->description)
        <div>
          <p style="margin: 0 0 8px; font-weight: 600; color: #374151; font-size: 14px;"><i class="fas fa-align-left" style="color: #c9690f; margin-right: 8px;"></i>Descripción</p>
          <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6; max-height: 320px; overflow-y: auto;">{!! nl2br(e($event->description)) !!}</p>
        </div>
      @endif
    </div>
  </div>

  <style>
    @media (max-width: 768px) {
      .modal-content .modal-body > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
      }
    }
  </style>
</div>
