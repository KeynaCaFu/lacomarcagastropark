<div id="scheduleDetailModal" class="schedule-modal" style="display:none;">
    <div class="schedule-modal-content" role="dialog" aria-modal="true" aria-labelledby="scheduleModalTitle">
        <div class="schedule-modal-header">
            <h5 id="scheduleModalTitle" style="margin: 0; font-weight: 800; color: #111827;">
                <i class="fas fa-calendar-day" style="color: #e18018; margin-right: 8px;"></i>
                <span id="modalDayName">Detalle del día</span>
            </h5>
            <button type="button" id="btnCloseScheduleModal" class="schedule-modal-close" aria-label="Cerrar">&times;</button>
        </div>

        <div class="schedule-modal-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px;">
                <div class="schedule-info-box">
                    <p class="schedule-info-label">Estado</p>
                    <p id="modalScheduleStatus" class="schedule-info-value">-</p>
                </div>
                <div class="schedule-info-box">
                    <p class="schedule-info-label">Abre</p>
                    <p id="modalScheduleOpen" class="schedule-info-value">-</p>
                </div>
                <div class="schedule-info-box">
                    <p class="schedule-info-label">Cierra</p>
                    <p id="modalScheduleClose" class="schedule-info-value">-</p>
                </div>
            </div>

            @if($canEditSchedule)
            <form id="scheduleEditForm" method="POST" action="#" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px;">
                @csrf
                @method('PUT')
                <div id="scheduleInlineError" style="display:none; margin-bottom: 12px; background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:10px 12px; font-size:13px; font-weight:600;"></div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px;">
                    <div>
                        <label for="edit_schedule_status" style="font-size: 12px; color: #374151; font-weight: 700;">Estado</label>
                        <select id="edit_schedule_status" name="status" style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px;">
                            <option value="1">Abierto</option>
                            <option value="0">Cerrado</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_schedule_opening" style="font-size: 12px; color: #374151; font-weight: 700;">Hora apertura</label>
                        <input id="edit_schedule_opening" type="time" name="opening_time" style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px;">
                    </div>
                    <div>
                        <label for="edit_schedule_closing" style="font-size: 12px; color: #374151; font-weight: 700;">Hora cierre</label>
                        <input id="edit_schedule_closing" type="time" name="closing_time" style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px;">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top: 12px;">
                    <button type="submit" class="btn-schedule-action btn-schedule-edit">
                        <i class="fas fa-pen"></i> Guardar cambios
                    </button>
                </div>
            </form>

            <form id="scheduleDeleteForm" method="POST" action="#" style="margin-top: 12px;">
                @csrf
                @method('DELETE')
                <button type="button" id="btnDeleteSchedule" class="btn-schedule-action btn-schedule-delete" style="width: 40%; justify-content: center;">
                    <i class="fas fa-trash"></i> Eliminar horario del día
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
