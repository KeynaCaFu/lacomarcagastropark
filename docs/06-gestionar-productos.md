# Gestionar Productos

## Descripción General

El módulo de gestión de productos permite crear, editar, eliminar y administrar toda la información de los productos del restaurante. Incluye gestión de precios, descripciones, catálogo y galería de fotos.

---

## Acceder al Módulo de Productos

1. **En el Dashboard**, haz clic en **"Gestionar Productos"** en el menú lateral
2. Verás la **lista de todos los productos** registrados
3. Cada producto se muestra con su información básica

---

## Vista de Listado de Productos

### Información por Producto
Cada fila de producto muestra:
- **Thumbnail** (mini foto del producto)
- **Nombre:** Nombre del producto
- **Categoría:** Categoría a la que pertenece
- **Precio:** Precio actual
- **Galeria:** Galeria de fotos
- **Estado:** Disponible o No disponible
- **Acciones:** Botones para ver, editar o eliminar

### Filtros y Búsqueda
- **Barra de búsqueda:** Busca productos por nombre
- **Filtro por categoría:** Filtra por tipo de producto
- **Filtro por estado:** Muestra disponibles o no disponibles
- **Accordion de filtros:** Expande/colapsa opciones adicionales

### Ordenamiento
- Puedes hacer clic en los encabezados de columna para ordenar

---

## Registrar Nuevo Producto

### Paso 1: Abrir Formulario de Crear
1. Haz clic en el botón **"Nuevo Producto"** en la parte superior derecha
2. Se abrirá un **modal o formulario con los campos** para crear el producto

### Paso 2: Completar Información Básica

**Campos Obligatorios (marcados con *):**

- **Nombre del Producto:** Nombre descriptivo
  - Ej: "Filete Mignon Premium" o "Ceviche Peruano"
  -  Usa nombres claros que describan el plato
  
- **Categoría:** Selecciona la categoría del producto
  - Opciones: Entradas, Platos Principales, Postres, Bebidas, etc.
  
- **Precio:** Precio de venta unitario
  - Ej: ₡ 2500
  
- **Estado:** Selecciona la disponibilidad
  - **Disponible:** El producto se puede vender
  - **No disponible:** El producto no está en stock

**Campos Opcionales:**

- **Descripción:** Detalles del producto
  - Ej: "Filete de res de 250g, jugoso y tierno, acompañado de verduras frescas y salsa de champiñones"
  - Describe ingredientes, preparación, tamaño, etc.
  
- **Ingredientes:** Componentes principales
  - Ej: "Res, champiñones, crema, ajo, sal, pimienta"
  
- **Alergias:** Información de alérgenos
  - Ej: "Contiene gluten, frutos secos (nueces)"
  
- **Código:** Código interno del producto
  - Ej: "PROD-001" o "FILETE-PREM-250"

### Paso 3: Subir Foto Principal
1. Haz clic en **"Examinar"**
2. Elige una foto clara y atractiva del producto
3. Formatos: JPG, PNG
4. Tamaño máximo: 2MB
5. La foto se previsualiza en el formulario

### Paso 4: Guardar Producto
1. Revisa que todos los datos sean correctos
2. Haz clic en **" Crear Producto"**
3. Se pedirá confirmación
4. Verás un mensaje: **"Producto registrado exitosamente"**
5. El producto aparecerá en la lista

---

##  Editar Producto

### Paso 1: Abrir Formulario de Edición
1. Busca el producto en la lista
2. Haz clic en el botón **"Editar"** (lápiz)

### Paso 2: Modificar Información
- Todos los campos se pueden editar
- Los campos marcados con * son obligatorios

**Campos que puedes cambiar:**
- Nombre del producto
- Categoría
- Precio
- Descripción
- Etiqueta
- Tipo de Producto
- Estado (Disponible/No disponible)
- Foto principal

### Paso 3: Actualizar Foto
1. Para cambiar la foto, haz clic en **"Examinar"**
2. Elige una nueva foto
3. La imagen se actualizará automáticamente

### Paso 4: Guardar Cambios
1. Revisa que todos los datos sean correctos
2. Haz clic en **"Guardar Cambios"**
3. Los cambios se aplicarán inmediatamente

---

## Eliminar Producto

### Paso 1: Seleccionar Producto
1. Busca el producto en la lista
2. Haz clic en el botón **"Eliminar"** (icono rojo de basura)

### Paso 2: Confirmar Eliminación
1. Aparecerá un **diálogo de confirmación**
2. Se mostrará el nombre del producto a eliminar
3. Lee el mensaje de advertencia

### Paso 3: Completar la Eliminación
1. Ingresa el **nombre del producto** para confirmar
2. Haz clic en **"Sí, eliminar"**
3. El producto será eliminado del sistema

