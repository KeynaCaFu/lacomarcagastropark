@if($products->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Galería</th>
                <th>Estado</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>
                        @if($product->photo)
                            <img src="{{ $product->photo }}" alt="{{ $product->name }}" 
                                 class="product-thumb" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <span class="badge badge-secondary">Sin foto</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        @if($product->category)
                            <span class="category-badge">{{ $product->category }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <strong>₡{{ number_format($product->price, 2, ',', '.') }}</strong>
                    </td>
                    <td>
                        <a href="{{ route('products.gallery', $product->product_id) }}" 
                           class="btn-action btn-view" title="Ver galería">
                            <i class="fas fa-images"></i> {{ $product->gallery_count ?? 0 }}
                        </a>
                    </td>
                    <td>
                        <span class="status-badge status-toggler {{ $product->status === 'Available' ? 'status-available' : 'status-unavailable' }}" 
                              data-product-id="{{ $product->product_id }}"
                              data-current-status="{{ $product->status }}"
                              style="cursor: pointer; transition: all 0.3s ease;"
                              title="Haz clic para cambiar el estado"
                              @if($product->status === 'Available')
                                  data-status-label="Disponible"
                              @else
                                  data-status-label="No disponible"
                              @endif>
                            @if($product->status === 'Available')
                                <span class="status-text" style="margin-right: 6px;">Disponible</span>
                                <i class="fas fa-check-circle" style="opacity: 0.8;"></i>
                            @else
                                <span class="status-text" style="margin-right: 6px;">No disponible</span>
                                <i class="fas fa-times-circle" style="opacity: 0.8;"></i>
                            @endif
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div class="actions" style="justify-content: center;">
                            <a href="{{ route('products.show', $product->product_id) }}" 
                               class="btn-action btn-view" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product->product_id) }}" 
                               class="btn-action btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="del-product-{{ $product->product_id }}" method="POST" action="{{ route('products.destroy', $product->product_id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-action btn-delete" data-id="{{ $product->product_id }}" data-name="{{ $product->name }}" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(method_exists($products, 'hasPages') ? $products->hasPages() : method_exists($products, 'links'))
        <div class="pagination-wrapper" style="margin-top: 16px; display: flex; justify-content: center;">
            <div class="pagination-container">
                {{ $products->onEachSide(1)->links() }}
            </div>
        </div>
        @if(method_exists($products, 'total'))
        <div style="text-align:center; color:#6b7280; font-size: 13px; margin-top:8px;">
            Mostrando <strong>{{ $products->firstItem() }}</strong> a <strong>{{ $products->lastItem() }}</strong> de <strong>{{ $products->total() }}</strong> productos
        </div>
        @endif
    @endif
@else
    <div class="empty-state">
        <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
        <p>No hay productos que mostrar</p>
    </div>
@endif

<script>
// Bind delete with Undo for Products table
(function(){
    document.querySelectorAll('.btn-delete[data-id]').forEach(btn => {
        if (btn.dataset._undoBound === 'true') return;
        btn.dataset._undoBound = 'true';
        btn.addEventListener('click', (e) => {
            const id = btn.dataset.id;
            const name = btn.dataset.name || 'este producto';
            const formId = 'del-product-' + id;
            const submitAction = () => { const f = document.getElementById(formId); if (f) f.submit(); };
            if (typeof window.confirmWithUndo === 'function') {
                const ask = window.swConfirm ? swConfirm({ html: `<div class='swal-title-like'>¿Seguro que deseas eliminar <b>${name}</b>?</div>`, confirmButtonText: 'Sí, eliminar' }) : Promise.resolve({ isConfirmed: confirm('¿Seguro que deseas eliminar este producto?') });
                ask.then(r => { if (r.isConfirmed) window.confirmWithUndo({ message: `Se eliminará: ${name}`, delayMs: 10000, onConfirm: submitAction, onUndo: function(){} }); });
            } else if (window.swConfirm) {
                swConfirm({ html: `<div class='swal-title-like'>¿Seguro que deseas eliminar <b>${name}</b>?</div>`, confirmButtonText: 'Sí, eliminar' }).then(r => { if (r.isConfirmed) submitAction(); });
            } else if (confirm('¿Seguro que deseas eliminar este producto?')) {
                submitAction();
            }
        });
    });
})();

// Toggle product status
(function(){
    document.querySelectorAll('.status-toggler').forEach(badge => {
        if (badge.dataset._statusBound === 'true') return;
        badge.dataset._statusBound = 'true';
        
        badge.addEventListener('click', async (e) => {
            const productId = badge.dataset.productId;
            const currentStatus = badge.dataset.currentStatus;
            const newStatus = currentStatus === 'Available' ? 'Unavailable' : 'Available';
            const newStatusLabel = newStatus === 'Available' ? 'Disponible' : 'No disponible';
            const currentStatusLabel = badge.dataset.statusLabel;
            
            // Show confirmation
            if (window.swConfirm) {
                const result = await swConfirm({
                    title: 'Cambiar estado',
                    html: `¿Cambiar de <b>${currentStatusLabel}</b> a <b>${newStatusLabel}</b>?`,
                    icon: 'question',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                });
                
                if (!result.isConfirmed) return;
            } else {
                const ok = confirm(`¿Cambiar de ${currentStatusLabel} a ${newStatusLabel}?`);
                if (!ok) return;
            }
            
            // Disable badge while updating
            badge.style.opacity = '0.5';
            badge.style.pointerEvents = 'none';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/productos/${productId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Error al actualizar el estado');
                }
                
                // Update badge visually
                badge.dataset.currentStatus = newStatus;
                badge.dataset.statusLabel = newStatusLabel;
                
                if (newStatus === 'Available') {
                    badge.classList.remove('status-unavailable');
                    badge.classList.add('status-available');
                    badge.innerHTML = `<span class="status-text" style="margin-right: 6px;">Disponible</span><i class="fas fa-check-circle" style="opacity: 0.8;"></i>`;
                } else {
                    badge.classList.remove('status-available');
                    badge.classList.add('status-unavailable');
                    badge.innerHTML = `<span class="status-text" style="margin-right: 6px;">No disponible</span><i class="fas fa-times-circle" style="opacity: 0.8;"></i>`;
                }
                
                // Restore opacity
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                
                // Show success toast
                let retries = 0;
                const checkAndShowSuccess = () => {
                    if (window.swToast) {
                        swToast.fire({
                            icon: 'success',
                            title: `Estado actualizado a ${newStatusLabel}`
                        });
                    } else if (retries < 50) {
                        retries++;
                        setTimeout(checkAndShowSuccess, 100);
                    }
                };
                setTimeout(checkAndShowSuccess, 100);
                
            } catch (error) {
                console.error('Error:', error);
                badge.style.opacity = '1';
                badge.style.pointerEvents = 'auto';
                
                if (window.swAlert) {
                    swAlert({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'No se pudo actualizar el estado',
                        confirmButtonColor: '#dc2626'
                    });
                } else {
                    alert(error.message || 'No se pudo actualizar el estado');
                }
            }
        });
    });
})();
</script>
