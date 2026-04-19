<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #D4773A 0%, #B85A2A 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px; }
        .welcome { font-size: 16px; margin-bottom: 20px; }
        .password-box { background-color: #f9f9f9; border-left: 4px solid #D4773A; padding: 20px; margin: 20px 0; font-family: monospace; }
        .password-code { font-size: 24px; font-weight: bold; color: #D4773A; letter-spacing: 2px; text-align: center; margin: 10px 0; }
        .warning { background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 15px; margin: 20px 0; color: #856404; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #dee2e6; }
        .button { display: inline-block; padding: 12px 30px; background-color: #D4773A; color: white; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .link { color: #D4773A; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>La Comarca Gastropark</h1>
            <p style="margin: 10px 0 0; font-size: 14px;">Recuperación de Contraseña</p>
        </div>

        <div class="content">
            <div class="welcome">
                Hola <strong>{{ $user->full_name ?? $user->name }}</strong>,
            </div>

            <p>Hemos recibido una solicitud para cambiar la contraseña de tu cuenta. Aquí está tu contraseña temporal:</p>

            <div class="password-box">
                <div style="font-size: 12px; color: #666; margin-bottom: 10px;">Contraseña Temporal:</div>
                <div class="password-code">{{ $temporaryPassword }}</div>
            </div>

            <div class="warning">
                <strong>Importante:</strong> Esta contraseña temporal es válida solo por <strong>15 minutos</strong>. Después de ese tiempo, deberás solicitar una nueva.
            </div>

            <h3>¿Qué hacer ahora?</h3>
            <ol>
                <li>1. Ve a tu perfil en La Comarca Gastropark</li>
                <li>2. Haz clic en "Cambiar contraseña"</li>
                <li>3. En el formulario, selecciona "Tengo una contraseña temporal"</li>
                <li>4. Pega la contraseña temporal anterior</li>
                <li>5. Ingresa tu nueva contraseña deseada</li>
                <li>6. Confirma la nueva contraseña</li>
            </ol>

            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Si no solicitaste este cambio de contraseña, puedes ignorar este correo. Tu contraseña actual sigue siendo válida.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} La Comarca Gastropark. Todos los derechos reservados.</p>
            <p>Si tienes problemas, contáctanos en <span class="link">lacomarcagastropark@gmail.com</span></p>
        </div>
    </div>
</body>
</html>
