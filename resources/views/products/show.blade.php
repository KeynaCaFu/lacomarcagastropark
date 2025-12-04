@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-box mr-2"></i> {{ $product->name }}
            </h1>
            <small class="text-muted">Detalles y gestión del producto</small>
        </div>
        <div class="col-md-4 text-right">
            <button type="button" class="btn btn-warning mr-2" data-toggle="modal" data-target="#editProductModal">
                <i class="fas fa-edit"></i> Editar
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Información principal -->
    <div class="row mb-4">
        <!-- Foto principal y datos básicos -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($product->photo)
                        <img src="{{ $product->photo }}" alt="{{ $product->name }}" 
                             class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                    @else
                        <div class="bg-light p-5 mb-3">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="text-muted mt-2">Sin foto</p>
                        </div>
                    @endif
                    <p class="small text-muted">Foto principal</p>
                </div>
            </div>

            <!-- Información rápida -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Información Rápida</h6>
                    <div class="mb-3">
                        <strong>Precio:</strong>
                        <h4 class="text-primary">₡{{ number_format($product->price, 2, ',', '.') }}</h4>
                    </div>
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        @if($product->status === 'Available')
                            <span class="badge badge-success">Disponible</span>
                        @else
                            <span class="badge badge-danger">No disponible</span>
                        @endif
                    </div>
                    @if($product->category)
                        <div class="mb-3">
                            <strong>Categoría:</strong>
                            <br><span class="badge badge-info">{{ $product->category }}</span>
                        </div>
                    @endif
                    @if($product->tag)
                        <div class="mb-3">
                            <strong>Etiqueta:</strong>
                            <br><span class="badge badge-secondary">{{ $product->tag }}</span>
                        </div>
                    @endif
                    @if($product->product_type)
                        <div class="mb-3">
                            <strong>Tipo:</strong>
                            <br><small>{{ $product->product_type }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Descripción y detalles -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Descripción</h5>
                </div>
                <div class="card-body">
                    @if($product->description)
                        <p>{{ $product->description }}</p>
                    @else
                        <p class="text-muted"><em>Sin descripción</em></p>
                    @endif
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Galería de Imágenes</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addGalleryModal">
                        <i class="fas fa-plus"></i> Agregar imagen
                    </button>
                </div>
                <div class="card-body">
                    @if($product->gallery->count() > 0)
                        <div class="row">
                            @foreach($product->gallery as $image)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card position-relative">
                                        <img src="{{ $image->image_url }}" alt="Galería" 
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <button type="button" class="btn btn-sm btn-danger btn-block" 
                                                    onclick="removeGalleryImage({{ $image->product_gallery_id }})">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-images fa-3x mb-2"></i>
                            <br>No hay imágenes en la galería
                        </p>
                    @endif
                </div>
            </div>

            <!-- Fecha de creación/actualización -->
            <div class="alert alert-info mt-4 mb-0">
                <small>
                    <strong>Creado:</strong> {{ $product->created_at->format('d/m/Y H:i') }}
                    <br>
                    <strong>Actualizado:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Producto -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editProductForm" method="POST" action="{{ route('products.update', $product->product_id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="edit_nombre" name="nombre" 
                               value="{{ $product->name }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_descripcion">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3">{{ $product->description }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_categoria">Categoría</label>
                            <input type="text" class="form-control" id="edit_categoria" name="categoria" 
                                   value="{{ $product->category }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_tipo_producto">Tipo de Producto</label>
                            <input type="text" class="form-control" id="edit_tipo_producto" name="tipo_producto" 
                                   value="{{ $product->product_type }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_precio">Precio *</label>
                            <input type="number" class="form-control" id="edit_precio" name="precio" 
                                   value="{{ $product->price }}" required step="0.01" min="0">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="edit_etiqueta">Etiqueta</label>
                            <input type="text" class="form-control" id="edit_etiqueta" name="etiqueta" 
                                   value="{{ $product->tag }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_foto">URL de Foto Principal</label>
                        <input type="text" class="form-control" id="edit_foto" name="foto" 
                               value="{{ $product->photo }}">
                    </div>

                    <div class="form-group">
                        <label for="edit_estado">Estado *</label>
                        <select class="form-control" id="edit_estado" name="estado" required>
                            <option value="Disponible" {{ $product->status === 'Available' ? 'selected' : '' }}>Disponible</option>
                            <option value="No disponible" {{ $product->status === 'Unavailable' ? 'selected' : '' }}>No disponible</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Agregar imagen a galería -->
<div class="modal fade" id="addGalleryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Agregar imagen a la galería</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addGalleryForm" method="POST" action="{{ route('products.gallery.add', $product->product_id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="image_url">URL de la imagen *</label>
                        <input type="text" class="form-control" id="image_url" name="image_url" 
                               required placeholder="https://ejemplo.com/imagen.jpg" maxlength="500">
                        <small class="form-text text-muted">Ingrese la URL completa de la imagen</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function removeGalleryImage(galleryId) {
    if (confirm('¿Está seguro de que desea eliminar esta imagen?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/productos/gallery/' + galleryId;
        form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                        '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Manejar submit del formulario agregar imagen
document.getElementById('addGalleryForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const response = await fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (response.ok) {
        const data = await response.json();
        alert(data.message);
        location.reload();
    } else {
        alert('Error al agregar la imagen');
    }
});
</script>
@endpush

@endsection
