@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div style="padding: 0 15px;">
    <!-- Header -->
    <div style="margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 8px;">
            <div style="flex:1; min-width:200px;">
                <h2 style="margin: 7px; color: #1f2937; font-weight: 712;">
                    <i class="fas fa-plus-circle" style="margin-right:8px;"></i> Registrar Nuevo Producto
                </h2>
                <div style="height:4px; width:120px; background:#f59e0b; border-radius:2px; margin-left:7px;"></div>
                <small class="text-muted">Agregue un nuevo producto al catálogo</small>
            </div>
            <div id="topBackButtonContainer" class="top-help" style="gap:8px;"></div>
        </div>
    </div>

    <!-- Formulario de Creación - Centrado -->
    <div style="display: flex; justify-content: center;">
        <div style="width: 100%; max-width: 800px;">
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-header d-flex align-items-center justify-content-between" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                    <h5 class="mb-0" style="color:#1f2937; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-file-alt"></i> Información del Producto
                    </h5>
                </div>
                <div class="card-body" style="padding:20px;">
                    <form action="{{ route('products.store') }}" method="POST" id="productForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Nombre del Producto *</strong></span>
                                <!-- Icono con tooltip junto al campo -->
                                <span class="ms-2 text-white-50" 
                                      title="El nombre debe ser único. Ejemplo: 'Café 1820 Molido'">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Nombre del producto">
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
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
                                      placeholder="Descripción detallada del producto">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fila: Categoría y Tipo de Producto -->
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
                                           value="{{ old('categoria') }}"
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
                                           value="{{ old('tipo_producto') }}"
                                           maxlength="50"
                                           placeholder="ej: Bebida, Entrada">
                                    @error('tipo_producto')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Fila: Precio y Etiqueta -->
                        <div class="form-grid-2col">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="precio" class="form-label d-flex align-items-center justify-content-between">
                                        <span><strong>Precio *</strong></span>
                                        <span class="ms-2 text-white-50" 
                                              title="Ingrese un valor mayor o igual a 0. Use decimales con punto (ej: 1500.50)">
                                            <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">₡</span>
                                        <input type="number" 
                                               class="form-control @error('precio') is-invalid @enderror" 
                                               id="precio" 
                                               name="precio" 
                                               value="{{ old('precio') }}"
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
                                           value="{{ old('etiqueta') }}"
                                           maxlength="100"
                                           placeholder="ej: Especial, Descuento">
                                    @error('etiqueta')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

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
                            <small class="form-text text-muted d-block mt-2">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                            @error('foto')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="form-group mb-4">
                            <label for="estado" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Estado *</strong></span>
                                <span class="ms-2 text-white-50" 
                                      title="Seleccione si el producto estará disponible para la venta o no">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <select class="form-control @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado" 
                                    required>
                                <option value="">-- Seleccione un estado --</option>
                                <option value="Disponible" {{ old('estado') == 'Disponible' ? 'selected' : '' }}>
                                    ✓ Disponible
                                </option>
                                <option value="No disponible" {{ old('estado') == 'No disponible' ? 'selected' : '' }}>
                                    ✗ No disponible
                                </option>
                            </select>
                            @error('estado')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-group" style="display:flex; gap:10px; flex-wrap:wrap;">
                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #e18018, #915016); border:none; font-weight:600;">
                                <i class="fas fa-save"></i> Crear Producto
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="border-color:#e5e7eb; color:#374151;">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mover botón de volver al header
    document.addEventListener('DOMContentLoaded', function() {
        const backButtonContainer = document.getElementById('topBackButtonContainer');
        const backButtonElement = document.getElementById('createBackButton');
        if (backButtonContainer && backButtonElement) {
            backButtonContainer.appendChild(backButtonElement);
        }
    });
    // SweetAlert2 CDN
    (function(){
        const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
        if (!existing) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
            document.head.appendChild(s);
        }
    })();
    // Mostrar nombre del archivo seleccionado
    document.getElementById('foto').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
        this.nextElementSibling.textContent = fileName;
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
                title: 'Crear producto',
                text: '¿Desea guardar este nuevo producto?',
                icon: 'question',
                confirmButtonText: 'Sí, guardar'
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

        // Mostrar errores de validación (si existen) en un solo modal
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
    function openCreateProductHelpModal() {
        const modal = document.getElementById('createProductHelpModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.pointerEvents = 'auto';
            document.body.style.overflow = 'hidden';
        }
    }

    // Cerrar modal de ayuda
    function closeCreateProductHelpModal() {
        const modal = document.getElementById('createProductHelpModal');
        if (modal) {
            modal.style.display = 'none';
            modal.style.pointerEvents = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Cerrar modal al hacer clic fuera
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('createProductHelpModal');
        if (e.target === modal) {
            closeCreateProductHelpModal();
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('createProductHelpModal');
            if (modal && modal.style.display === 'flex') {
                closeCreateProductHelpModal();
            }
        }
    });

    // Mover botón de ayuda al header y botón volver
    document.addEventListener('DOMContentLoaded', function() {
        const helpContainer = document.getElementById('topHelpContainer');
        const helpButtonContainer = document.getElementById('helpButtonContainerProducts');
        const helpButton = document.getElementById('helpButtonProducts');
        
        if (helpContainer && helpButtonContainer && helpButton) {
            helpContainer.appendChild(helpButton);
            helpButtonContainer.style.display = 'none';
        }

        const backButtonContainer = document.getElementById('topBackButtonContainer');
        const backButtonElement = document.getElementById('createBackButton');
        if (backButtonContainer && backButtonElement) {
            backButtonContainer.appendChild(backButtonElement);
        }
    });