**Nota:** Esta acción es permanente y no se puede deshacer durante 10 segundos. Se eliminarán también todas sus galerías de fotos.

---

## Gestionar Galería de Fotos

La galería permite agregar múltiples fotos a cada producto para mostrar detalles, presentación, ingredientes, etc.

### Paso 1: Acceder a la Galería
1. En el listado de productos, haz clic en **"Galería"** 

### Paso 2: Ver Fotos Existentes
- Se mostrarán todas las fotos agregadas al producto
- Cada foto tiene opciones para:
  - Establecer como principal
  - Eliminar

### Paso 3: Agregar Nueva Foto

#### Opción A: Arrastrar y Soltar
1. Arrastra una imagen desde tu computadora
2. Suéltala en el área designada
3. La foto se cargará automáticamente

#### Opción B: Hacer Clic para Seleccionar
1. Haz clic en **"Seleccionar fotos"** o **"Agregar Fotos"**
2. Se abrirá un explorador de archivos
3. Selecciona una o más fotos
4. Haz clic en abrir
5. Las fotos se cargarán al servidor

**Formatos:** JPG, PNG  
**Tamaño máximo por foto:** 2MB  
**Cantidad máxima de fotos:** Sin límite

### Paso 4: Establecer Foto Principal
1. En la galería, busca la foto que deseas como principal
2. Haz clic en **"Establecer como principal"**
3. Esta foto será la que se muestre en el listado de productos

### Paso 5: Eliminar Foto
1. Busca la foto que deseas eliminar en la galería
2. Haz clic en **"Eliminar"** (icono rojo de basura)
3. Se pedirá confirmación
4. Haz clic en **"Sí, eliminar"**
5. La foto será eliminada de la galería

---

## 🔍 Cambiar Estado de Producto

### Disponible / No Disponible
Los productos pueden tener dos estados:

- **Disponible:** Se muestra al público y se puede vender
- **No disponible:** Se ocultará temporalmente (sin stock, fuera de temporada, etc.)

### Pasos:
1. Haz clic en **"Editar"** en el producto
2. En el campo **"Estado"**, selecciona:
   - Disponible
   - No disponible
3. Haz clic en **"Guardar Cambios"**

**Ventaja:** Cambiar estado es más rápido que eliminar y recuperar el producto.

---

## Actualizar Precios

### Cambiar Precio Individual
1. Haz clic en **"Editar"** en el producto
2. En el campo **"Precio"**, ingresa el nuevo precio
3. Haz clic en **"Guardar Cambios"**

### Consejos de Precios 
**Actualización Regular:** Revisa precios según temporada 
**Promociones:** Nota cambios temporales con descripción  

---

## Mejores Prácticas

### Nombres de Productos
Sé descriptivo y específico  
Incluye tamaño o peso (Ej: "Pasta Carbonara 300g")  
Usa mayúscula inicial  
Evita abreviaturas confusas  

### Fotos
Imágenes claras con buena iluminación  
 Presentación atractiva del producto  
Mínimo 800x600 píxeles de resolución  
Varias ángulos diferentes en la galería  
Evitar fotos pixeladas o de mala calidad  

### Descripciones
Describe ingredientes principales  
Menciona alergias y restricciones  
Incluye tamaño o peso  
Destaca características especiales  
No escribas textos muy largos  

### Categorización
Usa categorías estándar y claras  
Agrupa productos similares  
Facilita la búsqueda de clientes  

---

##  Ejemplo de Producto Completo

**Nombre:** Tacos al Pastor Premium  
**Categoría:** Platos Principales  
**Precio:** ₡ 1400 
**Estado:** Disponible  

**Descripción:**
  "Tres tacos de tortilla de maíz casera rellenos de carne de cerdo marinada con especias tradicionales, piña caramelizada, cebolla morada y cilantro fresco. Acompañados con salsa verde, salsa roja y lima."

**Ingredientes:** Cerdo, piña, cebolla, cilantro, tortillas de maíz, especias variadas, limón  

**Fotos:**
- Presentación completa del plato
- Detalle de los ingredientes
- Foto de perfil lateral
- Detalle con salsa

---

## Solución de Problemas

### La foto no se carga
- Verifica el tamaño (máximo 2MB)
- Intenta con un formato diferente (JPG en lugar de PNG)
- Revisa tu conexión a internet

### El producto no aparece en la lista
- Verifica que esté marcado como "Disponible"
- Recarga la página del navegador (F5)
- Intenta cambiar los filtros

### No puedo editar el precio
- Asegúrate de que el campo tenga solo números y punto
- Ej correcto: ₡ 2550

---

**Anterior:** [Dashboard del Gerente](05-gerente-dashboard.md)  
**Ir al inicio:** [Volver a Inicio de Sesión](01-inicio-sesion.md)
