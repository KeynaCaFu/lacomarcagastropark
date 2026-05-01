// Real-time store schedule updates for plaza index page
function initPlazaIndexListeners(localIds) {
    if (!localIds || localIds.length === 0) return;

    localIds.forEach(localId => {
        window.Echo.channel(`establishment-updates.${localId}`)
            .listen('ScheduleUpdated', (data) => {
                updatePlazaIndexCard(data.local_id, data.schedules);
            });
    });

    function updatePlazaIndexCard(localId, schedules) {
        const card = document.querySelector(`.local-card-v2[data-local-id="${localId}"]`);
        if (!card) return;

        const chip = card.querySelector('.meta-chip.nowrap');
        if (!chip) return;

        const isOpen = isLocalCurrentlyOpen(schedules);
        const statusText = isOpen ? 'Abierto' : 'Cerrado';
        const statusClass = isOpen ? 'status-dot-open' : 'status-dot-closed';

        // Only update if status actually changed
        const currentStatus = chip.textContent.includes('Abierto');
        if (currentStatus !== isOpen) {
            chip.innerHTML = `<span class="status-dot ${statusClass}"></span> ${statusText}`;
        }
    }

    function isLocalCurrentlyOpen(schedules) {
        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const today = days[new Date().getDay()];
        const todaySchedule = schedules.find(s => s.day_of_week === today && s.status);

        if (!todaySchedule || !todaySchedule.opening_time || !todaySchedule.closing_time) {
            return false;
        }

        const now = new Date();
        const current = now.getHours() * 60 + now.getMinutes();
        const [oh, om] = todaySchedule.opening_time.split(':').map(Number);
        const [ch, cm] = todaySchedule.closing_time.split(':').map(Number);
        const openMinutes = oh * 60 + om;
        const closeMinutes = ch * 60 + cm;

        return current >= openMinutes && current < closeMinutes;
    }
}

window.initPlazaIndexListeners = initPlazaIndexListeners;
