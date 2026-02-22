# GUÍA RÁPIDA: Autenticación con Google en Laravel

## 1. Instalación (30 segundos)

```bash
composer require laravel/socialite
```

## 2. Configuración de Google Console (5 minutos)

1. Ve a: https://console.cloud.google.com/
2. Crea un proyecto
3. Habilita "Google+ API"
4. Crea credenciales OAuth 2.0 (tipo: Web Application)
5. Autoriza: `http://localhost:8000/auth/google/callback`
6. Copia: Client ID y Client Secret

## 3. Agregar Variables al .env

```env
GOOGLE_CLIENT_ID=tu_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=tu_secret_aqui
```

## 4. Verificar Base de Datos

```sql
-- Agregar columnas a tbuser si no existen
ALTER TABLE tbuser ADD COLUMN provider VARCHAR(255) NULL;
ALTER TABLE tbuser ADD COLUMN provider_id VARCHAR(255) NULL;
ALTER TABLE tbuser ADD COLUMN avatar VARCHAR(255) NULL;

-- Asegurar que existe el rol Cliente
INSERT INTO tbrole (role_type) VALUES ('Cliente');
```

## 5. ¡Listo!

Ya está todo configurado. El botón de Google aparece automáticamente en el login.

## Archivos Creados/Modificados

✅ **Creado:**
- `app/Http/Controllers/Auth/GoogleController.php` - Controlador completocon toda la lógica

✅ **Modificado:**
- `config/services.php` - Configuración de Google
- `routes/auth.php` - Rutas de Google
- `resources/views/auth/login.blade.php` - Botón de Google

## Cómo Funciona

1. Usuario hace clic en "Iniciar sesión con Google"
2. Se autentica en Google
3. Si es nuevo, se crea automáticamente el usuario con:
   - full_name: Nombre de Google
   - email: Email de Google
   - role_id: Cliente (automático)
   - provider: 'google'
   - provider_id: ID de Google
   - avatar: Foto de Google
4. Inicia sesión automáticamente y va al dashboard

## Preguntas Frecuentes

**P: ¿Qué pasa si el usuario ya existe?**
A: Se inicia sesión directamente sin crear otro usuario.

**P: ¿Se guarda la contraseña de Google?**
A: No. Se genera una contraseña random (no es necesaria para Google Auth).

**P: ¿Puedo asociar un usuario existente con Google?**
A: Sí, si comparten el mismo email, se asociarán automáticamente.

**P: ¿Dónde está el botón de Google?**
A: En `resources/views/auth/login.blade.php` (ya agregado).

**P: ¿Qué hacer si da error "OAuth redirect mismatch"?**
A: La URL en Google Console debe ser idéntica a la de tu aplicación.

## Soporte

Para más detalles, ver: `GOOGLE_AUTH_SETUP.md`
