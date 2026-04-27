<!-- ═══ MIS ÓRDENES: PANEL LATERAL ═══ -->
<div class="my-orders-drawer" :class="{ open: showMyOrdersDrawer }">
    <!-- Header -->
    <div class="drawer-header">
        <button class="btn-back-drawer" @click="closeMyOrdersDrawer" title="Volver">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="drawer-handle" @click="closeMyOrdersDrawer" title="Deslizar para cerrar"></button>
        <h3>Mis Órdenes</h3>
    </div>

    <!-- Body -->
    <div class="drawer-body">
        <!-- Confirmación de Cancelación -->
        <div v-if="selectedOrderToCancel" class="drawer-confirm-order">
            <div class="confirm-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h4>¿Cancelar orden?</h4>
            </div>
            <div class="confirm-body">
                <p class="confirm-text"><strong>@{{ selectedOrderToCancel.order_number }}</strong></p>
                <p class="confirm-text">Token: <code>@{{ selectedOrderToCancel.token }}</code></p>
                <p class="confirm-text">Esta orden está en estado: <strong>@{{ selectedOrderToCancel.status_label }}</strong></p>
                <p class="confirm-text" style="color: #d32f2f; margin-top: 12px;">
                     Una vez confirmada la preparación, no podrás cancelarla.
                </p>
                
                <!-- Motivo de cancelación (opcional) -->
                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--muted);">
                        Motivo (opcional):
                    </label>
                    <textarea 
                        v-model="cancelReason"
                        placeholder="¿Por qué cancelas la orden?"
                        style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 4px; font-family: inherit; font-size: 0.85rem; resize: vertical; min-height: 60px;">
                    </textarea>
                </div>
            </div>
            <div class="confirm-actions">
                <button class="btn-confirm-no" @click="cancelarSeleccion">
                    <i class="fas fa-times"></i> Mantener Orden
                </button>
                <button class="btn-confirm-yes btn-confirm-danger" @click="confirmarCancelacion" :disabled="isCancellingOrder">
                    <span v-if="!isCancellingOrder"><i class="fas fa-trash"></i> Cancelar Orden</span>
                    <span v-else><i class="fas fa-spinner fa-spin"></i> Cancelando...</span>
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="myOrders.length === 0" class="drawer-empty">
            <i class="fas fa-inbox"></i>
            <p>No tienes órdenes pendientes</p>
            <p class="empty-hint">Tus órdenes confirmadas aparecerán aquí</p>
        </div>

        <!-- Orders List -->
        <div v-else class="my-orders-list">
            <div v-for="order in myOrders" :key="order.order_id" class="order-card">
                <!-- Status Badge -->
                <div class="order-header">
                    <div class="order-number">
                        <strong>@{{ order.order_number }}</strong>
                        <span class="order-token" title="Token de verificación">@{{ order.token }}</span>
                    </div>
                    <div class="order-status" :class="order.status === 'Pending' ? 'status-pending' : 'status-preparing'">
                        <i class="fas" :class="order.status === 'Pending' ? 'fa-clock' : 'fa-fire'"></i>
                        @{{ order.status_label }}
                    </div>
                </div>

                <!-- Local info -->
                <div class="order-local">
                    <i class="fas fa-store"></i>
                    @{{ order.local_name }}
                </div>

                <!-- Items Summary -->
                <div class="order-items">
                    <div v-for="item in order.items" :key="item.product_id" class="order-item-summary">
                        <span class="item-name">@{{ item.product_name }}</span>
                        <span class="item-qty">x@{{ item.quantity }}</span>
                        <span class="item-price">₡@{{ (item.price * item.quantity).toFixed(2) }}</span>
                    </div>
                </div>

                <!-- Customization if exists -->
                <div v-if="order.items.some(i => i.customization)" class="order-customization">
                    <div v-for="item in order.items.filter(i => i.customization)" :key="item.product_id" class="custom-note">
                        <small><strong>@{{ item.product_name }}:</strong> @{{ item.customization }}</small>
                    </div>
                </div>

                <!-- Total and Time -->
                <div class="order-footer">
                    <div class="order-total">
                        <strong>Total:</strong>
                        <span style="font-weight: 600; color: var(--primary);">₡@{{ order.total_amount }}</span>
                    </div>
                    <div class="order-time">
                        <small>@{{ order.created_at }}</small>
                    </div>
                </div>

                <!-- Actions -->
                <div v-if="order.can_cancel" class="order-actions">
                    <button class="btn-cancel-order" @click="seleccionarParaCancelar(order)">
                        <i class="fas fa-ban"></i> Cancelar Orden
                    </button>
                    <p class="help-text">Aún puedes cancelar esta orden. Una vez en preparación, no será posible.</p>
                </div>
                <div v-else class="order-locked">
                    <i class="fas fa-lock"></i> Orden en preparación - No se puede cancelar
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="my-orders-drawer-overlay" v-if="showMyOrdersDrawer" @click="closeMyOrdersDrawer"></div>

