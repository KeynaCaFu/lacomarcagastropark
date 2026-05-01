import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Función para inicializar listeners de horario
window.initScheduleListener = function(localId) {
    if (!window.Echo) {
        console.error('✗ Echo no está disponible. Verifica que bootstrap.js se cargó correctamente.');
        return false;
    }

    try {
        console.log(`✓ Inicializando listener para local ID: ${localId}`);
        const channelName = `establishment-updates.${localId}`;
        console.log(`📡 Conectando a canal: ${channelName}`);

        window.Echo.channel(channelName)
            .listen('ScheduleUpdated', (data) => {
                console.log('✓ Evento ScheduleUpdated recibido:', data);
                if (data.schedules) {
                    updateScheduleDOM(data.schedules, data.local_id);
                    showScheduleToast();
                } else {
                    console.warn('⚠ Evento recibido pero sin datos de schedules');
                }
            })
            .error((error) => {
                console.error(`✗ Error en el canal ${channelName}:`, error);
            });

        console.log('✓ Listener de horarios inicializado exitosamente');
        return true;

    } catch (error) {
        console.error('✗ Error al inicializar listener:', error);
        return false;
    }
};

function updateScheduleDOM(schedules, localId) {
    console.log('📝 Actualizando DOM con horarios del día actual:', schedules);

    if (!schedules || !Array.isArray(schedules) || schedules.length === 0) {
        console.error('⚠ Schedules inválido, no es un array, o está vacío');
        return;
    }

    document.dispatchEvent(new CustomEvent('schedule-updated', {
        detail: { schedules, local_id: localId }
    }));

    console.log('✓ Evento CustomEvent "schedule-updated" dispatched para Vue');
    console.log(`✓ Emitiendo cambios para local_id=${localId}, día: ${schedules[0]?.day_of_week}`);
}

function showScheduleToast() {
    const toast = document.createElement('div');
    toast.textContent = 'Los horarios de atención han sido actualizados';
    toast.className = [
        'fixed bottom-5 right-5 z-50',
        'bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg',
        'opacity-0 transition-opacity duration-300',
    ].join(' ');

    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.opacity = '1'; });
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Función para inicializar listeners de estado de productos
window.initProductStatusListener = function(localId) {
    if (!window.Echo) {
        console.error('✗ Echo no disponible para ProductStatusListener');
        return false;
    }

    try {
        const channelName = `establishment-updates.${localId}`;
        console.log(`📦 Conectando listener de productos al canal: ${channelName}`);

        window.Echo.channel(channelName)
            .listen('ProductStatusUpdated', (data) => {
                console.log('✓ Evento ProductStatusUpdated recibido:', data);
                document.dispatchEvent(new CustomEvent('product-status-updated', {
                    detail: {
                        product_id:   data.product_id,
                        local_id:     data.local_id,
                        status:       data.status,
                        product_name: data.product_name,
                    }
                }));
            });

        console.log('✓ Listener de estado de productos inicializado');
        return true;

    } catch (error) {
        console.error('✗ Error al inicializar ProductStatusListener:', error);
        return false;
    }
};

// ── Listener para la vista index (múltiples locales) ──
window.initIndexScheduleListeners = function(localIds) {
    if (!window.Echo) return false;

    localIds.forEach(localId => {
        window.Echo.channel(`establishment-updates.${localId}`)
            .listen('ScheduleUpdated', (data) => {
                if (data.local_id && data.schedules) {
                    updateLocalStatusDot(data.local_id, data.schedules);
                    showScheduleToast();
                }
            });
    });

    return true;
};

function isLocalCurrentlyOpen(schedules) {
    const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const today = days[new Date().getDay()];
    const todaySchedule = schedules.find(s => s.day_of_week === today && s.status);

    if (!todaySchedule || !todaySchedule.opening_time || !todaySchedule.closing_time) return false;

    const now = new Date();
    const current = now.getHours() * 60 + now.getMinutes();
    const [oh, om] = todaySchedule.opening_time.split(':').map(Number);
    const [ch, cm] = todaySchedule.closing_time.split(':').map(Number);

    return current >= (oh * 60 + om) && current < (ch * 60 + cm);
}

function updateLocalStatusDot(localId, schedules) {
    const card = document.querySelector(`.local-card-v2[data-local-id="${localId}"]`);
    if (!card) return;

    const isOpen = isLocalCurrentlyOpen(schedules);
    const chip = card.querySelector('.meta-chip.nowrap');

    if (chip) {
        chip.innerHTML = `<span class="status-dot ${isOpen ? 'status-dot-open' : 'status-dot-closed'}"></span> ${isOpen ? 'Abierto' : 'Cerrado'}`;
    }

    // Notificar para actualizar el cache de horarios
    document.dispatchEvent(new CustomEvent('local-schedule-updated', {
        detail: { local_id: localId, schedules: schedules }
    }));
}
