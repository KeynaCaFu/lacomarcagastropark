<!-- ═══ MODAL: AGREGAR AL CARRITO ═══ -->
<div id="addToCartModal" class="modal-overlay" v-if="showAddToCartModal" @click="closeAddToCartModal">
    <div class="modal-content" @click.stop>
        <div class="modal-header">
            <h3>Agregar al Carrito</h3>
            <button class="modal-close" @click="closeAddToCartModal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <!-- PRODUCTO -->
            <div class="product-summary">
                <div v-if="currentProduct.photo_url" style="margin-bottom: 12px; border-radius: 8px; overflow: hidden; height: 80px; width: 80px;">
                    <img :src="currentProduct.photo_url" :alt="currentProduct.name" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <h4>@{{ currentProduct.name }}</h4>
                <p v-if="currentProduct.description" style="font-size: 0.8rem; color: var(--muted); margin: 6px 0 8px 0; line-height: 1.4;">@{{ currentProduct.description }}</p>
                <p class="price"><sup>₡</sup>@{{ currentProduct.price }}</p>
            </div>

            <!-- CANTIDAD -->
            <div class="form-group">
                <label>Cantidad *</label>
                <div class="quantity-control">
                    <button @click="decreaseQuantity" :disabled="quantity <= 1">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input v-model.number="quantity" type="number" min="1" @input="validateQuantity">
                    <button @click="increaseQuantity">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <small v-if="quantity < 1" style="color: #e0ab5c;">Cantidad debe ser mayor a 0</small>
            </div>

            <!-- ESPECIFICACIONES DEL PRODUCTO -->
            <div class="form-group">
                <label>Notas o Especificaciones 
                    <span style="font-size: 0.65rem; color: var(--muted); font-weight: 400;">(Opcional)</span>
                </label>
                <textarea 
                    v-model="customization"
                    placeholder="Ej: Sin cebolla, extra queso, alergias, etc."
                    maxlength="500"
                    rows="3"
                    @input="validateCustomization"></textarea>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 3px;">
                    <small v-if="customization.length > 0" style="color: var(--primary);">✓ Notas agregadas</small>
                    <small style="color: var(--muted);">@{{ customization.length }}/500</small>
                </div>
            </div>

            <!-- SUBTOTAL -->
            <div class="order-total">
                <span>Subtotal:</span>
                <span><sup>₡</sup>@{{ (currentProduct.price * quantity).toFixed(2) }}</span>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" @click="closeAddToCartModal">Cancelar</button>
            <button class="btn-confirm" @click="proceedAddToCart" :disabled="isAddingToCart || quantity < 1">
                <span v-if="!isAddingToCart"><i class="fas fa-shopping-cart"></i> Agregar al Carrito</span>
                <span v-else><i class="fas fa-spinner fa-spin"></i> Agregando...</span>
            </button>
        </div>
    </div>
</div>
