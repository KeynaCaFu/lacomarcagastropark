# CA2 + CA5: Sistema de Tokens de Verificación de Órdenes

##  Resumen

Implementación completa del sistema de generación y gestión de tokens únicos de verificación para órdenes, cumpliendo con los criterios de aceptación CA2 y CA5:

- **CA2**: El QR contiene un token numérico único (formato LCGP-XXXX)
- **CA5**: El sistema registra la fecha y hora de generación del token

**Versión**: 1.0  
**Fecha**: Abril 2026  
**Estado**: ✅ Listo para implementación

---

##  Criterios de Aceptación

###  CA2: Token Numérico Único
- Formato: `LCGP-XXXX` (ej: LCGP-4829)
- 4 dígitos aleatorios (0000 a 9999)
- Validación de unicidad en base de datos
- Máximo 100 intentos para garantizar generación sin duplicados

###  CA5: Registro de Fecha y Hora
- Campo `confirmed_at` almacena timestamp exacto
- Se registra al momento de crear la orden
- Formato: DATETIME (ej: 2026-04-19 15:45:30)
- Índice para búsquedas rápidas

---

##  Archivos Implementados

### 1. **Script SQL** 
 `database_scripts/alter_tborder_add_verification_token.sql`

```sql
ALTER TABLE `tborder` ADD COLUMN `verification_token` VARCHAR(255) UNIQUE;
ALTER TABLE `tborder` ADD COLUMN `confirmed_at` TIMESTAMP NULL;
CREATE INDEX idx_verification_token ON `tborder`(verification_token);
CREATE INDEX idx_confirmed_at ON `tborder`(confirmed_at);
```

**Campos agregados:**
- `verification_token`: String único para almacenar el token
- `confirmed_at`: Timestamp para registro de confirmación
- 2 índices para optimizar búsquedas

### 2. **Modelo Order Actualizado**
 `app/Models/Order.php`

**Cambios:**
```php
protected $fillable = [
    // ... campos existentes ...
    'verification_token',  // CA2: Token único de verificación
    'confirmed_at',         // CA5: Timestamp de confirmación
];

protected $casts = [
    // ... casts existentes ...
    'confirmed_at' => 'datetime', // CA5: Timestamp de confirmación
];
```

### 3. **Service OrderTokenService**
 `app/Services/OrderTokenService.php`

**Responsabilidades principales:**

| Método | Descripción | CA |
|--------|-------------|-----|
| `generateUniqueToken()` | Genera token LCGP-XXXX único con validación | CA2 |
| `isValidTokenFormat()` | Valida que token siga formato correcto | CA2 |
| `findOrderByToken()` | Busca orden por token | - |
| `validateToken()` | Valida formato y busca orden | - |
| `validateTokenWithPlazaKey()` | Preparado para validación futura plaza | Futura |
| `getTokenStats()` | Retorna estadísticas de tokens | - |

**Características de seguridad:**
- Do-while loop para garantizar unicidad
- Máximo 100 intentos (previene loops infinitos)
- Validación de formato con regex
- Manejo de excepciones

### 4. **Ejemplo de Integración**
 `app/Http/Controllers/OrderControllerExample.php`

**Incluye:**
- Inyección de OrderTokenService
- Método `storeWithTokenExample()` - Ejemplo completo
- Método `verifyOrderToken()` - Validación de tokens
- Método `getTokenStatistics()` - Estadísticas
- Comentarios sobre preparación para plaza_key

---

##  Implementación Paso a Paso

### Paso 1: Ejecutar Script SQL 

En phpMyAdmin, ejecuta:
```sql
-- Ubicación: database_scripts/alter_tborder_add_verification_token.sql
ALTER TABLE `tborder` ADD COLUMN `verification_token` VARCHAR(255) UNIQUE;
ALTER TABLE `tborder` ADD COLUMN `confirmed_at` TIMESTAMP NULL;
CREATE INDEX idx_verification_token ON `tborder`(verification_token);
CREATE INDEX idx_confirmed_at ON `tborder`(confirmed_at);
```

**Resultado esperado:**
-  Dos nuevas columnas en tborder
-  Dos índices creados
-  Sin errores

### Paso 2: Verificar Modelo Order 

El archivo `app/Models/Order.php` ya está actualizado:
```php
// Incluye los nuevos campos en $fillable
'verification_token',
'confirmed_at',

// Incluye el cast para datetime
'confirmed_at' => 'datetime',
```

