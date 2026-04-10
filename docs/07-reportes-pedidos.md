# Guía de Uso - Módulo de Reportes de Pedidos

## Overview (Resumen)

El módulo de **Reportes de Pedidos** permite a los gerentes de locales visualizar y analizar el volumen de pedidos en línea vs. presenciales en diferentes períodos de tiempo.

## Criterios de Aceptación Implementados

### CA1: Cantidad y Porcentaje
✅ El reporte muestra automáticamente:
- Cantidad de pedidos en línea
- Cantidad de pedidos presenciales  
- Porcentaje de cada tipo respecto al total

### CA2: Gráficos Comparativos
✅ Se presentan dos gráficos visuales:
- **Gráfico Pastel (Doughnut)**: Muestra la distribución porcentual
- **Gráfico de Barras**: Compara ingresos por tipo de pedido
- **Gráfico de Línea**: Tendencia diaria de pedidos

### CA3: Suma de Porcentajes = 100%
✅ Validación automática:
- El sistema calcula los porcentajes de forma que siempre sumen 100%
- Se ajustan decimales si es necesario para evitar errores de redondeo

### CA4: Filtros Personalizados
✅ Filtros disponibles:
- **Hoy**: Período del día actual
- **Esta Semana**: Lunes a domingo de la semana actual
- **Este Mes**: Primer al último día del mes actual
- **Este Año**: Enero a diciembre del año actual
- **Personalizado**: Rango de fechas personalizado por el usuario

### CA5: Exportación
✅ El reporte es exportable en múltiples formatos:
- **HTML Imprimible**: Se puede guardar como PDF desde el navegador (Ctrl+S)
- **Impresión Directa**: Botón "Imprimir" para imprimir a PDF

### CA6: Períodos con Un Solo Tipo
✅ Manejo correcto:
- Si solo hay pedidos en línea: muestra 100% en línea, 0% presencial
- Si solo hay pedidos presenciales: muestra 0% en línea, 100% presencial
- Los gráficos se adaptan correctamente sin errores

---

## Acceso al Módulo

1. **Autenticarse como Gerente** (admin local)
2. **Navegar a**: Menú Principal → **Reportes**
3. **Ruta directa**: `/reportes/pedidos`

---

## Flujo de Uso

### Paso 1: Seleccionar Período
```
┌─────────────────────────────────┐
│ Selector de Período             │
│ ┌─────────────────────────────┐ │
│ │ Período:                    │ │
│ │ □ Hoy                       │ │
│ │ □ Esta Semana               │ │
│ │ □ Este Mes (Seleccionado)   │ │
│ │ □ Este Año                  │ │
│ │ □ Personalizado             │ │
│ └─────────────────────────────┘ │
└─────────────────────────────────┘
```

### Paso 2: (Opcional) Especificar Fechas
Si selecciona "Personalizado", se habilitarán campos:
- **Desde**: YYYY-MM-DD
- **Hasta**: YYYY-MM-DD

### Paso 3: Hacer Click en "Filtrar"
El sistema recargará los datos del reporte

### Paso 4: Revisar Resultados
- Tarjetas resumen en la parte superior
- Gráficos visuales
- Tabla detallada
- Tendencia diaria

### Paso 5: (Opcional) Exportar
- **Botón "Exportar"**: Abre vista HTML imprimible
- **Botón "Imprimir"**: Abre cuadro de diálogo de impresión

---

## Estructura de Datos del Reporte

```json
{
  "orderStats": {
    "web": {
      "count": 45,
      "percentage": 65.22,
      "label": "En Línea"
    },
    "presential": {
      "count": 24,
      "percentage": 34.78,
      "label": "Presencial"
    },
    "total": 69,
    "period": {
      "start": "2024-04-01",
      "end": "2024-04-30",
      "startFormatted": "01/04/2024",
      "endFormatted": "30/04/2024"
    }
  },
  "revenueStats": {
    "web": {
      "revenue": 2450.50,
      "percentage": 58.90
    },
    "presential": {
      "revenue": 1710.30,
      "percentage": 41.10
    },
    "total": 4160.80
  }
}
```

---

## Casos de Prueba Implementados

### CP-204-01: Generar y Exportar Reporte Mensual [✅ POSITIVO]

**Precondiciones:**
- Gerente autenticado
- Semana actual con pedidos online Y presenciales

