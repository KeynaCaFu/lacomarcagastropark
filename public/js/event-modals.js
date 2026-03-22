// Modales AJAX para Eventos (ligero, carga partials via fetch)
class EventoModals {
    constructor(){
        console.log('[EventoModals] Constructor iniciado');
        this.cache = new Map();
        this.preloadTimeout = null;
        this.initGlobalListeners();
        console.log('[EventoModals] Constructor completado');
    }

    initGlobalListeners(){
        // cerrar al click fuera
        window.addEventListener('click', (ev) => {
            
            ['showModal','modalCreate','createModal','editModal'].forEach(id => {
                const modal = document.getElementById(id);
                if(modal && ev.target === modal) this.closeModal(id);
            });
        });
    document.addEventListener('keydown', (ev) => { if(ev.key === 'Escape'){ ['showModal','modalCreate','createModal','editModal'].forEach(id=>{ const m=document.getElementById(id); if(m && m.classList.contains('open')) this.closeModal(id); }); } });
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
        console.log('[fetchModalContent] Solicitando', type, 'para id:', id);
        
        if(this.cache.has(key)) {
            console.log('[fetchModalContent] Contenido encontrado en cache');
            return this.cache.get(key);
        }
        
        try{
            const url = type === 'show' 
                ? `/eventos/modal-show/${id}`
                : `/eventos/modal-edit/${id}`;
            
            console.log('[fetchModalContent] Haciendo fetch a:', url);
            const res = await fetch(url, { 
                method: 'GET',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                } 
            });
            
            console.log('[fetchModalContent] Respuesta recibida - Status:', res.status, 'OK:', res.ok);
            
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
            console.log('[fetchModalContent] HTML recibido - Longitud:', html.length);
            
            if(!html || html.trim().length === 0) {
                throw new Error('Respuesta vacía del servidor');
            }
            
            this.cache.set(key, html);
            console.log('[fetchModalContent] Contenido cacheado correctamente');
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
        console.log('[openShowModal] Iniciando con id:', id);
        try {
            console.log('[openShowModal] Buscando elementos del modal...');
            const modal = document.getElementById('showModal');
            const content = document.getElementById('showModalContent');
            console.log('[openShowModal] Modal encontrado:', !!modal, 'Content encontrado:', !!content);
            
            if(!modal || !content) {
                console.error('[openShowModal] Elementos no encontrados');
                return;
            }
            
            console.log('[openShowModal] Agregando clase open al modal');
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            const key = `show-${id}`;
            console.log('[openShowModal] Verificando cache con clave:', key);
            if(this.cache.has(key)){ 
                console.log('[openShowModal] Contenido encontrado en cache');
                content.innerHTML = this.cache.get(key); 
                return; 
            }
            
            
            console.log('[openShowModal] Obteniendo contenido del servidor');
            const html = await this.fetchModalContent('show', id);
            console.log('[openShowModal] Contenido recibido, longitud:', html.length);
            
            content.innerHTML = html;
            console.log('[openShowModal] HTML inyectado completamente');
        } catch(e){
            console.error('[openShowModal] Error:', e);
            const content = document.getElementById('showModalContent');
            if(content) {
                content.innerHTML = `<div style="padding: 20px; text-align: center;">
                    <div style="color: #dc2626; font-weight: bold; margin-bottom: 10px;">Error al cargar el detalle del evento</div>
                    <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">${e && e.message ? e.message : 'Error desconocido'}</div>
                    <button type="button" onclick="closeModal('showModal')" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
                </div>`;
            }
        }
    }

    async openEditModal(id){
        console.log('[openEditModal] Iniciando con id:', id);
        try {
            console.log('[openEditModal] Buscando elementos del modal...');
            const modal = document.getElementById('editModal');
            const content = document.getElementById('editModalContent');
            console.log('[openEditModal] Modal encontrado:', !!modal, 'Content encontrado:', !!content);
            
            if(!modal || !content) {
                console.error('[openEditModal] Elementos no encontrados - modal:', !!modal, 'content:', !!content);
                return;
            }
            
            console.log('[openEditModal] Agregando clase open al modal');
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            const key = `edit-${id}`;
            console.log('[openEditModal] Verificando cache con clave:', key);
            if(this.cache.has(key)){
                console.log('[openEditModal] Contenido encontrado en cache');
                content.innerHTML = this.cache.get(key);
                this.initEditForm(content);
                return;
            }
            
          
            
            console.log('[openEditModal] Obteniendo contenido del servidor');
            const html = await this.fetchModalContent('edit', id);
            console.log('[openEditModal] Contenido recibido del servidor, longitud:', html.length);
            
            content.innerHTML = html;
            console.log('[openEditModal] HTML inyectado en content');
            
            this.initEditForm(content);
            console.log('[openEditModal] Formulario inicializado');
        } catch(e){
            console.error('[openEditModal] Error:', e);
            const content = document.getElementById('editModalContent');
            if(content) {
                content.innerHTML = `<div style="padding: 20px; text-align: center;">
                    <div style="color: #dc2626; font-weight: bold; margin-bottom: 10px;">Error al cargar el formulario de edición</div>
                    <div style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">${e && e.message ? e.message : 'Error desconocido'}</div>
                    <button type="button" onclick="closeModal('editModal')" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cerrar</button>
                </div>`;
            }
        }
    }

