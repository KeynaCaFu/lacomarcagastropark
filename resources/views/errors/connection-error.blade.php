<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - Error de Conexión</title>

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
            --danger:        #E74C3C;
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

        .error-code {
            font-family: 'Cormorant Garamond', serif;
            font-size: 90px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), rgba(212,119,58,0.5));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            line-height: 1;
        }

        .error-icon {
            font-size: 50px;
            color: var(--danger);
            margin-bottom: 20px;
        }

        .error-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
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

        .error-details {
            background: rgba(212, 119, 58, 0.05);
            padding: 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 30px;
            border-left: 4px solid var(--primary);
            text-align: left;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .error-details strong {
            color: var(--text);
            display: block;
            margin-bottom: 5px;
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

        @media (max-width: 600px) {
            .logo {
                max-width: 140px;
            }

            .error-container {
                padding: 40px 20px;
            }

            .error-code {
                font-size: 70px;
            }

            .error-title {
                font-size: 26px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        .support-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--muted);
        }

        .support-info a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .support-info a:hover {
            color: #C26A32;
        }
    </style>
</head>
<body>
    <div class="error-container">
        {{-- <div class="logo-container">
            <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca Gastro Park" class="logo">
        </div> --}}
         <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="error-code">{{ $code ?? '503' }}</div>
       

        <h1 class="error-title">{{ $title ?? 'Error de Servidor' }}</h1>

        <p class="error-subtitle">
            {{ $message ?? 'Lamentablemente, no podemos conectar con nuestros sistemas en este momento. Puede haber un problema temporal con nuestros servidores.' }}
        </p>

        <div class="error-details">
            <strong><i class="fas fa-info-circle"></i> ¿Qué está pasando?</strong>
            La plaza gastronómica está experimentando dificultades técnicas. Nuestro equipo ya está trabajando para resolver el problema.
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="location.reload();">
                <i class="fas fa-redo icon-small"></i>
                Reintentar
            </button>
            <button class="btn btn-secondary" onclick="window.location.href = '/';">
                <i class="fas fa-home icon-small"></i>
                Ir al Inicio
            </button>
        </div>

        <div class="support-info">
            <p>¿El problema no se soluciona? <a href="mailto:soporte@lacomarca.com">Contacta con soporte técnico</a></p>
            <p style="margin-top: 8px;">
                <i class="fas fa-clock"></i> Tiempo de espera estimado: <strong>Menos de 15 minutos</strong>
            </p>
        </div>
    </div>
</body>
</html>
