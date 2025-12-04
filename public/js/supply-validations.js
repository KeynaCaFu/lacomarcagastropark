class InsumoValidations {
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
        // Validar stock vs estado
        document.addEventListener('change', (e) => {
            if (e.target.name === 'stock_actual' || e.target.name === 'estado') {
                this.validateStockVsEstado(e.target.form);
            }
        });

        // Validar fecha de vencimiento
        document.addEventListener('change', (e) => {
            if (e.target.name === 'fecha_vencimiento') {
                this.validateExpirationDate(e.target);
            }
        });

        // Validar precio
        document.addEventListener('input', (e) => {
            if (e.target.name === 'precio') {
                this.validatePrice(e.target);
            }
        });

        // Validar stock mínimo
        document.addEventListener('input', (e) => {
            if (e.target.name === 'stock_actual' || e.target.name === 'stock_minimo') {
                this.validateStockLevels(e.target.form);
            }
        });
    }

    setupFormValidations() {
        // Validar antes de enviar
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'createForm' || e.target.id === 'editForm') {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                }
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
            }
        });

        // Validaciones específicas
        if (!this.validateExpirationDate(form.querySelector('[name="fecha_vencimiento"]'))) {
            isValid = false;
        }

        if (!this.validateStockVsEstado(form)) {
            isValid = false;
        }

        if (!this.validatePrice(form.querySelector('[name="precio"]'))) {
            isValid = false;
        }

        if (!this.validateStockLevels(form)) {
            isValid = false;
        }

        // Mostrar resumen de errores si hay
        if (!isValid) {
            this.showValidationSummary(form, errors);
        }

        return isValid;
    }

    validateExpirationDate(dateInput) {
        if (!dateInput || !dateInput.value) return true;

        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate <= today) {
            this.showFieldError(dateInput, 'La fecha de vencimiento debe ser posterior a hoy');
            return false;
        }

        // Advertencia si vence pronto
        const daysUntilExpiry = Math.ceil((selectedDate - today) / (1000 * 60 * 60 * 24));
        if (daysUntilExpiry <= 7) {
            this.showFieldWarning(dateInput, `Advertencia: Este producto vence en ${daysUntilExpiry} días`);
        }

        this.clearFieldError(dateInput);
        return true;
    }

    validateStockVsEstado(form) {
        const stockActual = parseInt(form.querySelector('[name="stock_actual"]')?.value || 0);
        const estado = form.querySelector('[name="estado"]')?.value;

        if (stockActual === 0 && estado === 'Disponible') {
            this.showFormError(form, 'Un insumo sin stock no puede estar disponible');
            return false;
        }

        if (stockActual > 0 && estado === 'Agotado') {
            this.showFormError(form, 'Un insumo con stock no puede estar agotado');
            return false;
        }

        return true;
    }

    validatePrice(priceInput) {
        if (!priceInput || !priceInput.value) return true;

        const price = parseFloat(priceInput.value);

        if (price <= 0) {
            this.showFieldError(priceInput, 'El precio debe ser mayor a 0');
            return false;
        }

        if (price > 1000000) {
            this.showFieldWarning(priceInput, 'El precio parece muy alto, verifique que sea correcto');
        }

        this.clearFieldError(priceInput);
        return true;
    }

    validateStockLevels(form) {
        const stockActual = parseInt(form.querySelector('[name="stock_actual"]')?.value || 0);
        const stockMinimo = parseInt(form.querySelector('[name="stock_minimo"]')?.value || 0);

        if (stockActual < stockMinimo) {
            this.showFormWarning(form, 'El stock actual está por debajo del mínimo recomendado');
        }

        return true;
    }

    // Métodos de UI para mostrar errores
    showFieldError(field, message) {
        this.clearFieldError(field);
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
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

    clearFieldError(field) {
        if (!field) return;
        
        field.classList.remove('is-invalid', 'is-warning');
        const feedback = field.parentNode.querySelector('.invalid-feedback, .warning-feedback');
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
        const invalidFields = form.querySelectorAll('.is-invalid, .is-warning');
        invalidFields.forEach(field => this.clearFieldError(field));
    }

    showNotification(message, type) {
        // Reutilizar la función de notificaciones existente
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }
}

