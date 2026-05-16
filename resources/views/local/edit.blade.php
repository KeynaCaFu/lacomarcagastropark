@extends('layouts.app')

@section('title', 'Editar Local - '. ($local->name ?? 'La comarca'))

@push('styles')
    <style>
        /* Sistema de validación inline (field-error) */
        .field-error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .field-error i { font-size: 11px; }
        input.input-error, select.input-error, textarea.input-error {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 2px rgba(220,38,38,0.12) !important;
        }
        /* Ocultar alerta global de errores en esta página */
        .alert-danger {
            display: none !important;
        }
    </style>
@endpush

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
                        <i class="fas fa-edit" style="color: #e18018; margin-right: 8px;"></i>Editar Local
                    </h1>
                    <p class="text-muted mb-0">Actualiza la información general de {{ $local ? $local->name : 'tu local' }}</p>
                </div>
            </div>

            <!-- Formulario Principal -->
            <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-top: 19px;">
                <form action="{{ route('local.update') }}" method="POST" enctype="multipart/form-data" id="localForm" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Logo/Imagen del Local -->
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 12px; display: block; font-size: 14px;">
                            Logo del Local
                        </label>
                        <div class="row g-3">
                            <div class="col-md-3">
                                @if($local && $local->image_logo)
                                    <div style="position: relative;">
                                        <img src="{{ asset($local->image_logo) }}" 
                                             alt="{{ $local->name }}" 
                                             style="width: 100%; max-width: 120px; height: auto; border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <small class="text-muted d-block mt-2" style="font-size: 12px;">Logo actual</small>
                                    </div>
                                @else
                                    <div style="width: 120px; height: 120px; background: #f3f4f6; border-radius: 8px; border: 2px dashed #d1d5db; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="font-size: 32px; color: #9ca3af;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div>
                                    <input type="file" 
                                           name="image_logo" 
                                           id="image_logo"
                                           class="form-control @error('image_logo') input-error @enderror"
                                           accept="image/*"
                                           style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                    <div class="field-error" id="imageLogo Error" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                                    @error('image_logo')
                                        <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2" style="font-size: 13px;">
                                        JPEG, PNG, JPG, GIF. Máximo: 2 MB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 18px 0;">

                    <!-- Nombre del Local -->
                    <div class="mb-3">
                        <label for="name" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                            Nombre del Local <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" 
                               id="name"
                               name="name" 
                               class="form-control @error('name') input-error @enderror" 
                               value="{{ old('name', $local->name ?? '') }}"
                               placeholder="Ej: La Comarca - Centro"
                               style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <div class="field-error" id="nameError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                        @error('name')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                            Descripción
                        </label>
                        <textarea id="description"
                                  name="description" 
                                  rows="3"
                                  class="form-control @error('description') input-error @enderror"
                                  placeholder="Describe tu local, ubicación, ambiente..."
                                  maxlength="600"
                                  style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('description', $local->description ?? '') }}</textarea>
                        <div class="field-error" id="descriptionError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                        @error('description')
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between align-items-center" style="margin-top: 8px;">
                            <small class="text-muted" style="font-size: 13px;">Máximo 600 caracteres</small>
                            <small id="charCountDisplay" class="text-muted" style="font-size: 13px;"><span id="charCount">0</span>/600</small>
                        </div>
                    </div>

                    <!-- Contacto y Estado en columnas -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contact" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                                Contacto
                            </label>
                            <input type="text" 
                                   id="contact"
                                   name="contact" 
                                   class="form-control @error('contact') input-error @enderror" 
                                   value="{{ old('contact', $local->contact ?? '') }}"
                                   placeholder="0000-0000"
                                   pattern="\d{4}-\d{4}"
                                   style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                            <div class="field-error" id="contactError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                            @error('contact')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2" style="font-size: 13px;">
                                Formato: 0000-0000
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                                Estado <span style="color: #dc2626;">*</span>
                            </label>
                            <select id="status"
                                    name="status" 
                                    class="form-control @error('status') input-error @enderror"
                                    style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                <option value="">-- Selecciona un estado --</option>
                                <option value="Active" {{ old('status', $local->status ?? '') === 'Active' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="Inactive" {{ old('status', $local->status ?? '') === 'Inactive' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            <div class="field-error" id="statusError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span></span></div>
                            @error('status')
                                <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 18px 0;">

                    <!-- Botones de Acción -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('local.index') }}" 
                           class="btn btn-secondary" 
                           style="padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; border: 1px solid #d1d5db; background: #f9fafb; color: #374151; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" 
                                class="btn btn-primary" 
                                style="padding: 10px 61px; border-radius: 8px; font-weight: 600; font-size: 14px; background: linear-gradient(135deg, #e18018, #c9690f); border: none; color: white; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; cursor: pointer;">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

    // Mostrar mensajes de error con SweetAlert (solo para errores de sesión NO de validación)
    const errorMsg = @json(session('error'));
    if (errorMsg) {
        let retries = 0;
        const checkAndShowAlert = () => {
            if (window.swAlert) {
                swAlert({ icon: 'error', title: 'Error', text: errorMsg, confirmButtonColor: '#dc2626' });
            } else if (retries < 50) {
                retries++;
                setTimeout(checkAndShowAlert, 100);
            }
        };
        checkAndShowAlert();
    }

    // Helper functions for field-error validation
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorSpan = document.getElementById(fieldId + 'Error');
        if (field && errorSpan) {
            field.classList.add('input-error');
            errorSpan.style.display = 'flex';
            const span = errorSpan.querySelector('span');
            if (span) span.textContent = message;
        }
    }

    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorSpan = document.getElementById(fieldId + 'Error');
        if (field && errorSpan) {
            field.classList.remove('input-error');
            errorSpan.style.display = 'none';
        }
    }

    // Form submission validation
    const form = document.getElementById('localForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir envío por defecto

            const nameField = document.getElementById('name');
            const statusField = document.getElementById('status');

            let isValid = true;

            // Validate name
            if (!nameField.value.trim()) {
                showFieldError('name', 'El nombre del local es obligatorio');
                isValid = false;
            } else {
                clearFieldError('name');
            }

            // Validate status
            if (!statusField.value) {
                showFieldError('status', 'Debe seleccionar un estado');
                isValid = false;
            } else {
                clearFieldError('status');
            }

            // Si hay errores, no mostrar SweetAlert
            if (!isValid) {
                return;
            }

            // Si pasó validación, mostrar confirmación
            function showConfirm() {
                if (typeof window.swConfirm === 'undefined') {
                    setTimeout(showConfirm, 100);
                    return;
                }

                swConfirm({
                    title: 'Guardar cambios',
                    text: '¿Desea guardar los cambios del local?',
                    icon: 'question',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            showConfirm();
        });
    }

    // Preview de imagen
    const imageInput = document.getElementById('image_logo');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    if (window.swAlert) {
                        swAlert({ icon: 'warning', title: 'Formato no válido', text: 'Solo se aceptan imágenes JPG, PNG o GIF' });
                    } else {
                        alert('Solo se aceptan imágenes JPG, PNG o GIF');
                    }
                    imageInput.value = '';
                    return;
                }

                // Validar tamaño
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    if (window.swAlert) {
                        swAlert({ icon: 'warning', title: 'Archivo muy grande', text: 'La imagen no puede ser mayor a 2MB' });
                    } else {
                        alert('La imagen no puede ser mayor a 2MB');
                    }
                    imageInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {};
                reader.readAsDataURL(file);
            }
        });
    }

    // Formateo automático del contacto (0000-0000)
    const contactInput = document.getElementById('contact');
    if (contactInput) {
        contactInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Elimina caracteres no numéricos
            
            if (value.length >= 4) {
                value = value.substring(0, 8); // Limita a 8 dígitos
                if (value.length > 4) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }
            }
            
            e.target.value = value;
        });
    }

    // Contador de caracteres para descripción
    const descriptionInput = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const charCountDisplay = document.getElementById('charCountDisplay');
    if (descriptionInput) {
        // Actualizar contador al cargar la página
        charCount.textContent = descriptionInput.value.length;
        updateCharCountStyle();
        
        descriptionInput.addEventListener('input', function(e) {
            charCount.textContent = e.target.value.length;
            updateCharCountStyle();
        });
    }

    function updateCharCountStyle() {
        const currentLength = parseInt(charCount.textContent);
        const maxLength = 600;
        
        if (currentLength >= maxLength) {
            charCountDisplay.style.color = '#dc2626';
            charCountDisplay.style.fontWeight = '600';
            charCountDisplay.innerHTML = `<i class="fas fa-exclamation-circle" style="margin-right: 4px; color: #dc2626;"></i><span id="charCount" style="color: #dc2626;">${currentLength}</span><span style="color: #dc2626;">/600 (Máximo alcanzado)</span>`;
        } else {
            charCountDisplay.style.color = '#6b7280';
            charCountDisplay.style.fontWeight = 'normal';
            charCountDisplay.innerHTML = `<span id="charCount">${currentLength}</span>/600`;
        }
    }

    // Real-time validation
    ['name', 'status'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                if (this.value.trim()) clearFieldError(fieldId);
            });
            field.addEventListener('blur', function() {
                if (!this.value.trim()) showFieldError(fieldId, 'Este campo es obligatorio');
            });
        }
    });
});
</script>

<style>
    .form-control:focus,
    select:focus {
        border-color: #e18018 !important;
        box-shadow: 0 0 0 2px rgba(225, 128, 24, 0.1) !important;
    }

    .form-control:invalid,
    select:invalid {
        border-color: #dc2626;
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
@endsection