<style>
.my-orders-drawer {
    position: fixed;
    right: -400px;
    top: 0;
    width: 400px;
    height: 100vh;
    background: var(--card);
    border-left: 1px solid var(--border);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
    box-shadow: -4px 0 12px rgba(0,0,0,0.1);
}

.my-orders-drawer.open {
    right: 0;
}

.my-orders-drawer .drawer-header {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.my-orders-drawer .drawer-header h3 {
    margin: 0;
    flex: 1;
    font-size: 1.1rem;
    color: var(--text);
}

.my-orders-drawer .btn-back-drawer,
.my-orders-drawer .drawer-handle {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    font-size: 1.2rem;
    padding: 8px;
}

.my-orders-drawer .drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

.my-orders-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.order-card {
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    background: var(--surface);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    gap: 8px;
}

.order-number {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.order-number strong {
    font-size: 0.95rem;
    color: var(--text);
}

.order-token {
    font-size: 0.75rem;
    color: var(--muted);
    font-family: 'Courier New', monospace;
    background: rgba(0,0,0,0.05);
    padding: 2px 6px;
    border-radius: 3px;
}

.order-status {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
}

.order-status.status-pending {
    background: #fff3cd;
    color: #856404;
}

.order-status.status-preparing {
    background: #f8d7da;
    color: #721c24;
}

.order-local {
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.order-items {
    background: var(--card);
    border-radius: 4px;
    padding: 8px;
    margin: 8px 0;
    font-size: 0.8rem;
}

.order-item-summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    border-bottom: 1px solid var(--border);
}

.order-item-summary:last-child {
    border-bottom: none;
}

.item-name {
    flex: 1;
    color: var(--text);
    font-weight: 500;
}

.item-qty {
    color: var(--muted);
    margin: 0 8px;
}

.item-price {
    color: var(--primary);
    font-weight: 600;
}

.order-customization {
    background: rgba(0,0,0,0.03);
    border-left: 2px solid var(--primary);
    padding: 8px;
    border-radius: 4px;
    margin: 8px 0;
    font-size: 0.75rem;
}

.custom-note {
    display: block;
    margin: 4px 0;
    color: var(--muted);
}

.custom-note small {
    word-break: break-word;
}

.order-footer {
    border-top: 1px solid var(--border);
    padding-top: 8px;
    margin: 8px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-total {
    display: flex;
    gap: 8px;
    font-size: 0.9rem;
}

.order-time {
    font-size: 0.75rem;
    color: var(--muted);
}

.order-actions {
    margin-top: 8px;
}

.btn-cancel-order {
    width: 100%;
    background: #d32f2f;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-cancel-order:hover {
    background: #b71c1c;
}

.help-text {
    font-size: 0.7rem;
    color: var(--muted);
    margin: 6px 0 0;
    text-align: center;
}

.order-locked {
    background: #f5f5f5;
    border: 1px solid #ddd;
    color: #666;
    padding: 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.drawer-confirm-order {
    padding: 16px;
}

.confirm-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.confirm-header i {
    font-size: 1.5rem;
    color: #d32f2f;
}

.confirm-header h4 {
    margin: 0;
    color: var(--text);
}

.confirm-body {
    background: var(--surface);
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 16px;
    font-size: 0.9rem;
}

.confirm-text {
    margin: 8px 0;
    color: var(--text);
}

.confirm-text code {
    background: rgba(0,0,0,0.05);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}

.confirm-actions {
    display: flex;
    gap: 12px;
}

.btn-confirm-no,
.btn-confirm-yes {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.btn-confirm-no {
    background: var(--border);
    color: var(--text);
}

.btn-confirm-no:hover {
    background: #ddd;
}

.btn-confirm-danger {
    background: #d32f2f;
    color: white;
}

.btn-confirm-danger:hover {
    background: #b71c1c;
}

.btn-confirm-yes:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.drawer-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
}

.drawer-empty i {
    font-size: 3rem;
    color: var(--muted);
    margin-bottom: 16px;
    opacity: 0.5;
}

.drawer-empty p {
    margin: 8px 0;
    color: var(--muted);
}

.empty-hint {
    font-size: 0.85rem;
}

.my-orders-drawer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 1040;
    cursor: pointer;
}

@media (max-width: 768px) {
    .my-orders-drawer {
        width: 100%;
        right: -100%;
    }
}
</style>
