# Implementación: Cancelación de Órdenes por Cliente

## ✅ Funcionalidad Completada

El cliente puede ahora cancelar sus órdenes **únicamente mientras estén en estado "Pending" (Pendiente)**. Una vez que la orden pasa a "Preparing" (En preparación), ya no puede ser cancelada.

Cuando se cancela una orden, los ítems son automáticamente devueltos al carrito de compra.

---

## 🔄 Flujo Completo

```
1. Cliente confirma orden (QR + GPS)
   ↓
2. Orden se crea en estado "Pending" con Token LCGP-XXXX
   ↓
3. Se muestra drawer "Mis Órdenes" al cliente
   ↓
4. Cliente ve sus órdenes pendientes
   ↓
   ├─→ Si está en "Pending" → Botón "Cancelar Orden"
   └─→ Si está en "Preparing" → Bloqueada (no se puede cancelar)
   ↓
5. Cliente hace clic en "Cancelar Orden"
   ↓
6. Aparece modal de confirmación
   ↓
7. Si confirma:
   ├─→ Orden se marca como "Cancelled"
   ├─→ Ítems vuelven al carrito
   ├─→ Se muestra confirmación
   └─→ Cliente puede editar y re-confirmar
   
8. Si la orden pasa a "Preparing" (gerente la procesa):
   └─→ Ya NO se puede cancelar desde el cliente
```

---

## 📁 Cambios Realizados

### 1. Backend

#### CartController.php
**Nuevos métodos:**

- **`getMyOrders()`**
  - Obtiene órdenes pendientes y en preparación del cliente autenticado
  - Retorna información completa: número, token, estado, items, total, local
  - Incluye flag `can_cancel` para validar si se puede cancelar
  
- **`cancelOrder($orderId)`**
  - Válida que la orden pertenezca al usuario autenticado
  - Verifica que la orden esté en estado "Pending"
  - Actualiza estado a "Cancelled"
  - Recupera items y los devuelve al carrito (sesión)
  - Maneja customizaciones y cantidades correctamente
  - Transacción atómica (rollback en caso de error)

#### routes/web.php
**Nuevas rutas:**

```php
Route::middleware('auth')->group(function () {
    Route::get('carrito/api/mis-ordenes', [CartController::class, 'getMyOrders'])->name('my.orders');
    Route::post('carrito/api/cancelar/{orderId}', [CartController::class, 'cancelOrder'])->name('cancel.order');
});
```

### 2. Frontend

#### plaza/index.blade.php, product-detail.blade.php, show.blade.php

**Data properties agregados:**
```javascript
myOrders: [],
showMyOrdersDrawer: false,
isCancellingOrder: false,
selectedOrderToCancel: null,
cancelReason: ''
```

**Nuevos métodos Vue:**
- `loadMyOrders()` - Obtiene órdenes del servidor
- `closeMyOrdersDrawer()` - Cierra el drawer
- `seleccionarParaCancelar(order)` - Selecciona orden para cancelar
- `cancelarSeleccion()` - Cancela la selección
- `confirmarCancelacion()` - Envía solicitud de cancelación

**Cambio en `processCheckout()`:**
Después de éxito, carga automáticamente las órdenes del cliente:
```javascript
setTimeout(() => {
    this.loadMyOrders();
}, 1000);
```

#### _my_orders_drawer.blade.php (Nuevo)

Componente Vue que muestra:
- ✅ Lista de órdenes pendientes del cliente
- ✅ Información: número, token, estado, local, items, total, fecha
- ✅ Status badge: "Pendiente" (amarillo) o "En preparación" (rojo)
- ✅ Botón "Cancelar Orden" solo si está en "Pending"
- ✅ Modal de confirmación con opción de agregar motivo
- ✅ Estilos responsive y consistentes con diseño

---

## 🎯 Validaciones

### En el Backend:
1. ✅ Usuario autenticado
2. ✅ Orden pertenece al usuario
3. ✅ Orden está en estado "Pending"

### En el Frontend:
1. ✅ Verificar que `can_cancel = true` antes de mostrar botón
2. ✅ Confirmación visual antes de cancelar
3. ✅ Motivo de cancelación (opcional)
4. ✅ Feedback en tiempo real

---

## 💾 Base de Datos - Cambios

