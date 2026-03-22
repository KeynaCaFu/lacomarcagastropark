<!-- ===== MODAL EDITAR LOCAL ===== -->
<div id="modalEditarLocal"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1050;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:420px;box-shadow:0 8px 32px rgba(0,0,0,.18);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0;font-weight:700;font-size:17px;color:#111827;">
                <i class="fas fa-edit" style="color:#c9690f;margin-right:8px;"></i>Editar Local
            </h5>
            <button id="btnCloseEditLocal" type="button"
                    style="background:none;border:none;cursor:pointer;font-size:20px;color:#6b7280;line-height:1;">&times;</button>
        </div>

        <form id="formEditarLocal" method="POST" action="#" style="padding:20px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Nombre del local <span style="color:#dc2626;">*</span>
                </label>
                <input id="edit_local_name" type="text" name="name" required maxlength="255"
                       placeholder="Ej: Restaurante El Sabor"
                       style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;box-sizing:border-box;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                    Gerente asignado <span style="color:#dc2626;">*</span>
                </label>
                <select id="edit_manager_id" name="manager_id" required
                        style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;background:#fff;outline:none;box-sizing:border-box;">
                    <option value="">-- Seleccionar gerente --</option>
                    @foreach($gerentes as $gerente)
                        <option value="{{ $gerente->user_id }}">{{ $gerente->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" id="btnCloseEditLocal2"
                        style="padding:9px 18px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#374151;font-weight:600;font-size:14px;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                        style="padding:9px 18px;border:none;border-radius:8px;background:#e18018;color:#fff;font-weight:700;font-size:14px;cursor:pointer;">
                    <i class="fas fa-save" style="margin-right:6px;"></i>Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
