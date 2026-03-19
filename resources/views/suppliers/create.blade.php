@extends('layouts.app')

@section('title', 'Crear Proveedor')

@push('styles')
    <link href="{{ asset('css/productos.css') }}" rel="stylesheet">
    
    <style>
        /* Input focus color - Orange instead of Blue */
        .form-control:focus {
            border-color: #e18018 !important;
            box-shadow: 0 0 0 0.2rem rgba(225, 128, 24, 0.25) !important;
        }
        
        .custom-file-input:focus ~ .custom-file-label {
            border-color: #e18018;
        }
    </style>
@endpush

@section('content')
<div class="product-page-wrapper">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="product-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('suppliers.index') }}">
                    <i class="fas fa-truck me-1"></i> Proveedores
                </a>
            </li>
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
                <small class="text-muted">Agregue un nuevo proveedor al sistema</small>
            </div>
        </div>
    </div>

    <!-- Formulario -->
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

                    <div class="create-product-grid">

                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="create-product-left">

                            <!-- Nombre -->
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label d-flex align-items-center justify-content-between">
                                    <span><strong>Nombre del Proveedor *</strong></span>
                                    <span class="ms-2 text-white-50" title="Ingrese el nombre completo del proveedor">
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
                                       placeholder="Nombre del proveedor">

                                @error('nombre')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Teléfono y Email -->
                            <div class="form-grid-2col">
                                <div>
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
                                                   value="{{ old('telefono', isset($supplier) ? $supplier->phone : '') }}"
                                                   required
                                                   maxlength="9"
                                                     placeholder="8888-8888"
                                                   >
                                        </div>

                                        @error('telefono')
                                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label d-flex align-items-center justify-content-between">
                                            <span><strong>Email *</strong></span>
                                            <span class="ms-2 text-white-50" title="El correo debe ser @gmail.com">
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
                                                   pattern="^[a-zA-Z0-9._%+\-]+@gmail\.com$"
                                                   title="El correo debe ser @gmail.com"
                                                   placeholder="ej: proveedor@gmail.com">
                                        </div>

                                        @error('email')
                                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="create-product-right">

                            <!-- Galería -->
                            <div class="form-group mb-3">
                                <label for="imagenes" class="form-label d-flex align-items-center justify-content-between">
                                    <span><strong>Galería de Fotos (Facturas) *</strong></span>
                                    <span class="ms-2 text-white-50" title="Suba imágenes o PDFs de facturas">
                                        <i class="fas fa-info-circle" aria-label="Ayuda"></i>
                                    </span>
                                </label>

                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input @error('imagenes') is-invalid @enderror"
                                           id="imagenes"
                                           name="imagenes[]"
                                           accept="image/*,.pdf"
                                           multiple
                                           onchange="previewFiles(this)">
                                    <label class="custom-file-label" for="imagenes">Seleccionar archivos...</label>
                                </div>

                                <small class="form-text text-muted d-block mt-2">
                                    Formatos: JPG, PNG, PDF. Máx: 5MB por archivo
                                </small>

                                @error('imagenes')
                                    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                @enderror

                                @error('imagenes.*')
                                    <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                                @enderror

                                <!-- Preview -->
                                <div id="filePreviewContainer" style="margin-top: 12px; border: 2px dashed #e5e7eb; border-radius: 10px; overflow: hidden; background: #fafafa; display: flex; align-items: center; justify-content: center; min-height: 180px; transition: all 0.3s ease; flex-direction: column; padding: 16px;">
                                    <div id="filePreviewPlaceholder" style="text-align: center; padding: 20px; color: #9ca3af;">
                                        <i class="fas fa-file-upload" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 8px;"></i>
                                        <span style="font-size: 13px;">La vista previa aparecerá aquí</span>
                                    </div>
                                    <div id="previewContainer" class="row w-100" style="display:none;"></div>
                                </div>
                            </div>

                            <!-- Tip -->
                            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #92400e;">
                                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                                <strong>Tip:</strong> Adjunte al menos una foto o PDF de factura del proveedor.
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; margin-top: 20px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary" style="border-color:#e5e7eb; color:#374151;">
                            <i class="fas fa-times"></i> Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #e18018, #915016); border:none; font-weight:600;">
                            <i class="fas fa-save"></i> Crear Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewFiles(input) {
        const placeholder = document.getElementById('filePreviewPlaceholder');
        const container = document.getElementById('previewContainer');
        container.innerHTML = '';

        const fileName = input.files.length > 0
            ? (input.files.length === 1 ? input.files[0].name : `${input.files.length} archivos seleccionados`)
            : 'Seleccionar archivos...';

        const label = input.parentElement.querySelector('.custom-file-label');
        if (label) {
            label.textContent = fileName;
        }

        if (input.files && input.files.length > 0) {
            placeholder.style.display = 'none';
            container.style.display = 'flex';

            Array.from(input.files).forEach((file) => {
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-3';

                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const isImage = file.type.startsWith('image/');
                const isPDF = file.type === 'application/pdf';

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        col.innerHTML = `
                            <div class="card" style="border:1px solid #e5e7eb;">
                                <img src="${e.target.result}" class="card-img-top" style="height:120px; object-fit:cover;">
                                <div class="card-body p-2">
                                    <small class="d-block text-truncate" title="${file.name}">${file.name}</small>
                                    <small class="text-muted">${fileSize} MB</small>
                                </div>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else if (isPDF) {
                    col.innerHTML = `
                        <div class="card" style="border:1px solid #e5e7eb;">
                            <div style="height:120px; display:flex; align-items:center; justify-content:center; background:#fff7ed; color:#c2410c;">
                                <div class="text-center">
                                    <i class="fas fa-file-pdf" style="font-size:32px;"></i>
                                    <div class="small mt-2">PDF</div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <small class="d-block text-truncate" title="${file.name}">${file.name}</small>
                                <small class="text-muted">${fileSize} MB</small>
                            </div>
                        </div>
                    `;
                } else {
                    col.innerHTML = `
                        <div class="card" style="border:1px solid #e5e7eb;">
                            <div style="height:120px; display:flex; align-items:center; justify-content:center; background:#f9fafb; color:#6b7280;">
                                <div class="text-center">
                                    <i class="fas fa-file" style="font-size:32px;"></i>
                                    <div class="small mt-2">Archivo</div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <small class="d-block text-truncate" title="${file.name}">${file.name}</small>
                                <small class="text-muted">${fileSize} MB</small>
                            </div>
                        </div>
                    `;
                }

                container.appendChild(col);
            });
        } else {
            placeholder.style.display = 'block';
            container.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const supplierForm = document.getElementById('supplierForm');

        if (supplierForm) {
            supplierForm.addEventListener('submit', function (e) {
                const nombreInput = document.getElementById('nombre');
                const telefonoInput = document.getElementById('telefono');
                const emailInput = document.getElementById('email');
                const imagenesInput = document.getElementById('imagenes');

                const nombre = nombreInput ? nombreInput.value.trim() : '';
                const telefono = telefonoInput ? telefonoInput.value.trim() : '';
                const email = emailInput ? emailInput.value.trim() : '';

                if (!nombre) {
                    e.preventDefault();
                    if (window.swAlert) {
                        swAlert({
                            icon: 'warning',
                            title: 'Campo requerido',
                            text: 'El nombre del proveedor es obligatorio'
                        });
                    } else {
                        alert('El nombre del proveedor es obligatorio');
                    }
                    nombreInput?.focus();
                    return false;
                }

                if (!telefono) {
                    e.preventDefault();
                    if (window.swAlert) {
                        swAlert({
                            icon: 'warning',
                            title: 'Campo requerido',
                            text: 'El teléfono es obligatorio'
                        });
                    } else {
                        alert('El teléfono es obligatorio');
                    }
                    telefonoInput?.focus();
                    return false;
                }

                if (!email) {
                    e.preventDefault();
                    if (window.swAlert) {
                        swAlert({
                            icon: 'warning',
                            title: 'Campo requerido',
                            text: 'El correo electrónico es obligatorio'
                        });
                    } else {
                        alert('El correo electrónico es obligatorio');
                    }
                    emailInput?.focus();
                    return false;
                }

                if (!imagenesInput || !imagenesInput.files || imagenesInput.files.length === 0) {
                    e.preventDefault();
                    if (window.swAlert) {
                        swAlert({
                            icon: 'warning',
                            title: 'Archivo requerido',
                            text: 'Debe adjuntar al menos una foto o PDF de factura'
                        });
                    } else {
                        alert('Debe adjuntar al menos una foto o PDF de factura');
                    }
                    imagenesInput?.focus();
                    return false;
                }

                if (window.swConfirm) {
                    e.preventDefault();
                    swConfirm({
                        title: 'Crear proveedor',
                        text: '¿Desea guardar este nuevo proveedor?',
                        icon: 'question',
                        confirmButtonText: 'Sí, guardar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            supplierForm.submit();
                        }
                    });
                }
            });
        }

        const errorMsg = @json(session('error'));
        if (errorMsg && window.swAlert) {
            swAlert({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
                confirmButtonColor: '#dc2626'
            });
        }

        @if ($errors->any())
        if (window.swAlert) {
            swAlert({
                icon: 'error',
                title: 'Error',
                html: `<ul style="text-align:left;">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                confirmButtonColor: '#dc2626'
            });
        }
        @endif
    });
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const telefono = document.getElementById('telefono');

    if (telefono) {
        telefono.addEventListener('input', function () {
            let valor = this.value.replace(/\D/g, '');

            if (valor.length > 8) {
                valor = valor.slice(0, 8);
            }

            if (valor.length > 4) {
                valor = valor.slice(0, 4) + '-' + valor.slice(4);
            }

            this.value = valor;
        });
    }
});
</script>
@endpush

@endsection