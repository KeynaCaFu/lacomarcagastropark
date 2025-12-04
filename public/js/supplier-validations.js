class ProveedorValidations {
    constructor() {
        this.initializeValidations();
    }

    initializeValidations() {
        // Validaciones en tiempo real
        this.setupRealtimeValidations();
        
        // Validaciones de formularios
        this.setupFormValidations();
    }

    setupRealtimeValidations() {
        // Validar teléfono en tiempo real
        document.addEventListener('input', (e) => {
            if (e.target.name === 'telefono') {
                this.validatePhone(e.target);
            }
        });

        // Validar correo en tiempo real
        document.addEventListener('input', (e) => {
            if (e.target.name === 'correo') {
                this.validateEmail(e.target);
            }
        });

        // Validar total de compras
        document.addEventListener('input', (e) => {
            if (e.target.name === 'total_compras') {
                this.validateTotalCompras(e.target);
            }
        });

        // Validar nombre
        document.addEventListener('input', (e) => {
            if (e.target.name === 'nombre') {
                this.validateNombre(e.target);
            }
        });

        // Validar dirección
        document.addEventListener('input', (e) => {
            if (e.target.name === 'direccion') {
                this.validateDireccion(e.target);
            }
        });
    }

    setupFormValidations() {
        // Validar antes de enviar
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'createProveedorForm' || e.target.id === 'editProveedorForm') {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                }
            }
        });

        // Validar duplicados de correo
        document.addEventListener('blur', (e) => {
            if (e.target.name === 'correo') {
                this.checkDuplicateEmail(e.target);
            }
        });
    }

    validateForm(form) {
        let isValid = true;
        const errors = [];

        // Limpiar errores anteriores
        this.clearFormErrors(form);

        // Validar campos obligatorios
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo es obligatorio');
                isValid = false;
                errors.push(`${this.getFieldLabel(field)} es obligatorio`);
            }
        });

        // Validaciones específicas
        if (!this.validateNombre(form.querySelector('[name="nombre"]'))) {
            isValid = false;
            errors.push('Nombre del proveedor inválido');
        }

        if (!this.validatePhone(form.querySelector('[name="telefono"]'))) {
            isValid = false;
            errors.push('Número de teléfono inválido');
        }

        if (!this.validateEmail(form.querySelector('[name="correo"]'))) {
            isValid = false;
            errors.push('Correo electrónico inválido');
        }

        if (!this.validateDireccion(form.querySelector('[name="direccion"]'))) {
            isValid = false;
            errors.push('Dirección inválida');
        }

        if (!this.validateTotalCompras(form.querySelector('[name="total_compras"]'))) {
            isValid = false;
            errors.push('Total de compras inválido');
        }

        if (!this.validateInsumos(form)) {
            isValid = false;
            errors.push('Selección de insumos inválida');
        }

        // Mostrar resumen de errores si hay
        if (!isValid) {
            this.showValidationSummary(form, errors);
        }

        return isValid;
    }

    validateNombre(nombreInput) {
        if (!nombreInput || !nombreInput.value) return true;

        const nombre = nombreInput.value.trim();

        // Verificar longitud mínima
        if (nombre.length < 3) {
            this.showFieldError(nombreInput, 'El nombre debe tener al menos 3 caracteres');
            return false;
        }

        // Verificar longitud máxima
        if (nombre.length > 100) {
            this.showFieldError(nombreInput, 'El nombre no puede exceder 100 caracteres');
            return false;
        }

        // Verificar caracteres válidos (letras, números, espacios y algunos caracteres especiales)
        const nombreRegex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\.\,\&\'\"]+$/;
        if (!nombreRegex.test(nombre)) {
            this.showFieldError(nombreInput, 'El nombre contiene caracteres no válidos');
            return false;
        }

        // Verificar que no sea solo números
        if (/^\d+$/.test(nombre)) {
            this.showFieldError(nombreInput, 'El nombre no puede ser solo números');
            return false;
        }

        // Verificar que no tenga espacios excesivos
        if (/\s{3,}/.test(nombre)) {
            this.showFieldWarning(nombreInput, 'Evite espacios excesivos en el nombre');
        }

        this.clearFieldError(nombreInput);
        return true;
    }

    validatePhone(phoneInput) {
        if (!phoneInput || !phoneInput.value) return true;

        const phone = phoneInput.value.trim();

        // Verificar longitud mínima para Costa Rica
        if (phone.length < 8) {
            this.showFieldError(phoneInput, 'El teléfono debe tener al menos 8 dígitos');
            return false;
        }

        // Verificar longitud máxima
        if (phone.length > 20) {
            this.showFieldError(phoneInput, 'El teléfono no puede exceder 20 caracteres');
            return false;
        }

        // Verificar formato de teléfono costarricense (permite + - ( ) espacios)
        const phoneRegex = /^[\+]?[\d\s\-\(\)]{8,20}$/;
        if (!phoneRegex.test(phone)) {
            this.showFieldError(phoneInput, 'Formato de teléfono inválido');
            return false;
        }

        // Contar solo los dígitos
        const digitsOnly = phone.replace(/\D/g, '');
        
        // Verificar patrones comunes de Costa Rica
        if (digitsOnly.length >= 8) {
            // Teléfono móvil costarricense (8 dígitos)
            if (digitsOnly.length === 8 && /^[6789]/.test(digitsOnly)) {
                this.clearFieldError(phoneInput);
                return true;
            }
            
            // Teléfono fijo costarricense (8 dígitos, empieza con 2)
            if (digitsOnly.length === 8 && /^2/.test(digitsOnly)) {
                this.clearFieldError(phoneInput);
                return true;
            }

            // Teléfono con código de país (+506)
            if (digitsOnly.length === 11 && digitsOnly.startsWith('506')) {
                this.clearFieldError(phoneInput);
                return true;
            }
        }

        this.showFieldWarning(phoneInput, 'Verifique que el número sea válido para Costa Rica');
        return true; // No bloquear, solo advertir
    }

    validateEmail(emailInput) {
        if (!emailInput || !emailInput.value) return true;

        const email = emailInput.value.trim();

        // Verificar formato básico de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showFieldError(emailInput, 'Formato de correo electrónico inválido');
            return false;
        }

        // Verificar longitud máxima
        if (email.length > 100) {
            this.showFieldError(emailInput, 'El correo no puede exceder 100 caracteres');
            return false;
        }

        // Verificar que no tenga caracteres especiales peligrosos
        const dangerousChars = /[<>\"\']/;
        if (dangerousChars.test(email)) {
            this.showFieldError(emailInput, 'El correo contiene caracteres no válidos');
            return false;
        }

        // Verificar dominios comunes de Costa Rica
        const commonDomains = ['.com', '.cr', '.org', '.net', '.edu', '.gov'];
        const hasDomain = commonDomains.some(domain => email.toLowerCase().includes(domain));
        if (!hasDomain) {
            this.showFieldWarning(emailInput, 'Verifique que el dominio del correo sea correcto');
        }

        this.clearFieldError(emailInput);
        return true;
    }

    validateDireccion(direccionInput) {
        if (!direccionInput || !direccionInput.value) return true;

        const direccion = direccionInput.value.trim();

        // Verificar longitud mínima
        if (direccion.length < 10) {
            this.showFieldError(direccionInput, 'La dirección debe ser más específica (mínimo 10 caracteres)');
            return false;
        }

        // Verificar longitud máxima
        if (direccion.length > 500) {
            this.showFieldError(direccionInput, 'La dirección es demasiado larga (máximo 500 caracteres)');
            return false;
        }

        // Verificar que tenga contenido útil (no solo espacios o caracteres especiales)
        if (/^[\s\-\.\,]+$/.test(direccion)) {
            this.showFieldError(direccionInput, 'Ingrese una dirección válida');
            return false;
        }

        // Advertencia si parece incompleta
        if (direccion.length < 20) {
            this.showFieldWarning(direccionInput, 'Considere proporcionar una dirección más detallada');
        }

        this.clearFieldError(direccionInput);
        return true;
    }

    validateTotalCompras(totalInput) {
        if (!totalInput || !totalInput.value) return true;

        const total = parseFloat(totalInput.value);

        // Verificar que sea un número válido
        if (isNaN(total)) {
            this.showFieldError(totalInput, 'Ingrese un monto válido');
            return false;
        }

        // Verificar que sea positivo o cero
        if (total < 0) {
            this.showFieldError(totalInput, 'El total de compras no puede ser negativo');
            return false;
        }

        // Verificar límite máximo razonable (1000 millones de colones)
        if (total > 1000000000) {
            this.showFieldError(totalInput, 'El monto parece excesivamente alto');
            return false;
        }

        // Advertencia para montos muy altos
        if (total > 50000000) { // 50 millones de colones
            this.showFieldWarning(totalInput, 'Verifique que el monto sea correcto (monto muy alto)');
        }

        // Sugerencia para nuevos proveedores
        if (total === 0) {
            this.showFieldInfo(totalInput, 'Para nuevos proveedores, puede iniciar con ₡0');
        }

        this.clearFieldError(totalInput);
        return true;
    }

    validateInsumos(form) {
        const insumosCheckboxes = form.querySelectorAll('input[name="insumos[]"]');
        const checkedInsumos = form.querySelectorAll('input[name="insumos[]"]:checked');

        // No es obligatorio seleccionar insumos, pero si hay muchos seleccionados, advertir
        if (checkedInsumos.length > 20) {
            this.showFormWarning(form, 'Ha seleccionado muchos insumos. Verifique que sea correcto.');
        }

        return true; // Siempre válido, solo advertencias
    }

    checkDuplicateEmail(emailInput) {
        if (!emailInput.value) return;

        // Debounce para evitar múltiples llamadas
        clearTimeout(this.emailCheckTimeout);
        
        this.emailCheckTimeout = setTimeout(async () => {
            try {
                // Obtener el ID del proveedor si estamos editando
                const form = emailInput.closest('form');
                const isEditForm = form && form.id === 'editProveedorForm';
                let currentSupplierId = null;
                
                if (isEditForm) {
                    // Extraer ID de la URL del action del formulario
                    const actionUrl = form.action;
                    const matches = actionUrl.match(/\/proveedores\/(\d+)/);
                    if (matches) {
                        currentSupplierId = matches[1];
                    }
                }
                
                // Hacer llamada AJAX para verificar duplicado
                const response = await fetch('/proveedores/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        supplier_id: currentSupplierId
                    })
                });
                
                const data = await response.json();
                
                if (data.exists) {
                    this.showFieldError(emailInput, 'Este correo ya está registrado en otro proveedor');
                } else {
                    this.clearFieldError(emailInput);
                }
            } catch (error) {
                console.warn('No se pudo verificar el correo:', error);
                // Fallback a validación simple
                const commonEmails = [
                    'admin@test.com',
                    'proveedor@test.com',
                    'info@proveedor.com'
                ];
                
                if (commonEmails.includes(emailInput.value.toLowerCase())) {
                    this.showFieldWarning(emailInput, 'Este correo podría estar en uso. Verifique que sea correcto.');
                }
            }
        }, 500); // Esperar 500ms después de que el usuario deje de escribir
    }

    // Métodos de UI para mostrar errores, advertencias e información
    showFieldError(field, message) {
        this.clearFieldError(field);
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        field.parentNode.appendChild(errorDiv);
    }

    showFieldWarning(field, message) {
        this.clearFieldError(field);
        field.classList.add('is-warning');
        
        const warningDiv = document.createElement('div');
        warningDiv.className = 'warning-feedback';
        warningDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        field.parentNode.appendChild(warningDiv);
    }

    showFieldInfo(field, message) {
        this.clearFieldError(field);
        field.classList.add('is-info');
        
        const infoDiv = document.createElement('div');
        infoDiv.className = 'info-feedback';
        infoDiv.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
        field.parentNode.appendChild(infoDiv);
    }

    clearFieldError(field) {
        if (!field) return;
        
        field.classList.remove('is-invalid', 'is-warning', 'is-info');
        const feedback = field.parentNode.querySelector('.invalid-feedback, .warning-feedback, .info-feedback');
        if (feedback) {
            feedback.remove();
        }
    }

    showFormError(form, message) {
        this.showNotification(message, 'error');
    }

    showFormWarning(form, message) {
        this.showNotification(message, 'warning');
    }

    clearFormErrors(form) {
        const invalidFields = form.querySelectorAll('.is-invalid, .is-warning, .is-info');
        invalidFields.forEach(field => this.clearFieldError(field));
    }

    showValidationSummary(form, errors) {
        if (errors.length === 0) return;

        const summary = `Se encontraron ${errors.length} error(es):\n\n${errors.join('\n')}`;
        this.showNotification(summary, 'error');
    }

    getFieldLabel(field) {
        const label = field.closest('.mb-3')?.querySelector('label');
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    showNotification(message, type) {
        // Reutilizar la función de notificaciones existente o crear una simple
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            // Crear notificación simple
            this.createSimpleNotification(message, type);
        }
    }

    createSimpleNotification(message, type) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        
        notification.innerHTML = `
            <strong>${type === 'error' ? 'Error' : type === 'warning' ? 'Advertencia' : 'Información'}:</strong>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Inicializar validaciones cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    new ProveedorValidations();
});

// Agregar estilos CSS para las validaciones
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('validation-styles')) {
        const style = document.createElement('style');
        style.id = 'validation-styles';
        style.textContent = `
            .is-warning {
                border-color: #ffc107 !important;
                box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25) !important;
            }
            
            .is-info {
                border-color: #17a2b8 !important;
                box-shadow: 0 0 0 0.25rem rgba(23, 162, 184, 0.25) !important;
            }
            
            .warning-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875em;
                color: #856404;
            }
            
            .info-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875em;
                color: #0c5460;
            }
            
            .invalid-feedback {
                color: #dc3545;
            }
            
            .warning-feedback i,
            .info-feedback i,
            .invalid-feedback i {
                margin-right: 4px;
            }
        `;
        document.head.appendChild(style);
    }
});