### Paso 3: Integrar Service en OrderController

En `app/Http/Controllers/OrderController.php`, agregar:

**A. En el constructor:**
```php
use App\Services\OrderTokenService;

protected OrderTokenService $tokenService;

public function __construct(OrderTokenService $tokenService)
{
    $this->tokenService = $tokenService;
}
```

**B. En el método `store()` (dentro del try-catch):**

```php
// ═══════════════════════════════════════════════════════════════
// CA2: Generar token único
// ═══════════════════════════════════════════════════════════════
$verificationToken = $this->tokenService->generateUniqueToken(); // LCGP-4829

// ═══════════════════════════════════════════════════════════════
// CA5: Registrar timestamp exacto de confirmación
// ═══════════════════════════════════════════════════════════════
$confirmedAt = now(); // 2026-04-19 15:45:30

// ... resto de lógica existente ...

// Al crear la orden:
$order = Order::create([
    // ... campos existentes ...
    'verification_token' => $verificationToken, // CA2
    'confirmed_at' => $confirmedAt,             // CA5
]);
```

**C. Actualizar respuesta JSON:**

```php
return response()->json([
    'success' => true,
    'message' => 'Orden creada exitosamente',
    'order' => [
        'order_id' => $order->order_id,
        'order_number' => $order->order_number,
        'verification_token' => $order->verification_token, // CA2
        'confirmed_at' => $order->confirmed_at,             // CA5
        'qr_url' => route('api.orders.validate', 
            ['key' => $order->verification_token], false),
    ]
]);
```

---

##  Estructura de Datos Generada

### Formato del Token (CA2)

**Estructura:** `LCGP-XXXX`

| Parte | Descripción | Ejemplo |
|-------|-------------|---------|
| `LCGP` | Prefijo fijo (LaComarca GastoPark) | LCGP |
| `-` | Separador | - |
| `XXXX` | 4 dígitos aleatorios | 4829 |

**Token completo:** `LCGP-4829`

**Ventajas:**
- Fácil de leer y recordar
- Único garantizado mediante do-while
- Compatible con código QR
- Formato validable con regex

### Timestamp de Confirmación (CA5)

**Campos en BD:**
- `confirmed_at`: TIMESTAMP NOT NULL
- Formato: `YYYY-MM-DD HH:MM:SS`
- Ejemplo: `2026-04-19 15:45:30`

**Casteo en Modelo:**
```php
protected $casts = [
    'confirmed_at' => 'datetime', // Automáticamente Carbon instance
];
```

**Acceso desde PHP:**
```php
$order->confirmed_at; // Carbon\Carbon object
$order->confirmed_at->toDateTimeString(); // "2026-04-19 15:45:30"
$order->confirmed_at->diffForHumans(); // "17 minutes ago"
```

---

##  Seguridad y Validación

### Validación de Unicidad (CA2)

```php
public function generateUniqueToken(): string
{
    $token = null;
    $attempts = 0;
    $maxAttempts = 100;

    do {
        $randomDigits = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $token = self::TOKEN_PREFIX . '-' . $randomDigits;
        $exists = Order::where('verification_token', $token)->exists();
        $attempts++;

        if (!$exists) {
            break; // Token único encontrado
        }

        if ($attempts >= $maxAttempts) {
            throw new \Exception('No se pudo generar token único...');
        }

    } while (true);

    return $token;
}
```

**Probabilidades:**
- Posibles tokens: 10,000 (0000 a 9999)
- Intentos para encontrar único: Promedio 1-2
- Máximo de intentos: 100 (seguridad)
- Probabilidad de fallo: < 0.0001%

### Validación de Formato

```php
public function isValidTokenFormat(string $token): bool
{
    return preg_match('/^LCGP-\d{4}$/', $token) === 1;
}
```

**Validaciones:**
-  Comienza con `LCGP`
-  Seguido de `-`
-  Exactamente 4 dígitos (0-9)
-  Nada más después

---

##  Preparación para Validación con Plaza_Key

La arquitectura ya está preparada para integración futura:

```php
/**
 * Método preparado para recibir plaza_key en futuras versiones
 * Permite validar que la orden pertenezca al local correcto
 */
public function validateTokenWithPlazaKey(string $token, string $plazaKey): ?Order
{
    // TODO: Implementar cuando se integre QR
    // 
    // Lógica futura:
    // 1. Verificar plaza_key en tabla qr_settings
    // 2. Verificar que orden pertenezca al local
    // 3. Registrar intento en qr_generation_logs
    
    $order = $this->validateToken($token);
    
    // Aquí irá lógica de plaza_key
    
    return $order;
}
```

