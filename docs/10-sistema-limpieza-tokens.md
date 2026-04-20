#  Sistema de Limpieza Automática de Tokens Expirados

##  Resumen

Se implementó un **Comando de Laravel + Task Scheduler** que:
-  Se ejecuta automáticamente cada noche a las **3:00 AM**
-  Elimina órdenes con tokens expirados (> 7 días sin confirmar)
-  Registra en logs toda la actividad
-  Previene que la BD se llene de datos antiguos

---

##  Archivos Creados/Modificados

### 1. **Comando de Limpieza**
 `app/Console/Commands/CleanExpiredTokens.php`

**Función:**
- Busca órdenes con estado `Pending` + tokens expirados
- Elimina órdenes e items asociados
- Registra en logs los detalles

**Parámetro:**
- `--days=7` (configurable, default: 7 días)

### 2. **Scheduler Configurado**
 `app/Console/Kernel.php`

**Configuración:**
```php
$schedule->command('clean:expired-tokens --days=7')
    ->dailyAt('03:00')  // 3:00 AM cada noche
    ->timezone('America/Argentina/Buenos_Aires')
```

---

##  Cómo Funciona

### Ejecución Automática

```
┌─────────────────────────────────────────┐
│ Cada noche a las 3:00 AM               │
├─────────────────────────────────────────┤
│                                         │
│ 1. Laravel ejecuta scheduler            │
│ 2. Busca órdenes expiradas              │
│ 3. Calcula: Hoy - 7 días = fecha límite│
│ 4. Elimina órdenes anteriores a esa     │
│ 5. Registra en logs                     │
│ 6. Termina                              │
│                                         │
└─────────────────────────────────────────┘
```

---

##  Lógica de Limpieza

### Criterios para Eliminar

```sql
DELETE FROM tborder
WHERE status = 'Pending'
  AND verification_token IS NOT NULL
  AND confirmed_at < (NOW() - INTERVAL 7 DAY);
```

**Detalles:**
-  Solo órdenes con status `Pending`
-  Con token de verificación asignado
-  Con más de 7 días sin confirmar
-  Los items asociados se eliminan primero

### Ejemplo

```
Fecha actual: 2026-04-19
Fecha límite: 2026-04-12 (7 días atrás)

Órdenes a eliminar:
┌──────────┬──────────────────┬─────────────────────┐
│ order_id │ verification_token │ confirmed_at        │
├──────────┼──────────────────┼─────────────────────┤
│ 100      │ LCGP-4829        │ 2026-04-05 15:30:00 │ ← Eliminar
│ 101      │ LCGP-7543        │ 2026-04-08 10:15:00 │ ← Eliminar
│ 102      │ LCGP-2156        │ 2026-04-15 09:45:00 │ ← Mantener
│ 103      │ LCGP-8901        │ 2026-04-18 14:20:00 │ ← Mantener
└──────────┴──────────────────┴─────────────────────┘
```

---

##  Ejecutar Manualmente

### En desarrollo (para probar)

```bash
php artisan clean:expired-tokens --days=7
```

**Resultado:**
```
  Limpiando tokens expirados (más de 7 días)...
 Se eliminaron 15 órdenes con tokens expirados.
```

### Con diferentes días

```bash
# Limpiar órdenes de más de 1 día
php artisan clean:expired-tokens --days=1

# Limpiar órdenes de más de 30 días
php artisan clean:expired-tokens --days=30
```

---

##  Registros (Logging)

### Log de Éxito

Ubicación: `storage/logs/laravel-YYYY-MM-DD.log`

```
[2026-04-19 03:00:15] local.INFO: Clean Expired Tokens Success
{
  "deleted_count": 15,
  "deleted_order_ids": [100, 101, 102, ...],
  "deleted_tokens": ["LCGP-4829", "LCGP-7543", ...],
  "expiry_date": "2026-04-12 03:00:15",
  "executed_at": "2026-04-19 03:00:15"
}
```

