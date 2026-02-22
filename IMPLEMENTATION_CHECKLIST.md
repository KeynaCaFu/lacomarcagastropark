CHECKLIST DE IMPLEMENTACIÓN - Google Auth en Laravel
========================================================

Marca cada paso a medida que lo completes:

## PREPARACIÓN INICIAL

☐ Hacer backup de la base de datos
☐ Commitear cambios actuales en git
☐ Verificar que tienes acceso a Google Cloud Console

## INSTALACIÓN DEL PAQUETE

☐ Ejecutar: composer require laravel/socialite
☐ Verificar que el paquete está instalado: composer show laravel/socialite
☐ Ejecutar: composer dump-autoload

## CONFIGURACIÓN DE GOOGLE CLOUD CONSOLE

☐ Ir a: https://console.cloud.google.com/
☐ Crear un proyecto nuevo (ej: "La Comarca Gastropark")
☐ Habilitar "Google+ API"
☐ En "Credenciales", crear "OAuth 2.0 Client ID (Web Application)"
☐ Agregar URI autorizado: http://localhost:8000/auth/google/callback
☐ Copiar el Client ID
☐ Copiar el Client Secret
☐ *IMPORTANTE*: Guardar estos valores en un lugar seguro

## CONFIGURACIÓN DE ARCHIVOS

☐ Editar .env y agregar:
  ☐ GOOGLE_CLIENT_ID=tu_client_id_aqui
  ☐ GOOGLE_CLIENT_SECRET=tu_secret_aqui
  ☐ (Opcional) GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

☐ Verificar que config/services.php tiene la sección 'google' configurada

☐ Verificar que routes/auth.php tiene importado GoogleController

☐ Verificar que routes/auth.php tiene las dos rutas:
  ☐ Route::get('/auth/google', ...)
  ☐ Route::get('/auth/google/callback', ...)

☐ Verificar que app/Http/Controllers/Auth/GoogleController.php existe

## BASE DE DATOS

☐ Ejecutar en la base de datos:
  ```sql
  ALTER TABLE tbuser 
  ADD COLUMN provider VARCHAR(255) NULL,
  ADD COLUMN provider_id VARCHAR(255) NULL,
  ADD COLUMN avatar VARCHAR(255) NULL;
  ```

☐ Verificar que existe el rol "Cliente":
  ```sql
  SELECT * FROM tbrole WHERE role_type = 'Cliente';
  ```
  Si no existe, ejecutar:
  ```sql
  INSERT INTO tbrole (role_type) VALUES ('Cliente');
  ```

☐ Verificar que el campo password permite NULL:
  ```sql
  ALTER TABLE tbuser MODIFY password VARCHAR(255) NULL;
  ```

## VISTA (LOGIN)

☐ Verificar que resources/views/auth/login.blade.php tiene el botón de Google:
  ```blade
  <a href="{{ route('auth.google') }}" class="btn-google">
    <i class="fa-brands fa-google"></i> Google
  </a>
  ```

☐ Verificar que los estilos CSS para .btn-google existen en la sección @push('styles')

☐ Verificar que Font Awesome está cargado (para el icono de Google)

☐ Verificar que el divider-social existe en los estilos

## MODELO

☐ Verificar que app/Models/Tbuser.php:
  ☐ Extiende Authenticatable
  ☐ Tiene $fillable con: 'provider', 'provider_id', 'avatar'
  ☐ Tiene $table = 'tbuser'
  ☐ Tiene $primaryKey = 'user_id'

## PRUEBA EN DESARROLLO

☐ Ejecutar: php artisan config:cache
☐ Ejecutar: php artisan route:list | grep google
  (Debe mostrar dos rutas: auth.google y auth.google.callback)

☐ Abrir navegador: http://localhost:8000/login
☐ Verificar que aparece el botón "Google"
☐ Hacer clic en el botón
☐ Se debe redirigir a Google
☐ Autorizar la aplicación

## DURANTE EL LOGIN CON GOOGLE

☐ ¿Redirige a Google correctamente?
☐ ¿Autorizar la aplicación?
☐ ¿Redirigir de vuelta a tu app?
☐ ¿Se crea el usuario en la BD?
☐ ¿Está autenticado y redirigido al dashboard?

## DESPUÉS DEL LOGIN

☐ Verificar que:
  ☐ El usuario está en la tabla tbuser
  ☐ Tiene provider = 'google'
  ☐ Tiene provider_id relleno con el ID de Google
  ☐ Tiene avatar relleno con la URL de la foto
  ☐ Tiene role_id = (id del rol Cliente)
  ☐ Tiene status = 'Active'
  ☐ Tiene password relleno (contraseña random)

## PRUEBA: USUARIO EXISTENTE

