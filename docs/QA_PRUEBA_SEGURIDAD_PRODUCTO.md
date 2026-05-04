#  PRUEBA DE SEGURIDAD: Validación de Vulnerabilidades en Creación de Productos

##  Objetivo de la Prueba

Validar que el sistema de creación de productos está protegido contra ataques de validación de entradas mediante la detección de:
- Inyección SQL (SQL Injection)
- Cross-Site Scripting (XSS)
- Falsificación de Solicitud entre Sitios (CSRF)

---

##  Especificación de Seguridad

**Funcionalidad Probada:** `ProductController::store()` - Creación de producto  
**Fecha de Prueba:** 4 de Mayo de 2026  
**Herramientas:** OWASP ZAP, Postman  
**Alcance:** Validación de entrada (SQL Injection, XSS, CSRF)

---

##  Casos de Prueba de Seguridad

### 1.  INYECCIÓN SQL

#### Propósito
Verificar que los datos de entrada se sanitizan correctamente y no permiten ejecución de SQL arbitraria.

#### Test 1.1: Inyección SQL en Campo "Nombre"

**Vector de Ataque:**
```sql
nombre: '; DROP TABLE tbproduct; --
```

**Prerequisito:** Estar autenticado como admin local. Obtener `_token` del GET `http://127.0.0.1:8000/productos/create` y agregar header `Accept: application/json`.

**Pasos:**
1. Abrir Postman → New Request
2. Método: POST
3. URL: `http://127.0.0.1:8000/productos`
4. Headers: `Accept: application/json`
5. Body (form-data):
   ```
   _token = (obtenido del GET /productos/create)
   nombre = '; DROP TABLE tbproduct; --
   precio = 15.99
   estado = Disponible
   ```
6. Enviar

**Resultado Esperado:**  Payload almacenado como texto inofensivo
- Respuesta: 302 Found (redirección al listado — producto creado)
- La regla `required|string|max:255` acepta el payload porque es un string válido
- Base de datos: SIN DAÑO — Eloquent parametriza el valor, nunca lo ejecuta como SQL

**Resultado Real:**  PROTEGIDO (por ORM, no por validación de formato)
- ✓ La regla `string` acepta el payload — no bloquea caracteres SQL en el nombre
- ✓ Laravel Eloquent ORM usa prepared statements automáticamente
- ✓ El valor se pasa como parámetro `?`, nunca concatenado al query
- ✓ La tabla `tbproduct` sigue existiendo intacta

**Evidencia:**
```
POST /productos HTTP/1.1
Content-Type: multipart/form-data
Accept: application/json

nombre='; DROP TABLE tbproduct; --&precio=15.99&estado=Disponible

HTTP/1.1 302 Found
Location: http://127.0.0.1:8000/productos
(Producto creado con ese nombre — inofensivo porque Eloquent lo parametriza)
```

---

#### Test 1.2: Inyección SQL en Campo "Precio"

**Vector de Ataque:**
```
precio: 1 OR 1=1
```

**Pasos:**
1. Postman → POST `http://127.0.0.1:8000/productos`
2. Headers: `Accept: application/json` (requerido para ver 422 en vez de 302 redirect)
3. Body (form-data):
   ```
   _token = (obtenido del GET /productos/create)
   nombre = Producto Test
   precio = 1 OR 1=1
   estado = Disponible
   ```
4. Enviar

**Resultado Esperado:**  Validación rechaza entrada
- Código: 422 Unprocessable Entity (con header `Accept: application/json`)
- Error: "El precio debe ser un número"

**Nota:** Sin el header `Accept: application/json`, Laravel devuelve 302 redirect en vez de 422. El header es necesario para que la respuesta sea JSON.

**Resultado Real:**  PROTEGIDO
-  La regla `numeric` rechaza `1 OR 1=1` porque no es un número válido
-  Devuelve 422 con mensaje de error cuando se usa `Accept: application/json`

