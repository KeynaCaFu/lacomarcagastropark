<!-- Modal para crear nueva orden -->
<div id="createOrderModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 90%; max-width: 700px; max-height: 80vh; overflow-y: auto;">
        <!-- Header del Modal -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: #f9fafb;">
            <h3 style="margin: 0; color: #111;">
                <i class="fas fa-plus-circle" style="margin-right: 8px; color: #e18018;"></i> Crear Nueva Orden
            </h3>
            <button type="button" id="closeOrderModal" style="background: none; border: none; font-size: 24px; color: #999; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Contenido del Modal -->
        <form id="createOrderForm" style="padding: 20px;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <!-- Cliente (Opcional) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #111;">
                    Cliente <span style="color: #999; font-weight: 400;">(Opcional)</span>
                </label>
                <input type="text" id="customerSearch" placeholder="Buscar cliente por nombre o email..." style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                <input type="hidden" id="customerId" name="user_id" value="">
                <div id="customerResults" style="display: none; background: white; border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 8px; max-height: 200px; overflow-y: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
            </div>

            <!-- Productos -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #111;">Productos</label>
                
                <div id="productsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px;">
                    <!-- Se completa con JavaScript -->
                </div>
            </div>

            <!-- Resumen de Orden -->
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <table style="width: 100%; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 8px 0;">Producto</th>
                            <th style="text-align: center; padding: 8px 0; width: 50px;">Cant.</th>
                            <th style="text-align: right; padding: 8px 0; width: 80px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsSummary">
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 16px; color: #999;">Sin productos seleccionados</td>
                        </tr>
                    </tbody>
                </table>
                <div style="border-top: 1px solid #e5e7eb; margin-top: 12px; padding-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 16px; font-weight: 700;">
                    <span>Total:</span>
                    <span style="color: #e18018;">₡<span id="orderTotal">0.00</span></span>
                </div>
            </div>

            <!-- Tiempo de Preparación -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #111;">
                    Tiempo de Preparación (minutos) <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" name="preparation_time" id="preparationTime" min="1" value="30" required style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
            </div>

            <!-- Notas -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #111;">Notas Especiales</label>
                <textarea name="additional_notes" id="additionalNotes" placeholder="Ej: Sin picante, sin cebolla, etc." style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical; min-height: 80px;"></textarea>
            </div>

            <!-- Botones -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" id="cancelOrderBtn" style="padding: 10px 20px; border: 2px solid #e5e7eb; background: white; color: #666; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    Cancelar
                </button>
                <button type="submit" id="submitOrderBtn" style="padding: 10px 20px; background: linear-gradient(135deg, #e18018, #c9690f); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-check" style="margin-right: 6px;"></i>Crear Orden
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CSS para el modal -->
<style>
    #createOrderModal.show {
        display: flex !important;
    }

    .product-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    .product-card:hover {
        border-color: #e18018;
        box-shadow: 0 2px 8px rgba(225, 128, 24, 0.15);
    }

    .product-card.selected {
        border-color: #e18018;
        background: #fff7ed;
    }

    .product-card img {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .product-card-name {
        font-size: 12px;
        font-weight: 600;
        color: #111;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-card-price {
        font-size: 14px;
        font-weight: 700;
        color: #e18018;
    }

    .product-quantity-input {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 40px;
        height: 28px;
        padding: 0;
        text-align: center;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        font-weight: 600;
        color: #111;
        display: none;
    }

    .product-card.selected .product-quantity-input {
        display: block;
    }
</style>
