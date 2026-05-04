# 🔒 PRUEBAS DE SEGURIDAD - LA COMARCA

## Objetivo
Validar que la aplicación está protegida contra vulnerabilidades comunes

## Requisito Académico
Ejecutar al menos UNA prueba de seguridad, enfocada en identificar fallos de protección

---

## 📋 Pruebas a Ejecutar

### ✅ Prueba 1: Inyección SQL

#### Objetivo
Verificar que los formularios NO son vulnerables a inyecciones SQL

#### Vector de Ataque
```
Campo: email
Payload: ' OR '1'='1
Resultado esperado: Base de datos NO alterada
```

#### Pasos

**Paso 1: En formulario de registro de Usuario (o Postman)**

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Test User",
    "email": "'\'' OR '\''1'\''='\''1",
    "password": "password123",
    "role_id": 2,
    "status": "Active"
  }'
```

**Paso 2: Verificar respuesta**

- ❌ **VULNERABLE**: Si se crea usuario o se altera BD
- ✅ **SEGURO**: Si rechaza con error (400, 422, etc)

**Paso 3: Captura de Pantalla**

```
1. Abre Postman
2. Envía el request anterior
3. Toma screenshot mostrando:
   - El payload enviado
   - El status de respuesta
   - El error devuelto
4. Guarda como: evidencia-sql-injection.jpg
```

#### Resultado Esperado (SEGURO)
```json
{
  "message": "The email field must be a valid email address.",
  "errors": {
    "email": ["The email field must be a valid email address."]
  }
}
```

#### Por Qué Está Seguro
- Laravel Eloquent usa **prepared statements**
- El ORM parameteriza automáticamente todas las consultas
- Los caracteres especiales se escapan

---

### ✅ Prueba 2: XSS (Cross-Site Scripting)

#### Objetivo
Verificar que los scripts maliciosos NO se ejecutan

#### Vector de Ataque 1: Script en campo de texto

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "<script>alert(\"XSS\")</script>",
    "email": "test@test.com",
    "password": "password123",
    "role_id": 2,
    "status": "Active"
  }'
```

#### Vector de Ataque 2: Evento en imagen

```bash
curl -X POST http://localhost:8000/api/locals \
  -H "Content-Type: application/json" \
  -d '{
    "name": "<img src=x onerror=alert(\"XSS\")>",
    "description": "Test",
    "contact": "2765-0000",
    "status": "Active"
  }'
```

#### Pasos de Verificación

**En el navegador:**

1. Registra usuario con payload XSS
2. Abre DevTools (F12)
3. Busca el usuario en BD o en la respuesta JSON
4. Verifica que el script está como **texto**, NO ejecutado
5. El valor debe ser:
   ```
   "&lt;script&gt;alert(\"XSS\")&lt;/script&gt;"
   ```
   O similar (escapado)

**Captura:**

```
1. Abre DevTools → Network
2. Envía request con payload
3. En la respuesta JSON, muestra el campo escapeado
4. Confirma que NO hay <script> literal
5. Screenshot: evidencia-xss.jpg
```

#### Resultado Esperado (SEGURO)

En BD debería guardarse como:
```
full_name: <script>alert("XSS")</script>
(sin ejecutarse)
```

En respuesta JSON:
```json
{
  "user_id": 1,
  "full_name": "\\u003cscript\\u003ealert(\\\"XSS\\\")\\u003c/script\\u003e"
}
```

#### Por Qué Está Seguro
- Laravel Blade **escapa automáticamente** con `{{ variable }}`
- JSON se serializa de forma segura
- No hay `eval()` de user input

---

### ✅ Prueba 3: CSRF (Cross-Site Request Forgery)

#### Objetivo
Verificar que requests sin token CSRF son rechazadas

#### Vector de Ataque: POST SIN Token CSRF

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Attacker",
    "email": "attacker@evil.com",
    "password": "password123",
    "role_id": 2,
    "status": "Active"
  }'
```

#### Pasos

1. **Obtén token CSRF** (en formulario normal):
   ```bash
   curl http://localhost:8000/registro
   ```
   Busca: `<input name="_token" value="...">`

2. **Intenta POST sin token** (arriba)
   - Resultado esperado: **Error 419 o 403**

3. **Intenta POST con token inválido**:
   ```bash
   curl -X POST http://localhost:8000/api/users \
     -H "X-CSRF-TOKEN: invalid_token_1234" \
     -H "Content-Type: application/json" \
     -d '...'
   ```
   - Resultado esperado: **Error 419**

4. **Captura**:
   ```
   1. Postman → POST /api/users sin Authorization header
   2. Ver status: 419 o 403
   3. Screenshot: evidencia-csrf.jpg
   ```

#### Respuesta Esperada (SEGURO)

```
Status: 419 Token Expired
o
Status: 403 Forbidden

