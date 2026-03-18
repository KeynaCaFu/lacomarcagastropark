<div id="addScheduleModal" class="schedule-modal" style="display:none;">
    <div class="schedule-modal-content" role="dialog" aria-modal="true" aria-labelledby="addScheduleModalTitle">
        <div class="schedule-modal-header">
            <h5 id="addScheduleModalTitle" style="margin: 0; font-weight: 800; color: #111827;">
                <i class="fas fa-plus" style="color: #e18018; margin-right: 8px;"></i>Agregar nuevo horario
            </h5>
            <button type="button" id="btnCloseAddScheduleModal" class="schedule-modal-close" aria-label="Cerrar">&times;</button>
        </div>

        <div class="schedule-modal-body">
            <form id="scheduleAddForm" method="POST" action="{{ route('local.schedule.store') }}" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px;">
                @csrf
                <div id="addScheduleInlineError" style="display:none; margin-bottom: 12px; background:#fee2e2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:10px 12px; font-size:13px; font-weight:600;"></div>

                <!-- Paso 1: Seleccionar Día -->
                <div style="margin-bottom: 16px;">
                    <label for="add_schedule_day" style="font-size: 12px; color: #374151; font-weight: 700; display: block; margin-bottom: 6px;">
                        <i class="fas fa-calendar-alt" style="color: #e18018; margin-right: 6px;"></i>Día de la semana
                    </label>
                    <select id="add_schedule_day" name="day_of_week" style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; color: #111827;">
                        <option value="">-- Selecciona un día --</option>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <!-- Paso 2: Seleccionar Estado -->
                <div style="margin-bottom: 16px;">
                    <label for="add_schedule_status" style="font-size: 12px; color: #374151; font-weight: 700; display: block; margin-bottom: 6px;">
                        <i class="fas fa-check-circle" style="color: #e18018; margin-right: 6px;"></i>Estado
                    </label>
                    <select id="add_schedule_status" name="status" style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px; color: #111827;">
                        <option value="">-- Selecciona un estado --</option>
                        <option value="1">🟢 Abierto</option>
                        <option value="0">🔴 Cerrado</option>
                    </select>
                </div>

                <!-- Paso 3: Horarios de Apertura y Cierre (Solo si está abierto) -->
                <div id="scheduleTimeFields" style="display: none;">
                    <div style="margin-bottom: 12px;">
                        <label for="add_schedule_opening" style="font-size: 12px; color: #374151; font-weight: 700; display: block; margin-bottom: 6px;">
                            <i class="fas fa-door-open" style="color: #10b981; margin-right: 6px;"></i>Hora de apertura
                        </label>
                        <input id="add_schedule_opening" type="time" name="opening_time" 
                               style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label for="add_schedule_closing" style="font-size: 12px; color: #374151; font-weight: 700; display: block; margin-bottom: 6px;">
                            <i class="fas fa-door-closed" style="color: #ef4444; margin-right: 6px;"></i>Hora de cierre
                        </label>
                        <input id="add_schedule_closing" type="time" name="closing_time" 
                               style="width:100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 12px; font-size: 14px;">
                        <small id="timeValidationMessage" style="display: none; margin-top: 6px; padding: 8px; border-radius: 6px; font-weight: 600;"></small>
                    </div>
                </div>

                <!-- Botón de envío -->
                <div style="display:flex; justify-content:flex-end; gap: 8px; margin-top: 16px;">
                    <button type="button" id="btnCancelAddSchedule" class="btn-schedule-action" 
                            style="background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db;">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-schedule-action btn-schedule-edit">
                        <i class="fas fa-check"></i> Guardar horario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-schedule-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 9px;
        border: none;
        padding: 9px 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-schedule-edit {
        background: #e18018;
        color: #ffffff;
    }

    .btn-schedule-edit:hover {
        background: #c9690f;
        transform: translateY(-2px);
    }

    .btn-schedule-action:hover {
        transform: translateY(-2px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addScheduleModal = document.getElementById('addScheduleModal');
    const btnOpenAddScheduleModal = document.getElementById('btnOpenAddScheduleModal');
    const btnCloseAddScheduleModal = document.getElementById('btnCloseAddScheduleModal');
    const btnCancelAddSchedule = document.getElementById('btnCancelAddSchedule');
    const scheduleAddForm = document.getElementById('scheduleAddForm');
    const statusSelect = document.getElementById('add_schedule_status');
    const daySelect = document.getElementById('add_schedule_day');
    const timeFieldsContainer = document.getElementById('scheduleTimeFields');
    const openingInput = document.getElementById('add_schedule_opening');
    const closingInput = document.getElementById('add_schedule_closing');
    const addScheduleInlineError = document.getElementById('addScheduleInlineError');
    const timeValidationMessage = document.getElementById('timeValidationMessage');

    function openAddScheduleModal() {
        if (!addScheduleModal) return;
        // Resetear el formulario
        scheduleAddForm.reset();
        timeFieldsContainer.style.display = 'none';
        addScheduleInlineError.style.display = 'none';
        addScheduleInlineError.textContent = '';
        // Limpiar validación de tiempo real
        if (timeValidationMessage) {
            timeValidationMessage.style.display = 'none';
        }
        if (closingInput) {
            closingInput.style.borderColor = '#d1d5db';
        }
        addScheduleModal.style.display = 'flex';
    }

    function closeAddScheduleModal() {
        if (!addScheduleModal) return;
        addScheduleModal.style.display = 'none';
    }

    function showAddScheduleInlineError(message) {
        if (!addScheduleInlineError) return;
        addScheduleInlineError.textContent = message;
        addScheduleInlineError.style.display = 'block';
    }

    function clearAddScheduleInlineError() {
        if (!addScheduleInlineError) return;
        addScheduleInlineError.textContent = '';
        addScheduleInlineError.style.display = 'none';
    }

    function validateTimeInputs() {
        if (!openingInput.value || !closingInput.value) {
            // Si uno de los campos está vacío, no mostrar mensaje
            if (timeValidationMessage) {
                timeValidationMessage.style.display = 'none';
            }
            return true;
        }

        const isValid = openingInput.value < closingInput.value;

        if (timeValidationMessage) {
            if (!isValid) {
                timeValidationMessage.textContent = '⚠️ La hora de cierre debe ser posterior a la de apertura';
                timeValidationMessage.style.display = 'block';
                timeValidationMessage.style.background = '#fef2f2';
                timeValidationMessage.style.color = '#7f1d1d';
                timeValidationMessage.style.border = '1px solid #fecaca';
                closingInput.style.borderColor = '#ef4444';
            } else {
                timeValidationMessage.style.display = 'none';
                closingInput.style.borderColor = '#d1d5db';
            }
        }

        return isValid;
    }

    // Mostrar/ocultar campos de horario según el estado
    function updateTimeFieldsVisibility() {
        const statusValue = statusSelect.value;
        const isOpen = statusValue === '1';

        if (isOpen) {
            timeFieldsContainer.style.display = 'block';
        } else {
            timeFieldsContainer.style.display = 'none';
            openingInput.value = '';
            closingInput.value = '';
        }
        clearAddScheduleInlineError();
    }

    // Event listeners
    if (btnOpenAddScheduleModal) {
        btnOpenAddScheduleModal.addEventListener('click', openAddScheduleModal);
    }

    if (btnCloseAddScheduleModal) {
        btnCloseAddScheduleModal.addEventListener('click', closeAddScheduleModal);
    }

    if (btnCancelAddSchedule) {
        btnCancelAddSchedule.addEventListener('click', closeAddScheduleModal);
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', updateTimeFieldsVisibility);
    }

    if (openingInput) {
        openingInput.addEventListener('change', validateTimeInputs);
        openingInput.addEventListener('input', validateTimeInputs);
    }

    if (closingInput) {
        closingInput.addEventListener('change', validateTimeInputs);
        closingInput.addEventListener('input', validateTimeInputs);
    }

    if (addScheduleModal) {
        addScheduleModal.addEventListener('click', function(event) {
            if (event.target === addScheduleModal) {
                closeAddScheduleModal();
            }
        });
    }

    // Validar y enviar el formulario
    if (scheduleAddForm) {
        scheduleAddForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            clearAddScheduleInlineError();

            const dayValue = daySelect.value;
            const statusValue = statusSelect.value;
            const openingValue = openingInput.value;
            const closingValue = closingInput.value;

            // Validar que se haya seleccionado un día
            if (!dayValue || dayValue.trim() === '') {
                showAddScheduleInlineError('Debes seleccionar un día de la semana.');
                return;
            }

            // Validar que se haya seleccionado un estado
            if (!statusValue || statusValue.trim() === '') {
                showAddScheduleInlineError('Debes seleccionar un estado.');
                return;
            }

            // Validar si el día ya existe en los horarios mostrados
            const existingScheduleCard = document.querySelector(`.schedule-card[data-day="${dayValue}"]`);
            if (existingScheduleCard) {
                showAddScheduleInlineError(`⚠️ El ${dayValue} ya tiene un horario configurado. Puedes editarlo directamente al dar click en la tarjeta.`);
                return;
            }

            const isOpen = statusValue === '1';

            if (isOpen) {
                if (!openingValue || !closingValue) {
                    showAddScheduleInlineError('Debes indicar hora de apertura y cierre para un día abierto.');
                    return;
                }

                // Validar que la hora de cierre sea posterior a la de apertura
                if (!validateTimeInputs()) {
                    showAddScheduleInlineError('La hora de apertura debe ser menor a la hora de cierre.');
                    return;
                }
            }

            if (window.swConfirm) {
                const result = await swConfirm({
                    title: 'Agregar nuevo horario',
                    text: `¿Desea agregar el horario para ${dayValue}?`,
                    icon: 'question',
                    confirmButtonText: 'Sí, agregar',
                    cancelButtonText: 'Cancelar'
                });
                if (!result.isConfirmed) return;
            } else {
                const ok = confirm(`¿Desea agregar el horario para ${dayValue}?`);
                if (!ok) return;
            }

            try {
                const formData = new FormData(scheduleAddForm);
                const response = await fetch(scheduleAddForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    const data = await response.json();
                    const error = new Error(data.message || 'Error desconocido');
                    error.status = response.status;
                    error.details = data;
                    throw error;
                }

                // Éxito
                closeAddScheduleModal();
                let retries = 0;
                const checkAndShowSuccess = () => {
                    if (window.swToast) {
                        swToast.fire({
                            icon: 'success',
                            title: 'Horario agregado correctamente',
                            timer: 2000,
                            timerProgressBar: true
                        });
                        // Recargar después de que se muestre el toast
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    } else if (retries < 50) {
                        retries++;
                        setTimeout(checkAndShowSuccess, 100);
                    }
                };
                setTimeout(checkAndShowSuccess, 100);
            } catch(error) {
                console.error(error);
                showAddScheduleInlineError(error.message || 'Ocurrió un error al guardar el horario.');
            }
        });
    }
});
</script>
