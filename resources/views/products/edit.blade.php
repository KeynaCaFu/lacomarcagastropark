@extends('layouts.app')

@section('title', 'Editar Producto')

@push('styles')
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="product-page-wrapper">
    @include('products.partials.breadcrumb', ['crumbs' => [
        ['label' => $product->name, 'url' => route('products.show', $product->product_id)],
        ['label' => 'Editar']
    ]])

    <!-- Header -->
    <div class="product-page-header">
        <div class="product-page-header-flex">
            <div class="product-page-header-title">
                <h2>
                    <i class="fas fa-edit"></i> Actualizar Producto
                </h2>
                <div class="accent-bar"></div>
                <small class="text-muted">Modifique la información del producto: <strong>{{ $product->name }}</strong></small>
            </div>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="product-form-container">
        <div class="card product-card">
            <div class="card-header product-card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Información del Producto
                </h5>
            </div>
            <div class="card-body" style="padding:24px;">
                <form action="{{ route('products.update', $product->product_id) }}" method="POST" id="productForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="create-product-grid">
                        {{-- ======= COLUMNA IZQUIERDA: Datos principales ======= --}}
                        <div class="create-product-left">
                            <!-- Nombre -->
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                    <span><strong>Nombre del Producto *</strong></span>
                                    <span class="ms-2 text-white-50" title="El nombre debe ser único. Si cambia, no debe duplicar otro producto">
                                        <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                    </span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $product->name) }}"
                                       required
                                       maxlength="255"
                                       placeholder="Nombre del producto">
                                @error('nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Categoría y Tipo de Producto -->
                            <div class="form-grid-2col">
                                <div>
                                    <div class="form-group mb-3">
                                        <label for="categoria" class="form-label">
                                            <strong>Categoría</strong>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('categoria') is-invalid @enderror" 
                                               id="categoria" 
                                               name="categoria" 
                                               value="{{ old('categoria', $product->category) }}"
                                               maxlength="100"
                                               placeholder="ej: Bebidas, Comidas">
                                        @error('categoria')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group mb-3">
                                        <label for="tipo_producto" class="form-label">
                                            <strong>Tipo de Producto</strong>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('tipo_producto') is-invalid @enderror" 
                                               id="tipo_producto" 
                                               name="tipo_producto" 
                                               value="{{ old('tipo_producto', $product->product_type) }}"
                                               maxlength="50"
                                               placeholder="ej: Bebida, Entrada">
                                        @error('tipo_producto')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Precio y Etiqueta -->
                            <div class="form-grid-2col">
                                <div>
                                    <div class="form-group mb-3">
                                        <label for="precio" class="form-label d-flex align-items-center justify-content-between">
                                            <span><strong>Precio *</strong></span>
                                            <span class="ms-2 text-white-50" title="Ingrese un valor ≥ 0. Use punto para decimales (ej: 1500.50)">
                                                <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                            </span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">₡</span>
                                            <input type="number" 
                                                   class="form-control @error('precio') is-invalid @enderror" 
                                                   id="precio" 
                                                   name="precio" 
                                                   value="{{ old('precio', $product->price) }}"
                                                   required
                                                   step="0.01" 
                                                   min="0"
                                                   placeholder="0.00">
                                        </div>
                                        @error('precio')
                                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group mb-3">
                                        <label for="etiqueta" class="form-label">
                                            <strong>Etiqueta</strong>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('etiqueta') is-invalid @enderror" 
                                               id="etiqueta" 
                                               name="etiqueta" 
                                               value="{{ old('etiqueta', $product->tag) }}"
                                               maxlength="100"
                                               placeholder="ej: Especial, Descuento">
                                        @error('etiqueta')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group mb-3">
                                <label for="descripcion" class="form-label">
                                    <strong>Descripción</strong>
                                </label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="4"
                                          style="resize: vertical;"
                                          placeholder="Descripción detallada del producto">{{ old('descripcion', $product->description) }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ======= COLUMNA DERECHA: Imagen y Estado ======= --}}
                        <div class="create-product-right">
                            <!-- Foto Principal -->
                            <div class="form-group mb-3">
                                <label for="foto" class="form-label">
                                    <strong>Foto Principal</strong>
                                </label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('foto') is-invalid @enderror" 
                                           id="foto" 
                                           name="foto"
                                           accept="image/*">
                                    <label class="custom-file-label" for="foto">Seleccionar archivo...</label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">Formatos: JPG, PNG, GIF. Máx: 2MB</small>
                                @error('foto')
                                    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                @enderror

                                <!-- Preview de imagen (muestra foto actual o nueva selección) -->
                                <div id="imagePreviewContainer" style="margin-top: 12px; border: 2px {{ $product->photo_url ? 'solid' : 'dashed' }} #e5e7eb; border-radius: 10px; overflow: hidden; background: {{ $product->photo_url ? '#fff' : '#fafafa' }}; display: flex; align-items: center; justify-content: center; min-height: 180px; transition: all 0.3s ease;">
                                    @if($product->photo_url)
                                        <img id="imagePreview" src="{{ $product->photo_url }}" alt="{{ $product->name }}" style="width: 100%; max-height: 220px; object-fit: cover;">
                                        <div id="imagePreviewPlaceholder" style="display: none; text-align: center; padding: 20px; color: #9ca3af;">
                                            <i class="fas fa-image" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                                            <span style="font-size: 13px;">La vista previa aparecerá aquí</span>
                                        </div>
                                    @else
                                        <div id="imagePreviewPlaceholder" style="text-align: center; padding: 20px; color: #9ca3af;">
                                            <i class="fas fa-image" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                                            <span style="font-size: 13px;">Sin foto actual</span>
                                        </div>
                                        <img id="imagePreview" src="#" alt="Vista previa" style="display: none; width: 100%; max-height: 220px; object-fit: cover;">
                                    @endif
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group mb-3">
                                <label for="estado" class="form-label d-flex align-items-center justify-content-between">
                                    <span><strong>Estado *</strong></span>
                                    <span class="ms-2 text-white-50" title="Seleccione disponibilidad actual: Disponible o No disponible">
                                        <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                    </span>
                                </label>
                                <select class="form-control @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="">-- Seleccione un estado --</option>
                                    <option value="Disponible" {{ old('estado', $product->status === 'Available' ? 'Disponible' : $product->status) == 'Disponible' ? 'selected' : '' }}>
                                        ✓ Disponible
                                    </option>
                                    <option value="No disponible" {{ old('estado', $product->status === 'Unavailable' ? 'No disponible' : $product->status) == 'No disponible' ? 'selected' : '' }}>
                                        ✗ No disponible
                                    </option>
                                </select>
                                @error('estado')
                                    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Información de auditoría -->
                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #1e40af;">
                                <i class="fas fa-clock" style="margin-right: 6px;"></i>
                                <strong>Creado:</strong> {{ $product->created_at->format('d/m/Y H:i') }}<br>
                                <i class="fas fa-sync-alt" style="margin-right: 6px; margin-top: 4px;"></i>
                                <strong>Última modificación:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                            </div>

                            <!-- Tip -->
                            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #92400e; margin-top: 12px;">
                                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                                <strong>Tip:</strong> Para gestionar las imágenes adicionales, visite la <a href="{{ route('products.gallery', $product->product_id) }}" style="color: #c9690f; font-weight: 600;">galería del producto</a>.
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; margin-top: 20px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="border-color:#e5e7eb; color:#374151;">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #e18018, #915016); border:none; font-weight:600;">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal: Confirmar Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Eliminar Producto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar el producto <strong>{{ $product->name }}</strong>?</p>
                <p class="text-danger">
                    <strong>⚠️ Advertencia:</strong> Esta acción no se puede deshacer y eliminará:
                    <ul>
                        <li>El producto</li>
                        <li>Su galería de imágenes</li>
                        <li>Todo su historial</li>
                    </ul>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('products.destroy', $product->product_id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // SweetAlert2 CDN
    (function(){
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    })();
    // Mostrar nombre del archivo seleccionado y vista previa
    document.getElementById('foto').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
        this.nextElementSibling.textContent = fileName;

        // Vista previa de imagen
        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePreviewPlaceholder');
        const container = document.getElementById('imagePreviewContainer');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
                container.style.borderColor = '#c9690f';
                container.style.borderStyle = 'solid';
                container.style.background = '#fff';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            container.style.borderColor = '#e5e7eb';
            container.style.borderStyle = 'dashed';
            container.style.background = '#fafafa';
        }
    });

    // Validación del formulario en el cliente
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const precio = parseFloat(document.getElementById('precio').value);
        const estado = document.getElementById('estado').value;

        if (!nombre) {
            e.preventDefault();
            alert('El nombre del producto es obligatorio');
            document.getElementById('nombre').focus();
            return false;
        }

        if (!precio || precio < 0) {
            e.preventDefault();
            alert('El precio debe ser un número mayor o igual a 0');
            document.getElementById('precio').focus();
            return false;
        }

        if (!estado) {
            e.preventDefault();
            alert('Debe seleccionar un estado');
            document.getElementById('estado').focus();
            return false;
        }

        // Confirmación con SweetAlert antes de enviar
        if (window.swConfirm) {
            e.preventDefault();
            swConfirm({
                title: 'Actualizar producto',
                text: '¿Desea guardar los cambios de este producto?',
                icon: 'question',
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        }
    });

    // Mostrar alertas de éxito desde sesión (si existen)
    document.addEventListener('DOMContentLoaded', function(){
        const successMsg = @json(session('success'));
        if (successMsg) {
            // Esperar a que swToast esté disponible
            let retries = 0;
            const checkAndShowToast = () => {
                if (window.swToast) {
                    swToast.fire({ 
                        icon: 'success', 
                        title: successMsg
                    });
                } else if (retries < 50) {
                    retries++;
                    setTimeout(checkAndShowToast, 100);
                }
            };
            setTimeout(checkAndShowToast, 100);
        }

        const errorMsg = @json(session('error'));
        if (errorMsg && window.swAlert) {
            swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
        }

        // Mostrar errores de validación (si existen)
        @if ($errors->any())
        if (window.swAlert) {
            swAlert({
                icon: 'error',
                title: 'Errores de validación',
                html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                confirmButtonColor: '#dc2626'
            });
        }
        @endif
    });

    // Abrir modal de ayuda
    function openEditProductHelpModal() {
        const modal = document.getElementById('editProductHelpModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    // Cerrar modal de ayuda
    function closeEditProductHelpModal() {
        const modal = document.getElementById('editProductHelpModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('editProductHelpModal');
        if (e.target === modal) {
            closeEditProductHelpModal();
        }
    });

    // Mover botón de ayuda al header
    document.addEventListener('DOMContentLoaded', function() {
        const helpContainer = document.getElementById('topHelpContainer');
        const helpButtonContainer = document.getElementById('helpButtonContainerEdit');
        const helpButton = document.getElementById('helpButtonEdit');
        
        if (helpContainer && helpButtonContainer && helpButton) {
            helpContainer.appendChild(helpButton);
            helpButtonContainer.style.display = 'none';
        }

    });
</script>
@endpush


<!-- Botón de Ayuda para Editar Producto -->
<div id="helpButtonContainerEdit" style="display: none;">
    <button id="helpButtonEdit" type="button" class="btn-help" onclick="openEditProductHelpModal()">
        <i class="fas fa-question-circle"></i> Ayuda
    </button>
</div>



<!-- Modal de Ayuda para Editar Producto -->
<div id="editProductHelpModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpTitle">
        <div class="modal-header">
            <h3 id="helpTitle"><i class="fas fa-lightbulb"></i> Consejos para Editar un Producto</h3>
            <button type="button" class="close" aria-label="Cerrar" onclick="closeEditProductHelpModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <h5><i class="fas fa-heading"></i> Cambios Rápidos</h5>
                <p>
                    Puedes cambiar cualquier campo y guardar los cambios inmediatamente.<br>
                    <small class="text-muted">Los cambios se aplicarán en la tienda de inmediato.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-money-bill"></i> Precio</h5>
                <p>
                    Si cambias el precio, se actualizará para todos los clientes.<br>
                    <small class="text-muted">Ingresa el precio en colones con decimales (ej: 1500.50).</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-check"></i> Disponibilidad</h5>
                <p>
                    Marca como "No disponible" si temporalmente no tienes stock.<br>
                    <small class="text-muted">Puedes cambiar esto cuando vuelva a estar disponible.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-images"></i> Galería</h5>
                <p>
                    Ve a "Ver Detalles" para agregar más imágenes a la galería del producto.<br>
                    <small class="text-muted">Desde aquí también puedes ver todas las imágenes subidas.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-history"></i> Historial</h5>
                <p>
                    Puedes ver cuándo fue creado y modificado el producto al final del formulario.<br>
                    <small class="text-muted">Esto te ayuda a llevar un control de los cambios.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-image"></i> Cambiar Foto Principal</h5>
                <p>
                    Selecciona una nueva imagen para reemplazar la foto principal del producto.<br>
                    <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB. Verás una vista previa de la imagen actual.</small>
                </p>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeEditProductHelpModal()">Cerrar</button>
        </div>
    </div>
</div>

</div>
@endsection