```

---

### 2.  CROSS-SITE SCRIPTING (XSS)

#### Propósito
Verificar que el contenido inyectado no se ejecuta como código JavaScript en el navegador.

#### Test 2.1: XSS Reflejado en Campo "Nombre"

**Vector de Ataque:**
```javascript
nombre: <script>alert('XSS')</script>
```

**Pasos:**
1. Postman → POST `http://127.0.0.1:8000/productos`
2. Headers: `Accept: application/json`
3. Body (form-data):
   ```
   _token = (obtenido del GET /productos/create)
   nombre = <script>alert('XSS')</script>
   precio = 15.99
   estado = Disponible
   ```
4. Enviar

**Resultado Esperado:**  Payload almacenado pero script no se ejecuta
- Respuesta: 302 Found (producto creado — la regla `string` acepta el payload)
- El script se almacena en la BD como texto plano
- Blade escapa el contenido al mostrarlo en la vista

**Resultado Real:**  PROTEGIDO (protección en la capa de presentación, no en validación)
- ✓ La regla `required|string|max:255` acepta el payload — no bloquea tags HTML
- ✓ El valor se guarda en BD como texto: `<script>alert('XSS')</script>`
- ✓ Blade usa `{{ }}` que escapa automáticamente al mostrar en la vista

```php
// En ProductController, se guarda el nombre sin modificar:
'name' => $validated['nombre'],

// Laravel Blade escapa automáticamente en vistas con {{ }}:
{{ $product->name }}  // Convierte < > a &lt; &gt;
```

**Verificación en Vista:**
1. Crear un producto con: `<script>alert('test')</script>` via Postman → 302 
2. Abrir `http://127.0.0.1:8000/productos` en el navegador
3. Inspeccionar el elemento del producto con F12
4. Verificar en el HTML fuente que el script aparece escapado
5. Resultado: `&lt;script&gt;alert(&#039;test&#039;)&lt;/script&gt;` (no se ejecuta)

---

#### Test 2.2: XSS en Campo "Descripción"

**Vector de Ataque:**
```html
descripcion: <img src=x onerror="alert('XSS')">
```

**Resultado Real:**  PROTEGIDO (protección en la capa de presentación)
- La regla `nullable|string` acepta el payload — se almacena en BD como texto plano
- Blade escapa automáticamente con `{{ }}` al mostrar en la vista
- Se renderiza como: `&lt;img src=x onerror=&quot;alert(&#039;XSS&#039;)&quot;&gt;` (no se ejecuta)

---

#### Test 2.3: XSS DOM-Based (Si aplica)

**Verificación en DevTools:**
1. Abrir Product Creation Form
2. Inspeccionar campos
3. En Console ejecutar:
   ```javascript
   // Verificar que no hay eval() o innerHTML
   document.querySelectorAll('input').forEach(input => {
     input.value = '<script>alert("test")</script>';
   });
   // Enviar formulario
   // Verificar que no se ejecuta script
   ```

---

### 3.  CROSS-SITE REQUEST FORGERY (CSRF)

#### Propósito
Verificar que la aplicación protege contra ataques CSRF validando tokens en solicitudes POST.

#### Test 3.1: Crear Producto Sin Token CSRF

**Vector de Ataque:**
```
POST /products sin _token
```

**Herramienta:** Postman

**Pasos:**
1. Abrir Postman
2. Crear nuevo request: POST
3. URL: `http://127.0.0.1:8000/productos`
4. Body (form-data):
   ```
   nombre = Producto Test
   precio = 15.99
   estado = Disponible
   ```
5. **NO incluir** el campo `_token`
6. Enviar solicitud

**Resultado Esperado:**  Solicitud rechazada
- Código de respuesta: 419 (Token Mismatch)
- Mensaje: "CSRF token mismatch"

