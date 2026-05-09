import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Desbloquear Audio Context con la primera interacción del usuario
let audioCtxUnlocked = false;
document.addEventListener('click', function() {
    if (!audioCtxUnlocked) {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        ctx.resume().then(() => {
            audioCtxUnlocked = true;
            console.log('✓ Audio desbloqueado');
        });
    }
}, { once: false });

// Función para inicializar listeners de horario
window.initScheduleListener = function(localId) {
    if (!window.Echo) {
        console.error('✗ Echo no está disponible. Verifica que bootstrap.js se cargó correctamente.');
        return false;
    }

    try {
        console.log(`✓ Inicializando listener para local ID: ${localId}`);
        const channelName = `establishment-updates.${localId}`;
        console.log(`Conectando a canal: ${channelName}`);

        window.Echo.channel(channelName)
            .listen('ScheduleUpdated', (data) => {
                console.log('✓ Evento ScheduleUpdated recibido:', data);
                if (data.schedules) {
                    updateScheduleDOM(data.schedules, data.local_id);
                    showScheduleToast();
                } else {
                    console.warn('Evento recibido pero sin datos de schedules');
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
    console.log('Actualizando DOM con horarios del día actual:', schedules);

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
        console.log(`Conectando listener de productos al canal: ${channelName}`);

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

// Listener de eventos publicados en tiempo real
window.initEventListener = function() {
    if (!window.Echo) {
        console.error('✗ Echo no disponible para EventListener');
        return false;
    }

    try {
        console.log('Conectando listener de eventos al canal: public-events');

        window.Echo.channel('public-events')
            .listen('EventSynced', (data) => {
                console.log('✓ EventSynced recibido:', data);
                if (typeof window.syncEventoInDrawer === 'function') {
                    window.syncEventoInDrawer(data);
                } else {
                    document.dispatchEvent(new CustomEvent('evento-synced', { detail: data }));
                }
            });

        console.log('✓ Listener de eventos inicializado');
        return true;

    } catch (error) {
        console.error('✗ Error al inicializar EventListener:', error);
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

    document.dispatchEvent(new CustomEvent('local-schedule-updated', {
        detail: { local_id: localId, schedules: schedules }
    }));
}

// ── Listener de estado de orden (canal privado para cliente) ──
window.initOrderStatusListener = function(orderId) {
    if (!window.Echo) {
        console.error('✗ Echo no disponible para OrderStatusListener');
        return false;
    }

    try {
        const channelName = `order.${orderId}`;
        console.log(`[OrderListener] Conectando a canal privado: ${channelName}`);

        const subscription = window.Echo.private(channelName)
            .listen('OrderStatusUpdated', (data) => {
                console.log(`[OrderListener] ✅ Evento recibido en ${channelName}:`, {
                    order_id: data.order_id,
                    status: data.status,
                    updated_at: data.updated_at,
                });
                
                // Disparar evento personalizado
                document.dispatchEvent(new CustomEvent('order-status-updated', {
                    detail: {
                        order_id:   data.order_id,
                        status:     data.status,
                        updated_at: data.updated_at,
                    }
                }));
            })
            .error((status, message) => {
                console.error(`[OrderListener]  Error de autorización en ${channelName}:`, {
                    status: status,
                    message: message,
                });
            });

        console.log(`[OrderListener] ✓ Listener de orden ${orderId} inicializado`);
        
        // Guardar referencia para debugging
        window._orderListeners = window._orderListeners || {};
        window._orderListeners[orderId] = subscription;
        
        return true;

    } catch (error) {
        console.error(`[OrderListener] ✗ Error al inicializar:`, error);
        console.log('Stack trace:', error.stack);
        return false;
    }
};

// ── Listener de reseñas para notificación al gerente ──
window.initReviewListener = function(localId) {
    if (!window.Echo) {
        console.error('✗ Echo no disponible para ReviewListener');
        return false;
    }

    try {
        const channelName = `local.${localId}`;
        console.log(`⭐ Conectando listener de reseñas al canal: ${channelName}`);

        window.Echo.channel(channelName)
            .listen('NewReviewPosted', (data) => {
                console.log('✓ Evento NewReviewPosted recibido:', data);
                mostrarToastReview(data);
            });

        console.log('✓ Listener de reseñas inicializado');
        return true;

    } catch (error) {
        console.error('✗ Error al inicializar ReviewListener:', error);
        return false;
    }
};

// ── Listener de nuevas órdenes para notificación al gerente ──
window.initOrderListener = function(localId) {
    if (!window.Echo) {
        console.error('✗ Echo no disponible para OrderListener');
        return false;
    }

    try {
        const channelName = `orders.${localId}`;
        console.log(`🛒 Conectando listener de órdenes al canal: ${channelName}`);

        window.Echo.channel(channelName)
            .listen('NewOrderPlaced', (data) => {
                console.log('✓ Evento NewOrderPlaced recibido:', data);
                playOrderSound();
                mostrarToastOrden(data);
                if (typeof window.loadPendingOrdersNotif === 'function') {
                    window.loadPendingOrdersNotif();
                }
                agregarTarjetaOrden(data);
            })
            .listen('OrderCancelled', (data) => {
                console.log('✓ Evento OrderCancelled recibido:', data);
                playCancelSound();
                mostrarToastCancelacion(data);
                if (typeof window.loadPendingOrdersNotif === 'function') {
                    window.loadPendingOrdersNotif();
                }
                actualizarTarjetaCancelada(data);
            });

        console.log('✓ Listener de órdenes inicializado');
        return true;

    } catch (error) {
        console.error('✗ Error al inicializar OrderListener:', error);
        return false;
    }
};

function playOrderSound() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        audioCtx.resume().then(() => {
            [0, 0.22, 0.44].forEach(offset => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime + offset);
                gain.gain.setValueAtTime(0.5, audioCtx.currentTime + offset);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + offset + 0.18);
                osc.start(audioCtx.currentTime + offset);
                osc.stop(audioCtx.currentTime + offset + 0.18);
            });
        });
    } catch(e) {
        console.log('Audio no disponible:', e);
    }
}

function mostrarToastOrden(data) {
    if (window.swToast) {
        window.swToast.fire({
            icon: 'info',
            title: '🛒 Nueva orden recibida',
            text: `${data.order_number} — ${data.customer_name}`,
            timer: 8000,
        });
    }
}

function agregarTarjetaOrden(data) {
    const ordersList = document.querySelector('.orders-grid-list');
    if (!ordersList) return;

    const amount = parseFloat(data.total_amount).toLocaleString('es-CR', { minimumFractionDigits: 2 });
    const itemCount = data.items ? data.items.length : data.quantity;
    const timeStr = data.time ? String(data.time).substring(0, 5) : new Date().toTimeString().substring(0, 5);

    const card = document.createElement('div');
    card.className = 'order-card-item';
    card.dataset.orderId = data.order_id;
    card.dataset.status = 'Pending';
    card.dataset.userRoleId = '3';
    card.style.animation = 'fadeInDown 0.4s ease';
    card.innerHTML = `
        <div class="order-card-header">
            <div>
                <div class="order-card-number">${data.order_number}</div>
                <div class="order-card-customer">
                    <span style="display:inline-block;background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;margin-right:6px;">Cliente</span>
                    <span style="font-size:12px;color:#666;">${data.customer_name}</span>
                </div>
            </div>
            <div class="order-card-time">${timeStr}</div>
        </div>
        <div class="order-card-status">
            <span class="status-badge status-pending status-badge-clickable" data-order-id="${data.order_id}" style="cursor:pointer;position:relative;">
                <i class="fas fa-hourglass-start"></i>
                Pendiente
                <i class="fas fa-chevron-down" style="margin-left:6px;font-size:10px;"></i>
                <div class="status-dropdown" style="display:none;position:absolute;top:100%;left:0;margin-top:8px;background:white;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:1000;min-width:180px;">
                    <button type="button" class="status-dropdown-item status-dropdown-item-Preparing" data-status="Preparing">
                        <i class="fas fa-fire"></i> En Preparación
                    </button>
                    <button type="button" class="status-dropdown-item status-dropdown-item-Cancelled" data-status="Cancelled">
                        <i class="fas fa-times-circle"></i> Cancelado
                    </button>
                </div>
            </span>
        </div>
        <div class="order-card-footer">
            <div class="order-card-amount">₡${amount}</div>
            <div class="order-card-items">${itemCount} items</div>
        </div>
    `;

    // Si el tab activo no es Pending, ocultar la tarjeta
    const activeTab = document.querySelector('.order-tab.active');
    if (activeTab && activeTab.dataset.status !== 'Pending') {
        card.style.display = 'none';
    }

    // Reemplazar estado vacío si existe
    const emptyState = ordersList.parentElement?.querySelector('[style*="text-align: center"]');
    if (emptyState) emptyState.remove();

    // Insertar al inicio de la lista
    ordersList.insertBefore(card, ordersList.firstChild);

    // Click para cargar detalles (usa función global expuesta desde el blade)
    card.addEventListener('click', function() {
        document.querySelectorAll('.order-card-item').forEach(el => el.classList.remove('active'));
        this.classList.add('active');
        if (typeof window.loadOrderDetails === 'function') {
            window.loadOrderDetails(data.order_id);
        }
    });

    // Actualizar contador en tab Pending
    const pendingTabCount = document.querySelector('.order-tab[data-status="Pending"] .order-tab-count');
    if (pendingTabCount) {
        pendingTabCount.textContent = (parseInt(pendingTabCount.textContent) || 0) + 1;
    }

    // Actualizar stat card Total y Pendientes (primeras dos)
    const statNumbers = document.querySelectorAll('.order-stat-number');
    if (statNumbers[0]) statNumbers[0].textContent = (parseInt(statNumbers[0].textContent) || 0) + 1;
    if (statNumbers[1]) statNumbers[1].textContent = (parseInt(statNumbers[1].textContent) || 0) + 1;
}

function playCancelSound() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        audioCtx.resume().then(() => {
            // Tono descendente para cancelación
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(600, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(300, audioCtx.currentTime + 0.4);
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.4);
        });
    } catch(e) {
        console.log('Audio no disponible:', e);
    }
}

