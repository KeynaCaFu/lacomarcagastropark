@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-plus-circle mr-2"></i> Crear Nuevo Producto
            </h1>
            <small class="text-muted">Agregue un nuevo producto al catálogo</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario de Creación -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-form"></i> Información del Producto
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" id="productForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label">
                                <strong>Nombre del Producto *</strong>
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
                        <div class="row">
                            <div class="col-md-6">
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

                            <div class="col-md-6">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="precio" class="form-label">
                                        <strong>Precio *</strong>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
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

                            <div class="col-md-6">
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
                            <label for="estado" class="form-label">
                                <strong>Estado *</strong>
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
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Producto
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información útil -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Los campos marcados con <strong>*</strong> son obligatorios</li>
                        <li>El nombre del producto debe ser único</li>
                        <li>Puede agregar imágenes a la galería después de crear el producto</li>
                        <li>El precio debe ser mayor o igual a 0</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sidebar de ayuda -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Consejos
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Nombre del Producto</h6>
                    <p class="small text-muted">Usa un nombre descriptivo y claro. Ej: "Café 1820 Molido"</p>

                    <hr>

                    <h6>Descripción</h6>
                    <p class="small text-muted">Proporciona detalles útiles sobre el producto para que los clientes lo conozcan mejor</p>

                    <hr>

                    <h6>Precio</h6>
                    <p class="small text-muted">Ingresa el precio en colones. Usa puntos para decimales. Ej: 1500.50</p>

                    <hr>

                    <h6>Categoría</h6>
                    <p class="small text-muted">Usa categorías consistentes. Ej: Bebidas, Alimentos, Postres</p>

                    <hr>

                    <h6>Foto Principal</h6>
                    <p class="small text-muted">Usa imágenes de buena calidad. La galería se puede actualizar después</p>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>✓ Nota:</strong> Después de crear el producto, podrás agregar más imágenes a su galería
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
    });
</script>
@endpush

@endsection