☐ Ya con un usuario creado por Google, cerrar sesión
☐ Intentar loginearse nuevamente con Google
☐ Debe iniciar sesión sin crear otro usuario duplicado
☐ Verificar en BD que solo uno existe con ese email

## PRUEBA: MÚLTIPLES USUARIOS

☐ Crear usuario por Google con Email A
☐ Crear usuario por Google con Email B
☐ Verificar que no hay errores de duplicación
☐ Verificar que cada uno puede loginearse con su Google

## VERIFICAR SEGURIDAD

☐ El Client ID y Secret NO están en el código
☐ El Client ID y Secret están en .env
☐ .env está en .gitignore
☐ Las variables se llaman correctamente (GOOGLE_CLIENT_ID, etc.)

## VERIFICAR MANEJO DE ERRORES

☐ Si hay error en Google, muestra mensaje legible
☐ Si no existe el rol Cliente, da error específico
☐ Si hay error en BD, da error específico

## DOCUMENTACIÓN

☐ Legalización está actualizada con nuevos archivos:
  ☐ app/Http/Controllers/Auth/GoogleController.php
  ☐ GOOGLE_AUTH_SETUP.md
  ☐ GOOGLE_AUTH_QUICK_START.md
  ☐ DATABASE_GOOGLE_AUTH.sql
  ☐ BLADE_GOOGLE_AUTH_EXAMPLES.blade.php
  ☐ GOOGLE_AUTH_ADVANCED.txt

☐ Team está informado de los cambios
☐ Se documentó en el README o en wiki

## PRODUCCIÓN

☐ Cambiar GOOGLE_REDIRECT_URI a dominio real (no localhost)
☐ Agregar nuevo dominio en Google Console:
  ☐ https://tudominio.com/auth/google/callback
☐ Verificar SSL (HTTPS) en producción
☐ Verificar que APP_URL en .env es correcto
☐ Cambiar APP_DEBUG a false

## PROBLEMAS Y SOLUCIONES

Si aparece alguno de estos errores:

❌ "OAuth redirect mismatch"
✅ Solución: Verificar URL exacta en Google Console (http/https, www, puerto)

❌ "Class 'Socialite' not found"
✅ Solución: composer require laravel/socialite && composer dump-autoload

❌ "SQLSTATE[HY000]: General error: 1364..."
✅ Solución: ALTER TABLE tbuser MODIFY password VARCHAR(255) NULL;

❌ "Undefined variable: GOOGLE_CLIENT_ID"
✅ Solución: Agregar variables a .env y ejecutar php artisan config:cache

❌ "No se muestra el botón de Google"
✅ Solución: Verificar que Font Awesome 6+ está cargado

❌ "Se crea usuario duplicado"
✅ Solución: El controlador busca por email y provider_id, revisar lógica

❌ "El usuario no se autentica"
✅ Solución: Verificar en BD que role_id existe y está correcto

## VERIFICACIÓN FINAL

Hacer checklist de funcionalidad completa:

☐ Nuevo usuario por Google: Crea usuario + autentica
☐ Usuario existente por Google: Autentica sin duplicar
☐ Datos sincronizados: avatar, full_name, email, provider
☐ Mensajes de error claros
☐ Redireccionamientos correctos
☐ Panel de usuario muestra provider de autenticación
☐ Logout funciona correctamente
☐ No hay errores en logs

## COMMIT Y PUSH

☐ Agregar archivos nuevos a git:
  git add app/Http/Controllers/Auth/GoogleController.php
  git add GOOGLE_AUTH_*.md
  git add DATABASE_GOOGLE_AUTH.sql
  git add BLADE_GOOGLE_AUTH_EXAMPLES.blade.php

☐ Editar archivos en git:
  git add config/services.php
  git add routes/auth.php
  git add resources/views/auth/login.blade.php

☐ Commit:
  git commit -m "Feat: Implement Google OAuth authentication"

☐ Push:
  git push origin main

## NOTIFICACIONES

☐ Avisarle al team sobre:
  ☐ Nuevas variables de entorno necesarias
  ☐ Cambios en la BD (nuevas columnas)
  ☐ Nueva ruta de login con Google
  ☐ Ubicación de la documentación

## MONITOREO INICIAL

☐ Monitorear logs de la aplicación en primeras 24 horas
☐ Verificar que no hay errores recurrentes
☐ Verificar que usuarios nuevos se crean correctamente
☐ Verificar que no hay duplicación de usuarios

## ¡COMPLETADO! 🎉

Si llegaste hasta aquí y todo está marcado, la integración de Google Auth
está lista para producción.

¿Necesitas agregar más proveedores? Consulta GOOGLE_AUTH_ADVANCED.txt
para ejemplos de múltiples proveedores (Facebook, GitHub, etc.)

Contáctanos si tienes preguntas o necesitas ajustes personalizados.
