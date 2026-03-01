@extends('layouts.app')

@section('title', 'Galería - ' . $product->name)

@push('styles')
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div style="padding: 0 15px;">
        <!-- Header -->
        <div style="margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 8px;">
                <div style="flex:1; min-width:200px;">
                    <h2 style="margin: 7px; color: #1f2937; font-weight: 712;">
                        <i class="fas fa-images" style="margin-right:8px;"></i> Galería de Imágenes
                    </h2>
                    <div style="height:4px; width:120px; background:#f59e0b; border-radius:2px; margin-left:7px;"></div>
                    <small class="text-muted">Gestiona las imágenes del producto: <strong>{{ $product->name }}</strong></small>
                </div>
                <div id="topBackButtonContainer" class="top-help" style="gap:8px;"></div>
            </div>
        </div>

        <!-- Alertas -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Errores encontrados</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <div class="gallery-layout-grid">
            <!-- Panel de carga de imágenes -->
            <div>
                <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-header" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                        <h5 class="mb-0" style="color:#1f2937; font-weight:700; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-cloud-upload-alt"></i> Agregar Imagen
                        </h5>
                    </div>
                    <div class="card-body" style="padding:20px;">
                        <form action="{{ route('products.gallery.add', $product->product_id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="image" class="form-label"><strong>Seleccionar Imagen</strong></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                                    <label class="custom-file-label" for="image">Seleccionar archivo...</label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">Formatos: JPG, PNG, GIF • Máximo: 2MB</small>
                                @error('image')
                                    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" style="background: linear-gradient(135deg, #e18018, #915016); font-weight:600;">
                                <i class="fas fa-upload"></i> Subir Imagen
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card mt-3" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-header" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                        <h6 class="mb-0" style="color:#1f2937; font-weight:700; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-chart-bar"></i> Estadísticas
                        </h6>
                    </div>
                    <div class="card-body" style="padding:20px;">
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted mb-1">Total de imágenes</p>
                                <h4 style="color:#0ea5e9;">{{ count($gallery) }}</h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">Producto</p>
                                <small class="d-block text-truncate" title="{{ $product->name }}">{{ $product->name }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div>
                <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <div class="card-header" style="background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top-left-radius:12px; border-top-right-radius:12px;">
                        <h5 class="mb-0" style="color:#1f2937; font-weight:700; display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-images"></i> Imágenes del Producto
                        </h5>
                    </div>
                    <div class="card-body" style="padding:20px;">
                        @if(count($gallery) > 0)
                            <div class="row">
                                @foreach($gallery as $image)
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm gallery-item" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow:hidden;">
                                            <div class="position-relative gallery-image-container" style="background:#ffffff;">
                                                <img src="{{ $image->image_url }}" alt="Imagen de galería" class="card-img-top" style="height: 200px; object-fit: cover;">
                                                <span class="badge badge-primary position-absolute" style="top: 10px; right: 10px;">ID: {{ $image->gallery_id }}</span>
                                            </div>
                                            <div class="card-body" style="background:#ffffff;">
                                                <p class="card-text text-muted small mb-2"><i class="fas fa-image"></i> Imagen de galería</p>
                                            </div>
                                            <div class="card-footer" style="background:#f8fafc;">
                                                <button type="button" class="btn btn-sm btn-danger btn-block" onclick="confirmDelete({{ $image->gallery_id }})">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-image fa-5x text-muted mb-3" style="opacity: 0.5;"></i>
                                <p class="text-muted mb-0">No hay imágenes en la galería</p>
                                <small class="text-muted">Agrega imágenes usando el formulario de la izquierda</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Eliminar Imagen</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar esta imagen?</p>
                    <p class="text-danger"><strong>⚠️ Esta acción no se puede deshacer</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form method="POST" id="deleteForm" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Sí, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // SweetAlert2 se carga globalmente desde el layout
        // Mostrar nombre del archivo seleccionado
        document.getElementById('image').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Seleccionar archivo...';
            this.nextElementSibling.textContent = fileName;
        });

        // Confirmar eliminación
        function confirmDelete(galleryId) {
            if (window.swConfirm) {
                swConfirm({
                    title: 'Eliminar imagen',
                    text: '¿Desea eliminar esta imagen de la galería?',
                    icon: 'warning',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('deleteForm');
                        form.action = `/productos/gallery/${galleryId}`;
                        form.submit();
                    }
                });
            } else {
                const form = document.getElementById('deleteForm');
                form.action = `/productos/gallery/${galleryId}`;
                $('#deleteModal').modal('show');
            }
        }

        // Validación del formulario
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('image');
            if (!fileInput.files || !fileInput.files[0]) {
                e.preventDefault();
                alert('Por favor selecciona una imagen');
                return false;
            }
            const file = fileInput.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSize) {
                e.preventDefault();
                alert('La imagen no puede ser mayor a 2MB');
                return false;
            }
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                alert('Solo se aceptan imágenes JPG, PNG o GIF');
                return false;
            }

            // Confirmación antes de subir
            if (window.swConfirm) {
                e.preventDefault();
                swConfirm({
                    title: 'Subir imagen',
                    text: '¿Desea subir esta imagen?',
                    icon: 'question',
                    confirmButtonText: 'Sí, subir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            }
        });

        // Abrir modal de ayuda
        function openGalleryHelpModal() {
            const modal = document.getElementById('galleryHelpModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        // Cerrar modal de ayuda
        function closeGalleryHelpModal() {
            const modal = document.getElementById('galleryHelpModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('galleryHelpModal');
            if (e.target === modal) {
                closeGalleryHelpModal();
            }
        });

        // Mover botones al header
        document.addEventListener('DOMContentLoaded', function() {
            const helpContainer = document.getElementById('topHelpContainer');
            const helpButtonContainer = document.getElementById('helpButtonContainerGallery');
            const helpButton = document.getElementById('helpButtonGallery');
            if (helpContainer && helpButtonContainer && helpButton) {
                helpContainer.appendChild(helpButton);
                helpButtonContainer.style.display = 'none';
            }
            const backButtonContainer = document.getElementById('topBackButtonContainer');
            const backButtonElement = document.getElementById('galleryBackButton');
            if (backButtonContainer && backButtonElement) {
                backButtonContainer.appendChild(backButtonElement);
            }
            // Session alerts (success/error) and validation errors
            const successMsg = @json(session('success'));
            if (successMsg && window.swAlert) {
                swAlert({ icon: 'success', title: 'Éxito', text: successMsg });
            }
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
        });
    </script>
    @endpush

    <!-- Botón de Volver para Galería -->
    <div id="galleryBackButtonContainer" style="display: none;">
        <a id="galleryBackButton" href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="display: inline-flex; align-items: center; gap: 5px; border-color:#e5e7eb; color:#374151;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        </div>

    <!-- Botón de Ayuda para Galería -->
    <div id="helpButtonContainerGallery" style="display: none;">
        <button id="helpButtonGallery" type="button" class="btn-help" onclick="openGalleryHelpModal()">
            <i class="fas fa-question-circle"></i> Ayuda
        </button>
        </div>

    <!-- Modal de Ayuda para Galería -->
    <div id="galleryHelpModal" class="custom-modal" style="display:none;">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="helpTitle">
            <div class="modal-header">
                <h3 id="helpTitle"><i class="fas fa-lightbulb"></i> Consejos para Agregar Imágenes</h3>
                <button type="button" class="close" aria-label="Cerrar" onclick="closeGalleryHelpModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-section">
                    <h5><i class="fas fa-image"></i> Usa Imágenes de Buena Calidad</h5>
                    <p>Selecciona imágenes claras y bien iluminadas que muestren el producto de manera atractiva.<br><small class="text-muted">Esto ayuda a los clientes a ver mejor el producto.</small></p>
                </div>
                <div class="detail-section">
                    <h5><i class="fas fa-ruler"></i> Tamaño Recomendado</h5>
                    <p>Tamaño recomendado: 800x600px o mayor. Máximo 2MB.<br><small class="text-muted">Las imágenes muy pequeñas se verán pixeladas, y las muy grandes ralentizarán la carga.</small></p>
                </div>
                <div class="detail-section">
                    <h5><i class="fas fa-upload"></i> Formatos Aceptados</h5>
                    <p>Se aceptan: JPG, PNG, GIF<br><small class="text-muted">Usa JPG para fotos naturales y PNG para imágenes con transparencia.</small></p>
                </div>
                <div class="detail-section">
                    <h5><i class="fas fa-star"></i> Imagen Principal</h5>
                    <p>La primera imagen que subes será la principal del producto.<br><small class="text-muted">Puedes subir varias imágenes, pero la primera es la que se mostrará en la lista.</small></p>
                </div>
                <div class="detail-section">
                    <h5><i class="fas fa-images"></i> Múltiples Imágenes</h5>
                    <p>Puedes agregar varias imágenes del mismo producto desde diferentes ángulos.<br><small class="text-muted">Esto permite a los clientes ver el producto de manera más completa.</small></p>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeGalleryHelpModal()">Cerrar</button>
            </div>
        </div>
        </div>

    @endsection
