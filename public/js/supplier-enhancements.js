
class TableSorter {
    constructor(tableSelector) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) return;
        
        this.tbody = this.table.querySelector('tbody');
        this.headers = this.table.querySelectorAll('th.sortable-header');
        this.currentSort = { column: null, direction: 'asc' };
        
        this.initSorting();
    }

    initSorting() {
        this.headers.forEach((header, index) => {
            header.addEventListener('click', () => this.sortTable(index, header));
        });
    }

    sortTable(columnIndex, header) {
        const rows = Array.from(this.tbody.querySelectorAll('tr'));
        const isNumeric = header.dataset.type === 'numeric';
        const isDate = header.dataset.type === 'date';
        
        // Determinar dirección de ordenamiento
        let direction = 'asc';
        if (this.currentSort.column === columnIndex) {
            direction = this.currentSort.direction === 'asc' ? 'desc' : 'asc';
        }
        
        // Ordenar filas
        rows.sort((a, b) => {
            const aValue = a.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
            const bValue = b.querySelectorAll('td')[columnIndex]?.textContent.trim() || '';
            
            let comparison = 0;
            
            if (isNumeric) {
                const aNum = parseFloat(aValue.replace(/[^\d.-]/g, '')) || 0;
                const bNum = parseFloat(bValue.replace(/[^\d.-]/g, '')) || 0;
                comparison = aNum - bNum;
            } else if (isDate) {
                const aDate = new Date(aValue);
                const bDate = new Date(bValue);
                comparison = aDate - bDate;
            } else {
                comparison = aValue.localeCompare(bValue, 'es');
            }
            
            return direction === 'asc' ? comparison : -comparison;
        });
        
        // Actualizar UI
        this.headers.forEach(h => {
            h.classList.remove('asc', 'desc', 'active');
        });
        header.classList.add(direction, 'active');
        
        // Reordenar DOM
        rows.forEach(row => this.tbody.appendChild(row));
        
        // Guardar estado actual
        this.currentSort = { column: columnIndex, direction };
        
        // Mostrar indicador de ordenamiento
        this.showSortIndicator(header.textContent, direction);
    }

    showSortIndicator(columnName, direction) {
        // Remover indicador anterior
        const existing = document.querySelector('.sort-indicator');
        if (existing) existing.remove();
        
        // Crear nuevo indicador
        const indicator = document.createElement('span');
        indicator.className = 'sort-indicator';
        indicator.innerHTML = `Ordenado por: ${columnName} ${direction === 'asc' ? '↑' : '↓'}`;
        
        // Insertar antes de la tabla
        const container = this.table.parentElement;
        container.insertBefore(indicator, this.table);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            indicator.style.opacity = '0';
            setTimeout(() => indicator.remove(), 300);
        }, 3000);
    }
}


class FormValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        if (!this.form) return;
        
        this.initValidation();
        this.trackChanges();
    }

    initValidation() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Validar en blur (cuando pierde el foco)
            input.addEventListener('blur', () => this.validateField(input));
            
            // Limpiar error en focus
            input.addEventListener('focus', () => {
                input.classList.remove('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            });
        });
    }

    validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        const required = input.required;
        let isValid = true;
        let errorMessage = '';

        // Validar campo requerido
        if (required && !value) {
            isValid = false;
            errorMessage = 'Este campo es obligatorio';
        }
        
        // Validar email
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Formato de correo electrónico inválido';
            }
        }
        
        // Validar teléfono (básico)
        if (input.name === 'telefono' && value) {
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Formato de teléfono inválido';
            }
        }
        
        // Validar números
        if (type === 'number' && value) {
            const num = parseFloat(value);
            if (isNaN(num)) {
                isValid = false;
                errorMessage = 'Debe ser un número válido';
            }
            if (input.min && num < parseFloat(input.min)) {
                isValid = false;
                errorMessage = `El valor mínimo es ${input.min}`;
            }
        }
        
        // Actualizar UI
        this.updateFieldValidation(input, isValid, errorMessage);
        
        return isValid;
    }

    updateFieldValidation(input, isValid, errorMessage) {
        // Remover feedback anterior
        const existingFeedback = input.parentElement.querySelector('.invalid-feedback');
        if (existingFeedback) existingFeedback.remove();
        
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            
            // Agregar mensaje de error
            const feedback = document.createElement('div');
            feedback.className = 'validation-feedback invalid-feedback';
            feedback.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
            input.parentElement.appendChild(feedback);
        }
    }

    trackChanges() {
        let hasChanges = false;
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                hasChanges = true;
                if (window.proveedorModals) {
                    window.proveedorModals.markUnsavedChanges(true);
                }
            });
        });
        
        // Advertir al salir con cambios no guardados
        window.addEventListener('beforeunload', (e) => {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '¿Está seguro de que desea salir? Tiene cambios sin guardar.';
            }
        });
        
        // Limpiar flag al enviar
        this.form.addEventListener('submit', () => {
            hasChanges = false;
        });
    }

    validateAll() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        let allValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                allValid = false;
            }
        });
        
        return allValid;
    }
}


