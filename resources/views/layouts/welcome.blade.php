<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenido - La Comarca')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Personalizado La Comarca -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #485a1a 0%, #232c0c 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-icon {
            color: #485a1a;
            font-size: 5rem;
            margin-bottom: 30px;
            text-shadow: 0 4px 8px rgba(72, 90, 26, 0.3);
        }
        
        .welcome-title {
            color: #232c0c;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-subtitle {
            color: #6c757d;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .btn-gestionar {
            background: linear-gradient(135deg, #485a1a, #5a6d20);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 50px;
            box-shadow: 0 8px 16px rgba(72, 90, 26, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-gestionar:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(72, 90, 26, 0.4);
            color: white;
        }
        
        .btn-gestionar:active {
            transform: translateY(-1px);
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 153, 0, 0.1);
        }
        
        .floating-elements::before {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::after {
            width: 150px;
            height: 150px;
            bottom: 10%;
            right: 10%;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .feature-icons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            opacity: 0.7;
        }
        
        .feature-icons i {
            font-size: 2rem;
            color: #485a1a;
        }
        
        @media (max-width: 768px) {
            .welcome-card {
                margin: 20px;
                padding: 40px 30px;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .welcome-subtitle {
                font-size: 1rem;
            }
            
            .btn-gestionar {
                padding: 12px 30px;
                font-size: 1.1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="floating-elements"></div>
    
    <div class="welcome-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>