@extends('layouts.app')

@section('title', 'Crear Proveedor')

@push('styles')
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="product-page-wrapper">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
        <ol class="breadcrumb" style="padding: 0; background: none;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Proveedores</a></li>
            <li class="breadcrumb-item active">Crear Proveedor</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="product-page-header">
        <div class="product-page-header-flex">
            <div class="product-page-header-title">
                <h2>
                    <i class="fas fa-user-plus"></i> Registrar Nuevo Proveedor
                </h2>
                <div class="accent-bar"></div>
                <small class="text-muted">Complete los datos del proveedor</small>
            </div>
        </div>
    </div>

    <!-- Formulario de Creación -->
    <div class="product-form-container">
        <div class="card product-card">
            <div class="card-header product-card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Información del Proveedor
                </h5>
            </div>
            <div class="card-body" style="padding:24px;">
                <form action="{{ route('suppliers.store') }}" method="POST" id="supplierForm" enctype="multipart/form-data">
                    @csrf

                    <div style="max-width: 600px;">
                        <!-- Nombre -->
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Nombre del Proveedor *</strong></span>
                                <span class="ms-2 text-white-50" title="Nombre completo del proveedor">
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
                                   placeholder="Ej: Juan García Suministros">
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="form-group mb-3">
                            <label for="telefono" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Teléfono *</strong></span>
                                <span class="ms-2 text-white-50" title="Número de contacto del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}"
                                       required
                                       maxlength="20"
                                       placeholder="Ej: +506 8765-4321">
                            </div>
                            @error('telefono')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Email *</strong></span>
                                <span class="ms-2 text-white-50" title="Correo electrónico del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       required
                                       maxlength="255"
                                       placeholder="Ej: contacto@proveedor.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Galería de Fotos -->
                        <div class="form-group mb-4">
                            <label for="imagenes" class="form-label d-flex align-items-center justify-content-between">
                                <span><strong>Galería de Fotos (Facturas) *</strong></span>
                                <span class="ms-2 text-white-50" title="Fotos o PDFs de facturas del proveedor">
                                    <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:#f8fafc; border:1px solid #e5e7eb;">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </span>
                                <input type="file" 
                                       class="form-control @error('imagenes') is-invalid @enderror" 
                                       id="imagenes" 
                                       name="imagenes[]" 
                                       accept="image/*,.pdf" 
                                       multiple
                                       onchange="previewImages(this)">
                            </div>
                            <small class="form-text text-muted d-block mt-2">
                                Puedes subir múltiples fotos o PDFs de facturas. Formato: JPG, PNG, PDF (máx. 5MB por archivo)
                            </small>
                            @error('imagenes')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                            @error('imagenes.*')
                                <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Preview de imágenes -->
                        <div id="imagePreview" class="mb-4" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-check-circle me-2" style="color: #059669;"></i>Archivos seleccionados:
                            </label>
                            <div id="previewContainer" class="row"></div>
                        </div>

                        <!-- Botones de Acción -->
                        <div style="display: flex; gap: 12px; margin-top: 32px; justify-content: flex-start;">
                            <button type="submit" class="btn-primary" style="background: #059669; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s;">
                                <i class="fas fa-check"></i> Crear Proveedor
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn-secondary" style="background: #e5e7eb; color: #374151; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImages(input) {
    const previewDiv = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';

    if (input.files && input.files.length > 0) {
        previewDiv.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const isImage = file.type.startsWith('image/');
            const isPDF = file.type === 'application/pdf';
            
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            
            let preview = '';
            if (isImage) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = col.querySelector('img');
                    if (img) img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                preview = `<img src="" alt="${file.name}" class="img-thumbnail" style="height: 120px; object-fit: cover; width: 100%;">`;
            } else if (isPDF) {
                preview = `<div class="bg-danger text-white p-4 text-center" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                    <div>
                        <i class="fas fa-file-pdf" style="font-size: 32px;"></i>
                        <p class="mb-0 small mt-2">PDF</p>
                    </div>
                </div>`;
            }
            
            col.innerHTML = `
                ${preview}
                <small class="d-block mt-1 text-truncate" title="${file.name}">${file.name}</small>
                <small class="text-muted">${fileSize} MB</small>
            `;
            previewContainer.appendChild(col);
        });
    } else {
        previewDiv.style.display = 'none';
    }
}
</script>

@endsection