**Pasos:**
1. Ingresar al reporte 'Pedidos'
2. Seleccionar período "Este Mes"
3. Click "Filtrar"
4. Verificar que ambos tipos de pedidos aparecen
5. Vérificas que la suma de porcentajes = 100%
6. Click "Exportar"
7. Verificar descarga/impresión del reporte

**Resultado Esperado:**
- Gráfico visible con ambos tipos ✓
- Porcentajes válidos ✓
- Archivo descargado/imprimido ✓

---

### CP-205-02: Reporte con Solo Un Tipo de Pedido [✅ BORDE]

**Precondiciones:**
- Gerente autenticado
- Período con ÚNICAMENTE pedidos presenciales

**Pasos:**
1. Seleccionar período con solo presenciales
2. Click "Filtrar"
3. Verificar que muestra 100% presencial, 0% en línea
4. Verificar que no hay errores de división

**Resultado Esperado:**
- Gráfico muestra 100% presencial ✓
- Sin errores de cálculo ✓
- Visualización correcta ✓

---

## Detalles Técnicos

### Rutas API
```
GET  /reportes/pedidos              # Vista principal
GET  /reportes/api/pedidos          # API JSON para AJAX
GET  /reportes/descargar/html       # Descarga HTML imprimible
GET  /reportes/exportar/pdf         # PDF (si DomPDF está instalado)
```

### Controlador: `ReportController`
Métodos principales:
- `index()`: Renderiza vista con datos
- `getData()`: Retorna JSON para actualizaciones AJAX
- `downloadHTML()`: Genera HTML imprimible
- `exportPDF()`: Genera PDF

### Data Class: `ReportData`
Métodos principales:
- `getOrdersByOrigin()`: Calcula pedidos por origen
- `getRevenueByOrigin()`: Calcula ingresos por origen
- `getDailyTrend()`: Obtiene qtrend diario
- `getPeriodDates()`: Convierte período a fechas
- `validateDateRange()`: Valida rango de fechas

---

## Ejemplos de Uso

### Ejemplo 1: Ver Reporte del Mes Actual
```bash
# URL
GET /reportes/pedidos?period=month

# Resultado
- Se muestran estadísticas del mes actual
- Gráficos con datos acumulados
- Tabla con detalle por tipo
```

### Ejemplo 2: Reporte Personalizado
```bash
# URL
GET /reportes/pedidos?period=custom&start_date=2024-04-01&end_date=2024-04-15

# Resultado
- Se muestran estadísticas del 1-15 de abril
- Período específico en los gráficos
- Opción para exportar
```

### Ejemplo 3: Descargar Reporte
```bash
# URL
GET /reportes/descargar/html?period=month

# Resultado
- Se abre vista de descarga
- Se puede imprimir a PDF desde navegador
- Formato profesional con validaciones
```

---

## Resolución de Problemas

### Problema: No aparecen datos
**Solución:**
1. Verificar que el período tiene pedidos registrados
2. Verificar que el usuario está autenticado como gerente
3. Verificar que el local tiene pedidos en la BD

### Problema: Porcentajes no suman 100%
**Solución:**
1. Esto ha sido mitigado automáticamente
2. El sistema ajusta decimales
3. Si persiste, verificar cálculos en `ReportData::getOrdersByOrigin()`

### Problema: Gráficos no cargan
**Solución:**
1. Verificar que Chart.js está cargado (CDN)
2. Verificar que no hay errores en consola del navegador
3. Recargar página

---

## Notas de Desarrollo

### Dependencias
- **Laravel 9+**: Framework base
- **Chart.js 3.9+**: Gráficos (vía CDN)
- **Tailwind CSS**: Estilos

### Modelos Utilizados
- `Order`: Modelo de pedidos
- `User`: Modelo de usuario/gerente
- `Local`: Modelo de local

### Campos del Modelo Order
- `origin`: 'web' o 'presencial'
- `date`: Fecha del pedido
- `total_amount`: Monto del pedido
- `local_id`: Local asociado

---

## Próximas Mejoras (Futuros)

- [ ] Exportación a Excel
- [ ] Exportación a PDF (barryvdh/laravel-dompdf)
- [ ] Gráficos con más dimensiones (por categoría, por hora)
- [ ] Comparación año a año
- [ ] Alertas automáticas de tendencias
- [ ] Generación de reportes programados

---

**Última actualización**: Abril 7, 2024
**Estado**: ✅ Implementado y Funcional