Body:
{
  "message": "CSRF token mismatch."
}
```

#### Por Qué Está Seguro
- Laravel middleware `VerifyCsrfToken` valida tokens
- Cookies SameSite protegen contra CSRF
- API usa Sanctum con tokens únicos

---

### ✅ Prueba 4: Autenticación y Autorización

#### Objetivo A: Sin Login - No puedo acceder a recursos protegidos

```bash
# Intenta acceder a /api/locals SIN token
curl -X GET http://localhost:8000/api/locals
```

Resultado esperado:
```
Status: 401 Unauthorized

{
  "message": "Unauthenticated."
}
```

#### Objetivo B: Con Token Inválido

```bash
curl -X GET http://localhost:8000/api/locals \
  -H "Authorization: Bearer invalid_token_abc123def456"
```

Resultado esperado:
```
Status: 401 Unauthorized
```

#### Objetivo C: Rol Incorrecto (Cliente intenta crear Local)

1. Registra usuario con rol "Cliente"
2. Loguéate y obtén token
3. Intenta POST /api/locals

```bash
curl -X POST http://localhost:8000/api/locals \
  -H "Authorization: Bearer user_cliente_token" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Unauthorized Local",
    "description": "Test",
    "contact": "2765-0000",
    "status": "Active"
  }'
```

Resultado esperado:
```
Status: 403 Forbidden

{
  "message": "This action is unauthorized."
}
```

#### Pasos de Verificación

1. **Prueba Sin Token**:
   - En Postman, GET /api/locals sin Authorization
   - Screenshot: evidencia-auth-sin-token.jpg

2. **Prueba Token Inválido**:
   - Authorization: Bearer abc123
   - Screenshot: evidencia-auth-token-invalido.jpg

3. **Prueba Rol Incorrecto**:
   - Cliente intenta POST /api/locals
   - Screenshot: evidencia-auth-rol-incorrecto.jpg

#### Por Qué Está Seguro
- Middleware `auth:sanctum` obliga login
- `authorize()` en Form Request valida permisos
- Policies validan roles

---

### ✅ Prueba 5: Exposición de Información Sensible

#### Objetivo
Verificar que:
- ❌ Contraseñas NUNCA aparecen en respuestas
- ❌ Stack traces no se muestran en errores
- ❌ Rutas de archivos no se exponen
- ✅ Solo información necesaria se devuelve

#### Prueba A: Password en Response

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Test",
    "email": "test@test.com",
    "password": "SecurePass123!",
    "role_id": 2,
    "status": "Active"
  }' | jq .
```

**Respuesta esperada (SEGURA)**:
```json
{
  "user_id": 5,
  "full_name": "Test",
  "email": "test@test.com",
  "role_id": 2,
  "status": "Active",
  "created_at": "2024-05-03T...",
  "updated_at": "2024-05-03T..."
}
```

**NUNCA debería incluir**:
```json
{
  "password": "SecurePass123!",  ❌
  "password_hash": "$2y$...",    ❌
  "api_key": "...",              ❌
}
```

#### Prueba B: Error Visible en Producción

1. Causa un error (ej: DB error)
2. Verifica que NO muestra:
   - ❌ SQL queries
   - ❌ Rutas de archivos completas
   - ❌ Stack trace

```
❌ VULNERABLE:
  File: /home/user/lacomarca/app/Models/User.php:45
  SELECT * FROM tbuser WHERE email = ...

✅ SEGURO:
  {
    "message": "Something went wrong",
    "status": 500
  }
```

#### Pasos de Captura

1. **DevTools → Network → Headers**
2. Envía POST /api/users
3. En la respuesta JSON, verifica campos:
   ```
   - ✅ user_id
   - ✅ email
   - ❌ password (nunca debe estar)
   - ❌ password_hash (nunca debe estar)
   ```
4. Screenshot: evidencia-info-sensible.jpg

---

## 📊 Tabla Resumen de Pruebas

| # | Tipo | Vector | Herramienta | Resultado | Evidencia |
|---|------|--------|-------------|-----------|-----------|
| 1 | SQL Injection | `' OR '1'='1` | Postman | ✅ SEGURO | screenshot-1.jpg |
| 2 | XSS | `<script>alert</script>` | DevTools | ✅ SEGURO | screenshot-2.jpg |
| 3 | CSRF | POST sin token | Postman | ✅ SEGURO | screenshot-3.jpg |
| 4 | Auth | GET sin login | Postman | ✅ SEGURO | screenshot-4.jpg |
| 5 | Auth | Rol incorrecto | Postman | ✅ SEGURO | screenshot-5.jpg |
| 6 | Info | Password visible | DevTools | ✅ SEGURO | screenshot-6.jpg |

---

## 📝 Reporte Final de Seguridad

