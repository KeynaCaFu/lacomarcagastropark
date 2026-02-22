INSTALACIÓN Y CONFIGURACIÓN DE AUTENTICACIÓN CON GOOGLE
========================================================

## PASO 1: Instalar Socialite

```bash
composer require laravel/socialite
```

## PASO 2: Obtener Credenciales de Google

1. Ir a: https://console.cloud.google.com/
2. Crear un nuevo proyecto o seleccionar uno existente
3. Habilitar la API de Google+
4. Ir a "Credenciales" (Credentials)
5. Crear una "OAuth 2.0 Client ID" para Aplicación web
6. Agregar las URLs autorizadas:
   - Orígenes autorizados: http://localhost:8000, http://tudominio.com
   - URIs autorizados: http://localhost:8000/auth/google/callback, http://tudominio.com/auth/google/callback
7. Copiar el Client ID y Client Secret

## PASO 3: Configurar el archivo .env

Agregar las siguientes variables al archivo .env:

```env
GOOGLE_CLIENT_ID=tu_client_id_aqui.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_client_secret_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Para producción, cambiar localhost:8000 por tu dominio real.

## PASO 4: Verificar la Tabla tbuser

La tabla debe tener estos campos:
- user_id (PRIMARY KEY)
- full_name
- email (UNIQUE)
- phone (NULLABLE)
- password
- role_id (FOREIGN KEY)
- status (DEFAULT 'Active')
- provider (NULLABLE) - Para guardar 'google'
- provider_id (NULLABLE) - Para guardar el ID de Google
- avatar (NULLABLE) - Para guardar la foto de perfil

Si necesitas agregar los campos a una tabla existente sin migraciones:

```sql
ALTER TABLE tbuser ADD COLUMN provider VARCHAR(255) NULL;
ALTER TABLE tbuser ADD COLUMN provider_id VARCHAR(255) NULL;
ALTER TABLE tbuser ADD COLUMN avatar VARCHAR(255) NULL;
```

## PASO 5: Verificar el Modelo Tbuser

El modelo debe extender Authenticatable:

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Tbuser extends Authenticatable
{
    protected $table = 'tbuser';
    protected $primaryKey = 'user_id';
    public $timestamps = false;
    
    protected $fillable = [
        'full_name', 'email', 'phone', 'password', 'role_id', 'status',
        'provider', 'provider_id', 'avatar'
    ];
    
    // ... resto del modelo
}
```

## PASO 6: Asegurar que existe el Rol "Cliente"

La base de datos debe tener un rol con role_type = 'Cliente':

```sql
INSERT INTO tbrole (role_type) VALUES ('Cliente');
```

## PASO 7: Usar el Botón en el Formulario (YA AGREGADO)

El botón de Google ya está agregado en el formulario de login en:
`resources/views/auth/login.blade.php`

Se ve así en el HTML:
```blade
<a href="{{ route('auth.google') }}" class="btn-google">
    <i class="fa-brands fa-google"></i> Google
</a>
```

## PASO 8: Probar la Autenticación

1. Ir a: http://localhost:8000/login
2. Hacer clic en el botón "Google"
3. Autorizar la aplicación
4. Se creará automáticamente un usuario si no existe
5. Se iniciará sesión automáticamente

## FLUJO DE AUTENTICACIÓN

1. Usuario hace clic en "Iniciar sesión con Google"
2. Se redirige a Google para autenticación
3. Google redirige de vuelta con un código
4. El controlador GoogleController obtiene los datos del usuario
5. Si el usuario existe, se inicia sesión
6. Si no existe, se crea un nuevo usuario con:
   - full_name: Nombre de Google
   - email: Email de Google
   - role_id: Cliente (automático)
   - status: Active
   - provider: 'google'
   - provider_id: ID de Google
   - avatar: Foto de Google
7. Se inicia sesión automáticamente y redirige al dashboard

## SOLUCIÓN DE PROBLEMAS

### "SQLSTATE[HY000]: General error: 1364 Field 'password' doesn't have a default value"
- Solución: El campo password debe permitir NULL o tener un valor por defecto. El controlador genera una contraseña random.

### "Undefined variable: GOOGLE_CLIENT_ID"
- Solución: Asegurar que las variables estén en el archivo .env (no en .env.example)
- Ejecutar: `php artisan config:cache`

### "OAuth redirect mismatch"
- Solución: La URL en Google Console debe coincidir exactamente con GOOGLE_REDIRECT_URI
- Verificar: http://localhost:8000/auth/google/callback (con protocolo http/https correcto)

### "Cannot verify CSRF token mismatch"
- Solución: El botón de Google es un <a> tag que redirige directamente (sin POST), entonces no hay CSRF

## ARCHIVOS MODIFICADOS Y CREADOS

✅ Creados:
- app/Http/Controllers/Auth/GoogleController.php - Controlador con lógica completa
- GOOGLE_AUTH_SETUP.md - Este archivo (instrucciones)

✅ Modificados:
- config/services.php - Agregada configuración de Google
- routes/auth.php - Agregadas rutas de Google
- resources/views/auth/login.blade.php - Agregado botón de Google

## VARIABLES DE ENTORNO NECESARIAS

```env
APP_URL=http://localhost:8000  # Importante para la URL de callback
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback  # Opcional, se genera automáticamente
```

## ESTRUCTURA DEL CONTROLADOR

El GoogleController tiene 3 métodos:

1. **redirectToGoogle()**: Redirige a Google para autenticación
2. **handleGoogleCallback()**: 
   - Obtiene datos del usuario desde Google
   - Busca si el usuario existe
   - Si no existe, lo crea con role_id de Cliente
   - Inicia sesión automáticamente
   - Redirige al dashboard
3. **logout()**: Cierra la sesión

## SEGURIDAD

- Los datos del usuario se validan desde Google
- La contraseña se genera de forma segura (bcrypt)
- El estado del usuario se establece como "Active" por defecto
- Se mantiene la separación de proveedores (Google, otros futuros, etc.)

¡Listo! Con esto tu autenticación con Google debería funcionar perfectamente.
