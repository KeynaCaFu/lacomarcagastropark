<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenido - La Comarca')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS Personalizado La Comarca -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

         .logo-img {
            max-width: 273px;
                height: auto;
                margin: -105px auto -80px auto;
        }
        
        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .welcome-card {
            background: rgb(13, 14, 12);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(57, 58, 55, 0.3);
            padding: 43px 60px;
            text-align: center;
            max-width: 514px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(26, 24, 24, 0.2);
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
            color: #161819;
            font-size: 1.2rem;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .btn-gestionar {
            background: linear-gradient(-1deg, #e18018, #915016);
            color: white;
            border: none;
            padding: 8px 34px;
            font-size: 1.3rem;
            font-weight: 601;
            border-radius: 37px;
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
        
        @media (max-width: 992px) {
            .welcome-card {
                padding: 40px 40px;
            }
            .logo-img {
                max-width: 200px;
                margin: -70px auto -50px auto;
            }
        }

        @media (max-width: 768px) {
            .welcome-card {
                margin: 20px;
                padding: 35px 24px;
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

            .logo-img {
                max-width: 160px;
                margin: -50px auto -30px auto;
            }
        }

        @media (max-width: 480px) {
            .welcome-card {
                margin: 12px;
                padding: 28px 16px;
            }
            .logo-img {
                max-width: 130px;
                margin: -35px auto -20px auto;
            }
            .welcome-title {
                font-size: 1.6rem;
            }
            .btn-gestionar {
                padding: 10px 24px;
                font-size: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    
    <div class="welcome-container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>