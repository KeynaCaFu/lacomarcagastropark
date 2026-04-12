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
        <!-- Confirmación de Vaciar Carrito -->
        <div v-if="showConfirmClear" class="drawer-confirm-order">
            <div class="confirm-header">
                <i class="fas fa-exclamation-circle"></i>
                <h4>¿Vaciar carrito?</h4>
            </div>
            <div class="confirm-body">
                <p class="confirm-text">Se eliminarán todos los items del carrito. Esta acción no se puede deshacer.</p>
            </div>
            <div class="confirm-actions">
                <button class="btn-confirm-no" @click="cancelClearCart">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn-confirm-yes btn-confirm-danger" @click="confirmClearCart">
                    <i class="fas fa-trash"></i> Vaciar
                </button>
            </div>
        </div>

        <!-- Confirmación de Orden (Reemplaza el contenido del carrito) -->
        <div v-else-if="showConfirmOrder" class="drawer-confirm-order">
            <div class="confirm-header">
                <i class="fas fa-check-circle"></i>
                <h4>¿Confirmar orden?</h4>
            </div>
            <div class="confirm-body">
                <!-- Resumen de Items -->
                <div class="confirm-items-summary">
                    <div v-for="(item, index) in drawerCart" :key="index" class="confirm-item">
                        <div class="confirm-item-left">
                            <div class="confirm-item-name">@{{ item.name }}</div>
                            <div class="confirm-item-qty">x@{{ item.quantity }}</div>
                        </div>
                        <div class="confirm-item-price">
                            ₡@{{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="confirm-total">
                    <span>Total:</span>
                    <strong>₡@{{ totalDrawerPrice.toFixed(2) }}</strong>
                </div>
                <p class="confirm-text">¿Deseas proceder con esta orden?</p>
            </div>
            <div class="confirm-actions">
                <button class="btn-confirm-no" @click="cancelConfirmOrder">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn-confirm-yes" @click="processCheckout" :disabled="isCheckingOut">
                    <span v-if="!isCheckingOut"><i class="fas fa-check"></i> Confirmar</span>
                    <span v-else><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="drawerCart.length === 0" class="drawer-empty">
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
    <div class="drawer-footer" v-if="!showConfirmOrder && !showConfirmClear && drawerCart.length > 0">
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
            <button class="btn-drawer-clear" @click="goToClearCart">
                <i class="fas fa-trash"></i> Vaciar Carrito
            </button>
            <button class="btn-drawer-checkout" @click="goToCheckout" :disabled="isCheckingOut">
                <span v-if="!isCheckingOut"><i class="fas fa-check-circle"></i> Confirmar Orden</span>
                <span v-else><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
            </button>
        </div>
    </div>

</div>