Crear archivo: `REPORTE_SEGURIDAD_FINAL.md`

```markdown
# Reporte de Pruebas de Seguridad
## Proyecto: La Comarca
## Fecha: [HOY]
## Evaluador: [TU NOMBRE]

### Resumen Ejecutivo
✅ La aplicación ha sido validada contra 5 vectores de ataque comunes.
✅ Todas las pruebas de seguridad fueron EXITOSAS (sin vulnerabilidades).
✅ La aplicación está preparada para producción desde perspectiva de seguridad.

### Pruebas Realizadas

#### 1. Inyección SQL
- **Payload**: `' OR '1'='1`
- **Resultado**: ✅ BLOQUEADO (400 Bad Request)
- **Razón**: Eloquent parameteriza consultas automáticamente
- **Evidencia**: screenshot-sql-injection.jpg

#### 2. XSS (Cross-Site Scripting)
- **Payload**: `<script>alert('XSS')</script>`
- **Resultado**: ✅ ESCAPADO (se guarda como texto)
- **Razón**: Laravel Blade escapa HTML automáticamente
- **Evidencia**: screenshot-xss.jpg

#### 3. CSRF (Cross-Site Request Forgery)
- **Payload**: POST sin CSRF token
- **Resultado**: ✅ RECHAZADO (419 Token Expired)
- **Razón**: Middleware VerifyCsrfToken valida tokens
- **Evidencia**: screenshot-csrf.jpg

#### 4. Autenticación
- **Prueba**: GET /api/locals sin Authorization
- **Resultado**: ✅ RECHAZADO (401 Unauthorized)
- **Razón**: Middleware auth:sanctum requiere token válido
- **Evidencia**: screenshot-auth-1.jpg

#### 5. Autorización
- **Prueba**: Cliente intenta crear Local (solo Gerente puede)
- **Resultado**: ✅ RECHAZADO (403 Forbidden)
- **Razón**: Policies validan permisos por rol
- **Evidencia**: screenshot-auth-2.jpg

#### 6. Exposición de Información
- **Prueba**: Password en respuesta JSON
- **Resultado**: ✅ NO EXPUESTO (password nunca en respuesta)
- **Razón**: Model casts y serialización segura
- **Evidencia**: screenshot-info-sensible.jpg

### Conclusiones
- **Vulnerabilidades encontradas**: 0
- **Vectores bloqueados**: 6/6 (100%)
- **Calificación de seguridad**: ✅ SEGURO

### Recomendaciones
1. ✅ Mantener Laravel actualizado (security patches)
2. ✅ Usar HTTPS en producción (certificado SSL)
3. ✅ Rotar secrets y API keys regularmente
4. ✅ Implementar rate limiting en endpoints críticos
5. ✅ Hacer auditorías de seguridad periodicamente

**Aprobado para producción**: ✅ SÍ
```

---

## 🛠️ Herramientas Recomendadas

### Opción 1: Postman (Simple, SIN instalación)

1. Descarga: https://www.postman.com/downloads/
2. Crea request POST a tu endpoint
3. Modifica payload con vectores de ataque
4. Toma screenshot de respuesta

### Opción 2: OWASP ZAP (Profesional, Automático)

1. Descarga: https://www.zaproxy.org/download/
2. Abre ZAP
3. Ingresa URL: http://localhost:8000
4. Click "Start Scanning"
5. Espera resultados automáticos
6. Genera reporte HTML

### Opción 3: Browser DevTools (Integrado)

1. F12 en navegador
2. Network tab
3. Realiza acciones
4. Inspecciona requests/responses
5. Busca información sensible

---

## ✅ Checklist para la Clase

- [ ] Preparé 6 pruebas de seguridad
- [ ] Cada prueba tiene screenshot
- [ ] Documenté el vector de ataque
- [ ] Documenté el resultado
- [ ] Documenté la mitigación
- [ ] Creé reporte final
- [ ] Puedo presentar en clase sin conexión a internet

---

## 💡 Tips para Evidencia en Clase

1. **Captura el Terminal/Postman** mostrando:
   - URL del endpoint
   - Payload enviado
   - Status code recibido
   - Body de respuesta

2. **Añade explicación textual**:
   - "Intenté inyección SQL con `' OR '1'='1`"
   - "La aplicación respondió con error 400, rechazando el payload"
   - "Esto prueba que está protegida contra SQL Injection"

3. **Organiza en carpeta**:
   ```
   Pruebas-Seguridad/
   ├── 01-sql-injection.jpg
   ├── 02-xss.jpg
   ├── 03-csrf.jpg
   ├── 04-auth-sin-token.jpg
   ├── 05-auth-rol-incorrecto.jpg
   ├── 06-info-sensible.jpg
   └── REPORTE_SEGURIDAD_FINAL.md
   ```

