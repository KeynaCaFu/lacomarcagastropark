// Gestión de Modales para Insumos
class InsumoModals {
    constructor() {
        this.cache = new Map(); 
        this.preloadTimeout = null;
        this.initEventListeners();
    }

    // Inicializar event listeners
    initEventListeners() {
        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', (event) => {
            const modals = ['showModal', 'createModal', 'editModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    this.closeModal(modalId);
                }
            });
        });

        // Cerrar modal con la tecla Escape
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                    const modals = ['showModal', 'createModal', 'editModal'];
                    modals.forEach(modalId => {
                        const modal = document.getElementById(modalId);
                        if (modal && (modal.style.display === 'block' || modal.style.display === 'flex')) {
                            this.closeModal(modalId);
                        }
                    });
                }
        });

        // Manejar envío de formularios
        document.addEventListener('submit', (event) => {
            if (event.target.id === 'createForm') {
                this.handleCreateSubmit(event);
            } else if (event.target.id === 'editForm') {
                this.handleEditSubmit(event);
            }
        });
    }

    // Precargar contenido del modal al pasar el mouse
    preloadModal(type, insumoId) {
        if (this.preloadTimeout) {
            clearTimeout(this.preloadTimeout);
        }
        
        this.preloadTimeout = setTimeout(() => {
            const cacheKey = `${type}-${insumoId}`;
            if (!this.cache.has(cacheKey)) {
                this.fetchModalContent(type, insumoId);
            }
        }, 200); // Espera 200ms antes de precargar
    }

    // Obtener contenido del modal
    async fetchModalContent(type, insumoId) {
        const cacheKey = `${type}-${insumoId}`;
        
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const url = `/insumos/${insumoId}/${type}-modal`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            this.cache.set(cacheKey, html);
            return html;
        } catch (error) {
            console.error('Error fetching modal content:', error);
            throw error;
        }
    }

    // Abrir modal de crear
    openCreateModal() {
        const modal = document.getElementById('createModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Limpiar formulario
            const form = document.getElementById('createForm');
            if (form) {
                form.reset();
            }
        }
    }

    // Abrir modal de ver detalles
    async openShowModal(insumoId) {
        const modal = document.getElementById('showModal');
        const content = document.getElementById('showModalContent');
        
        if (!modal || !content) return;

    // Mostrar modal inmediatamente
    modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Verificar si ya está en caché
        const cacheKey = `show-${insumoId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            return;
        }

        // Mostrar loading solo si no está en caché
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        
        try {
            const html = await this.fetchModalContent('show', insumoId);
            content.innerHTML = html;
        } catch (error) {
            content.innerHTML = `
                <div class="error text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p>Error al cargar los detalles del insumo</p>
                    <button class="btn btn-sm btn-primary" onclick="closeModal('showModal')">Cerrar</button>
                </div>
            `;
        }
    }

    // Abrir modal de editar
    async openEditModal(insumoId) {
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        if (!modal || !content) return;

    // Mostrar modal inmediatamente
    modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Verificar si ya está en caché
        const cacheKey = `edit-${insumoId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            if (typeof setupEditValidations === 'function') {
                setupEditValidations();
            }
            return;
        }

        // Mostrar loading solo si no está en caché
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        
        try {
            const html = await this.fetchModalContent('edit', insumoId);
            content.innerHTML = html;
            
            // Configurar validaciones después de cargar el contenido
            if (typeof setupEditValidations === 'function') {
                setupEditValidations();
            }
        } catch (error) {
            content.innerHTML = `
                <div class="error text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p>Error al cargar el formulario de edición</p>
                    <button class="btn btn-sm btn-primary" onclick="closeModal('editModal')">Cerrar</button>
                </div>
            `;
        }
    }

    // Cerrar modal
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Limpiar validaciones cuando se cierre el modal
        if (modalId === 'editModal' && typeof clearValidations === 'function') {
            clearValidations('edit');
        } else if (modalId === 'createModal' && typeof clearValidations === 'function') {
            clearValidations('create');
        }
    }

    // Manejar envío del formulario de crear
    async handleCreateSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Mostrar confirmación antes de crear
        const confirmed = await this.showConfirmDialog(
            '¿Está seguro de crear este insumo?',
            'Se agregará un nuevo insumo al inventario con los datos ingresados.',
            'Sí, crear',
            'Cancelar'
        );
        
        if (!confirmed) {
            return; // Usuario canceló
        }
        
        const formData = new FormData(form);
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                this.closeModal('createModal');
                this.showNotification('success', data.message || 'Insumo creado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al crear el insumo');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al crear el insumo. Por favor, inténtalo de nuevo.');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }

    // Manejar envío del formulario de editar
    async handleEditSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Mostrar confirmación antes de editar
        const confirmed = await this.showConfirmDialog(
            '¿Está seguro de editar este insumo?',
            'Los cambios realizados se guardarán permanentemente.',
            'Sí, editar',
            'Cancelar'
        );
        
        if (!confirmed) {
            return; // Usuario canceló
        }
        
        const formData = new FormData(form);
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                this.closeModal('editModal');
                this.showNotification('success', data.message || 'Insumo actualizado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Error al actualizar el insumo');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('error', 'Error al actualizar el insumo. Por favor, inténtalo de nuevo.');
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }

    // Mostrar diálogo de confirmación personalizado
    showConfirmDialog(title, message, confirmText = 'Confirmar', cancelText = 'Cancelar') {
        return new Promise((resolve) => {
            // Crear overlay y modal de confirmación
            const overlay = document.createElement('div');
            overlay.className = 'confirm-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10002;
                animation: fadeIn 0.2s ease;
            `;
            
            const dialog = document.createElement('div');
            dialog.className = 'confirm-dialog';
            dialog.style.cssText = `
                background: white;
                border-radius: 12px;
                padding: 24px;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                animation: slideDown 0.3s ease;
            `;
            
            dialog.innerHTML = `
                <div style="margin-bottom: 16px;">
                    <h4 style="margin: 0 0 8px 0; color: #333; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-circle" style="color: #ff9900;"></i>
                        ${title}
                    </h4>
                    <p style="margin: 0; color: #666; font-size: 14px;">${message}</p>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button class="confirm-cancel-btn" style="
                        padding: 8px 20px;
                        border: 2px solid #dee2e6;
                        background: white;
                        color: #6c757d;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                        transition: all 0.2s;
                    ">
                        <i class="fas fa-times"></i> ${cancelText}
                    </button>
                    <button class="confirm-confirm-btn" style="
                        padding: 8px 20px;
                        border: none;
                        background: #485a1a;
                        color: white;
                        border-radius: 8px;
                        cursor: pointer;
                        font-size: 14px;
                        font-weight: 500;
                        transition: all 0.2s;
                    ">
                        <i class="fas fa-check"></i> ${confirmText}
                    </button>
                </div>
            `;
            
            // Añadir estilos de animación si no existen
            if (!document.getElementById('confirm-dialog-styles')) {
                const styles = document.createElement('style');
                styles.id = 'confirm-dialog-styles';
                styles.textContent = `
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }
                    @keyframes slideDown {
                        from { transform: translateY(-20px); opacity: 0; }
                        to { transform: translateY(0); opacity: 1; }
                    }
                    .confirm-cancel-btn:hover {
                        background: #f8f9fa !important;
                        border-color: #485a1a !important;
                        color: #485a1a !important;
                    }
                    .confirm-confirm-btn:hover {
                        background: #3a4815 !important;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                    }
                `;
                document.head.appendChild(styles);
            }
            
            overlay.appendChild(dialog);
            document.body.appendChild(overlay);
            
            const handleResponse = (confirmed) => {
                overlay.style.animation = 'fadeOut 0.2s ease';
                setTimeout(() => {
                    overlay.remove();
                    resolve(confirmed);
                }, 200);
            };
            
            // Event listeners
            dialog.querySelector('.confirm-cancel-btn').addEventListener('click', () => handleResponse(false));
            dialog.querySelector('.confirm-confirm-btn').addEventListener('click', () => handleResponse(true));
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) handleResponse(false);
            });
            
            // ESC para cancelar
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    handleResponse(false);
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
        });
    }

    // Establecer estado de loading en botón
    setButtonLoading(button, isLoading) {
        if (!button) return;
        
        if (isLoading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }

    // Mostrar notificación (soporta action opcional)
    showNotification(type, message, options = {}) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${this.getIconForType(type)}"></i>
                <span>${message}</span>
            </div>
            <div class="notification-actions"></div>
            <button class="notification-close" aria-label="Cerrar" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        `;
        // Acción opcional (por ejemplo, Deshacer)
        if (options && options.actionLabel && typeof options.onAction === 'function') {
            const actions = notification.querySelector('.notification-actions');
            const btn = document.createElement('button');
            btn.className = 'notification-action-btn';
            btn.textContent = options.actionLabel;
            btn.addEventListener('click', () => {
                try { options.onAction(); } finally { notification.remove(); }
            });
            actions.appendChild(btn);
        }
        
        // Agregar estilos si no existen
        if (!document.getElementById('notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10001;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    max-width: 400px;
                    animation: slideInRight 0.3s ease-out;
                }
                .notification-success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                .notification-error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
                .notification-warning {
                    background-color: #fff3cd;
                    color: #856404;
                    border: 1px solid #ffeeba;
                }
                .notification-info {
                    background-color: #d1ecf1;
                    color: #0c5460;
                    border: 1px solid #bee5eb;
                }
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex: 1;
                }
                .notification-actions { margin-right: 8px; }
                .notification-action-btn { background: transparent; border: 1px solid currentColor; border-radius: 12px; padding: 2px 8px; cursor: pointer; font-size: .85rem; }
                .notification-close {
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    opacity: 0.7;
                    padding: 0;
                    color: inherit;
                }
                .notification-close:hover {
                    opacity: 1;
                }
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(styles);
        }
        
        // Agregar al DOM
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    getIconForType(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            default: return 'fa-info-circle';
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.insumoModals = new InsumoModals();
    // Exponer notificaciones globales para reutilizar desde validaciones u otros módulos
    window.showNotification = function(messageOrType, maybeTypeOrMessage, options) {
        // Soporta dos firmas:
        // 1) showNotification('success', 'Mensaje', {actionLabel, onAction})
        // 2) showNotification('Mensaje', 'success', {..})
        if (!window.insumoModals) return;
        let type, message;
        if (['success','error','warning','info'].includes(messageOrType)) {
            type = messageOrType;
            message = maybeTypeOrMessage || '';
        } else {
            message = messageOrType || '';
            type = ['success','error','warning','info'].includes(maybeTypeOrMessage) ? maybeTypeOrMessage : 'info';
        }
        window.insumoModals.showNotification(type, message, options || {});
    };

    // Confirmación con opción de Deshacer (utilidad genérica)
    // Uso sugerido: confirmWithUndo({
    //   message: 'Insumo eliminado', delayMs: 5000,
    //   onConfirm: () => fetch('/insumos/ID/delete', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}}),
    //   onUndo: () => fetch('/insumos/ID/restore', {method:'POST'})
    // })
    window.confirmWithUndo = function({ message, delayMs = 5000, onConfirm, onUndo }) {
        let cancelled = false;
        const timer = setTimeout(async () => {
            if (!cancelled && typeof onConfirm === 'function') {
                try { await onConfirm(); } catch (e) { console.error(e); showNotification('error', 'No se pudo completar la acción'); }
            }
        }, delayMs);

        showNotification('warning', message || 'Acción programada', {
            actionLabel: 'Deshacer',
            onAction: async () => {
                cancelled = true;
                clearTimeout(timer);
                if (typeof onUndo === 'function') {
                    try { await onUndo(); } catch (e) { console.error(e); }
                }
                showNotification('info', 'Acción deshecha');
            }
        });
        return () => { cancelled = true; clearTimeout(timer); };
    };

    // Estilos mínimos para marcar fila pendiente de eliminación
    if (!document.getElementById('supply-delete-aux-styles')) {
        const style = document.createElement('style');
        style.id = 'supply-delete-aux-styles';
        style.textContent = `
            .about-to-delete { opacity: .45; filter: grayscale(15%); transition: opacity .2s ease; }
        `;
        document.head.appendChild(style);
    }

    // Exponer función para interceptar formularios DELETE de supplies
    window.initSupplyDeleteInterceptors = function(root) {
        const scope = root || document;
        const deleteForms = Array.from(scope.querySelectorAll('form.d-inline'))
            .filter(f => f.action && /suppl|insum|supplie|supplies/i.test(f.action) && f.querySelector('input[name="_method"][value="DELETE"]'));

        deleteForms.forEach(form => {
            // Evitar duplicar listeners
            if (form.dataset.deleteBound === 'true') return;
            form.dataset.deleteBound = 'true';

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Mostrar confirmación elegante primero
                const confirmed = await window.insumoModals.showConfirmDialog(
                    '¿Está seguro de eliminar este insumo?',
                    'Esta acción se puede deshacer en los próximos segundos.',
                    'Sí, eliminar',
                    'Cancelar'
                );
                
                if (!confirmed) {
                    return; // Usuario canceló
                }

                const row = form.closest('tr');
                if (row) row.classList.add('about-to-delete');

                const tokenInput = form.querySelector('input[name="_token"]');

                // Función para ejecutar el DELETE real (tras el delay del toast)
                const doDelete = async () => {
                    try {
                        const fd = new FormData();
                        if (tokenInput) fd.append('_token', tokenInput.value);
                        fd.append('_method', 'DELETE');

                        const resp = await fetch(form.action, {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            body: fd,
                            credentials: 'same-origin'
                        });

                        if (!resp.ok) throw new Error('HTTP ' + resp.status);
                        try { await resp.json(); } catch(_) {}

                        showNotification('success', 'Insumo eliminado');
                        // Tras eliminar, refrescar solo la tabla si existe loader global
                        if (window.reloadSuppliesTable) {
                            setTimeout(() => window.reloadSuppliesTable(), 700);
                        } else {
                            setTimeout(() => window.location.reload(), 700);
                        }
                    } catch (err) {
                        console.error(err);
                        showNotification('error', 'No se pudo eliminar el insumo');
                        if (row) row.classList.remove('about-to-delete');
                    }
                };

                // Restaurar visualmente si se deshace
                const undo = async () => {
                    if (row) row.classList.remove('about-to-delete');
                };

                // Mostrar toast con opción de deshacer y programar el delete real
                confirmWithUndo({
                    message: 'Insumo marcado para eliminar',
                    delayMs: 8000,
                    onConfirm: doDelete,
                    onUndo: undo
                });
            }, { capture: true });
        });
    };

    // Ejecutar una vez al cargar la página
    window.initSupplyDeleteInterceptors(document);
});

