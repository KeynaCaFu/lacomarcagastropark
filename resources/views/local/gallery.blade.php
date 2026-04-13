@extends('layouts.app')

@section('title', 'Galería del Local - ' . ($local->name ?? 'La Comarca'))

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            @include('local.partials.breadcrumb')

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <div>
                    <h1 class="h3 mb-2" style="color: #111827; font-weight: 700;">
                        <i class="fas fa-images" style="color: #e18018; margin-right: 8px;"></i>Galería del Local
                    </h1>
                    <p class="text-muted mb-0">Administra las imágenes de {{ $local ? $local->name : 'tu local' }}</p>
                </div>
            </div>

            <!-- Formulario de Subida -->
            <div class="card border-0 mb-4" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px;">
                <h5 style="color: #111827; font-weight: 700; margin-bottom: 16px;">
                    <i class="fas fa-cloud-upload-alt" style="color: #c9690f; margin-right: 8px;"></i>Subir Nueva Imagen
                </h5>
                
                <form action="{{ route('local.gallery.upload') }}" method="POST" enctype="multipart/form-data" id="galleryForm">
                    @csrf
                    <div class="mb-3">
                        <label for="gallery_image" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 12px; display: block; font-size: 14px;">
                            Selecciona una Imagen
                        </label>
                        <input type="file" 
                               name="gallery_image" 
                               id="gallery_image"
                               class="form-control @error('gallery_image') is-invalid @enderror"
                               accept="image/*"
                               style="padding: 10px 12px; border: 2px dashed #d1d5db; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('gallery_image')
                            <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2" style="font-size: 13px;">
                            JPEG, PNG, JPG, GIF. Máximo: 2 MB
                        </small>
                    </div>

                    <button type="submit" 
                            class="btn btn-primary" 
                            style="padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; background: linear-gradient(135deg, #e18018, #c9690f); border: none; color: white; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; cursor: pointer;">
                        <i class="fas fa-upload"></i> Subir Imagen
                    </button>
                </form>
            </div>

            <!-- Galería de Imágenes -->
            <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px;">
                <h5 style="color: #111827; font-weight: 700; margin-bottom: 20px;">
                    <i class="fas fa-photo-video" style="color: #8b5cf6; margin-right: 8px;"></i>Imágenes ({{ count($images) }})
                </h5>

                @if(count($images) > 0)
                    <div class="row g-4">
                        @foreach($images as $image)
                            <div class="col-md-4 col-lg-3">
                                <div style="position: relative; border-radius: 8px; overflow: hidden; background: #f3f4f6; border: 1px solid #e5e7eb; group">
                                    <!-- Imagen -->
                                    <img src="{{ $image->image_url }}" 
                                         alt="Imagen galería" 
                                         style="width: 100%; height: 200px; object-fit: cover; display: block;">
                                    
                                    <!-- Botón eliminar siempre visible en la esquina -->
                                    <form action="{{ route('local.gallery.delete', $image->local_gallery_id) }}" method="POST" style="position: absolute; top: 8px; right: 8px; z-index: 100;">
                                        @csrf
                                        @method('DELETE')
                                        
                                        <button type="button" 
                                                class="btn btn-sm delete-image-btn" 
                                                style="padding: 8px 10px; border-radius: 6px; font-weight: 600; font-size: 13px; border: none; background: rgba(239, 68, 68, 0.9); color: white; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 4px; backdrop-filter: blur(4px);"
                                                onmouseover="this.style.background='rgba(220, 38, 38, 0.95)'"
                                                onmouseout="this.style.background='rgba(239, 68, 68, 0.9)'"
                                                onclick="confirmDeleteImage(this.closest('form'));">
                                            <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                                            <span>Eliminar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px; border: 2px dashed #e5e7eb;">
                        <i class="fas fa-image" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block;"></i>
                        <p style="color: #6b7280; font-size: 16px; margin-bottom: 0;">
                            No hay imágenes en la galería. Sube tu primera imagen para comenzar.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #c9690f !important;
        box-shadow: 0 0 0 2px rgba(225, 128, 24, 0.1) !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #d97c13, #b85f0d) !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(225, 128, 24, 0.2);
    }

    .btn-secondary:hover {
        background: #f3f4f6 !important;
        border-color: #9ca3af;
    }

    /* Breadcrumb Styles */
    .local-breadcrumb {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .local-breadcrumb ol {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0;
    }

    .local-breadcrumb li {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .local-breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.2s ease;
    }

    .local-breadcrumb a.breadcrumb-home {
        color: #e18018;
        font-weight: 600;
    }

    .local-breadcrumb a.breadcrumb-home:hover {
        color: #c9690f;
    }

    .local-breadcrumb a:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .local-breadcrumb .breadcrumb-separator {
        color: #d1d5db;
        margin: 0 4px;
        font-size: 12px;
    }

    .local-breadcrumb .current {
        color: #6b7280;
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar mensajes de éxito con SweetAlert Toast
    const successMsg = @json(session('success'));
    if (successMsg) {
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
        checkAndShowToast();
    }

    // Mostrar mensajes de error con SweetAlert
    const errorMsg = @json(session('error'));
    if (errorMsg && window.swAlert) {
        swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
    }
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

    // Validación del formulario de subida
    const uploadForm = document.getElementById('galleryForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('gallery_image');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                if (window.swAlert) {
                    swAlert({ icon: 'warning', title: 'Advertencia', text: 'Por favor selecciona una imagen' });
                } else {
                    alert('Por favor selecciona una imagen');
                }
                return false;
            }

            const file = fileInput.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file.size > maxSize) {
                e.preventDefault();
                if (window.swAlert) {
                    swAlert({ icon: 'warning', title: 'Archivo muy grande', text: 'La imagen no puede ser mayor a 2MB' });
                } else {
                    alert('La imagen no puede ser mayor a 2MB');
                }
                return false;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                if (window.swAlert) {
                    swAlert({ icon: 'warning', title: 'Formato no válido', text: 'Solo se aceptan imágenes JPG, PNG o GIF' });
                } else {
                    alert('Solo se aceptan imágenes JPG, PNG o GIF');
                }
                return false;
            }

            // Confirmación antes de subir
            if (window.swConfirm) {
                e.preventDefault();
                swConfirm({
                    title: 'Subir imagen',
                    text: '¿Desea subir esta imagen a la galería?',
                    icon: 'question',
                    confirmButtonText: 'Sí, subir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        uploadForm.submit();
                    }
                });
            }
        });
    }

    // Función para confirmar eliminación de imagen
    window.confirmDeleteImage = function(form) {
        if (window.swConfirm) {
            swConfirm({
                title: 'Eliminar imagen',
                text: '¿Está seguro de que desea eliminar esta imagen?',
                icon: 'warning',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('¿Está seguro de que desea eliminar esta imagen?')) {
                form.submit();
            }
        }
    };
});
</script>

@endsection
