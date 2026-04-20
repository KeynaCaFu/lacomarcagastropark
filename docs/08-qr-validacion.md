# 08 - Gestión de QR de Validación

##  Resumen

El sistema de **QR de Validación** permite al administrador global generar un código QR único y estático para validar órdenes en los locales. Este QR contiene una clave secreta que se envía a través de la URL y puede ser escaneado desde dispositivos para verificar la autenticidad de los pedidos.

**Disponible desde:** Abril 2026  
**Versión:** 1.0  
**Acceso:** Solo Administrador Global

---

##  Funcionalidades Principales

### 1. **Generar/Actualizar QR**
- Crear un nuevo código QR con clave única
- Actualizar la clave existente (genera un QR completamente nuevo)
- Sistema de auditoría: registra quién generó, cuándo y desde dónde
- La clave anterior se guarda para historial

### 2. **Descargar QR**
- Exportar el código QR como imagen PNG (500x500px)
- Alta calidad para impresión física
- Nombre de archivo automático: `qr-validacion-YYYY-MM-DD-HHmmss.png`

### 3. **Ver Historial**
- Registro completo de todas las acciones realizadas
- Información capturada:
  - **Acción**: generate, update, download
  - **Clave antigua/nueva**: para auditoría
  - **Admin responsable**: nombre y email
  - **IP del administrador**: para seguridad
  - **User Agent**: navegador y sistema operativo usado
  - **Timestamp**: fecha y hora exacta
- Búsqueda y filtrado de acciones
- Paginación: 20 registros por página

### 4. **Ver QR Activo**
- Visualización en tiempo real del código QR
- URL completa generada
- Clave secreta visible
- Información de creación y última actualización
- Acceso rápido desde el Dashboard

---

##  Cómo Acceder

### Opción 1: Desde el Dashboard
1. Inicia sesión como **Administrador Global**
2. Ve a `/admin` (Dashboard)
3. Busca el botón rápido **"QR Validación"** (Alt+Q)
4. Haz clic para ir a la gestión

### Opción 2: Desde el Sidebar
1. Inicia sesión como **Administrador Global**
2. En el menú lateral (sidebar), busca **"QR Validación"** con el icono de QR
3. Haz clic para acceder

### Opción 3: URL Directa
```
http://tu-app/qr-validacion
```

---

##  Requisitos de Instalación

### 1. **Paquete PHP**
```bash
composer require endroid/qr-code:^5.0
```

**Nota:** Se usa `endroid/qr-code` en lugar de `simplesoftwareio/simple-qrcode` por mejor compatibilidad con Laravel 9 y PHP 8.0+

### 2. **Base de Datos**
Ejecuta el script SQL en phpMyAdmin:

```sql
-- Ubicación: /database_scripts/create_qr_settings_table.sql
```

**Tablas creadas:**
- `qr_settings`: almacena configuración del QR (clave, URL, estado)
- `qr_generation_logs`: registro de auditoría de todas las acciones

### 3. **Modelos Creados**
- `App\Models\QrSetting`
- `App\Models\QrGenerationLog`

---

##  Estructura Técnica

### Rutas

```php
// Acceso protegido solo para Administrador Global
Route::middleware(['auth', 'verified', 'admin.global'])->group(function () {
    Route::prefix('qr-validacion')->name('qr.')->group(function () {
        Route::get('/', [QrAdminController::class, 'index'])->name('index');
        Route::post('/generar', [QrAdminController::class, 'generate'])->name('generate');
        Route::get('/descargar', [QrAdminController::class, 'download'])->name('download');
        Route::get('/historial', [QrAdminController::class, 'logs'])->name('logs');
    });
});
```

### Controlador

**Ubicación:** `app/Http/Controllers/QrAdminController.php`

**Métodos Principales:**
- `index()`: Muestra el QR activo e interfaz de gestión
- `generate()`: Genera o actualiza el QR (CA1-CA2)
- `download()`: Descarga el QR como PNG (CA3)
- `logs()`: Muestra historial de auditoría (CA5)

### Modelos

#### QrSetting
```php
// Tabla: qr_settings
$table->id();                           // ID único
$table->string('qr_key')->unique();    // Clave secreta
$table->longText('qr_url');            // URL completa del QR
$table->boolean('is_active')->default(1); // Estado activo
$table->foreignId('generated_by');     // Admin que lo generó
$table->timestamps();
```

#### QrGenerationLog
```php
// Tabla: qr_generation_logs
$table->id();
$table->foreignId('qr_setting_id');    // Relación con QR
$table->string('action');              // generate, update, download
$table->string('old_key')->nullable(); // Clave anterior
$table->string('new_key')->nullable(); // Nueva clave
$table->foreignId('admin_id');         // Admin responsable
$table->string('admin_ip')->nullable(); // IP del admin
$table->longText('user_agent')->nullable(); // Browser info
$table->timestamps();
```