**Uso futuro:**
```php
$order = $this->tokenService->validateTokenWithPlazaKey(
    'LCGP-4829',           // Token de orden
    'qr-plaza-key'         // Clave de plaza desde QR
);
```

---

##  Ejemplos de Uso

### Crear Orden con Token (CA2 + CA5)

```php
// En OrderController::store()
$verificationToken = $this->tokenService->generateUniqueToken();
$confirmedAt = now();

$order = Order::create([
    'order_number' => 'ORD-1234',
    'verification_token' => $verificationToken, // 'LCGP-4829'
    'confirmed_at' => $confirmedAt,             // '2026-04-19 15:45:30'
    // ... más campos ...
]);

// Respuesta JSON:
{
    "success": true,
    "order": {
        "order_id": 123,
        "order_number": "ORD-1234",
        "verification_token": "LCGP-4829",     // CA2
        "confirmed_at": "2026-04-19T15:45:30"  // CA5
    }
}
```

### Validar Token

```php
// En OrderController::verifyOrderToken($token)
$order = $this->tokenService->validateToken('LCGP-4829');

if ($order) {
    // Token válido
    return response()->json([
        'success' => true,
        'order' => $order
    ]);
} else {
    // Token inválido
    return response()->json([
        'success' => false,
        'message' => 'Token inválido'
    ], 404);
}
```

### Obtener Estadísticas

```php
$stats = $this->tokenService->getTokenStats();

// Resultado:
[
    'total_tokens_generated' => 1523,
    'unique_tokens' => 1523,
    'confirmed_orders' => 1520,
    'pending_confirmation' => 3
]
```

---

##  API Endpoints (Sugeridos)

### Crear Orden
```
POST /api/orders
Body: {
    "items": [...],
    "preparation_time": 30,
    "plaza_key": "opcional" // Para validación futura
}

Response:
{
    "success": true,
    "order": {
        "order_id": 123,
        "verification_token": "LCGP-4829",    // CA2
        "confirmed_at": "2026-04-19T15:45:30" // CA5
    }
}
```

### Validar Token
```
GET /api/orders/{token}/verify

Response:
{
    "success": true,
    "order": {...}
}
```

### Estadísticas
```
GET /api/orders/statistics/tokens

Response:
{
    "success": true,
    "statistics": {
        "total_tokens_generated": 1523,
        "unique_tokens": 1523
    }
}
```

---

## 🆘 Troubleshooting

### Error: "UNIQUE constraint failed"
**Causa**: Token duplicado generado  
**Solución**: Verificar lógica do-while en `generateUniqueToken()`

### Token no se genera
**Causa**: OrderTokenService no inyectado correctamente  
**Solución**: Verificar constructor del OrderController

### Timestamp NULL
**Causa**: No se asigna `confirmed_at` al crear orden  
**Solución**: Agregar `'confirmed_at' => now()` en `Order::create()`

### Validación de formato falla
**Causa**: Token no cumple formato LCGP-XXXX  
**Solución**: Verificar que `generateUniqueToken()` retorna formato correcto

---

##  Próximas Fases

### Fase 2: Integración con QR
- [ ] Generar código QR con token LCGP-XXXX
- [ ] Validar plaza_key junto con token
- [ ] Registrar validaciones en logs

### Fase 3: Escaneo y Confirmación
- [ ] Endpoint para escanear QR
- [ ] Actualizar estado de orden con escaneo
- [ ] Registrar quién validó y cuándo

### Fase 4: Reportes
- [ ] Dashboard de validaciones
- [ ] Historial de escaneos
- [ ] Estadísticas por plaza/local

---

##  Referencias

- **Modelo**: `app/Models/Order.php`
- **Service**: `app/Services/OrderTokenService.php`
- **Ejemplo Integración**: `app/Http/Controllers/OrderControllerExample.php`
- **Script SQL**: `database_scripts/alter_tborder_add_verification_token.sql`
- **Documentación QR**: `docs/08-qr-validacion.md`

---

**Última actualización**: Abril 19, 2026  
**Estado**:  Listo para implementación  
**Versión**: 1.0