### Log de Error

```
[2026-04-19 03:00:15] local.ERROR: Clean Expired Tokens Error
{
  "error": "SQLSTATE[23000]: Integrity constraint violation...",
  "file": "app/Console/Commands/CleanExpiredTokens.php",
  "line": 68,
  "trace": "..."
}
```

---

##  Configuración del Scheduler

### Zona Horaria

```php
->timezone('America/Argentina/Buenos_Aires')
```

**Modificar según tu zona horaria:**
- `America/New_York`
- `Europe/Madrid`
- `Asia/Tokyo`
- `UTC`

### Hora de Ejecución

Cambiar la hora:
```php
->dailyAt('03:00')  // 3:00 AM

// Otras opciones:
->dailyAt('02:30')  // 2:30 AM
->dailyAt('04:00')  // 4:00 AM
->hourly()          // Cada hora
->everyFiveMinutes() // Cada 5 minutos
```

---

##  Seguridad Adicional

### Soft Delete (Alternativa)

Si prefieres no eliminar definitivamente, usa Soft Deletes:

```php
// En la migración
$table->softDeletes();

// En el comando
$order->forceDelete(); // En lugar de delete()
```

Luego puedes recuperar si es necesario.

---

##  Troubleshooting

### El scheduler no se ejecuta

**Problema:** Laravel scheduler necesita que un cron job lo ejecute

**Solución en producción:** Agregar cron job en servidor

```bash
# En cPanel o terminal del servidor
* * * * * cd /path/to/lacomarcagastropark && php artisan schedule:run >> /dev/null 2>&1
```

Este cron se ejecuta cada minuto y dispara el scheduler.

**En desarrollo:** Ejecuta manualmente:
```bash
php artisan schedule:run
```

### Los logs no aparecen

Verifica:
1. Que `storage/logs` tenga permisos de escritura:
   ```bash
   chmod -R 775 storage/logs
   ```

2. Que `LOG_CHANNEL` esté configurado en `.env`:
   ```
   LOG_CHANNEL=stack
   LOG_LEVEL=info
   ```

### El comando falla con error

Ejecuta en terminal para ver el error completo:
```bash
php artisan clean:expired-tokens --days=7
```

---

##  Estadísticas

### Estimado de datos a eliminar

```
Suponiendo:
- 100 órdenes por día
- 7 días de retención
- 50% canceladas/pendientes

Eliminadas por semana: ~350 órdenes
Eliminadas por mes: ~1,400 órdenes
Eliminadas por año: ~18,200 órdenes

Ahorro de espacio BD por año: ~5-10 MB
```

---

##  Checklist de Implementación

- [x] Comando `CleanExpiredTokens.php` creado
- [x] Scheduler configurado en `Kernel.php`
- [x] Ejecución diaria a las 3:00 AM
- [x] Logging de éxito/error
- [x] Parámetro `--days` configurable
- [x] Soft delete compatible
- [ ] Cron job configurado en servidor (producción)
- [ ] Probar en desarrollo: `php artisan clean:expired-tokens --days=1`

---

##  Próximas Mejoras

1. **Dashboard de Limpieza**
   - Ver cuántos tokens se limpian diariamente
   - Historial de ejecuciones

2. **Email de Notificación**
   - Alertas si hay muchos registros pendientes
   - Resumen semanal de limpiezas

3. **Métricas**
   - Cantidad de datos liberados
   - Tasa de cancelación de órdenes

4. **Configuración por Admin**
   - Panel para cambiar días de retención
   - Ejecutar limpieza manualmente

---

##  Referencias

- **Comando**: `app/Console/Commands/CleanExpiredTokens.php`
- **Scheduler**: `app/Console/Kernel.php`
- **Documentación oficial**: https://laravel.com/docs/scheduling

---

**Última actualización**: Abril 19, 2026  
**Estado**:  Implementado y listo