</script>
@endpush

<!-- Botón de Volver para Crear Producto -->
<div id="createBackButtonContainer" style="display: none;">
    <a id="createBackButton" href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="display: inline-flex; align-items: center; gap: 5px; border-color:#e5e7eb; color:#374151;">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<!-- Botón de Ayuda para Productos -->
<div id="helpButtonContainerProducts" style="display: none;">
    <button id="helpButtonProducts" type="button" class="btn btn-help" onclick="openCreateProductHelpModal()">
        <i class="fas fa-question-circle"></i> Ayuda
    </button>
</div>

<!-- Modal de Ayuda para Crear Producto -->
<div id="createProductHelpModal" class="custom-modal" style="display:none;">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpTitle">
        <div class="modal-header">
            <h3 id="helpTitle"><i class="fas fa-lightbulb"></i> Consejos para Crear un Producto</h3>
            <button type="button" class="close" aria-label="Cerrar" onclick="closeCreateProductHelpModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-section">
                <h5><i class="fas fa-heading"></i> Nombre del Producto</h5>
                <p>
                    Usa un nombre descriptivo y claro. Ejemplo: "Café 1820 Molido"<br>
                    <small class="text-muted">El nombre debe ser único y no puede repetirse con otros productos.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-align-left"></i> Descripción</h5>
                <p>
                    Proporciona detalles útiles sobre el producto para que los clientes lo conozcan mejor.<br>
                    <small class="text-muted">Incluye ingredientes, origen, características especiales, etc.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-tag"></i> Categoría</h5>
                <p>
                    Usa categorías consistentes. Ejemplos: Bebidas, Alimentos, Postres<br>
                    <small class="text-muted">Esto ayuda a organizar mejor los productos en el catálogo.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-leaf"></i> Tipo de Producto</h5>
                <p>
                    Especifica el tipo. Ejemplos: Bebida, Entrada, Plato Principal<br>
                    <small class="text-muted">Útil para filtrar productos por tipo.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-money-bill"></i> Precio</h5>
                <p>
                    Ingresa el precio en colones. Usa puntos para decimales. Ejemplo: 1500.50<br>
                    <small class="text-muted">El precio debe ser mayor o igual a 0.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-image"></i> Foto Principal</h5>
                <p>
                    Usa imágenes de buena calidad (JPG, PNG, GIF). Tamaño máximo: 2MB<br>
                    <small class="text-muted">Recomendado: 800x600px o mayor. La galería se puede actualizar después de crear el producto.</small>
                </p>
            </div>

            <div class="detail-section">
                <h5><i class="fas fa-check"></i> Estado</h5>
                <p>
                    Selecciona si el producto estará disponible para la venta o no.<br>
                    <small class="text-muted">Puedes cambiar el estado en cualquier momento desde el detalle del producto.</small>
                </p>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeCreateProductHelpModal()">Cerrar</button>
        </div>
    </div>
</div>

</div>
@endsection
