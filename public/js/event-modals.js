// Modales AJAX para Eventos (ligero, carga partials via fetch)
class EventoModals {
    constructor(){
        this.cache = new Map();
        this.preloadTimeout = null;
        this.initGlobalListeners();
    }

    initGlobalListeners(){
        // cerrar al click fuera
        window.addEventListener('click', (ev) => {
            
            ['showModal','modalCreate','createModal','editModal'].forEach(id => {
                const modal = document.getElementById(id);
                if(modal && ev.target === modal) this.closeModal(id);
            });
        });
    document.addEventListener('keydown', (ev) => { if(ev.key === 'Escape'){ ['showModal','modalCreate','createModal','editModal'].forEach(id=>{ const m=document.getElementById(id); if(m && (m.style.display==='flex' || m.classList.contains && m.classList.contains('open'))) this.closeModal(id); }); } });
    }

    preloadModal(type, id){
        if(this.preloadTimeout) clearTimeout(this.preloadTimeout);
        this.preloadTimeout = setTimeout(()=>{ 
            const key = `${type}-${id}`; 
            if(!this.cache.has(key)) {
                this.fetchModalContent(type, id).catch(err => console.error('Preload fetch failed:', err));
            }
        }, 200);
    }

    async fetchModalContent(type, id){
        const key = `${type}-${id}`;
        if(this.cache.has(key)) return this.cache.get(key);
        try{
            // Usar nuevas rutas con prefijo modal- para evitar conflictos de routing
            const url = type === 'show' 
                ? `/eventos/modal-show/${id}`
                : `/eventos/modal-edit/${id}`;
            const res = await fetch(url, { 
                method: 'GET',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                } 
            });
            if(!res.ok){
                let serverMsg = '';
                try { serverMsg = await res.text(); } catch(_) {}
                const err = new Error(`HTTP ${res.status} ${res.statusText}`);
                err.details = serverMsg;
                err.url = url;
                throw err;
            }
            const html = await res.text();
            this.cache.set(key, html);
            return html;
        }catch(err){
            console.error('Error fetching event modal:', err);
            throw err;
        }
    }

    async openShowModal(id){
        const modal = document.getElementById('showModal');
        const content = document.getElementById('showModalContent');
        if(!modal || !content) return;
    modal.style.display = 'flex'; document.body.style.overflow='hidden';
        const key = `show-${id}`;
        if(this.cache.has(key)){ content.innerHTML = this.cache.get(key); return; }
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        try{
            const html = await this.fetchModalContent('show', id);
            content.innerHTML = html;
        } catch(e){
            content.innerHTML = `<div class="p-3 text-center text-danger">
                <div><strong>Error al cargar el modal.</strong></div>
                <div class="small">${e && e.message ? e.message : 'Error desconocido'}</div>
            </div>`;
        }
    }

    async openEditModal(id){
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        if(!modal || !content) return;
    modal.style.display = 'flex'; document.body.style.overflow='hidden';
        const key = `edit-${id}`;
        if(this.cache.has(key)){
            content.innerHTML = this.cache.get(key);
            this.initEditForm(content);
            return;
        }
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        try{
            const html = await this.fetchModalContent('edit', id);
            content.innerHTML = html;
            this.initEditForm(content);
        } catch(e){
            content.innerHTML = `<div class="p-3 text-center text-danger">
                <div><strong>Error al cargar el modal.</strong></div>
                <div class="small">${e && e.message ? e.message : 'Error desconocido'}</div>
            </div>`;
        }
    }

    // Inicializa handlers específicos del partial de edición que no ejecutan scripts inline al inyectar innerHTML
    initEditForm(container){
        if(!container) container = document.getElementById('editModalContent');
        if(!container) return;
        const form = container.querySelector('#editForm');
        if(!form) return;
        // evitar duplicar listeners
        if(form.dataset._editConfirmBound === 'true') return;
        form.dataset._editConfirmBound = 'true';

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            let ok = false;
            try{
                // Reutilizar las confirmaciones ya implementadas para Insumos si están disponibles
                if (window.insumoModals && typeof window.insumoModals.showConfirmDialog === 'function'){
                    ok = await window.insumoModals.showConfirmDialog(
                        '¿Estás seguro de actualizar el Evento?',
                        'Los cambios realizados se guardarán permanentemente.',
                        'Sí, actualizar',
                        'Cancelar'
                    );
                } else if (typeof window.swConfirm !== 'undefined'){
                    const res = await swConfirm({
                        title: '',
                        html: `<div class="swal-title-like">¿Estás seguro de actualizar el Evento?</div>`,
                        confirmButtonText: 'Sí, actualizar'
                    });
                    ok = res.isConfirmed === true;
                } else {
                    ok = confirm('¿Estás seguro de actualizar el evento?');
                }
            } catch(err){ ok = confirm('¿Estás seguro de actualizar el evento?'); }

            if(ok === true) form.submit();
        });
    }

    closeModal(id){ 
        const modal = document.getElementById(id); 
        if(!modal) return; 
        modal.style.display='none'; 
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow='auto'; 
    }

    openCreateModal(){
        const modal = document.getElementById('createModal');
        if(!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
}

// Exponer funciones globales para compatibilidad con views
document.addEventListener('DOMContentLoaded', function(){
    window.eventoModals = new EventoModals();
});

function openShowModal(id){ if(window.eventoModals) window.eventoModals.openShowModal(id); }
function openEditModal(id){ if(window.eventoModals) window.eventoModals.openEditModal(id); }
function preloadShowModal(id){ if(window.eventoModals) window.eventoModals.preloadModal('show', id); }
function preloadEditModal(id){ if(window.eventoModals) window.eventoModals.preloadModal('edit', id); }
function closeModal(id){ if(window.eventoModals) window.eventoModals.closeModal(id); }
function openCreateModal(){ if(window.eventoModals) window.eventoModals.openCreateModal(); }
