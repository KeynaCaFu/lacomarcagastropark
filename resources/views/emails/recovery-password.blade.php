<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #e18018, #915016);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background-color: white;
            padding: 20px;
        }
        .password-box {
            background-color: #f9f9f9;
            border: 2px solid #e18018;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .password-box .label {
            font-weight: bold;
            color: #e18018;
            margin-bottom: 10px;
        }
        .password-box .password {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            font-family: monospace;
            letter-spacing: 2px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            background-color: #f4f4f4;
            border-radius: 0 0 5px 5px;
        }
        a {
            color: #e18018;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Recuperación de Contraseña</h2>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $userName }}</strong>,</p>
            
            <p>Hemos recibido una solicitud para recuperar tu contraseña. Tu contraseña temporal es:</p>
            
            <div class="password-box">
                <div class="label">Contraseña Temporal:</div>
                <div class="password">{{ $tempPassword }}</div>
            </div>
            
            <p><strong>Instrucciones:</strong></p>
            <ol>
                <li>Ingresa a la plataforma con tu email y la contraseña temporal proporcionada</li>
                <li>Se te solicitará cambiar tu contraseña por una nueva</li>
                <li>Elige una contraseña segura que solo tú conozcas</li>
            </ol>
            
            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul>
                    <li>Esta contraseña temporal es válida por 24 horas</li>
                    <li>Por seguridad, cámbiala tan pronto como inicies sesión</li>
                    <li>Si no solicitaste esta recuperación, ignora este email</li>
                    <li>Nunca compartas tu contraseña con nadie</li>
                </ul>
            </div>
            
            <p>Si tienes problemas para acceder, contacta al equipo de soporte.</p>
            
            <p>Saludos,<br>
            <strong>Equipo de La Comarca Gastro Park</strong></p>
        </div>
        
        <div class="footer">
            <p>Este es un email automático, por favor no respondas a este mensaje.</p>
            <p>&copy; 2025 La Comarca Gastro Park. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
