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
        document.addEventListener('keydown', (ev) => { 
            if(ev.key === 'Escape'){ 
                ['showModal','modalCreate','createModal','editModal'].forEach(id=>{ 
                    const m = document.getElementById(id); 
                    if(m && m.classList.contains('open')) this.closeModal(id); 
                }); 
            } 
        });
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
        
        if(this.cache.has(key)) {
            return this.cache.get(key);
        }
        
        try{
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
                console.error('[fetchModalContent] Error - Status:', res.status, 'URL:', url, 'Message:', serverMsg.substring(0, 200));
                throw err;
            }
            
            const html = await res.text();
            
            if(!html || html.trim().length === 0) {
                throw new Error('Respuesta vacía del servidor');
            }
            
            this.cache.set(key, html);
            return html;
        }catch(err){
            console.error('[fetchModalContent] Error capturado:', {
                type: err.constructor.name, 
                message: err.message, 
                url: err.url,
                type_param: type,
                id: id
            });
            throw err;
        }
    }

    async openShowModal(id){
        try {
            const modal = document.getElementById('showModal');
            const content = document.getElementById('showModalContent');
            
            if(!modal || !content) {
                console.error('[openShowModal] Elementos no encontrados');
                return;
            }
            
            const key = `show-${id}`;
            if(this.cache.has(key)){ 
                content.innerHTML = this.cache.get(key);
            } else {
                const html = await this.fetchModalContent('show', id);
                content.innerHTML = html;
            }
            
            // Mostrar el modal solo después de que el contenido esté completamente cargado
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        } catch(e){
            console.error('[openShowModal] Error:', e);
            const modal = document.getElementById('showModal');
            const content = document.getElementById('showModalContent');
            if(content) {
                content.innerHTML = `<div style="padding: 20px; text-align: center;">
                    <div style="color: #dc2626; font-weight: bold; margin-bottom: 10px;">Error al cargar el detalle del evento</div>
                    <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">${e && e.message ? e.message : 'Error desconocido'}</div>
                    <button type="button" onclick="closeModal('showModal')" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
                </div>`;
            }
            if(modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        }
    }

    async openEditModal(id){
        try {
            const modal = document.getElementById('editModal');
            const content = document.getElementById('editModalContent');
            
            if(!modal || !content) {
                console.error('[openEditModal] Elementos no encontrados - modal:', !!modal, 'content:', !!content);
                return;
            }
            
            const key = `edit-${id}`;
            if(this.cache.has(key)){
                content.innerHTML = this.cache.get(key);
                this.initEditForm(content);
            } else {
                const html = await this.fetchModalContent('edit', id);
                content.innerHTML = html;
                this.initEditForm(content);
            }
            
            // Mostrar el modal solo después de que el contenido esté completamente cargado
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        } catch(e){
            console.error('[openEditModal] Error:', e);
            const modal = document.getElementById('editModal');
            const content = document.getElementById('editModalContent');
            if(content) {
                content.innerHTML = `<div style="padding: 20px; text-align: center;">
                    <div style="color: #dc2626; font-weight: bold; margin-bottom: 10px;">Error al cargar el formulario de edición</div>
                    <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">${e && e.message ? e.message : 'Error desconocido'}</div>
                    <button type="button" onclick="closeModal('editModal')" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
                </div>`;
            }
            if(modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        }
    }

    // Inicializa handlers específicos del partial de edición que no ejecutan scripts inline al inyectar innerHTML
    initEditForm(container){
        if(!container) container = document.getElementById('editModalContent');
        if(!container) {
            console.error('[initEditForm] Container no encontrado');
            return;
        }
        
        const form = container.querySelector('#editForm');
        if(!form) {
            console.error('[initEditForm] Formulario #editForm no encontrado en container');
            return;
        }
        
        // evitar duplicar listeners
        if(form.dataset._editConfirmBound === 'true') {
            return;
        }
        
        form.dataset._editConfirmBound = 'true';

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            
            // Retry logic para esperar a que swConfirm esté disponible
            let maxRetries = 50;
            while (typeof window.swConfirm === 'undefined' && maxRetries > 0) {
                await new Promise(resolve => setTimeout(resolve, 100));
                maxRetries--;
            }
            
            let ok = false;
            try{
                if (typeof window.swConfirm !== 'undefined'){
                    const res = await swConfirm({
                        title: '¿Actualizar evento?',
                        text: 'Se actualizarán los datos del evento',
                        icon: 'question',
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    });
                    ok = res.isConfirmed === true;
                } else {
                    ok = confirm('¿Estás seguro de actualizar el evento?');
                }
            } catch(err){
                console.error('[editForm submit] Error en confirmación:', err);
                ok = confirm('¿Estás seguro de actualizar el evento?');
            }

            if(ok === true) {
                form.submit();
            }
        });
    }

    closeModal(id){ 
        const modal = document.getElementById(id); 
        if(!modal) return; 
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = 'auto';
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
let _eventoModalsInstance = null;

function ensureEventoModals() {
    if (!_eventoModalsInstance) {
        _eventoModalsInstance = new EventoModals();
    }
    return _eventoModalsInstance;
}

function openShowModal(id) { 
    ensureEventoModals().openShowModal(id).catch(err => console.error('[openShowModal] Error:', err));
}

function openEditModal(id) { 
    ensureEventoModals().openEditModal(id).catch(err => console.error('[openEditModal] Error:', err));
}

function preloadShowModal(id) { 
    ensureEventoModals().preloadModal('show', id);
}

function preloadEditModal(id) { 
    ensureEventoModals().preloadModal('edit', id);
}

function closeModal(id) { 
    ensureEventoModals().closeModal(id);
}

function openCreateModal() { 
    ensureEventoModals().openCreateModal();
}

// Inicializar INMEDIATAMENTE para que las funciones globales estén disponibles
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureEventoModals);
} else {
    ensureEventoModals();
}
