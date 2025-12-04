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
            const res = await fetch(`/usuarios/${userId}`, {
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            });
            
            if(!res.ok) throw new Error('HTTP ' + res.status);
            
            const user = await res.json();
            
            // Cargar partial con formulario
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

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            
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
                window.location.reload();
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
            alert('Ocurrió un error inesperado.');
        }
    }

    closeModal(id){ 
        const modal = document.getElementById(id); 
        if(!modal) return; 
        modal.style.display = 'none'; 
        document.body.style.overflow = 'auto';
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
