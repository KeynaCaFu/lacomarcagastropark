# Gestionar Usuarios

## Descripción General

Como administrador, tienes control total sobre la gestión de usuarios del sistema. Puedes crear nuevos usuarios, editar información existente y eliminar usuarios cuando sea necesario.

---

## Acceder al Módulo de Usuarios

1. **En el Dashboard**, haz clic en **"Usuarios"** en el menú lateral
2. **En el menu de la izquierda tambien podras acceder a **Usuarios**
2. Verás la **lista de todos los usuarios** registrados en el sistema

---

## Vista de Listado de Usuarios

### Columnas Disponibles
- **Nombre:** Nombre completo del usuario
- **Email:** Correo electrónico registrado
- **Teléfono:** Teléfono registrado
- **Rol:** Categoria de acceso (Administrador, Gerente, etc.)
- **Estado:** Activo o Inactivo
- **Acciones:** Botones para editar o eliminar

### Filtros y Búsqueda
- **Barra de búsqueda:** Busca usuarios por nombre o email
- **Filtro por rol:** Filtra usuarios por su rol
- **Filtro por estado:** Muestra usuarios activos o inactivos

---

## ➕ Crear Nuevo Usuario

### Paso 1: Abrir Formulario de Crear
1. Haz clic en el botón **"➕ Nuevo Usuario"** en la parte superior
2. Se abrirá un **modal con el formulario de creación**

### Paso 2: Completar los Datos

**Campos Obligatorios (marcados con *):**

- **Nombre Completo:** Ej: "Juan Pérez"
- **Correo Electrónico:** Correo electrónico único. Ej: "juan@ejemplo.com"
  - ⚠️ El email debe ser válido y no estar registrado
- **Rol:** Selecciona el rol del usuario
  - Admin: Acceso completo
  - Gerente: Acceso limitado a productos
- **Contraseña:** Mínimo 8 caracteres recomendado
  - 💡 Usa contraseñas fuertes con mayúsculas, minúsculas y números
- **Confirmar Contraseña:** Repite la contraseña
- **Estado:** Activo o Inactivo, si coloca inactivo no lograra iniciar sesion.

**Campos Opcionales:**

- **Teléfono:** Número de contacto

### Paso 3: Guardar
1. Revisa que todos los datos sean correctos
2. Haz clic en **"✔️ Crear Usuario"**
3. Verás un mensaje de confirmación: **"Usuario registrado exitosamente"**
4. El nuevo usuario aparecerá en la lista

---

## ✏️ Editar Usuario

### Paso 1: Abrir Formulario de Edición
1. Busca el usuario en la lista
2. Haz clic en el botón **"✏️ Editar"** (en la fila del usuario)

### Paso 2: Modificar Datos
- Puedes cambiar cualquier
- Los campos marcados con * son obligatorios

**Campos que puedes editar:**
- Nombre completo
-correo electrónico
- Contraseña (dejar en blanco si no deseas cambiar)
- Rol
- Teléfono
- Estado (Activo/Inactivo)

### Paso 3: Guardar Cambios
1. Haz clic en **"✔️ Guardar Cambios"**
2. Si cambias la contraseña, aparecerá una confirmación
3. Los cambios se aplicarán inmediatamente

---

## 🗑️ Eliminar Usuario

### Paso 1: Seleccionar Usuario
1. Busca el usuario en la lista
2. Haz clic en el botón **"🗑️ Eliminar"** (icono rojo de basura)

### Paso 2: Confirmar Eliminación
1. Aparecerá un **diálogo de confirmación** preguntando si estás seguro
2. Verás el nombre del usuario que vas a eliminar
3. Este nombre debe coincidir

### Paso 3: Completar la Eliminación
1. Ingresa el **nombre del usuario** para confirmar
2. Haz clic en **"✔️ Sí, eliminar"**
3. El usuario será eliminado del sistema

**⚠️ Nota:** Esta acción se permite darle deshacer cambios si se elimino por error

---

## 🔍 Cambiar Estado de Usuario

### Activar/Desactivar Usuario
1. En la lista, busca el usuario
2. Haz clic en **"Editar"**
3. Cambia el estado a:
   - **Activo:** El usuario puede acceder al sistema
   - **Inactivo:** El usuario no puede acceder al sistema
4. Haz clic en **"Guardar Cambios"**

**✨ Ventaja:** Desactivar usuario es más seguro que eliminar, ya que preserva su información.

---

## 💡 Consejos Útiles

✅ **Contraseñas Seguras:** Usa combinaciones de letras, números y caracteres especiales  
✅ **Correos electrónicos Válidos:** Asegúrate de ingresar correctamente el email  
✅ **Roles Apropiados:** Asigna roles según las responsabilidades del usuario  
✅ **Backup de Datos:** Ante de eliminar, considera desactivar primero  
✅ **Búsqueda Rápida:** Usa la barra de búsqueda para encontrar usuarios fácilmente  

---

**Siguiente Sección:** [Gestionar Eventos](04-gestionar-eventos.md)  
**Anterior:** [Dashboard del Administrador](02-admin-dashboard.md)