// Validaciones en tiempo real para formularios de insumos
document.addEventListener('DOMContentLoaded', function() {
    
    // Validar nombre (solo letras, espacios, guiones y puntos)
    function validateNombre(input) {
        const regex = /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\.]*$/;
        const value = input.value;
        
        if (!regex.test(value)) {
            showFieldError(input, 'Solo se permiten letras, espacios, guiones y puntos');
            return false;
        } else if (value.length > 255) {
            showFieldError(input, 'El nombre no puede tener más de 255 caracteres');
            return false;
        } else if (value.trim() === '') {
            showFieldError(input, 'El nombre es obligatorio');
            return false;
        } else {
            clearFieldError(input);
            return true;
        }
    }

    // Validar números enteros
    function validateInteger(input, min = 0, max = 999999) {
        const value = parseInt(input.value);
        
        if (isNaN(value)) {
            showFieldError(input, 'Debe ser un número entero válido');
            return false;
        } else if (value < min) {
            showFieldError(input, `El valor no puede ser menor a ${min}`);
            return false;
        } else if (value > max) {
            showFieldError(input, `El valor no puede ser mayor a ${max.toLocaleString()}`);
            return false;
        } else {
            clearFieldError(input);
            return true;
        }
    }

    // Validar precio
    function validatePrecio(input) {
        if (!input) return true;
        // Normalizar separador decimal: permitir coma o punto
        const normalized = (input.value || '').toString().replace(',', '.');
        const value = parseFloat(normalized);
        
        if (isNaN(value)) {
            showFieldError(input, 'Debe ser un precio válido');
            return false;
        } else if (value <= 0) {
            showFieldError(input, 'El precio debe ser mayor a 0');
            return false;
        } else if (value > 999999.99) {
            showFieldError(input, 'El precio no puede ser mayor a ₡999,999.99');
            return false;
        } else {
            clearFieldError(input);
            return true;
        }
    }

    // Validar fecha de vencimiento
    function validateFechaVencimiento(input) {
        if (!input.value) {
            clearFieldError(input);
            return true; // Es opcional
        }
        
        const fechaIngresada = new Date(input.value);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaIngresada <= hoy) {
            showFieldError(input, 'La fecha debe ser posterior a hoy');
            return false;
        } else {
            clearFieldError(input);
            return true;
        }
    }

    // Mostrar error en campo
    function showFieldError(input, message) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        
        let feedback = input.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    }

    // Limpiar error de campo
    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = '';
        }
    }

    // Aplicar validaciones a formularios
    function setupValidations(formPrefix) {
        console.log(`Configurando validaciones para: ${formPrefix}`);
        
        // Validar nombre
        const nombreInput = document.getElementById(`${formPrefix}_nombre`);
        if (nombreInput) {
            // Remover event listeners anteriores
            nombreInput.removeEventListener('input', nombreInput._validateHandler);
            nombreInput.removeEventListener('keypress', nombreInput._keypressHandler);
            
            // Crear nuevos handlers
            nombreInput._validateHandler = function() {
                validateNombre(this);
            };
            
            nombreInput._keypressHandler = function(e) {
                const regex = /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\.]$/;
                if (!regex.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                    e.preventDefault();
                }
            };
            
            nombreInput.addEventListener('input', nombreInput._validateHandler);
            nombreInput.addEventListener('keypress', nombreInput._keypressHandler);
        }

        // Validar campos numéricos enteros
        ['stock_actual', 'stock_minimo'].forEach(field => {
            const input = document.getElementById(`${formPrefix}_${field}`);
            if (input) {
                // Remover handlers anteriores
                input.removeEventListener('input', input._validateHandler);
                input.removeEventListener('keypress', input._keypressHandler);
                
                // Crear nuevos handlers
                input._validateHandler = function() {
                    validateInteger(this, 0, 999999);
                };
                
                input._keypressHandler = function(e) {
                    if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                        e.preventDefault();
                    }
                };
                
                input.addEventListener('input', input._validateHandler);
                input.addEventListener('keypress', input._keypressHandler);
            }
        });

        // Validar cantidad (mínimo 1)
        const cantidadInput = document.getElementById(`${formPrefix}_cantidad`);
        if (cantidadInput) {
            cantidadInput.removeEventListener('input', cantidadInput._validateHandler);
            cantidadInput.removeEventListener('keypress', cantidadInput._keypressHandler);
            
            cantidadInput._validateHandler = function() {
                validateInteger(this, 1, 999999);
            };
            
            cantidadInput._keypressHandler = function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            };
            
            cantidadInput.addEventListener('input', cantidadInput._validateHandler);
            cantidadInput.addEventListener('keypress', cantidadInput._keypressHandler);
        }

        // Validar precio
        const precioInput = document.getElementById(`${formPrefix}_precio`);
        if (precioInput) {
            precioInput.removeEventListener('input', precioInput._validateHandler);
            
            precioInput._validateHandler = function() {
                validatePrecio(this);
            };
            
            precioInput.addEventListener('input', precioInput._validateHandler);
            // Formatear a 2 decimales al salir
            precioInput.removeEventListener('blur', precioInput._formatHandler);
            precioInput._formatHandler = function() {
                const ok = validatePrecio(this);
                if (!ok) return;
                let v = (this.value || '').toString().replace(',', '.');
                const num = parseFloat(v);
                if (!isNaN(num)) {
                    // Mostrar con 2 decimales, usando coma si el usuario ingresó coma
                    const usedComma = /,/.test(this.value);
                    const formatted = num.toFixed(2);
                    this.value = usedComma ? formatted.replace('.', ',') : formatted;
                }
            };
            precioInput.addEventListener('blur', precioInput._formatHandler);
        }

        // Validar fecha de vencimiento
        const fechaInput = document.getElementById(`${formPrefix}_fecha_vencimiento`);
        if (fechaInput) {
            fechaInput.removeEventListener('change', fechaInput._validateHandler);
            
            fechaInput._validateHandler = function() {
                validateFechaVencimiento(this);
            };
            
            fechaInput.addEventListener('change', fechaInput._validateHandler);
            
            // Establecer fecha mínima (mañana)
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            fechaInput.min = tomorrow.toISOString().split('T')[0];
        }

        // Validaciones lógicas adicionales
        const stockActualInput = document.getElementById(`${formPrefix}_stock_actual`);
        const estadoSelect = document.getElementById(`${formPrefix}_estado`);
        
        if (stockActualInput && estadoSelect) {
            function validateStockEstado() {
                const stock = parseInt(stockActualInput.value) || 0;
                const estado = estadoSelect.value;
                
                if (stock === 0 && estado === 'Disponible') {
                    showFieldError(estadoSelect, 'Un insumo sin stock no puede estar disponible');
                } else if (stock > 0 && estado === 'Agotado') {
                    showFieldError(estadoSelect, 'Un insumo con stock no puede estar agotado');
                } else {
                    clearFieldError(estadoSelect);
                }
            }
            
            // Remover handlers anteriores
            stockActualInput.removeEventListener('change', stockActualInput._stockEstadoHandler);
            estadoSelect.removeEventListener('change', estadoSelect._stockEstadoHandler);
            
            // Crear nuevos handlers
            stockActualInput._stockEstadoHandler = validateStockEstado;
            estadoSelect._stockEstadoHandler = validateStockEstado;
            
            stockActualInput.addEventListener('change', stockActualInput._stockEstadoHandler);
            estadoSelect.addEventListener('change', estadoSelect._stockEstadoHandler);
        }
    }

    // Validar formulario antes de enviar
    function validateFormBeforeSubmit(form) {
        const inputs = form.querySelectorAll('input[required], select[required]');
        let allValid = true;
        
        inputs.forEach(input => {
            if (input.type === 'text' && input.name === 'nombre') {
                if (!validateNombre(input)) allValid = false;
            } else if (input.type === 'number') {
                if (input.name === 'precio') {
                    if (!validatePrecio(input)) allValid = false;
                } else if (input.name === 'cantidad') {
                    if (!validateInteger(input, 1, 999999)) allValid = false;
                } else {
                    if (!validateInteger(input, 0, 999999)) allValid = false;
                }
            } else if (input.type === 'date') {
                if (!validateFechaVencimiento(input)) allValid = false;
            } else if (input.tagName === 'SELECT') {
                if (!input.value) {
                    showFieldError(input, 'Este campo es obligatorio');
                    allValid = false;
                } else {
                    clearFieldError(input);
                }
            }
        });
        
        return allValid;
    }

    // Configurar validaciones para formulario de crear
    setupValidations('create');
    
    // Función global para configurar validaciones de editar
    window.setupEditValidations = function() {
        console.log('Configurando validaciones para modal de editar');
        setTimeout(() => {
            setupValidations('edit');
            
            // También configurar el evento de submit para el form de editar
            const editForm = document.getElementById('editForm');
            if (editForm) {
                // Remover handler anterior si existe
                if (editForm._submitHandler) {
                    editForm.removeEventListener('submit', editForm._submitHandler);
                }
                
                // Crear nuevo handler
                editForm._submitHandler = function(e) {
                    if (!validateFormBeforeSubmit(this)) {
                        e.preventDefault();
                        showAlert('Por favor corrige los errores en el formulario antes de continuar', 'error');
                    }
                };
                
                editForm.addEventListener('submit', editForm._submitHandler);
            }
        }, 100); // Pequeño delay para asegurar que el DOM esté listo
    };

    // Aplicar validación a formulario de crear
    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            if (!validateFormBeforeSubmit(this)) {
                e.preventDefault();
                showAlert('Por favor corrige los errores en el formulario antes de continuar', 'error');
            }
        });
    }

    // Limpiar validaciones cuando se cierra un modal
    window.clearValidations = function(formPrefix) {
        const form = document.getElementById(`${formPrefix}Form`);
        if (form) {
            // Limpiar todos los errores visuales
            const invalidInputs = form.querySelectorAll('.is-invalid, .is-valid');
            invalidInputs.forEach(input => {
                input.classList.remove('is-invalid', 'is-valid');
            });
            
            // Limpiar mensajes de error
            const feedbacks = form.querySelectorAll('.invalid-feedback');
            feedbacks.forEach(feedback => {
                feedback.textContent = '';
            });
        }
    };
});

// Función para mostrar alertas
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.maxWidth = '400px';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}