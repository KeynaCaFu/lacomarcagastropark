<!-- ═══ CART DRAWER: PANEL LATERAL ═══ -->
<div class="cart-drawer-overlay" v-if="showCartDrawer" @click="closeCartDrawer"></div>

<div class="cart-drawer" :class="{ open: showCartDrawer }">
    <!-- Header -->
    <div class="drawer-header">
        <button class="btn-back-drawer" @click="closeCartDrawer" title="Volver">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h3>Mi Carrito</h3>
    </div>

    <!-- Body -->
    <div class="drawer-body">
        <!-- Empty State -->
        <div v-if="drawerCart.length === 0" class="drawer-empty">
            <i class="fas fa-shopping-cart"></i>
            <p>Tu carrito está vacío</p>
            <p class="empty-hint">Agrega productos para comenzar</p>
        </div>

        <!-- Items List -->
        <div v-else class="drawer-items">
            <div v-for="(item, index) in drawerCart" :key="index" class="drawer-item">
                <!-- Image (Miniatura) -->
                <div class="drawer-item-img">
                    <img :src="item.photo_url" :alt="item.name" onerror="this.src='{{ asset('images/product-placeholder.png') }}'">
                </div>

                <!-- Details & Controls -->
                <div class="drawer-item-content">
                    <!-- Nombre con Cantidad arriba a la derecha -->
                    <div class="drawer-item-header">
                        <div class="drawer-item-name">@{{ item.name }}</div>
                        <div class="drawer-item-qty-badge">
                            <small>Cantidad:</small>
                            <strong>@{{ item.quantity }}</strong>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div v-if="item.description" class="drawer-item-desc">@{{ item.description }}</div>
                    
                    <!-- Customization Notes -->
                    <div v-if="item.customization" class="drawer-item-custom">
                        <small> @{{ item.customization }}</small>
                    </div>

                    <!-- Footer: Price & Actions -->
                    <div class="drawer-item-footer">
                        <div class="info-unitario">
                            <small>Precio Unitario:</small>
                            <strong>₡@{{ parseFloat(item.price).toFixed(2) }}</strong>
                        </div>
                        <div class="info-subtotal">
                            <small>Subtotal:</small>
                            <strong>₡@{{ (parseFloat(item.price) * item.quantity).toFixed(2) }}</strong>
                        </div>
                        <div class="drawer-item-controls">
                            <button @click="updateItemQty(index, item.quantity - 1)" :disabled="item.quantity <= 1" class="btn-qty" title="Reducir">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button @click="removeFromCart(index)" class="btn-remove" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button @click="updateItemQty(index, item.quantity + 1)" class="btn-qty" title="Aumentar">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="drawer-footer" v-if="drawerCart.length > 0">
        <!-- Summary -->
        <div class="drawer-summary">
            <div class="summary-line">
                <span>Items:</span>
                <strong>@{{ drawerCart.length }}</strong>
            </div>
            <div class="summary-line">
                <span>Total Unidades:</span>
                <strong>@{{ totalDrawerQty }}</strong>
            </div>
            <div class="summary-line total">
                <span>Total:</span>
                <strong>₡@{{ totalDrawerPrice.toFixed(2) }}</strong>
            </div>
        </div>

        <!-- Buttons -->
        <div class="drawer-actions">
            <button class="btn-drawer-clear" @click="clearDrawerCart">
                <i class="fas fa-trash"></i> Vaciar Carrito
            </button>
            <button class="btn-drawer-checkout" @click="goToCheckout" :disabled="isCheckingOut">
                <span v-if="!isCheckingOut"><i class="fas fa-check-circle"></i> Confirmar Orden</span>
                <span v-else><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
            </button>
        </div>
    </div>
</div>
