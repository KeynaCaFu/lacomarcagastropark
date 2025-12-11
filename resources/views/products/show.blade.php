@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div style="flex:1;">
                <h2 style="margin: 7px; color: #1f2937; font-weight: 712;">
                    <i class="fas fa-box" style="margin-right:8px;"></i> {{ $product->name }}
                </h2>
                <div style="height:4px; width:120px; background:#f59e0b; border-radius:2px; margin-left:7px;"></div>
                <small class="text-muted">Detalles y gestión del producto</small>
            </div>
            <div id="topBackButtonContainer" class="top-help" style="gap:8px;"></div>
        </div>
    </div>

    <!-- Información principal -->
    <div class="row mb-4">
        <!-- Foto principal y datos básicos -->
        <div class="col-md-4">
            <div class="card mb-4" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body text-center" style="padding:20px;">
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
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-body" style="padding:20px;">
                    <h6 class="card-title" style="color:#1f2937; font-weight:700;">Información Rápida</h6>
                    <div class="mb-3">
                        <strong>Precio:</strong>
                        <h4 style="color:#0ea5e9;">₡{{ number_format($product->price, 2, ',', '.') }}</h4>
                    </div>
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        @if($product->status === 'Available')
                            <span class="status-badge status-available">Disponible</span>
                        @else
                            <span class="status-badge status-unavailable">No disponible</span>
                        @endif
                    </div>
                    @if($product->category)
                        <div class="mb-3">
                            <strong>Categoría:</strong>
                            <br><span class="category-badge">{{ $product->category }}</span>
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
            <div class="card mb-4" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-header" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                    <h5 class="mb-0" style="color:#1f2937; font-weight:700;">Descripción</h5>
                </div>
                <div class="card-body" style="padding:20px;">
                    @if($product->description)
                        <p>{{ $product->description }}</p>
                    @else
                        <p class="text-muted"><em>Sin descripción</em></p>
                    @endif
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                    <h5 class="mb-0" style="color:#1f2937; font-weight:700;">Galería de Imágenes</h5>
                    {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addGalleryModal">
                        <i class="fas fa-plus"></i> Agregar imagen
                    </button> --}}
                </div>
                <div class="card-body" style="padding:20px;">
                    @if($product->gallery->count() > 0)
                        <div class="row">
                            @foreach($product->gallery as $image)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card position-relative" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
                                        <img src="{{ $image->image_url }}" alt="Galería" 
                                             class="card-img-top" style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-2" style="background:#ffffff;">
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
            <div class="alert alert-info mt-4 mb-0" style="border-radius:8px;">
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
// SweetAlert2 CDN
(function(){
    const existing = document.querySelector('script[src*="cdn.jsdelivr.net/npm/sweetalert2"]');
    if (!existing) {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
        document.head.appendChild(s);
    }
})();
// Mover botón Volver al header
document.addEventListener('DOMContentLoaded', function() {
    const backButtonContainer = document.getElementById('topBackButtonContainer');
    const backButtonElement = document.getElementById('productShowBackButton');
    if (backButtonContainer && backButtonElement) {
        backButtonContainer.appendChild(backButtonElement);
    }
    // Session success/error alerts
    const successMsg = @json(session('success'));
    if (successMsg && window.Swal) {
        Swal.fire({ icon: 'success', title: 'Éxito', text: successMsg, confirmButtonColor: '#16a34a' });
    }
    const errorMsg = @json(session('error'));
    if (errorMsg && window.Swal) {
        Swal.fire({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
    }
    // Validation errors
    @if ($errors->any())
    if (window.Swal) {
        Swal.fire({
            icon: 'error',
            title: 'Errores de validación',
            html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            confirmButtonColor: '#dc2626'
        });
    }
    @endif
});

function removeGalleryImage(galleryId) {
    if (window.Swal) {
        Swal.fire({
            title: 'Eliminar imagen',
            text: '¿Desea eliminar esta imagen de la galería?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/productos/gallery/' + galleryId;
                form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                                '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    } else if (confirm('¿Está seguro de que desea eliminar esta imagen?')) {
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
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Imagen agregada',
                text: data.message || 'La imagen se agregó correctamente',
                confirmButtonColor: '#16a34a'
            }).then(() => location.reload());
        } else {
            alert(data.message || 'La imagen se agregó correctamente');
            location.reload();
        }
    } else {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al agregar la imagen'
            });
        } else {
            alert('Error al agregar la imagen');
        }
    }
});

// Mostrar alertas de éxito desde sesión (si existen)
document.addEventListener('DOMContentLoaded', function(){
    const successMsg = @json(session('success'));
    if (successMsg && window.Swal) {
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: successMsg,
            confirmButtonColor: '#16a34a'
        });
    }
});
</script>
@endpush

@endsection

<!-- Botón de Volver oculto para mover al header -->
<div id="productShowBackButtonContainer" style="display:none;">
    <a id="productShowBackButton" href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="display:inline-flex; align-items:center; gap:5px; border-color:#e5e7eb; color:#374151;">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
></div>