    // Inicializa handlers específicos del partial de edición que no ejecutan scripts inline al inyectar innerHTML
    initEditForm(container){
        console.log('[initEditForm] Iniciando');
        if(!container) container = document.getElementById('editModalContent');
        if(!container) {
            console.error('[initEditForm] Container no encontrado');
            return;
        }
        
        const form = container.querySelector('#editForm');
        console.log('[initEditForm] Formulario encontrado:', !!form);
        if(!form) {
            console.error('[initEditForm] Formulario #editForm no encontrado en container');
            return;
        }
        
        // evitar duplicar listeners
        if(form.dataset._editConfirmBound === 'true') {
            console.log('[initEditForm] Listener ya está vinculado, saliendo');
            return;
        }
        
        console.log('[initEditForm] Agregando listener de submit');
        form.dataset._editConfirmBound = 'true';

        form.addEventListener('submit', async function(e){
            console.log('[editForm submit] Iniciado');
            e.preventDefault();
            
            // Retry logic para esperar a que swConfirm esté disponible
            let maxRetries = 50;
            while (typeof window.swConfirm === 'undefined' && maxRetries > 0) {
                console.log('[editForm submit] Esperando swConfirm... retries:', maxRetries);
                await new Promise(resolve => setTimeout(resolve, 100));
                maxRetries--;
            }
            
            let ok = false;
            try{
                if (typeof window.swConfirm !== 'undefined'){
                    console.log('[editForm submit] Usando swConfirm');
                    const res = await swConfirm({
                        title: '¿Actualizar evento?',
                        text: 'Se actualizarán los datos del evento',
                        icon: 'question',
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    });
                    ok = res.isConfirmed === true;
                } else {
                    console.log('[editForm submit] Usando confirm nativo');
                    ok = confirm('¿Estás seguro de actualizar el evento?');
                }
            } catch(err){
                console.error('[editForm submit] Error en confirmación:', err);
                ok = confirm('¿Estás seguro de actualizar el evento?');
            }

            console.log('[editForm submit] Confirmado:', ok);
            if(ok === true) {
                console.log('[editForm submit] Enviando formulario');
                form.submit();
            }
        });
        console.log('[initEditForm] Completado');
    }

    closeModal(id){ 
        console.log('[closeModal] Cerrando modal:', id);
        const modal = document.getElementById(id); 
        console.log('[closeModal] Modal encontrado:', !!modal);
        if(!modal) return; 
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow='auto';
        console.log('[closeModal] Modal cerrado:', id);
    }

    openCreateModal(){
        console.log('[openCreateModal] Iniciando');
        const modal = document.getElementById('createModal');
        console.log('[openCreateModal] Modal encontrado:', !!modal);
        if(!modal) return;
        
        console.log('[openCreateModal] Agregando clase open');
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        console.log('[openCreateModal] Modal abierto');
    }
}

// Exponer funciones globales para compatibilidad con views
let _eventoModalsInstance = null;

function ensureEventoModals() {
    console.log('[ensureEventoModals] Verificando instancia...');
    if (!_eventoModalsInstance) {
        console.log('[ensureEventoModals] Creando nueva instancia');
        _eventoModalsInstance = new EventoModals();
    }
    console.log('[ensureEventoModals] Instancia disponible');
    return _eventoModalsInstance;
}

function openShowModal(id) { 
    console.log('[openShowModal global] Llamado con id:', id);
    ensureEventoModals().openShowModal(id).catch(err => console.error('[openShowModal global] Error:', err));
}

function openEditModal(id) { 
    console.log('[openEditModal global] Llamado con id:', id);
    ensureEventoModals().openEditModal(id).catch(err => console.error('[openEditModal global] Error:', err));
}

function preloadShowModal(id) { 
    console.log('[preloadShowModal global] Llamado');
    ensureEventoModals().preloadModal('show', id);
}

function preloadEditModal(id) { 
    console.log('[preloadEditModal global] Llamado');
    ensureEventoModals().preloadModal('edit', id);
}

function closeModal(id) { 
    console.log('[closeModal global] Cerrando modal:', id);
    ensureEventoModals().closeModal(id);
}

function openCreateModal() { 
    console.log('[openCreateModal global] Llamado');
    ensureEventoModals().openCreateModal();
}

// Inicializar INMEDIATAMENTE para que las funciones globales estén disponibles
console.log('[event-modals.js] Script cargado - Inicializando EventoModals inmediatamente');
ensureEventoModals();

