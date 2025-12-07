@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-edit mr-2"></i> Editar Producto
            </h1>
            <small class="text-muted">Modifique la información del producto: {{ $product->name }}</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="fas fa-form"></i> Información del Producto
                    </h5>
                    <span class="ms-2" 
                          title="• Los campos marcados con * son obligatorios\n• El nombre debe mantenerse único\n• El precio debe ser mayor o igual a 0">
                        <i class="fas fa-info-circle" aria-label="Información"></i>
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->product_id) }}" method="POST" id="productForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Nombre del Producto *</strong></span>
                                <span class="ms-2 text-white-50" 
                                      title="El nombre debe ser único. Si cambia, no debe duplicar otro producto">
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

                        <!-- Descripción -->
                        <div class="form-group mb-3">
                            <label for="descripcion" class="form-label">
                                <strong>Descripción</strong>
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4"
                                      placeholder="Descripción detallada del producto">{{ old('descripcion', $product->description) }}</textarea>
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
                                           value="{{ old('categoria', $product->category) }}"
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
                                           value="{{ old('tipo_producto', $product->product_type) }}"
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
                                    <label for="precio" class="form-label d-flex align-items-center justify-content-between">
                                        <span><strong>Precio *</strong></span>
                                        <span class="ms-2 text-white-50" 
                                              title="Ingrese un valor ≥ 0. Use punto para decimales (ej: 1500.50)">
                                            <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                        </span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">₡</span>
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

                            <div class="col-md-6">
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

                            @if($product->photo)
                                <div class="mt-3">
                                    <small class="text-muted">Foto actual:</small>
                                    <br>
                                    <img src="{{ $product->photo }}" alt="{{ $product->name }}" 
                                         class="img-thumbnail mt-2" style="max-width: 200px; max-height: 150px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <!-- Estado -->
                        <div class="form-group mb-4">
                            <label for="estado" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Estado *</strong></span>
                                <span class="ms-2 text-white-50" 
                                      title="Seleccione disponibilidad actual: Disponible o No disponible">
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
                        <div class="alert alert-info alert-sm mb-3">
                            <small>
                                <strong>Creado:</strong> {{ $product->created_at->format('d/m/Y H:i') }}<br>
                                <strong>Última modificación:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
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
                    <h6>Cambios Rápidos</h6>
                    <p class="small text-muted">Puedes cambiar cualquier campo y guardar los cambios inmediatamente</p>

                    <hr>

                    <h6>Precio</h6>
                    <p class="small text-muted">Si cambias el precio, se actualizará para todos los clientes</p>

                    <hr>

                    <h6>Disponibilidad</h6>
                    <p class="small text-muted">Marca como "No disponible" si temporalmente no tienes stock</p>

                    <hr>

                    <h6>Galería</h6>
                    <p class="small text-muted">Ve a "Ver Detalles" para agregar más imágenes a la galería</p>

                    <hr>

                    <h6>Historial</h6>
                    <p class="small text-muted">Puedes ver cuándo fue creado y modificado el producto arriba</p>
                </div>
            </div>

            {{-- <!-- Acciones adicionales -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-trash"></i> Zona de Peligro
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Si deseas eliminar este producto y su galería, haz clic en el botón de abajo
                    </p>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash-alt"></i> Eliminar Producto
                    </button>
                </div>
            </div> --}}
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