**Resultado Real:**  PROTEGIDO
```
HTTP/1.1 419 Unknown Status
Content-Type: application/json

{
  "message": "CSRF token mismatch."
}
```

**Análisis:**
Laravel verifica automáticamente el token CSRF en todos los requests POST/PUT/DELETE mediante el middleware VerifyCsrfToken.

---

#### Test 3.2: Usar Token CSRF Expirado

**Vector de Ataque:**
```
Utilizar un token CSRF obtenido hace más de 2 horas
```

**Herramienta:** Postman

**Pasos:**
1. En GET `http://127.0.0.1:8000/productos/create`, obtener token CSRF del HTML
2. Esperar 2+ horas (o usar un token de una sesión anterior)
3. En Postman, usar ese token en POST `http://127.0.0.1:8000/productos`
4. Enviar solicitud

**Resultado Esperado:**  Solicitud rechazada
- Código: 419
- Mensaje: Token expirado

**Resultado Real:**  PROTEGIDO
```
HTTP/1.1 419 Unknown Status
{
  "message": "CSRF token mismatch."
}
```



##  Herramientas de Prueba


### 2. Postman (Pruebas Manuales)

**Descarga:** https://www.postman.com/downloads/

**Pasos de Uso:**
1. Instalar Postman
2. Crear nueva colección: "Pruebas Seguridad Productos"
3. Para cada test case:
   - Crear nuevo request POST a `http://127.0.0.1:8000/productos`
   - Agregar header `Accept: application/json` para recibir errores 422 en JSON
   - Inyectar payload malicioso en campos
   - Enviar y documentar respuesta
4. Validar códigos de respuesta esperados:
   - `302 Found` → validación pasó, producto creado (SQL Injection en nombre, XSS)
   - `422 Unprocessable Entity` → validación rechazó el input (SQL Injection en precio)
   - `419 Unknown Status` → CSRF token inválido o ausente
5. Verificar mensajes de error
6. Documentar evidencia

**Ventajas:**
- Control manual de payloads
- Fácil documentación de respuestas
- Reutilizable para pruebas repetidas
- Ideal para validación de CSRF tokens


##  Conclusiones de Validación de Entradas

**ESTADO GENERAL:  PROTEGIDO**

### Protecciones Validadas

 **SQL Injection:**
- **Nombre:** Acepta el payload (regla `string`) pero el ORM lo parametriza — respuesta 302, BD intacta
- **Precio:** La regla `numeric` rechaza operadores SQL — respuesta 422 (con `Accept: application/json`)
- Datos se parametrizan con prepared statements, nunca concatenados al query

 **XSS (Cross-Site Scripting):**
- La regla `string` acepta tags HTML — no hay sanitización en la capa de entrada
- Protección real en la capa de presentación: Blade escapa con `{{ }}`
- Scripts inyectados se almacenan como texto y se renderizan como entidades HTML (no se ejecutan)
- Respuesta al crear: 302 (producto creado, script inofensivo al mostrar)

 **CSRF (Cross-Site Request Forgery):**
- Middleware VerifyCsrfToken valida tokens en todos los requests POST/PUT/DELETE
- Sin token o con token de sesión distinta: 419 Token Mismatch
- Token de sesión expirada: 419 Token Mismatch

### Recomendaciones
1. Ejecutar escaneo con OWASP ZAP antes de cada release
2. Mantener Laravel actualizado con patches de seguridad
3. Revisar logs de seguridad regularmente
4. Mantener validaciones actualizadas según OWASP Top 10

---

##  Referencias

- [OWASP Top 10 2021](https://owasp.org/Top10/)
- [Laravel Security](https://laravel.com/docs/security)
- [OWASP ZAP User Guide](https://www.zaproxy.org/docs/desktop/)
- [Postman Documentation](https://learning.postman.com/)
- [MDN Web Security - Input Validation](https://developer.mozilla.org/en-US/docs/Web/Security)

---

##  Evidencia de Pruebas

**Evidencia:**


