import _ from 'lodash';
window._ = _;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

try {
    // Log de configuración
    const viteConfig = {
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        host: import.meta.env.VITE_PUSHER_HOST,
        port: import.meta.env.VITE_PUSHER_PORT,
        scheme: import.meta.env.VITE_PUSHER_SCHEME,
    };

    console.log('[Bootstrap] Configuración VITE_PUSHER:', viteConfig);

    // Validar que tenemos al menos key y cluster
    if (!viteConfig.key) {
        throw new Error('VITE_PUSHER_APP_KEY no está configurado en .env');
    }
    if (!viteConfig.cluster) {
        throw new Error('VITE_PUSHER_APP_CLUSTER no está configurado en .env');
    }

    // Configurar Echo
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        wsHost: import.meta.env.VITE_PUSHER_HOST
            ? import.meta.env.VITE_PUSHER_HOST
            : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        encrypted: true,
    });

    console.log('✓ Echo inicializado correctamente');
    console.log('✓ Broadcaster: pusher');
    console.log('✓ Cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);

} catch (error) {
    console.error('✗ Error al inicializar Echo:', error);
    console.log('Intentando fallback a log broadcaster...');
    
    try {
        window.Echo = new Echo({
            broadcaster: 'log'
        });
        console.log('✓ Log broadcaster activado como fallback');
    } catch (fallbackError) {
        console.error('✗ Error al activar fallback:', fallbackError);
    }
}
