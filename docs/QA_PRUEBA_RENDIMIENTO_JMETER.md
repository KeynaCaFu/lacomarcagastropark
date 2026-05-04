#  PRUEBA DE RENDIMIENTO: Creación de Productos con JMeter

##  Objetivo de la Prueba

Validar el rendimiento y escalabilidad del endpoint de creación de productos bajo carga simulada de **150 usuarios concurrentes** durante 2 minutos.

---

##  Especificación de la Prueba

| Parámetro | Valor | Descripción |
|-----------|-------|-------------|
| **Usuarios Concurrentes** | 150 | Número máximo de usuarios simultáneos |
| **Ramp-up Time** | 30 segundos | Tiempo para alcanzar 150 usuarios |
| **Duración del Test** | 120 segundos | 2 minutos de prueba |
| **URL Base** | `http://localhost:8000` | Servidor bajo prueba |
| **Endpoint Probado** | `POST /products` | Creación de producto |
| **Método HTTP** | POST | Formulario de datos |
| **Tipo de Dato** | form-data | Envío de formulario |

---

##  Ubicación del Script

```
tests/performance/jmeter_crear_producto_150_usuarios.jmx
```

---

##  Cómo Ejecutar la Prueba

### Opción 1: Desde GUI de JMeter

```bash
# Abrir JMeter con el script
jmeter -t tests/performance/jmeter_crear_producto_150_usuarios.jmx
```

1. Click en "Run" (botón verde de inicio)
2. Esperar a que se complete (aproximadamente 2-3 minutos)
3. Ver resultados en tiempo real

### Opción 2: Modo Línea de Comandos (No-GUI)

```bash
# Ejecutar en modo headless (más rápido)
jmeter -n -t tests/performance/jmeter_crear_producto_150_usuarios.jmx \
  -l results/jmeter_results.jtl \
  -j results/jmeter.log
```

### Opción 3: Con HTML Report (Reporte Visual)

```bash
# Ejecutar y generar reporte HTML
jmeter -n -t tests/performance/jmeter_crear_producto_150_usuarios.jmx \
  -l results/jmeter_results.jtl \
  -e -o results/html_report
```

---

##  Métricas Clave a Monitorear

### 1. **Tiempo de Respuesta**

```
Métrica                    | Criterio         | Esperado
---------------------------|-----------------|----------
Tiempo Promedio            | < 1000ms         | ≈ 500-800ms
Tiempo Mínimo              | Baseline         | ≈ 100-200ms
Tiempo Máximo              | < 5000ms         | ≈ 2000-3000ms
Percentil 50 (Mediana)     | < 1000ms         | ≈ 500-700ms
Percentil 95               | < 2000ms         | ✓ CRÍTICO
Percentil 99               | < 3000ms         | ≈ 2000-2500ms
```

### 2. **Tasa de Error**

```
Métrica                    | Criterio Máximo  | Esperado
---------------------------|-----------------|----------
Error Rate Total           | < 1%             | ≈ 0%
Errores HTTP 5xx           | 0                | 0
Errores HTTP 4xx           | Aceptable        | Algunos de validación
Errores de Conexión        | 0                | 0
Timeouts                   | 0                | 0
```

### 3. **Throughput (Rendimiento)**

```
Métrica                    | Objetivo         | Esperado
---------------------------|-----------------|----------
Transacciones/segundo      | > 1.0 TPS        | ≈ 1.5-2.5 TPS
Solicitudes/segundo        | > 2.0 RPS        | ≈ 3-5 RPS
Bytes/segundo              | Informativo      | ≈ 10-50 KB/s
```

---

##  Parámetros del Script JMeter

### Variables Configurables

Puedes modificar estos parámetros directamente en JMeter:

```
BASE_URL        = http://localhost:8000
NUM_USUARIOS    = 150
RAMP_UP_TIME    = 30 (segundos)
TEST_DURATION   = 120 (segundos)
```

### Flujo de Ejecución

1. **GET /products/create** 
   - Obtiene el formulario HTML
   - Extrae el token CSRF
   - Tiempo esperado: 50-100ms

2. **POST /products**
   - Envía datos del producto
   - Valida los datos
   - Guarda en BD
   - Redirige a lista
   - Tiempo esperado: 200-500ms

---

##  Datos de Prueba

### Campos Enviados

```javascript
{
  "_token": "${csrf_token}",                    // Token CSRF extraído
  "nombre": "Producto Test {timestamp} {threadNum}",  // Nombre único
  "descripcion": "Descripción del producto de prueba de carga",
  "categoria": "Categoría Test",
  "etiqueta": "Test",
  "tipo_producto": "Plato Principal",
  "precio": "${__Random(5,100)}.${__Random(0,99)}",  // Precio aleatorio
  "estado": "Disponible"
}
```

