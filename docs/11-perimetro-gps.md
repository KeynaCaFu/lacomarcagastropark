# Perímetro de Seguridad (GPS)

## Descripción General

El módulo de **Perímetro de Seguridad** permite establecer una "geovalla" (geofence) alrededor de La Comarca GastroPark. Su objetivo principal es validar que los clientes se encuentren físicamente dentro de las instalaciones para poder procesar pedidos, evitando fraudes o pedidos accidentales desde ubicaciones remotas.

---

## Acceder al Módulo GPS

1. **En el Dashboard**, haz clic en **"Perímetro de Seguridad"** en el menú lateral.
2. Verás el **Panel de Configuración Actual** con los datos de ubicación y el mapa de vista previa.

---

## Vista de Resumen (Index)

### Información Actual
El panel izquierdo muestra los valores vigentes:
- **Latitud Centro:** Coordenada norte/sur del centro de la plaza.
- **Longitud Centro:** Coordenada este/oeste del centro de la plaza.
- **Radio Permitido:** La distancia máxima (en metros) desde el centro donde se permiten pedidos.

### Visualización del Área
El mapa de la derecha muestra un **círculo naranja**. Solo los clientes ubicados dentro de este círculo podrán confirmar sus órdenes.

---

## Actualizar el Perímetro

Para modificar la ubicación o el radio, haz clic en el botón **"Editar Perímetro"**.

### Paso 1: Establecer el Centro
Tienes tres formas de definir la ubicación:

1. **Uso de GPS (Recomendado):** 
   - Párate físicamente en el centro del GastroPark con un teléfono o laptop.
   - Haz clic en **"Usar mi ubicación actual"**.
   - El sistema capturará tus coordenadas exactas automáticamente.
   
2. **Mapa Interactivo:**
   - Haz clic en cualquier parte del mapa para mover el marcador.
   - Arrastra el marcador azul a la posición deseada.
   
3. **Entrada Manual:**
   - Escribe directamente las coordenadas si las tienes (ej. desde Google Maps).

### Paso 2: Definir el Radio
Ingresa la cantidad de metros en el campo **"Radio Permitido"**. 
- El mapa se actualizará en tiempo real mostrando el tamaño del nuevo perímetro.
- **Recomendación:** Un radio de entre 50m y 150m suele ser suficiente para cubrir toda la plaza.

### Paso 3: Guardar Cambios
Haz clic en **"Guardar Cambios"** para aplicar la nueva configuración.

---

## Detalles Técnicos

### Tabla de Base de Datos
- **Tabla:** `tbplaza_config`
- **Campos:** `latitude`, `longitude`, `radius_meters`.

### Integración
Este valor es consumido por el proceso de compra (Checkout) para comparar la posición del GPS del cliente contra estas coordenadas usando la fórmula de Haversine (cálculo de distancia en una esfera).

---

## Mejores Prácticas

**Precisión del Centro:** Asegúrate de que el punto central esté realmente en el medio del local para que el radio cubra todas las mesas por igual.  
**Margen de Error:** Considera que los GPS de los teléfonos pueden tener un error de 5 a 10 metros; no configures un radio demasiado ajustado.  
**Pruebas en Sitio:** Una vez configurado, intenta realizar un pedido desde los extremos de la plaza para verificar que el sistema lo permite.  
**Actualización:** Si la plaza se expande o se mueve el área de mesas, recuerda actualizar el radio aquí.

---

## Solución de Problemas

### El botón de "Ubicación Actual" no funciona
- Verifica que el sitio tenga certificado SSL (HTTPS).
- Asegúrate de haber dado permiso de ubicación al navegador.

### El mapa no carga
- Verifica tu conexión a internet.
- El sistema utiliza **Leaflet.js** y **OpenStreetMap**, que requieren acceso a la red para descargar los mapas.

### Las coordenadas no se guardan
- Verifica que el formato sea numérico (ej: 9.9333 y no 9,9333).

---

**Anterior:** Gestionar Eventos  
**Siguiente:** QR de Validación