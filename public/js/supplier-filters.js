
    // Sistema de Filtrado para Proveedores 
    class ProveedorFilters {
        constructor() {
            this.initializeFilters();
            this.totalProveedores = 0;
            this.setupEventListeners();
        }

        initializeFilters() {
            // Contar total de proveedores
            this.countTotalProveedores();
            this.updateResultCounter();
        
            // Configurar collapse icon
            this.setupCollapseIcon();
        }

        setupEventListeners() {
            // Filtrado en tiempo real para nombre
            document.getElementById('filtroNombre')?.addEventListener('input', () => {
                this.aplicarFiltros();
            });

            // Filtrado inmediato para selects
            document.getElementById('filtroEstado')?.addEventListener('change', () => {
                this.aplicarFiltros();
            });

            document.getElementById('filtroInsumos')?.addEventListener('change', () => {
                this.aplicarFiltros();
            });

            // Configurar icono del collapse
            const collapse = document.getElementById('filtrosCollapse');
            if (collapse) {
                collapse.addEventListener('shown.bs.collapse', () => {
                    const icon = document.getElementById('filtrosIcon');
                    if (icon) {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    }
                });

                collapse.addEventListener('hidden.bs.collapse', () => {
                    const icon = document.getElementById('filtrosIcon');
                    if (icon) {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    }
                });
            }
        }

        setupCollapseIcon() {
            // El acordeón permanece cerrado por defecto
            // El usuario debe hacer clic para abrirlo
        }

        countTotalProveedores() {
            const tableRows = document.querySelectorAll('.proveedor-row');
            const cardItems = document.querySelectorAll('.proveedor-card-item');
            this.totalProveedores = Math.max(tableRows.length, cardItems.length);
        }

        aplicarFiltros() {
            const filtros = this.obtenerFiltros();
            let visibles = 0;

            // Filtrar filas de tabla
            const tableRows = document.querySelectorAll('.proveedor-row');
            tableRows.forEach(row => {
                if (this.cumpleFiltros(row, filtros)) {
                    row.style.display = '';
                    visibles++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Filtrar cards
            const cardItems = document.querySelectorAll('.proveedor-card-item');
            cardItems.forEach(card => {
                if (this.cumpleFiltros(card, filtros)) {
                    card.style.display = '';
                    if (visibles === 0) visibles++; // Evitar doble conteo
                } else {
                    card.style.display = 'none';
                }
            });

            // Si usamos cards, contar solo los visibles
            if (cardItems.length > 0) {
                visibles = Array.from(cardItems).filter(card => card.style.display !== 'none').length;
            }

            this.updateResultCounter(visibles);
            this.mostrarMensajeVacio(visibles === 0);
        }

        obtenerFiltros() {
            return {
                nombre: document.getElementById('filtroNombre')?.value.toLowerCase().trim() || '',
                estado: document.getElementById('filtroEstado')?.value || '',
                insumos: document.getElementById('filtroInsumos')?.value || ''
            };
        }

        cumpleFiltros(elemento, filtros) {
            const nombre = elemento.dataset.nombre || '';
            const estado = elemento.dataset.estado || '';
            const insumos = parseInt(elemento.dataset.supplies) || 0;

            // Filtro por nombre
            if (filtros.nombre && !nombre.includes(filtros.nombre)) {
                return false;
            }

            // Filtro por estado
            if (filtros.estado && estado !== filtros.estado) {
                return false;
            }

            // Filtro por insumos
            if (filtros.insumos) {
                if (filtros.insumos === 'con-insumos' && insumos === 0) {
                    return false;
                }
                if (filtros.insumos === 'sin-insumos' && insumos > 0) {
                    return false;
                }
            }

            return true;
        }

        updateResultCounter(visibles = null) {
            const counter = document.getElementById('resultadosFiltro');
            if (!counter) return;

            if (visibles === null) {
                visibles = this.totalProveedores;
            }

            if (visibles === this.totalProveedores) {
                counter.textContent = `Mostrando todos los ${this.totalProveedores} proveedores`;
                counter.className = 'text-muted small align-self-center ms-2';
            } else if (visibles === 0) {
                counter.textContent = 'No se encontraron proveedores con los filtros aplicados';
                counter.className = 'text-warning small align-self-center ms-2';
            } else {
                counter.textContent = `Mostrando ${visibles} de ${this.totalProveedores} proveedores`;
                counter.className = 'text-info small align-self-center ms-2';
            }
        }

        mostrarMensajeVacio(mostrar) {
            let mensajeVacio = document.getElementById('mensajeProveedoresVacio');
        
            if (mostrar && !mensajeVacio) {
                // Crear mensaje de no encontrados
                const contenedor = document.querySelector('.container-fluid');
                mensajeVacio = document.createElement('div');
                mensajeVacio.id = 'mensajeProveedoresVacio';
                mensajeVacio.className = 'row mt-4';
                mensajeVacio.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron proveedores</h5>
                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="limpiarFiltros()">
                                <i class="fas fa-times me-1"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                `;
                contenedor.appendChild(mensajeVacio);
            } else if (!mostrar && mensajeVacio) {
                mensajeVacio.remove();
            }
        }

        limpiarFiltros() {
            // Limpiar todos los inputs
            document.getElementById('filtroNombre').value = '';
            document.getElementById('filtroEstado').value = '';
            document.getElementById('filtroInsumos').value = '';

            // Mostrar todos los elementos
            document.querySelectorAll('.proveedor-row, .proveedor-card-item').forEach(elemento => {
                elemento.style.display = '';
            });

            // Actualizar contador
            this.updateResultCounter();

            // Quitar mensaje de vacío
            this.mostrarMensajeVacio(false);

            // Mostrar notificación
            this.mostrarNotificacion('Filtros limpiados', 'info');
        }

        mostrarNotificacion(mensaje, tipo = 'info') {
            // Crear notificación simple
            const notif = document.createElement('div');
            notif.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
            notif.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
            notif.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
        
            document.body.appendChild(notif);
        
            // Auto-remover
            setTimeout(() => {
                if (notif.parentNode) {
                    notif.remove();
                }
            }, 3000);
        }

        // Métodos de utilidad para exportar/importar filtros
        exportarFiltros() {
            const filtros = this.obtenerFiltros();
            localStorage.setItem('proveedoresFiltros', JSON.stringify(filtros));
            this.mostrarNotificacion('Filtros guardados', 'success');
        }

        importarFiltros() {
            const filtrosGuardados = localStorage.getItem('proveedoresFiltros');
            if (filtrosGuardados) {
                const filtros = JSON.parse(filtrosGuardados);
                document.getElementById('filtroNombre').value = filtros.nombre || '';
                document.getElementById('filtroEstado').value = filtros.estado || '';
                document.getElementById('filtroInsumos').value = filtros.insumos || '';
            
                this.aplicarFiltros();
                this.mostrarNotificacion('Filtros restaurados', 'success');
            } else {
                this.mostrarNotificacion('No hay filtros guardados', 'warning');
            }
        }
    }

    // Funciones globales para los botones
    function aplicarFiltros() {
        if (window.proveedorFilters) {
            window.proveedorFilters.aplicarFiltros();
        }
    }

    function limpiarFiltros() {
        if (window.proveedorFilters) {
            window.proveedorFilters.limpiarFiltros();
        }
    }

    function guardarFiltros() {
        if (window.proveedorFilters) {
            window.proveedorFilters.exportarFiltros();
        }
    }

    function restaurarFiltros() {
        if (window.proveedorFilters) {
            window.proveedorFilters.importarFiltros();
        }
    }

    // Inicializar cuando se carga la página
    document.addEventListener('DOMContentLoaded', function() {
        // Pequeña espera para asegurar que todo esté cargado
        setTimeout(() => {
            window.proveedorFilters = new ProveedorFilters();
        }, 100);
    });

    // Reinicializar al redimensionar ventana para responsive
    window.addEventListener('resize', function() {
        if (window.proveedorFilters) {
            window.proveedorFilters.setupCollapseIcon();
        }
    });


// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    // Pequeña espera para asegurar que todo esté cargado
    setTimeout(() => {
        window.proveedorFilters = new ProveedorFilters();
    }, 100);
});

// Reinicializar al redimensionar ventana para responsive
window.addEventListener('resize', function() {
    if (window.proveedorFilters) {
        window.proveedorFilters.setupCollapseIcon();
    }
});