---

##  API Endpoint

### Validación de QR

**GET** `/api/orders/validate?key=XXXXX`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "QR code is valid",
  "qr_key": "8JFJLIUEAM11BETWOYKB",
  "is_active": true,
  "created_at": "2026-04-19T15:30:45.000000Z"
}
```

**Respuesta Error (404):**
```json
{
  "success": false,
  "message": "Invalid or inactive QR code"
}
```

**Parámetros:**
- `key` (requerido): Clave secreta del QR

**Notas:**
- No requiere autenticación
- Solo valida QRs activos
- Retorna información básica sin datos sensibles
- Adaptable para futuros sistemas de validación de órdenes

---

## 💾 Criterios de Aceptación (CA)

 **CA1:** Mostrar el QR actual en el panel administrativo  
 **CA2:** Generar y actualizar la clave del QR  
 **CA3:** Descargar el QR como imagen PNG  
 **CA4:** Interfaz responsive para desktop y móvil  
 **CA5:** Registrar todas las acciones en logs de auditoría  

---

##  Información Capturada en Auditoría

| Campo | Descripción | Uso |
|-------|-------------|-----|
| `action` | Tipo de acción realizada | Identificar qué se hizo |
| `old_key` | Clave anterior (si aplica) | Rastrear cambios |
| `new_key` | Nueva clave generada | Verificar generación |
| `admin_id` | ID del administrador | Responsabilidad |
| `admin_ip` | Dirección IP del admin | Seguridad y geolocalización |
| `user_agent` | Browser y SO del admin | Detectar uso anómalo |
| `created_at` | Timestamp de la acción | Timeline de eventos |

---

##  Seguridad

-  Solo accesible para Administrador Global
-  Middleware `admin.global` en todas las rutas
-  Auditoría completa de cambios
-  IP logging para detección de anomalías
-  User Agent capturado para análisis de dispositivos
-  Clave secreta única de 20 caracteres alfanuméricos
-  URL completamente funcional pero sin exponer datos sensibles

---

##  Adaptabilidad a Producción

El sistema está configurado para adaptarse automáticamente:

**Desarrollo:**
```
http://127.0.0.1:8000/api/orders/validate?key=8JFJLIUEAM11BETWOYKB
```

**Producción:**
```
https://tudominio.com/api/orders/validate?key=8JFJLIUEAM11BETWOYKB
```

Solo necesitas actualizar `APP_URL` en el `.env`. El código usa `url()` helper que automáticamente usa el dominio configurado.

---

##  Vistas

### Vista Principal (index)
- Tarjeta del QR con visualización en tiempo real
- Botones de acción: Generar, Descargar
- Últimos 10 logs en preview
- Enlace a historial completo

### Vista Historial (logs)
- Tabla paginada de auditoría
- Información completa de cada acción
- 20 registros por página
- Búsqueda y filtrado disponibles

---

##  Archivos Relacionados

| Archivo | Descripción |
|---------|-------------|
| `database_scripts/create_qr_settings_table.sql` | Script de creación de tablas |
| `app/Models/QrSetting.php` | Modelo de configuración QR |
| `app/Models/QrGenerationLog.php` | Modelo de auditoría |
| `app/Http/Controllers/QrAdminController.php` | Controlador principal |
| `resources/views/admin/qr/index.blade.php` | Vista principal |
| `resources/views/admin/qr/logs.blade.php` | Vista de historial |
| `routes/api.php` | Definición del endpoint API |
| `routes/web.php` | Rutas web (línea 233) |

---

##  Troubleshooting

### Error: Route not defined
**Solución:** Asegúrate de haber ejecutado el SQL en phpMyAdmin

### Error: 403 Solo gerentes pueden acceder
**Solución:** Inicia sesión como Administrador Global, no como gerente

### El QR no se muestra
**Solución:** Limpia caché del navegador (Ctrl+F5) y recarga

### No se descarga el PNG
**Solución:** Verifica que tengas el paquete `endroid/qr-code` instalado: `composer show endroid/qr-code`

---

##  Próximas Mejoras Sugeridas

1. **Validación de órdenes:** Integrar el endpoint con el sistema de órdenes
2. **Escaneo en tiempo real:** Dashboard con estadísticas de escaneos
3. **Notificaciones:** Alertas cuando el QR es validado


---

**Última actualización:** Abril 19, 2026  
**Versión:** 1.0  
**Estado:**  Funcional
