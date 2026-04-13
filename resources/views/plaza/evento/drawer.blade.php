<!-- Events Drawer (Reutilizable en index y show) -->
<div v-if="showEventsDrawer" class="eventos-drawer-overlay" @click="closeEventsDrawer"></div>
<div class="eventos-drawer" :class="{ 'active': showEventsDrawer }">
    <!-- Header -->
    <div class="eventos-drawer-header">
        <div>
            <h2 class="eventos-drawer-title">
                <i class="fas fa-calendar"></i> Eventos
            </h2>
            <p class="eventos-drawer-subtitle">Descubre los eventos a tu alrededor</p>
        </div>
        <button @click="closeEventsDrawer" class="drawer-close-btn">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Tabs -->
    <div class="eventos-drawer-tabs">
        <button @click="eventosTab = 'hoy'" :class="['drawer-tab-btn', { active: eventosTab === 'hoy' }]">
            <i class="fas fa-clock"></i> Hoy
            <span v-if="eventosHoy && eventosHoy.length > 0" class="drawer-tab-badge">@{{ eventosHoy.length }}</span>
        </button>
        <button @click="eventosTab = 'proximos'" :class="['drawer-tab-btn', { active: eventosTab === 'proximos' }]">
            <i class="fas fa-calendar-plus"></i> Próximos
            <span v-if="eventosProximos && eventosProximos.length > 0" class="drawer-tab-badge">@{{ eventosProximos.length }}</span>
        </button>
    </div>

    <!-- Content -->
    <div class="eventos-drawer-content">
        <!-- Hoy -->
        <div v-if="eventosTab === 'hoy'" class="drawer-eventos-list">
            <div v-if="!eventosHoy || eventosHoy.length === 0" class="drawer-empty">
                <i class="fas fa-calendar-xmark"></i>
                <p>No hay eventos hoy</p>
            </div>
            <div v-for="evento in eventosHoy" :key="evento.event_id" class="drawer-evento-item">
                <div class="drawer-evento-img">
                    <img :src="evento.image_url" :alt="evento.title" loading="lazy">
                </div>
                <div class="drawer-evento-info">
                    <h3 class="drawer-evento-title">@{{ evento.title }}</h3>
                    <p class="drawer-evento-desc">@{{ evento.description.substring(0, 80) }}...</p>
                    <div class="drawer-evento-meta">
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            @{{ new Date(evento.start_at).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }) }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-map-pin"></i>
                            @{{ evento.location }}
                        </span>
                    </div>
                </div>
                <button @click="detalleEvento(evento)" class="drawer-evento-view">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Próximos -->
        <div v-if="eventosTab === 'proximos'" class="drawer-eventos-list">
            <div v-if="!eventosProximos || eventosProximos.length === 0" class="drawer-empty">
                <i class="fas fa-calendar-xmark"></i>
                <p>No hay eventos próximos</p>
            </div>
            <div v-for="evento in eventosProximos" :key="evento.event_id" class="drawer-evento-item">
                <div class="drawer-evento-img">
                    <img :src="evento.image_url" :alt="evento.title" loading="lazy">
                </div>
                <div class="drawer-evento-info">
                    <h3 class="drawer-evento-title">@{{ evento.title }}</h3>
                    <p class="drawer-evento-desc">@{{ evento.description.substring(0, 80) }}...</p>
                    <div class="drawer-evento-meta">
                        <span class="meta-item">
                            <i class="fas fa-calendar"></i>
                            @{{ new Date(evento.start_at).toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' }) }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-map-pin"></i>
                            @{{ evento.location }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