// ========== BUSCADOR DE PROVEEDORES ==========
// Función para filtrar proveedores en tiempo real
function initSupplierSearch() {
    // Buscador en modal de crear
    const createSearchInput = document.getElementById('create_buscarProveedor');
    if (createSearchInput && !createSearchInput.dataset.initialized) {
        createSearchInput.dataset.initialized = 'true';
        createSearchInput.addEventListener('input', function() {
            filterSuppliers('create');
        });
        
        // Limpiar búsqueda al abrir modal
        createSearchInput.addEventListener('focus', function() {
            this.value = '';
            filterSuppliers('create');
        });
    }
    
    // Buscador en modal de editar
    const editSearchInput = document.getElementById('edit_buscarProveedor');
    if (editSearchInput && !editSearchInput.dataset.initialized) {
        editSearchInput.dataset.initialized = 'true';
        editSearchInput.addEventListener('input', function() {
            filterSuppliers('edit');
        });
    }
}

// Función helper para filtrar proveedores
function filterSuppliers(modalType) {
    const searchInput = document.getElementById(`${modalType}_buscarProveedor`);
    const proveedoresList = document.getElementById(`${modalType}_proveedoresList`);
    
    if (!searchInput || !proveedoresList) return;
    
    const searchTerm = searchInput.value.toLowerCase().trim();
    const items = proveedoresList.querySelectorAll('.proveedor-item');
    
    let visibleCount = 0;
    items.forEach(item => {
        const nombre = item.getAttribute('data-nombre') || '';
        const telefono = item.getAttribute('data-telefono') || '';
        
        const matches = nombre.includes(searchTerm) || telefono.includes(searchTerm);
        
        if (matches) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    let noResultsMsg = proveedoresList.querySelector('.no-results-msg');
    if (visibleCount === 0 && searchTerm !== '') {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('p');
            noResultsMsg.className = 'text-muted text-center no-results-msg mt-2';
            noResultsMsg.innerHTML = '<i class="fas fa-search"></i> No se encontraron proveedores';
            proveedoresList.appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = 'block';
    } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
    }
}

// Inicializar buscador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initSupplierSearch);

// Observador para detectar cuando se carga contenido dinámico en el modal de editar
const editModalObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
            initSupplierSearch();
        }
    });
});

// Observar cambios en el contenido del modal de editar
document.addEventListener('DOMContentLoaded', () => {
    const editModalContent = document.getElementById('editModalContent');
    if (editModalContent) {
        editModalObserver.observe(editModalContent, {
            childList: true,
            subtree: true
        });
    }
});

// Funciones globales para compatibilidad
function openCreateModal() {
    if (window.insumoModals) {
        window.insumoModals.openCreateModal();
    }
}

function openShowModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.openShowModal(insumoId);
    }
}

function openEditModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.openEditModal(insumoId);
    }
}

// Función para precargar modal al pasar el mouse
function preloadShowModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.preloadModal('show', insumoId);
    }
}

function preloadEditModal(insumoId) {
    if (window.insumoModals) {
        window.insumoModals.preloadModal('edit', insumoId);
    }
}

function closeModal(modalId) {
    if (window.insumoModals) {
        window.insumoModals.closeModal(modalId);
    }
}