**Ninguno requerido.** Se utiliza la estructura existente de `tborder`:
- Campo `status` (ya existe)
- Campo `cancellation_reason` (ya existe)

---

## 🔌 Endpoints

### Obtener Órdenes del Cliente
```
GET /carrito/api/mis-ordenes
Headers:
  - Authorization: Bearer {token} (por sesión)
Response:
{
  "success": true,
  "orders": [
    {
      "order_id": 1,
      "order_number": "ORD-1234",
      "token": "LCGP-5678",
      "status": "Pending",
      "status_label": "Pendiente",
      "total_amount": "100.00",
      "quantity": 3,
      "local_name": "Pizza La Nonna",
      "created_at": "2026-04-26 14:30:00",
      "confirmed_at": "2026-04-26 14:30:05",
      "can_cancel": true,
      "items": [
        {
          "product_id": 5,
          "product_name": "Pizza Margarita",
          "quantity": 2,
          "customization": "Sin cebolla",
          "price": 45.00
        }
      ]
    }
  ]
}
```

### Cancelar Orden
```
POST /carrito/api/cancelar/{orderId}
Body:
{
  "reason": "Olvidé agregar algo"  // Opcional
}
Response:
{
  "success": true,
  "message": "Orden cancelada exitosamente. Los items han sido devueltos a tu carrito.",
  "cart_count": 3
}
```

---

## 🎨 UX/UI

### Drawer "Mis Órdenes"
Se muestra automáticamente después de confirmar una orden con:

1. **Header**
   - Botón cerrar
   - Título "Mis Órdenes"

2. **Lista de Órdenes**
   - Número de orden + Token (copiable)
   - Estado con icon y color
   - Local asignado
   - Items con cantidades y precios
   - Customizaciones (si hay)
   - Total y fecha

3. **Botones de Acción**
   - Si `can_cancel = true`: Botón rojo "Cancelar Orden"
   - Si `can_cancel = false`: Texto bloqueado en gris

4. **Modal de Confirmación**
   - Muestra detalles de la orden
   - Campo para motivo (opcional)
   - Botones: "Mantener Orden" / "Cancelar Orden"

5. **Empty State**
   - Si no hay órdenes: Icono de inbox vacío

---

## 🧪 Casos de Uso

### Caso 1: Cliente Cancela Orden Pending
```
1. Cliente confirma orden → Estado: "Pending"
2. Drawer "Mis Órdenes" se abre automáticamente
3. Cliente hace clic en "Cancelar Orden"
4. Modal pide confirmación
5. Cliente confirma
6. Orden → "Cancelled"
7. Ítems vuelven al carrito
8. Toast: "Orden cancelada. Items devueltos al carrito"
```

### Caso 2: Cliente Intenta Cancelar Orden en Preparación
```
1. Gerente cambia orden a "Preparing"
2. Cliente abre "Mis Órdenes"
3. Ve la orden con estado "En preparación"
4. Botón "Cancelar" no aparece (bloqueado)
5. Texto: "Orden en preparación - No se puede cancelar"
```

### Caso 3: Items Devueltos al Carrito
```
1. Cliente tenía orden: 2x Pizza (₡90) + 1x Bebida (₡25)
2. Cancela orden
3. Abre carrito
4. Ve esos 3 items nuevamente disponibles
5. Si tenía otros items, se suman
6. Puede editar y re-confirmar
```

---

## ⚠️ Notas Importantes

1. **Los ítems se devuelven usando el precio actual del producto**, no el histórico. Esto es intencional para permitir cambios de precio.

2. **Las customizaciones se preservan** exactamente como estaban en la orden original.

3. **Transacción atómica**: Si algo falla, ni la orden se cancela ni los items se devuelven.

4. **Permisos**: Solo el dueño de la orden puede cancelarla.

5. **Estados**: 
   - Puede cancelar: ✅ "Pending"
   - No puede cancelar: ❌ "Preparing", "Ready", "Delivered", "Cancelled"

---

## 🚀 Próximos Pasos (Opcional)

1. Agregar notificación al gerente cuando un cliente cancela
2. Registrar razón de cancelación en BD (ya está el campo)
3. Estadísticas de cancelaciones
4. Email de confirmación de cancelación
5. Permitir que gerente reembolse manualmente
6. Sistema de devoluciones/cambios

