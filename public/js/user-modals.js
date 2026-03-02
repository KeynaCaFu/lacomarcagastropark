// Modales para Gestión de Usuarios (ligero, basado en EventoModals)
class UserModals {
    constructor(){
        this.initGlobalListeners();
    }

    initGlobalListeners(){
        // Cerrar al click fuera
        window.addEventListener('click', (ev) => {
            ['userCreateModal','userEditModal'].forEach(id => {
                const modal = document.getElementById(id);
                if(modal && ev.target === modal) this.closeModal(id);
            });
        });
        
        // Cerrar con ESC
        document.addEventListener('keydown', (ev) => { 
            if(ev.key === 'Escape'){
                ['userCreateModal','userEditModal'].forEach(id=>{ 
                    const m = document.getElementById(id); 
                    if(m && m.style.display === 'flex') this.closeModal(id); 
                }); 
            } 
        });
    }

    openCreateModal(){
        const modal = document.getElementById('userCreateModal');
        if(!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Limpiar errores previos
        const form = document.getElementById('createUserForm');
        if(form){
            form.reset();
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }
    }

    async openEditModal(userId){
        const modal = document.getElementById('userEditModal');
        const content = document.getElementById('editUserContent');
        if(!modal || !content) return;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Mostrar loading
        content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
        
        try{
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            
            // Cargar partial con formulario (sin obtener usuario con contraseña)
            const partialRes = await fetch(`/usuarios/${userId}/edit-modal`, {
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if(!partialRes.ok) throw new Error('HTTP ' + partialRes.status);
            
            const html = await partialRes.text();
            content.innerHTML = html;
            this.initEditForm(content);
        } catch(e){ 
            console.error('Error:', e);
            content.innerHTML = '<div class="p-3 text-center text-danger">Error al cargar</div>';
        }
    }

    initEditForm(container){
        if(!container) container = document.getElementById('editUserContent');
        if(!container) return;
        const form = container.querySelector('#editUserForm');
        if(!form) return;
        
        // Evitar duplicar listeners
        if(form.dataset._editBound === 'true') return;
        form.dataset._editBound = 'true';

        // Inicializar validación de contraseña para modal
        this.initPasswordValidation(container);

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            // Confirmación SweetAlert consistente
            if (window.swConfirm) {
                const res = await swConfirm({
                    title: 'Editar usuario',
                    text: '¿Desea guardar los cambios de este usuario?',
                    icon: 'question',
                    confirmButtonText: 'Sí, actualizar'
                });
                if (!res.isConfirmed) return;
            }
            
            // El submitBtn puede estar dentro del form o referenciado con form="editUserForm"
            let submitBtn = form.querySelector('button[type="submit"]');
            if(!submitBtn) {
                submitBtn = document.querySelector('button[type="submit"][form="editUserForm"]');
            }
            if(!submitBtn) return; // Si no encuentra el botón, salir
            if(submitBtn.disabled) return; // Evitar envíos múltiples
            
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            
            const formData = new FormData(form);
            const userId = form.getAttribute('data-user-id');
            
            try{
                const res = await fetch(`/usuarios/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if(!res.ok){
                    const data = await res.json();
                    throw data;
                }
                
                // Éxito
                window.userModals.closeModal('userEditModal');
                let retries = 0;
                const checkAndShowSuccess = () => {
                    if (window.swToast) {
                        swToast.fire({
                            icon: 'success',
                            title: 'Usuario actualizado correctamente'
                        });
                        window.location.reload();
                    } else if (retries < 50) {
                        retries++;
                        setTimeout(checkAndShowSuccess, 100);
                    }
                };
                setTimeout(checkAndShowSuccess, 100);
            } catch(error){
                window.userModals.handleValidationErrors(error, form);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    handleValidationErrors(error, form){
        console.error(error);
        
        if(error.errors){
            form.querySelectorAll('.is-invalid').forEach(i => i.classList.remove('is-invalid'));
            
            Object.entries(error.errors).forEach(([field, messages]) => {
                const input = form.querySelector(`[name="${field}"]`);
                if(input){
                    input.classList.add('is-invalid');
                    const feedback = input.nextElementSibling;
                    if(feedback && feedback.classList.contains('invalid-feedback')){
                        feedback.textContent = messages[0];
                    }
                }
            });
        } else {
            if (window.swAlert) {
                swAlert({ icon: 'error', title: 'Error', text: 'Ocurrió un error inesperado.' });
            } else {
                alert('Ocurrió un error inesperado.');
            }
        }
    }

    closeModal(id){ 
        const modal = document.getElementById(id); 
        if(!modal) return; 
        modal.style.display = 'none'; 
        document.body.style.overflow = 'auto';
    }

    initPasswordValidation(container){
        const passwordField = container.querySelector('#edit_modal_password');
        const confirmField = container.querySelector('#edit_modal_password_confirmation');
        const strengthFill = container.querySelector('#editModalStrengthFill');
        const strengthText = container.querySelector('#editModalStrengthText');
        const matchFeedback = container.querySelector('#editModalMatchFeedback');
        
        if (!passwordField || !confirmField || !strengthFill || !strengthText || !matchFeedback) {
            return;
        }

        const validatePasswordStrength = (password) => {
            let strength = 0;
            if (password.length >= 8) strength += 25;
            if (/[a-z]/.test(password)) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            return {
                strength,
                text: strength === 0 ? '' : strength === 25 ? 'Muy débil' : strength === 50 ? 'Débil' : strength === 75 ? 'Buena' : 'Fuerte',
                color: strength === 0 ? '' : strength <= 25 ? '#dc2626' : strength <= 50 ? '#f97316' : strength <= 75 ? '#eab308' : '#16a34a'
            };
        };

        const updatePasswordStrength = () => {
            const result = validatePasswordStrength(passwordField.value);
            const strengthContainer = passwordField.closest('.col-md-6').querySelector('.password-strength');
            
            if (passwordField.value.length > 0) {
                strengthContainer.classList.add('active');
                strengthFill.style.width = result.strength + '%';
                strengthFill.style.backgroundColor = result.color;
                strengthText.textContent = result.text;
                strengthText.style.color = result.color;
            } else {
                strengthContainer.classList.remove('active');
            }
        };

        const updatePasswordMatch = () => {
            if (!confirmField.value) {
                matchFeedback.classList.remove('show');
                return;
            }
            
            if (passwordField.value === confirmField.value) {
                matchFeedback.classList.add('show', 'success');
                matchFeedback.classList.remove('error');
                matchFeedback.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
            } else {
                matchFeedback.classList.add('show', 'error');
                matchFeedback.classList.remove('success');
                matchFeedback.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
            }
        };

        passwordField.addEventListener('input', () => {
            updatePasswordStrength();
            if (confirmField.value) updatePasswordMatch();
        });

        confirmField.addEventListener('input', updatePasswordMatch);
    }
}

// Exponer funciones globales
document.addEventListener('DOMContentLoaded', function(){
    window.userModals = new UserModals();
});

function openUserCreateModal(){ 
    if(window.userModals) window.userModals.openCreateModal(); 
}

function openUserEditModal(userId){ 
    if(window.userModals) window.userModals.openEditModal(userId); 
}

function closeUserModal(id){ 
    if(window.userModals) window.userModals.closeModal(id); 
}
