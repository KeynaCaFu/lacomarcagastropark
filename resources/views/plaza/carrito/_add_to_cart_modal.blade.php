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
                <h4>@{{ currentProduct.name }}</h4>
                <p class="price"><sup>₡</sup>@{{ currentProduct.price }}</p>
            </div>

            <!-- CANTIDAD -->
            <div class="form-group">
                <label>Cantidad</label>
                <div class="quantity-control">
                    <button @click="decreaseQuantity" :disabled="quantity <= 1">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input v-model.number="quantity" type="number" min="1" @input="validateQuantity">
                    <button @click="increaseQuantity">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- ESPECIFICACIONES DEL PRODUCTO -->
            <div class="form-group">
                <label>Notas o Especificaciones (Opcional)</label>
                <textarea 
                    v-model="customization"
                    placeholder="Ej: Sin cebolla, extra queso, alergias, etc."
                    maxlength="500"
                    rows="3"></textarea>
                <small>@{{ customization.length }}/500</small>
            </div>

            <!-- SUBTOTAL -->
            <div class="order-total">
                <span>Subtotal:</span>
                <span><sup>₡</sup>@{{ (currentProduct.price * quantity).toFixed(2) }}</span>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" @click="closeAddToCartModal">Cancelar</button>
            <button class="btn-confirm" @click="proceedAddToCart" :disabled="isAddingToCart">
                <span v-if="!isAddingToCart"><i class="fas fa-shopping-cart"></i> Agregar al Carrito</span>
                <span v-else><i class="fas fa-spinner fa-spin"></i> Agregando...</span>
            </button>
        </div>
    </div>
</div>
