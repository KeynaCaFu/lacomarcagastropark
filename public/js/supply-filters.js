// Sistema de Filtrado para Insumos (Supplies) 
class SupplyFilters {
    constructor() {
        this.initializeFilters();
        this.totalSupplies = 0;
        this.activeChips = [];
        this.spinnerTimeout = null;
        this.setupEventListeners();
    }

    initializeFilters() {
        // Contar total de insumos
        this.countTotalSupplies();
        this.updateResultCounter();

        // Inyectar contenedor de chips si no existe
        this.ensureChipsContainer();
        // Inyectar estilos mínimos (spinner/chips)
        this.injectAuxStyles();
    }

    setupEventListeners() {
        // Filtrado en tiempo real para búsqueda
        const filtroNombre = document.getElementById('filtroNombre');
        filtroNombre?.addEventListener('input', () => {
            this.aplicarFiltros();
        });
        // Atajos: Enter aplica, Esc limpia
        filtroNombre?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.aplicarFiltros();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.limpiarFiltros();
            }
        });

        // Eventos para links de filtros rápidos (estado)
        document.querySelectorAll('.filtro-estado').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const estado = e.currentTarget.dataset.estado || '';
                this.aplicarFiltroEstado(estado);
            });
        });

        // Eventos para links de filtros de stock y vencimiento
        document.querySelectorAll('.filtro-stock').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const stock = e.currentTarget.dataset.stock || '';
                this.aplicarFiltroStock(stock);
            });
        });

        document.querySelectorAll('.filtro-vencimiento').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const vencimiento = e.currentTarget.dataset.vencimiento || '';
                this.aplicarFiltroVencimiento(vencimiento);
            });
        });
    }

    countTotalSupplies() {
        const tableRows = document.querySelectorAll('.supply-row');
        this.totalSupplies = tableRows.length;
    }

    aplicarFiltroEstado(estado) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-stock, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de estado
        document.querySelectorAll('.filtro-estado').forEach(link => {
            if (link.dataset.estado === estado) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ estado });
    }

    aplicarFiltroStock(stock) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-estado, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de stock
        document.querySelectorAll('.filtro-stock').forEach(link => {
            if (link.dataset.stock === stock) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ stock });
    }

    aplicarFiltroVencimiento(vencimiento) {
        // Limpiar otros filtros
        document.querySelectorAll('.filtro-estado').forEach(link => {
            link.classList.remove('activo');
        });

        // Aplicar filtro de vencimiento
        document.querySelectorAll('.filtro-vencimiento').forEach(link => {
            if (link.dataset.vencimiento === vencimiento) {
                link.classList.add('activo');
            } else {
                link.classList.remove('activo');
            }
        });

        this.aplicarFiltros({ vencimiento });
    }

    aplicarFiltros(filtrosExtra = {}) {
        // Mostrar spinner breve para feedback
        this.showInlineSpinner(true);
        const filtros = {
            buscar: document.getElementById('filtroNombre')?.value.toLowerCase().trim() || '',
            estado: filtrosExtra.estado !== undefined ? filtrosExtra.estado : this.obtenerFiltroActivo('.filtro-estado'),
            stock: filtrosExtra.stock !== undefined ? filtrosExtra.stock : this.obtenerFiltroActivo('.filtro-stock'),
            vencimiento: filtrosExtra.vencimiento !== undefined ? filtrosExtra.vencimiento : this.obtenerFiltroActivo('.filtro-vencimiento')
        };

        let visibles = 0;

        // Filtrar filas de tabla
        const tableRows = document.querySelectorAll('.supply-row');
        tableRows.forEach(row => {
            if (this.cumpleFiltros(row, filtros)) {
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });

        this.updateResultCounter(visibles);
        this.mostrarMensajeVacio(visibles === 0);
        this.renderChips(filtros);

        // Ocultar spinner tras un pequeño delay para que sea perceptible
        clearTimeout(this.spinnerTimeout);
        this.spinnerTimeout = setTimeout(() => this.showInlineSpinner(false), 150);
    }

    obtenerFiltroActivo(selector) {
        const activo = document.querySelector(`${selector}.activo`);
        if (!activo) return '';
        
        if (selector.includes('estado')) return activo.dataset.estado || '';
        if (selector.includes('stock')) return activo.dataset.stock || '';
        if (selector.includes('vencimiento')) return activo.dataset.vencimiento || '';
        return '';
    }

    cumpleFiltros(row, filtros) {
        const nombre = row.dataset.nombre || '';
        const estado = row.dataset.estado || '';
        const stockBajo = row.dataset.stockBajo === 'true';
        const vencimiento = row.dataset.vencimiento || '';

        // Filtro por nombre/búsqueda
        if (filtros.buscar && !nombre.includes(filtros.buscar)) {
            return false;
        }

        // Filtro por estado
        if (filtros.estado) {
            if (filtros.estado === 'Disponible' && estado !== 'Disponible') return false;
            if (filtros.estado === 'Agotado' && estado !== 'Agotado') return false;
            if (filtros.estado === 'Vencido' && estado !== 'Vencido') return false;
        }

        // Filtro por stock
        if (filtros.stock === 'bajo' && !stockBajo) {
            return false;
        }

        // Filtro por vencimiento
        if (filtros.vencimiento) {
            if (filtros.vencimiento === 'por_vencer' && vencimiento !== 'por_vencer') return false;
            if (filtros.vencimiento === 'vencidos' && vencimiento !== 'vencido') return false;
            if (filtros.vencimiento === 'buenos' && vencimiento !== 'bueno') return false;
        }

        return true;
    }

    updateResultCounter(visibles = null) {
        const totalText = document.getElementById('totalSuppliesText');
        if (!totalText) return;

        if (visibles === null) {
            visibles = this.totalSupplies;
        }
        totalText.innerHTML = `📦 <strong>${visibles}</strong> de <strong>${this.totalSupplies}</strong> insumos`;
    }

    mostrarMensajeVacio(mostrar) {
        const tbody = document.querySelector('.table tbody');
        if (!tbody) return;

        let mensajeVacio = document.getElementById('mensajeSuppliesVacio');

        if (mostrar && !mensajeVacio) {
            // Crear mensaje de no encontrados
            mensajeVacio = document.createElement('tr');
            mensajeVacio.id = 'mensajeSuppliesVacio';
            mensajeVacio.innerHTML = `
                <td colspan="8" class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">😔 No se encontraron insumos</h4>
                    <p class="text-muted">No hay insumos que coincidan con los filtros seleccionados.</p>
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltrosSupply()">
                        <i class="fas fa-eraser"></i> Quitar Filtros
                    </button>
                </td>
            `;
            tbody.appendChild(mensajeVacio);
        } else if (!mostrar && mensajeVacio) {
            mensajeVacio.remove();
        }
    }

    limpiarFiltros() {
        // Limpiar búsqueda
        const filtroNombre = document.getElementById('filtroNombre');
        if (filtroNombre) {
            filtroNombre.value = '';
        }

        // Quitar clase activo de todos los filtros
        document.querySelectorAll('.filtro-estado, .filtro-stock, .filtro-vencimiento').forEach(link => {
            link.classList.remove('activo');
        });

        // Mostrar todos los elementos
        document.querySelectorAll('.supply-row').forEach(row => {
            row.style.display = '';
        });

        // Actualizar contador
        this.updateResultCounter();

        // Quitar mensaje de vacío
        this.mostrarMensajeVacio(false);

        // Limpiar chips
        this.renderChips({ buscar: '', estado: '', stock: '', vencimiento: '' });
    }

    // UI helpers
    ensureChipsContainer() {
        const filtroNombre = document.getElementById('filtroNombre');
        if (!filtroNombre) return;
        // Buscar contenedor existente
        let chips = document.getElementById('chipsFiltros');
        if (!chips) {
            chips = document.createElement('div');
            chips.id = 'chipsFiltros';
            chips.className = 'chips-filtros mt-2';
            // Insertar justo después del input de filtro
            filtroNombre.parentNode.insertBefore(chips, filtroNombre.nextSibling);
        }
    }

    renderChips(filtros) {
        const chips = document.getElementById('chipsFiltros');
        if (!chips) return;

        chips.innerHTML = '';
        const map = [];
        if (filtros.buscar) map.push({ key: 'Búsqueda', value: filtros.buscar, onClear: () => { const i=document.getElementById('filtroNombre'); if(i){i.value='';} this.aplicarFiltros({ buscar: '' }); } });
        if (filtros.estado) map.push({ key: 'Estado', value: filtros.estado, onClear: () => { document.querySelectorAll('.filtro-estado').forEach(l=>l.classList.remove('activo')); this.aplicarFiltros({ estado: '' }); } });
        if (filtros.stock) map.push({ key: 'Stock', value: filtros.stock, onClear: () => { document.querySelectorAll('.filtro-stock').forEach(l=>l.classList.remove('activo')); this.aplicarFiltros({ stock: '' }); } });
        if (filtros.vencimiento) map.push({ key: 'Vencimiento', value: filtros.vencimiento, onClear: () => { document.querySelectorAll('.filtro-vencimiento').forEach(l=>l.classList.remove('activo')); this.aplicarFiltros({ vencimiento: '' }); } });

        if (map.length === 0) {
            chips.style.display = 'none';
            return;
        }
        chips.style.display = '';

        map.forEach(item => {
            const chip = document.createElement('span');
            chip.className = 'chip';
            chip.innerHTML = `<strong>${item.key}:</strong> ${item.value} <button type="button" class="chip-close" aria-label="Quitar">×</button>`;
            chip.querySelector('.chip-close').addEventListener('click', () => item.onClear());
            chips.appendChild(chip);
        });

        // Botón limpiar todo si hay más de 1 chip
        if (map.length > 1) {
            const clearAll = document.createElement('button');
            clearAll.type = 'button';
            clearAll.className = 'btn btn-sm btn-outline-secondary ms-2';
            clearAll.textContent = 'Quitar filtros';
            clearAll.addEventListener('click', () => this.limpiarFiltros());
            chips.appendChild(clearAll);
        }
    }

    showInlineSpinner(show) {
        const totalText = document.getElementById('totalSuppliesText');
        if (!totalText) return;

        let spinner = document.getElementById('supplyFilterSpinner');
        if (show) {
            if (!spinner) {
                spinner = document.createElement('span');
                spinner.id = 'supplyFilterSpinner';
                spinner.className = 'ms-2 inline-spinner';
                spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Aplicando filtros…';
                totalText.appendChild(spinner);
            }
        } else if (spinner) {
            spinner.remove();
        }
    }

    injectAuxStyles() {
        if (document.getElementById('supply-filters-aux-styles')) return;
        const style = document.createElement('style');
        style.id = 'supply-filters-aux-styles';
        style.textContent = `
            .chips-filtros { display:flex; flex-wrap:wrap; gap:.5rem; align-items:center; }
            .chip { background:#f1f3f5; border:1px solid #dee2e6; color:#495057; border-radius:16px; padding:4px 8px; font-size:.875rem; }
            .chip .chip-close { background:transparent; border:none; cursor:pointer; margin-left:6px; line-height:1; }
            .inline-spinner { color:#6c757d; font-size:.875rem; }
        `;
        document.head.appendChild(style);
    }
}

// Funciones globales
function limpiarFiltrosSupply() {
    if (window.supplyFilters) {
        window.supplyFilters.limpiarFiltros();
    }
}

function buscarEnTiempoReal() {
    if (window.supplyFilters) {
        window.supplyFilters.aplicarFiltros();
    }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        window.supplyFilters = new SupplyFilters();
    }, 100);
});
