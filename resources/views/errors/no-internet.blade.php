<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - Sin Conexión</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:       #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --primary-glow:  rgba(212,119,58,0.25);
            --bg:            #0A0908;
            --surface:       #111009;
            --card:          #161310;
            --card-hover:    #1D1914;
            --border:        #252018;
            --border-light:  #302820;
            --text:          #F5F0E8;
            --muted:         #7A7060;
            --warning:       #F39C12;
            --radius:        14px;
            --radius-sm:     8px;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .error-container {
            width: 100%;
            max-width: 600px;
            background: var(--card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            padding: 60px 40px;
            text-align: center;
            backdrop-filter: blur(10px);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo {
            max-width: 180px;
            height: auto;
            opacity: 0.95;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 0.95;
                transform: translateY(0);
            }
        }

        .error-icon {
            font-size: 80px;
            color: var(--warning);
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .error-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text);
        }

        .error-subtitle {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error-message {
            background: rgba(243, 156, 18, 0.1);
            border-left: 4px solid var(--warning);
            padding: 15px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 30px;
            text-align: left;
            font-size: 14px;
            color: var(--text);
        }

        .connectivity-checklist {
            background: rgba(212, 119, 58, 0.05);
            padding: 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 30px;
            border: 1px solid var(--border);
            text-align: left;
        }

        .connectivity-checklist h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 13px;
            color: var(--muted);
        }

        .checklist-item:last-child {
            margin-bottom: 0;
        }

        .checklist-icon {
            color: var(--primary);
            min-width: 20px;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #C26A32;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(212, 119, 58, 0.3);
        }

        .btn-secondary {
            background: var(--surface);
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--card-hover);
            border-color: var(--primary);
        }

        .icon-small {
            font-size: 16px;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(243, 156, 18, 0.1);
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            color: var(--warning);
            font-size: 13px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--warning);
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        @media (max-width: 600px) {
            .logo {
                max-width: 140px;
            }

            .error-container {
                padding: 40px 20px;
            }

            .error-title {
                font-size: 28px;
            }

            .error-icon {
                font-size: 60px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        .footer-text {
            margin-top: 30px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        .footer-text a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-text a:hover {
            color: #C26A32;
        }

        .offline-note {
            background: rgba(212, 119, 58, 0.1);
            padding: 15px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="error-container">
        
        <div class="status-indicator">
            <span class="status-dot"></span>
            Sin conexión a internet
        </div>

        <div class="error-icon">
            <i class="fas fa-wifi"></i>
        </div>

        {{-- <div class="logo-container">
            <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca Gastro Park" class="logo">
        </div> --}}

        <h1 class="error-title">Conexión Perdida</h1>

        <p class="error-subtitle">
            La plaza gastronómica requiere conexión a internet para acceder a todo el contenido.
        </p>

        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> 
            No detectamos conexión a internet en este momento. Verifica tu conexión y vuelve a intentar.
        </div>

        <div class="connectivity-checklist">
            <h3><i class="fas fa-list-check"></i> Opciones para verificar</h3>
            <div class="checklist-item">
                <span class="checklist-icon">1.</span>
                <span>Asegúrate de that tu conexión WiFi esté activada</span>
            </div>
            <div class="checklist-item">
                <span class="checklist-icon">2.</span>
                <span>Verifica que otros dispositivos puedan acceder a internet</span>
            </div>
            <div class="checklist-item">
                <span class="checklist-icon">4.</span>
                <span>Intenta acceder nuevamente a la plaza</span>
            </div>
        </div>

        <div class="offline-note">
            <i class="fas fa-info-circle"></i> 
            Esta página se recargará automáticamente cuando recuperes la conexión.
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="location.reload();">
                <i class="fas fa-redo icon-small"></i>
                Reintentar Ahora
            </button>
            <button class="btn btn-secondary" onclick="window.history.back();">
                <i class="fas fa-arrow-left icon-small"></i>
                Atrás
            </button>
        </div>

        <div class="footer-text">
            <p>
                <i class="fas fa-phone"></i> Contáctanos: <strong>+506 6141-5178</strong><br>
                La Comarca Gastro Park - Tu plaza gastronómica
            </p>
        </div>
    </div>

    <script>
        // Detectar automáticamente cuando la conexión vuelve
        let isOnline = navigator.onLine;

        window.addEventListener('online', function() {
            if (!isOnline) {
                isOnline = true;
                // Esperar 2 segundos y recargar
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });

        window.addEventListener('offline', function() {
            isOnline = false;
        });

        // Intento periódico de reconexión
        setInterval(function() {
            if (isOnline) {
                fetch(window.location.href, { method: 'HEAD', mode: 'no-cors' })
                    .then(function() {
                        location.reload();
                    })
                    .catch(function() {
                        // Sin conexión aún
                    });
            }
        }, 15000); // Cada 10 segundos
    </script>
</body>
</html>
