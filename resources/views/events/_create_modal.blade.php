
<div id="createModal" class="sage-modal" aria-hidden="true" style="z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; padding: 10px;">
    <div class="modal-backdrop" style="background:rgba(0,0,0,0.5); backdrop-filter: blur(3px); z-index:9997; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
        <div class="modal-card" style="box-sizing: border-box; max-width: 740px; width: calc(100% - 20px); z-index:9998; background:#fdfdfc; display: flex; flex-direction: column; max-height: calc(100vh - 20px); min-height: auto; position: relative; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);" role="dialog" aria-modal="true">
            <div class="modal-header" style="background:#faf9f6; padding: 15px 20px; border-bottom: 2px solid #ff9900; flex-shrink: 0;">
                <h3 class="modal-title"><i class="fas fa-plus"></i> Agregar Nuevo Evento</h3>
                <a href="javascript:void(0)" class="modal-close" aria-label="Cerrar" onclick="closeModal('createModal')" style="cursor: pointer; font-size: 28px; color: #4b4545;">×</a>
            </div>

            <form id="formCreate" action="{{ route('eventos.guardar') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                @csrf

                <div class="modal-body" style="overflow-y: auto; overflow-x: hidden; flex: 1; padding: 20px; -webkit-overflow-scrolling: touch;">
                    <div class="form-grid2">
                        <div>
                            <label class="label">Nombre del Evento *</label>
                            <input class="field" name="title" required placeholder="Ej. Noche de Tapas" value="{{ old('title') }}">

                            <label class="label">Fecha *</label>
                            <input type="date" class="field" name="date" required value="{{ old('date') }}" min="{{ date('Y-m-d') }}">

                            <label class="label">Hora *</label>
                            <input type="time" class="field" name="time" required value="{{ old('time') }}">
                        </div>

                        <div>
                            <label class="label">Imagen (archivo opcional)</label>
                            <input type="file" class="field" name="photo" id="photoInputCreate" accept="image/*">
                            <div style="color:#6f7a71; font-size:14px; margin-top:6px;">Formatos: JPG, PNG. Máx: 2MB</div>

                            <label class="label" style="margin-top:12px;">Estado *</label>
                            <select class="field" name="status" required>
                                <option value="" disabled {{ old('status') ? '' : 'selected' }}>Seleccione un estado</option>
                                <option value="activo"   {{ old('status')==='activo' ? 'selected':'' }}>Activo</option>
                                <option value="inactivo" {{ old('status')==='inactivo' ? 'selected':'' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <label class="label" style="margin-top:10px;">Ubicación (opcional)</label>
                    <input class="field" name="location" placeholder="Ej. Plaza central" value="{{ old('location') }}">

                    <label class="label" style="margin-top:10px;">Descripción *</label>
                    <textarea name="description" class="field" style="min-height:100px; resize:vertical;" required placeholder="Describe el evento...">{{ old('description') }}</textarea>
                </div>

                <div class="modal-footer" style="background:#faf9f6; padding: 15px 20px; border-top: 1px solid #e5e7eb; flex-shrink: 0; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('createModal')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-modal-save">
                        Guardar Evento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('formCreate');
    if (form && !form.dataset._createConfirmBound) {
        form.dataset._createConfirmBound = 'true';
        form.addEventListener('submit', function(e){
            e.preventDefault();
            
            // Función con retry logic para asegurar que swConfirm está disponible
            const showConfirm = () => {
                if (typeof window.swConfirm === 'undefined') {
                    // Si aún no está disponible, esperar 100ms y reintentar
                    setTimeout(showConfirm, 100);
                    return;
                }
                
                swConfirm({
                    title: '¿Guardar evento?',
                    text: 'Se guardará el evento con los datos ingresados',
                    icon: 'question',
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar'
                }).then(r => { if (r.isConfirmed) form.submit(); });
            };
            
            showConfirm();
        });
    }
})();
</script>

<style>
    /* =================================================================
       MODAL DE CREAR EVENTO - RESPONSIVE CON SCROLL
       ================================================================= */

    /* Base styles */
    #createModal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9997;
        display: none !important;
        align-items: center;
        justify-content: center;
        padding: 10px;
        box-sizing: border-box;
    }
    
    #createModal.open {
        display: flex !important;
    }

    .modal-card {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-sizing: border-box;
    }

    /* Estructura flex del formulario */
    #formCreate {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
        min-height: 0;
    }

    #formCreate .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        min-height: 0;
        -webkit-overflow-scrolling: touch;
    }

    #formCreate .modal-footer {
        flex-shrink: 0;
        overflow: visible;
    }

    /* ================================================================= */
    /* MOBILE PEQUEÑO (< 576px) */
    /* ================================================================= */
    @media (max-width: 575.98px) {
        #createModal {
            padding: 8px;
        }

        .modal-card {
            width: calc(100% - 16px) !important;
            max-width: 100% !important;
            max-height: calc(100vh - 16px) !important;
            min-height: auto;
        }

        .modal-header {
            padding: 12px 15px !important;
        }

        .modal-header h3 {
            font-size: 1rem !important;
        }

        .modal-body {
            padding: 15px !important;
        }

        #formCreate .modal-body {
            min-height: 200px;
        }

        .modal-footer {
            padding: 12px 15px !important;
            gap: 8px !important;
            flex-direction: column-reverse !important;
        }

        .modal-footer .btn-modal-save,
        .modal-footer .btn-modal-cancel {
            width: 100%;
            padding: 10px 12px !important;
            font-size: 0.9rem !important;
        }

        .form-grid2 {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
        }

        .form-grid2 > div {
            width: 100% !important;
        }
    }

    /* ================================================================= */
    /* TABLET PEQUEÑO (576px - 767px) */
    /* ================================================================= */
    @media (min-width: 576px) and (max-width: 767.98px) {
        #createModal {
            padding: 12px;
        }

        .modal-card {
            width: calc(100% - 24px) !important;
            max-width: 100% !important;
            max-height: calc(100vh - 24px) !important;
        }

        .modal-body {
            padding: 18px !important;
        }

        .modal-footer {
            padding: 15px 18px !important;
            gap: 10px !important;
        }

        .form-grid2 {
            display: flex !important;
            flex-direction: column !important;
            gap: 15px !important;
        }

        .form-grid2 > div {
            width: 100% !important;
        }
    }

    /* ================================================================= */
    /* TABLET (768px - 991px) */
    /* ================================================================= */
    @media (min-width: 768px) and (max-width: 991.98px) {
        #createModal {
            padding: 20px;
        }

        .modal-card {
            width: calc(100% - 40px) !important;
            max-width: 700px !important;
            max-height: calc(100vh - 40px) !important;
        }

        .modal-body {
            padding: 20px !important;
        }

        .modal-footer {
            padding: 15px 20px !important;
            gap: 12px !important;
        }

        .form-grid2 {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 15px !important;
        }
    }

    /* ================================================================= */
    /* DESKTOP (>= 992px) */
    /* ================================================================= */
    @media (min-width: 992px) {
        .form-grid2 {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 20px !important;
        }

        .form-grid2 > div {
            width: 100% !important;
        }
    }

    /* ================================================================= */
    /* SCROLL SUAVE EN DISPOSITIVOS iOS */
    /* ================================================================= */
    .modal-body {
        -webkit-overflow-scrolling: touch;
    }

    /* Scrollbar personalizada para navegadores modernos */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c9690f;
        border-radius: 4px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a85310;
    }
</style>
