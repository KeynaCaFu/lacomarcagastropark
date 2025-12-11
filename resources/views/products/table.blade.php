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
                        @if($product->status === 'Available')
                            <span class="status-badge status-available">Disponible</span>
                        @else
                            <span class="status-badge status-unavailable">No disponible</span>
                        @endif
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
</script>