### Validaciones en la Prueba

```
✓ Código de respuesta: 200, 302, o 301
✓ Timeout máximo: 5000ms
✓ No errores de conexión
```

---

##  Interpretación de Resultados

### Escenario 1:  ÓPTIMO

```
Usuarios activos: 150
Tiempo promedio: 450ms
Percentil 95: 1200ms
Error rate: 0%
Throughput: 2.2 TPS

Conclusión: El sistema escala bien bajo carga normal
```

### Escenario 2:  ACEPTABLE

```
Usuarios activos: 150
Tiempo promedio: 800ms
Percentil 95: 1800ms
Error rate: 0.5%
Throughput: 1.5 TPS

Conclusión: El sistema funciona pero hay margen de mejora
Recomendación: Optimizar queries, aumentar pool de conexiones
```

### Escenario 3:  CRÍTICO

```
Usuarios activos: 150
Tiempo promedio: 2500ms
Percentil 95: 5000ms+
Error rate: > 2%
Timeouts: > 10

Conclusión: El sistema NO escala adecuadamente
Recomendación: Investigar cuellos de botella, aumentar recursos
```

---

##  Análisis de Resultados

### Dónde Buscar los Resultados

**En GUI:**
- Vista de Árbol de Resultados (Result Tree)
- Tabla Agregada (Summary Table)
- Gráficos

**En Archivos:**

```
results/jmeter_results.jtl        # Datos crudos (CSV)
results/jmeter.log                # Log de ejecución
results/html_report/index.html    # Reporte visual
```

### Análisis del Archivo JTL

```bash
# Ver resumen rápido
grep -E "success|elapsed" results/jmeter_results.jtl | head -20

# Calcular percentil 95 (bash)
awk -F',' '{print $8}' results/jmeter_results.jtl | sort -n | \
  awk '{a[NR]=$1} END {print a[int(NR*0.95)]}'
```

---

##  Solución de Problemas

### Error: "No se puede conectar a localhost:8000"

```
Verificar:
1. ¿El servidor Laravel está corriendo?
2. ¿El puerto 8000 es correcto?
3. ¿Se puede acceder con curl?

Solución:
php artisan serve  # Iniciar servidor local
```

### Error: "CSRF token no se extrae"

```
Verificar:
1. La regex en el extractor es correcta
2. El formulario contiene el token CSRF
3. Las cookies se están enviando

Debug:
- Activar "View Results Tree" en JMeter
- Buscar el token en respuesta GET
```

### Error: "Error rate > 1%"

```
Investigar:
1. Errores de validación (400)
2. Errores de servidor (500)
3. Timeouts o desconexiones

Solución:
- Revisar logs del servidor: storage/logs/laravel.log
- Aumentar memory_limit de PHP
- Optimizar queries con índices
```

### Rendimiento Lento

```
Causas comunes:
1. Base de datos lenta
2. Falta de índices
3. Pool de conexiones insuficiente
4. Servidor sobrecargado

Solución:
- Usar índices en campos filtrados
- Aumentar max_connections en MySQL
- Implementar caché (Redis/Memcached)
- Escalabilidad horizontal
```

## Formato de Reporte Esperado

```
=== RESULTADOS FINAL PRUEBA DE RENDIMIENTO ===

Duración Total:         120 segundos
Usuarios Pico:          150 usuarios
Solicitudes Totales:    2,847
Solicitudes Exitosas:   2,835
Error Rate:             0.42%

Tiempos de Respuesta:
├─ Promedio:            523 ms
├─ Mínimo:              87 ms
├─ Máximo:              3,421 ms
├─ Percentil 50:        445 ms
├─ Percentil 95:        1,234 ms
└─ Percentil 99:        2,145 ms

Throughput:             23.7 requests/sec (23.7 RPS)
                       2.37 transactions/sec (2.37 TPS)

Códigos de Respuesta:
├─ 200 OK:              1,450 (51%)
├─ 302 REDIRECT:        1,385 (49%)
└─ 5xx ERROR:           12 (0.42%)

CONCLUSIÓN:  SISTEMA APROBADO PARA CARGA DE 150 USUARIOS
```

##  Referencias

- [JMeter Documentación Oficial](https://jmeter.apache.org/usermanual/)
- [Performance Testing Best Practices](https://jmeter.apache.org/usermanual/properties-reference.html)
- [Percentil 95: Qué es y por qué importa](https://en.wikipedia.org/wiki/Percentile)

---

## 📝 Notas

- Los tiempos pueden variar según hardware disponible
- Ejecutar el test en un servidor dedicado para resultados precisos
- Tomar múltiples ejecuciones y promediar para fiabilidad
- Considerar ejecutar de noche para evitar interferencias