function enhancedDeleteConfirmation(supplierId, supplierName, suppliesCount) {
    const modal = document.getElementById('deleteConfirmModal') || createDeleteConfirmModal();
    const content = modal.querySelector('.modal-body');
    
    let warningHtml = `
        <div class="delete-confirmation-enhanced">
            <h4 class="mb-3">¿Eliminar proveedor?</h4>
            <p><strong>${supplierName}</strong></p>
    `;
    
    if (suppliesCount > 0) {
        warningHtml += `
            <div class="delete-warning-box">
                <div class="warning-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Advertencia: Este proveedor tiene dependencias</span>
                </div>
                <p>Al eliminar este proveedor:</p>
                <ul class="delete-impact-list">
                    <li><i class="fas fa-unlink"></i> Se desvincularán <strong>${suppliesCount} insumo(s)</strong></li>
                    <li><i class="fas fa-undo"></i> Tendrá <strong>10 segundos</strong> para deshacer la eliminación</li>
                    <li><i class="fas fa-database"></i> Los datos se eliminarán permanentemente después</li>
                </ul>
            </div>
        `;
    } else {
        warningHtml += `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Este proveedor no tiene insumos asociados. Tendrá 10 segundos para deshacer la eliminación.
            </div>
        `;
    }
    
    warningHtml += `
            <div class="action-buttons">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteConfirmModal()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete(${supplierId})">
                    <i class="fas fa-trash"></i> Confirmar Eliminación
                </button>
            </div>
        </div>
    `;
    
    content.innerHTML = warningHtml;
    modal.style.display = 'block';
}

function createDeleteConfirmModal() {
    const modal = document.createElement('div');
    modal.id = 'deleteConfirmModal';
    modal.className = 'custom-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Confirmación de Eliminación</h3>
                <span class="close" onclick="closeDeleteConfirmModal()">&times;</span>
            </div>
            <div class="modal-body"></div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
}

function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) modal.style.display = 'none';
}

function confirmDelete(supplierId) {
    closeDeleteConfirmModal();
    const form = document.getElementById(`deleteForm${supplierId}`);
    if (form) {
        // Agregar campo oculto de confirmación
        const confirmed = document.createElement('input');
        confirmed.type = 'hidden';
        confirmed.name = 'confirmed';
        confirmed.value = '1';
        form.appendChild(confirmed);
        
        // Enviar con AJAX
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.proveedorModals) {
                    window.proveedorModals.showOperationStatus('success', 'Proveedor eliminado exitosamente');
                    window.proveedorModals.showNotification('success', data.message || 'Proveedor eliminado', {
                        actionText: 'Deshacer',
                        actionUrl: data.restore_url,
                        timeout: 10000
                    });
                }
                // Remover fila de la tabla
                setTimeout(() => {
                    const row = document.querySelector(`tr[data-supplier-id="${supplierId}"]`);
                    if (row) row.remove();
                }, 500);
            } else if (data.requires_confirmation) {
                // Mostrar confirmación adicional
                if (confirm(data.message)) {
                    // Reenviar con confirmación
                    confirmDelete(supplierId);
                }
            } else {
                if (window.proveedorModals) {
                    window.proveedorModals.showOperationStatus('error', data.message || 'Error al eliminar');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.proveedorModals) {
                window.proveedorModals.showOperationStatus('error', 'Error al eliminar el proveedor');
            }
        });
    }
}

//atajos
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl + F - Focus en búsqueda
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('filtroNombre');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl + H - Mostrar ayuda
        if (e.ctrlKey && e.key === 'h') {
            e.preventDefault();
            if (typeof showHelpModal === 'function') {
                showHelpModal();
            }
        }
        
        // Alt + N - Nuevo proveedor
        if (e.altKey && e.key === 'n') {
            e.preventDefault();
            if (typeof openCreateProveedorModal === 'function') {
                openCreateProveedorModal();
            }
        }
    });
}


document.addEventListener('DOMContentLoaded', () => {
    // Inicializar ordenamiento de tabla
    new TableSorter('.proveedores-table');
    
    // Inicializar validadores de formularios
    new FormValidator('#createProveedorForm');
    new FormValidator('#editProveedorForm');
    
    // Inicializar atajos de teclado
    initKeyboardShortcuts();
    
    // Inicializar tooltips de Bootstrap si está disponible
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
