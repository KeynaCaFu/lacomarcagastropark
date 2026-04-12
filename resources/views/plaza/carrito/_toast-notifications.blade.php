<script>
    // Sistema mejorado de notificaciones
    window.showNotification = function(config) {
        // Crear contenedor de notificaciones si no existe
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        // Determinar el ícono según el tipo
        let iconClass = 'fas fa-check-circle';
        let notificationType = 'info';
        
        if (config.icon === 'success' || config.type === 'success') {
            iconClass = 'fas fa-check-circle';
            notificationType = 'success';
        } else if (config.icon === 'error' || config.type === 'error') {
            iconClass = 'fas fa-times-circle';
            notificationType = 'error';
        } else if (config.icon === 'warning' || config.type === 'warning') {
            iconClass = 'fas fa-exclamation-circle';
            notificationType = 'warning';
        }

        // Crear el elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification-item ${notificationType}`;
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon-wrapper">
                    <i class="${iconClass}"></i>
                </div>
                <div class="notification-text">
                    <p class="notification-title">${config.title || 'Notificación'}</p>
                    ${config.message ? `<p class="notification-message">${config.message}</p>` : ''}
                </div>
            </div>
            <div class="notification-progress"></div>
        `;

        container.appendChild(notification);

        // Animación de entrada
        const timeout = config.timer || 5500;
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                notification.remove();
            }, 400);
        }, timeout);
    };

    // Para compatibilidad, hacer que showToast use el nuevo sistema
    const originalShowToast = window.showToast;
    window.showToast = function(config) {
        window.showNotification(config);
    };
</script>