function mostrarToastCancelacion(data) {
    if (window.swToast) {
        window.swToast.fire({
            icon: 'warning',
            title: '❌ Orden cancelada por el cliente',
            text: `${data.order_number} — ${data.customer_name}`,
            timer: 8000,
        });
    }
}

function actualizarTarjetaCancelada(data) {
    const card = document.querySelector(`.order-card-item[data-order-id="${data.order_id}"]`);
    if (!card) return;

    // Actualizar el estado interno de la tarjeta
    card.dataset.status = 'Cancelled';

    // Actualizar el badge de estado
    const badge = card.querySelector('.status-badge');
    if (badge) {
        badge.className = 'status-badge status-cancelled';
        badge.style.cursor = 'default';
        badge.innerHTML = `<i class="fas fa-times-circle"></i> Cancelado`;
    }

    // Ocultar o mostrar según el tab activo
    const activeTab = document.querySelector('.order-tab.active');
    const activeStatus = activeTab ? activeTab.dataset.status : null;
    card.style.display = (activeStatus === 'Cancelled') ? 'block' : 'none';

    // Actualizar contadores: Pending -1, Cancelled +1
    const pendingTabCount = document.querySelector('.order-tab[data-status="Pending"] .order-tab-count');
    if (pendingTabCount) {
        pendingTabCount.textContent = Math.max(0, (parseInt(pendingTabCount.textContent) || 0) - 1);
    }

    const cancelledTabCount = document.querySelector('.order-tab[data-status="Cancelled"] .order-tab-count');
    if (cancelledTabCount) {
        cancelledTabCount.textContent = (parseInt(cancelledTabCount.textContent) || 0) + 1;
    }

    // Stat card Pendientes -1
    const statNumbers = document.querySelectorAll('.order-stat-number');
    if (statNumbers[1]) {
        statNumbers[1].textContent = Math.max(0, (parseInt(statNumbers[1].textContent) || 0) - 1);
    }
}

// Auto-registro de listeners para órdenes activas en la página de historial.
// app.js corre como módulo diferido, DESPUÉS de que el inline script montó Vue
// y estableció los atributos data-status, por lo que el DOM ya está listo.
(function registerActiveOrderListeners() {
    const activeStatuses = ['Pending', 'Preparing', 'Ready'];
    document.querySelectorAll('.order-card[data-order-id]').forEach(function (card) {
        const orderId = parseInt(card.getAttribute('data-order-id'), 10);
        const status  = card.getAttribute('data-status');
        if (orderId && activeStatuses.includes(status)) {
            window.initOrderStatusListener(orderId);
        }
    });
})();

function mostrarToastReview(data) {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        audioCtx.resume().then(() => {
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime);
            oscillator.frequency.setValueAtTime(600, audioCtx.currentTime + 0.15);
            oscillator.frequency.setValueAtTime(900, audioCtx.currentTime + 0.3);
            gainNode.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.5);
        });
    } catch(e) {
        console.log('Audio no disponible:', e);
    }

    if (window.swToast) {
        window.swToast.fire({
            icon: 'info',
            title: '⭐ Nueva reseña',
            text: `${data.client_name} dejó una reseña en ${data.product_name}`,
            timer: 6000,
        });
    }
}