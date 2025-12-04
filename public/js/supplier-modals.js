// Gestión de Modales para Proveedores
class ProveedorModals {
    constructor() {
        // Cache para almacenar contenido de modales
        this.cache = new Map();
        // Timeout para prelado
        this.preloadTimeout = null;
        // Estado de cambios no guardados
        this.hasUnsavedChanges = false;
        
        this.initEventListeners();
        this.ensureModalsOnBody();
        this.createLoadingOverlay();
        this.createUnsavedIndicator();
    }

    // Crear overlay de loading
    createLoadingOverlay() {
        if (!document.getElementById('loadingOverlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(overlay);
        }
    }

    // Crear indicador de cambios no guardados
    createUnsavedIndicator() {
        if (!document.getElementById('unsavedIndicator')) {
            const indicator = document.createElement('div');
            indicator.id = 'unsavedIndicator';
            indicator.className = 'unsaved-changes-indicator';
            indicator.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>Tienes cambios sin guardar</span>';
            document.body.appendChild(indicator);
        }
    }

    // Mostrar loading overlay
    showLoading(message = 'Procesando...') {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('active');
        }
        this.showOperationStatus('processing', message);
    }

    // Ocultar loading overlay
    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }

    // Mostrar estado de operación (processing, success, error, warning)
    showOperationStatus(type, message) {
        // Remover estados previos
        const existing = document.querySelector('.operation-status');
        if (existing) {
            existing.remove();
        }

        const status = document.createElement('div');
        status.className = `operation-status ${type}`;
        
        let icon = '';
        switch(type) {
            case 'processing':
                icon = '<div class="mini-spinner"></div>';
                break;
            case 'success':
                icon = '<i class="fas fa-check-circle"></i>';
                break;
            case 'error':
                icon = '<i class="fas fa-times-circle"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle"></i>';
                break;
        }

        status.innerHTML = `${icon} <span>${message}</span>`;
        document.body.appendChild(status);

        // Auto-remover después de 3 segundos (excepto processing)
        if (type !== 'processing') {
            setTimeout(() => {
                status.style.animation = 'slideIn 0.3s ease reverse';
                setTimeout(() => status.remove(), 300);
            }, 3000);
        }

        return status;
    }

    // Marcar formulario con cambios no guardados
    markUnsavedChanges(hasChanges = true) {
        this.hasUnsavedChanges = hasChanges;
        const indicator = document.getElementById('unsavedIndicator');
        if (indicator) {
            if (hasChanges) {
                indicator.classList.add('visible');
            } else {
                indicator.classList.remove('visible');
            }
        }
    }

    // Mostrar loading en botón específico
    setButtonLoading(button, loading = true) {
        if (loading) {
            button.classList.add('loading');
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
        } else {
            button.classList.remove('loading');
            button.disabled = false;
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        }
    }

    // Inicializar event listeners
    initEventListeners() {
        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', (event) => {
            const modals = ['showProveedorModal', 'createProveedorModal', 'editProveedorModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && event.target === modal) {
                    this.closeModal(modalId);
                }
            });
        });

        // Atajos de teclado mejorados
        document.addEventListener('keydown', (event) => {
            // ESC - Cerrar modal abierto
            if (event.key === 'Escape') {
                const modals = ['showProveedorModal', 'createProveedorModal', 'editProveedorModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (modal && (modal.style.display === 'block' || modal.style.display === 'flex')) {
                        this.closeModal(modalId);
                    }
                });
            }
            
            // Ctrl+N - Nuevo proveedor
            if (event.ctrlKey && event.key === 'n') {
                event.preventDefault();
                this.openCreateModal();
            }
            
            // Enter en formularios - Guardar (solo si no está en textarea)
            if (event.key === 'Enter' && !event.shiftKey) {
                const target = event.target;
                if (target.form && target.tagName !== 'TEXTAREA' && target.type !== 'submit') {
                    const form = target.form;
                    if (form.id === 'createProveedorForm' || form.id === 'editProveedorForm') {
                        event.preventDefault();
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn && !submitBtn.disabled) {
                            submitBtn.click();
                        }
                    }
                }
            }
        });

        // Manejar envío de formularios
        document.addEventListener('submit', (event) => {
            if (event.target.id === 'createProveedorForm') {
                this.handleCreateSubmit(event);
            } else if (event.target.id === 'editProveedorForm') {
                this.handleEditSubmit(event);
            }
        });

        // Alertas para asignación de Insumos (con deshacer)
        document.addEventListener('change', (event) => {
            const target = event.target;
            if (target && target.matches('input[name="insumos[]"]')) {
                const label = target.closest('.form-check')?.querySelector('.form-check-label');
                const insumoTexto = label ? label.textContent.trim().split('\n')[0] : 'Insumo';
                const asignado = target.checked;

                // Guardar estado previo para deshacer
                const prevState = !asignado;
                const checkbox = target;

                this.showNotification('info', `${asignado ? 'Asignado' : 'Quitado'}: ${insumoTexto}` , {
                    actionText: 'Deshacer',
                    actionUrl: null,
                    timeout: 10000
                });

                // Vincular acción 'Deshacer' manualmente si existe botón
                const notifications = document.querySelectorAll('.notification');
                const lastNotif = notifications[notifications.length - 1];
                const actionBtn = lastNotif?.querySelector('.notification-action');
                if (actionBtn) {
                    actionBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        checkbox.checked = prevState;
                        // Disparar evento change para que cualquier lógica conectada se ejecute
                        checkbox.dispatchEvent(new Event('change'));
                        lastNotif.remove();
                    }, { once: true });
                }
            }
        });
    }

    // Asegurar que los modales estén adjuntos directamente al <body>
    // para evitar que queden debajo o recortados por contenedores con overflow/transform
    ensureModalsOnBody() {
        const modalIds = ['showProveedorModal', 'createProveedorModal', 'editProveedorModal'];
        modalIds.forEach(id => {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                try {
                    document.body.appendChild(el);
                } catch (e) {
                    // Si falla por alguna razón, no bloquear la app
                    console.warn(`No se pudo mover el modal ${id} al body:`, e);
                }
            }
        });
    }

    // Abrir modal de crear
    openCreateModal() {
        const modal = document.getElementById('createProveedorModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.body.classList.add('modal-open');
            
            // Limpiar formulario
            const form = document.getElementById('createProveedorForm');
            if (form) {
                form.reset();
                // Resetear valor por defecto del total de compras
                const totalComprasInput = document.getElementById('create_proveedor_total_compras');
                if (totalComprasInput) {
                    totalComprasInput.value = '0';
                }
            }
        }
    }

    // Abrir modal de ver detalles
    async openShowModal(proveedorId) {
        const modal = document.getElementById('showProveedorModal');
        const content = document.getElementById('showProveedorModalContent');
        
        if (!modal || !content) return;

    // Mostrar modal inmediatamente
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
        
        // Verificar si tenemos el contenido en caché
        const cacheKey = `show-${proveedorId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            return;
        }
        
        // Si no está en caché, mostrar loading y cargar
        content.innerHTML = '<div class="loading">Cargando detalles del proveedor...</div>';
        
        try {
            const html = await this.fetchModalContent('show', proveedorId);
            content.innerHTML = html;
        } catch (error) {
            console.error('Error loading modal content:', error);
            content.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error al cargar los detalles del proveedor
                    <br><small>Por favor, inténtalo de nuevo</small>
                </div>
            `;
        }
    }

    // Abrir modal de editar
    async openEditModal(proveedorId) {
        console.log('Loading edit modal for proveedor:', proveedorId);
        
        const modal = document.getElementById('editProveedorModal');
        const content = document.getElementById('editProveedorModalContent');
        
        if (!modal || !content) {
            console.error('Modal elements not found');
            alert('Error: No se pudieron encontrar los elementos del modal.');
            return;
        }

    // Mostrar modal inmediatamente
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.body.classList.add('modal-open');
        
        // Verificar si tenemos el contenido en caché
        const cacheKey = `edit-${proveedorId}`;
        if (this.cache.has(cacheKey)) {
            content.innerHTML = this.cache.get(cacheKey);
            return;
        }
        
        // Si no está en caché, mostrar loading y cargar
        content.innerHTML = '<div class="loading text-center p-4"><i class="fas fa-spinner fa-spin"></i> Cargando formulario de edición...</div>';
        
        try {
            const html = await this.fetchModalContent('edit', proveedorId);
            content.innerHTML = html;
            console.log('Modal content loaded successfully');
        } catch (error) {
            console.error('Error loading modal:', error);
            content.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Error al cargar el formulario</h5>
                    <p>${error.message}</p>
                    <button class="btn btn-secondary" onclick="closeProveedorModal('editProveedorModal')">Cerrar</button>
                </div>
            `;
        }
    }

    // Obtener contenido del modal (con caché)
    async fetchModalContent(type, proveedorId) {
        const cacheKey = `${type}-${proveedorId}`;
        
        // Si ya está en caché, devolverlo
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }
        
        // Construir URL según el tipo de modal
        const url = type === 'show' 
            ? `/proveedores/${proveedorId}/show-modal`
            : `/proveedores/${proveedorId}/edit-modal`;
        
        console.log('Fetching:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        
        const html = await response.text();
        
        // Guardar en caché
        this.cache.set(cacheKey, html);
        
        return html;
    }

    // Precargar contenido del modal (para hover)
    preloadModal(type, proveedorId) {
        const cacheKey = `${type}-${proveedorId}`;
        
        // Si ya está en caché, no hacer nada
        if (this.cache.has(cacheKey)) {
            return;
        }
        
        // Cancelar precarga anterior si existe
        if (this.preloadTimeout) {
            clearTimeout(this.preloadTimeout);
        }
        
        // Esperar un poco antes de precargar (para evitar precargas innecesarias)
        this.preloadTimeout = setTimeout(() => {
            this.fetchModalContent(type, proveedorId).catch(err => {
                console.warn('Preload failed:', err);
            });
        }, 200);
    }

    // Cerrar modal
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.body.classList.remove('modal-open');
        }
    }

    // Manejar envío del formulario de crear
    async handleCreateSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        this.showLoading('Creando proveedor...');
        
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
            
            this.hideLoading();
            
            if (response.ok && data.success) {
                this.showOperationStatus('success', data.message || 'Proveedor creado exitosamente');
                this.closeModal('createProveedorModal');
                this.markUnsavedChanges(false);
                this.showNotification('success', data.message || 'Proveedor creado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                // Manejar errores específicos del servidor
                let errorMessage = 'Error al crear el proveedor';
                
                if (data.errors) {
                    // Errores de validación Laravel
                    const errorList = Object.values(data.errors).flat();
                    errorMessage = errorList.join('<br>');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                
                this.showOperationStatus('error', errorMessage);
                this.showNotification('error', errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            this.hideLoading();
            
            let errorMessage = 'Error al crear el proveedor. Por favor, inténtalo de nuevo.';
            
            // Manejar diferentes tipos de errores
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                errorMessage = 'Error de conexión. Verifique su conexión a internet e inténtelo de nuevo.';
            } else if (error.response) {
                switch (error.response.status) {
                    case 422:
                        errorMessage = 'Datos inválidos. Verifique todos los campos del formulario.';
                        break;
                    case 500:
                        errorMessage = 'Error del servidor. Contacte al administrador si el problema persiste.';
                        break;
                    case 403:
                        errorMessage = 'No tiene permisos para realizar esta acción.';
                        break;
                    default:
                        errorMessage = `Error ${error.response.status}: ${error.response.statusText}`;
                }
            }
            
            this.showOperationStatus('error', errorMessage);
            this.showNotification('error', errorMessage);
        } finally {
            this.setButtonLoading(submitButton, false);
        }
    }

    // Manejar envío del formulario de editar
    async handleEditSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Deshabilitar botón y mostrar loading
        this.setButtonLoading(submitButton, true);
        this.showLoading('Actualizando proveedor...');
        
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
            
            this.hideLoading();
            
            if (response.ok && data.success) {
                this.showOperationStatus('success', data.message || 'Proveedor actualizado exitosamente');
                this.closeModal('editProveedorModal');
                this.markUnsavedChanges(false);
                this.showNotification('success', data.message || 'Proveedor actualizado exitosamente');
                setTimeout(() => location.reload(), 1000);
            } else {
                // Manejar errores específicos del servidor
                let errorMessage = 'Error al actualizar el proveedor';
                
                if (data.errors) {
                    // Errores de validación Laravel
                    const errorList = Object.values(data.errors).flat();
                    errorMessage = errorList.join('<br>');
                } else if (data.message) {
                    errorMessage = data.message;
                }
                
                this.showNotification('error', errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            let errorMessage = 'Error al actualizar el proveedor. Por favor, inténtalo de nuevo.';
            
            // Manejar diferentes tipos de errores
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                errorMessage = 'Error de conexión. Verifique su conexión a internet e inténtelo de nuevo.';
            } else if (error.response) {
                switch (error.response.status) {
                    case 422:
                        errorMessage = 'Datos inválidos. Verifique todos los campos del formulario.';
                        break;
                    case 500:
                        errorMessage = 'Error del servidor. Contacte al administrador si el problema persiste.';
                        break;
                    case 403:
                        errorMessage = 'No tiene permisos para realizar esta acción.';
                        break;
                    case 404:
                        errorMessage = 'Proveedor no encontrado.';
                        break;
                    default:
                        errorMessage = `Error ${error.response.status}: ${error.response.statusText}`;
                }
            }
            
            this.showNotification('error', errorMessage);
        } finally {
            this.setButtonLoading(submitButton, false);
        }
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

    // Mostrar modal de confirmación de eliminación
    async showDeleteConfirmation(supplierId, supplierName, suppliesCount) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'custom-modal';
            modal.id = 'deleteConfirmModal';
            modal.style.display = 'flex';
            
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header bg-danger text-white">
                        <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
                        <span class="close text-white" onclick="this.closest('.custom-modal').remove()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3"><strong>¿Está seguro de eliminar este proveedor?</strong></p>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> <strong>${supplierName}</strong>
                        </div>
                        ${suppliesCount > 0 ? `
                        <p class="mb-2">Este proveedor tiene:</p>
                        <ul>
                            <li><strong>${suppliesCount}</strong> insumo(s) asociado(s)</li>
                        </ul>
                        ` : '<p class="text-muted">Este proveedor no tiene insumos asociados.</p>'}
                    </div>
                    <div class="modal-actions d-flex gap-2">
                        <button type="button" class="btn btn-secondary flex-fill" onclick="this.closest('.custom-modal').remove()">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger flex-fill" id="confirmDeleteBtn">
                            <i class="fas fa-trash me-1"></i> Sí, Eliminar
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
            document.body.classList.add('modal-open');
            
            // Manejar confirmación
            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                modal.remove();
                document.body.style.overflow = 'auto';
                document.body.classList.remove('modal-open');
                resolve(true);
            });
            
            // Manejar cancelación con ESC
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    modal.remove();
                    document.body.style.overflow = 'auto';
                    document.body.classList.remove('modal-open');
                    document.removeEventListener('keydown', escHandler);
                    resolve(false);
                }
            };
            document.addEventListener('keydown', escHandler);
            
            // Manejar clic fuera del modal
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    document.body.style.overflow = 'auto';
                    document.body.classList.remove('modal-open');
                    resolve(false);
                }
            });
        });
    }

    // Mostrar notificación (con acción opcional y auto-cierre)
    showNotification(type, message, options = {}) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const actionHtml = options.actionText && options.actionUrl ? `
            <button class="notification-action btn btn-sm btn-link">${options.actionText}</button>
        ` : '';

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            ${actionHtml}
            <button class="notification-close" aria-label="Cerrar">
                <i class="fas fa-times"></i>
            </button>
        `;
        
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
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex: 1;
                }
                .notification-action {
                    color: inherit;
                    text-decoration: underline;
                    margin-right: 8px;
                }
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

        // Manejar cierre manual
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });

        // Manejar acción opcional
        const actionBtn = notification.querySelector('.notification-action');
        if (actionBtn && options.actionUrl) {
            actionBtn.addEventListener('click', () => {
                // Navegar a la URL de acción (por ejemplo, restaurar)
                window.location.href = options.actionUrl;
            });
        }

        // Auto-remover después de X milisegundos (default 10s)
        const timeout = typeof options.timeout === 'number' ? options.timeout : 10000;
        if (timeout > 0) {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, timeout);
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.proveedorModals = new ProveedorModals();
});

