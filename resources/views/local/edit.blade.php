@extends('layouts.app')

@section('title', 'Editar Local - '. ($local->name ?? 'La comarca'))

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-1" style="color: #111827; font-weight: 700; margin-top: 14px;">
                    <i class="fas fa-store" style="color: #e18018; margin-right: 8px;"></i>Mi Local
                </h1>
                <p class="text-muted mb-0">Edita la información general de{{ $local ? ' ' . $local->name : ' tu local' }}</p>
            </div>

            <!-- Formulario Principal -->
            <div class="card border-0" style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-top: 19px;">
                <form action="{{ route('local.update') }}" method="POST" enctype="multipart/form-data" id="localForm">
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
                                           class="form-control @error('image_logo') is-invalid @enderror"
                                           accept="image/*"
                                           style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                    @error('image_logo')
                                        <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
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
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $local->name ?? '') }}"
                               placeholder="Ej: La Comarca - Centro"
                               style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('name')
                            <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
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
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe tu local, ubicación, ambiente..."
                                  style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('description', $local->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2" style="font-size: 13px;">Máximo 1000 caracteres</small>
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
                                   class="form-control @error('contact') is-invalid @enderror" 
                                   value="{{ old('contact', $local->contact ?? '') }}"
                                   placeholder="+34 123 456 789"
                                   style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                            @error('contact')
                                <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label" style="font-weight: 600; color: #374151; margin-bottom: 8px; display: block; font-size: 14px;">
                                Estado <span style="color: #dc2626;">*</span>
                            </label>
                            <select id="status"
                                    name="status" 
                                    class="form-control @error('status') is-invalid @enderror"
                                    style="padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                    required>
                                <option value="">-- Selecciona un estado --</option>
                                <option value="Active" {{ old('status', $local->status ?? '') === 'Active' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="Inactive" {{ old('status', $local->status ?? '') === 'Inactive' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 18px 0;">

                    <!-- Botones de Acción -->
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('dashboard') }}" 
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
    // Validación del formulario
    const form = document.getElementById('localForm');
    
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Preview de imagen
    const imageInput = document.getElementById('image_logo');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Podrías agregar una vista previa aquí
                    console.log('Imagen seleccionada:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
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
</style>
@endsection