// Funciones globales para compatibilidad
function openCreateProveedorModal() {
    if (window.proveedorModals) {
        window.proveedorModals.openCreateModal();
    }
}

function openShowProveedorModal(proveedorId) {
    if (window.proveedorModals) {
        window.proveedorModals.openShowModal(proveedorId);
    }
}

function openEditProveedorModal(proveedorId) {
    console.log('Attempting to open edit modal for proveedor ID:', proveedorId);
    
    if (window.proveedorModals) {
        window.proveedorModals.openEditModal(proveedorId);
    } else {
        console.error('proveedorModals not initialized');
        alert('Error: Sistema de modales no inicializado. Por favor, recargue la página.');
    }
}

function closeProveedorModal(modalId) {
    if (window.proveedorModals) {
        window.proveedorModals.closeModal(modalId);
    }
}

// Funciones de precarga para optimizar UX
function preloadShowProveedorModal(proveedorId) {
    if (window.proveedorModals) {
        window.proveedorModals.preloadModal('show', proveedorId);
    }
}

function preloadEditProveedorModal(proveedorId) {
    if (window.proveedorModals) {
        window.proveedorModals.preloadModal('edit', proveedorId);
    }
}

// Función para confirmar eliminación con modal personalizado
async function confirmDeleteSupplier(supplierId, supplierName, suppliesCount) {
    if (window.proveedorModals) {
        return await window.proveedorModals.showDeleteConfirmation(supplierId, supplierName, suppliesCount);
    }
    return confirm('¿Estás seguro de eliminar este proveedor?');
}