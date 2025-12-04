@extends('layouts.app')

@section('title', 'Dashboard - La Comarca Admin')

@section('content')

<div class="dashboard-content">
    <div class="row">
    
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <h3 id="totalInsumos">0</h3>
                    <p>Total Insumos</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+12%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-info">
                    <h3 id="totalProveedores">0</h3>
                    <p>Total Proveedores</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+5%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3 id="insumosLowStock">0</h3>
                    <p>Stock Bajo</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-down text-warning"></i>
                    <span class="text-warning">-2</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3 id="valorTotal">₡0</h3>
                    <p>Valor Total Inventario</p>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+8%</span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
       
        <div class="col-lg-6 mb-4">
            <div class="action-card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="#" class="action-btn action-btn-info" onclick="showComingSoon()">
                            <div class="action-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="action-text">
                                <span class="action-title">Ver Reportes</span>
                                <span class="action-subtitle">Estadísticas y análisis</span>
                            </div>
                        </a>
                        
                        <a href="#" class="action-btn action-btn-warning" onclick="showComingSoon()">
                            <div class="action-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="action-text">
                                <span class="action-title">Exportar Datos</span>
                                <span class="action-subtitle">Descargar información</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
       
        <div class="col-lg-6 mb-4">
            <div class="action-card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Sistema iniciado correctamente</h6>
                                <p class="text-muted">Bienvenido a La Comarca Admin</p>
                                <small class="text-muted">Hace un momento</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Dashboard cargado</h6>
                                <p class="text-muted">Estadísticas actualizadas</p>
                                <small class="text-muted">Ahora</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="activity-content">
                                <h6>Base de datos conectada</h6>
                                <p class="text-muted">Conexión establecida con bdsage</p>
                                <small class="text-muted">Hace 1 minuto</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        
        <div class="col-lg-4 mb-4">
            <div class="alert-card alert-warning">
                <div class="alert-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>Alerta de Stock Bajo</h6>
                </div>
                <div class="alert-body">
                    <p>Hay productos que requieren atención</p>
                    <button class="btn btn-warning btn-sm" onclick="checkLowStock()">
                        Ver Productos
                    </button>
                </div>
            </div>
        </div>
        
   
        <div class="col-lg-4 mb-4">
            <div class="alert-card alert-success">
                <div class="alert-header">
                    <i class="fas fa-check-circle"></i>
                    <h6>Estado del Sistema</h6>
                </div>
                <div class="alert-body">
                    <p>Todos los servicios funcionando correctamente</p>
                    <button class="btn btn-success btn-sm" onclick="checkSystemStatus()">
                        Ver Detalles
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="col-lg-4 mb-4">
            <div class="alert-card alert-info">
                <div class="alert-header">
                    <i class="fas fa-lightbulb"></i>
                    <h6>Consejo del Día</h6>
                </div>
                <div class="alert-body">
                    <p>Mantén siempre actualizado el stock mínimo de tus productos</p>
                    <button class="btn btn-info btn-sm" onclick="showTips()">
                        Más Consejos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

.dashboard-content {
    padding: 30px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
}


.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #485a1a, #ff9900);
}

.stat-icon {
    width: 65px;
    height: 65px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    position: relative;
}

.stat-icon.bg-primary {
    background: linear-gradient(135deg, #485a1a, #5a6d20);
}

.stat-icon.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.stat-icon.bg-warning {
    background: linear-gradient(135deg, #ffc107, #ff9900);
}

.stat-icon.bg-info {
    background: linear-gradient(135deg, #17a2b8, #007bff);
}

.stat-icon i {
    font-size: 1.8rem;
    color: white;
}

.stat-info {
    flex: 1;
}

.stat-info h3 {
    margin: 0;
    font-size: 2.2rem;
    font-weight: 700;
    color: #232c0c;
    line-height: 1;
}

.stat-info p {
    margin: 5px 0 0 0;
    color: #6c757d;
    font-size: 0.95rem;
    font-weight: 500;
}

.stat-trend {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.stat-trend i {
    font-size: 1.2rem;
    margin-bottom: 2px;
}

.stat-trend span {
    font-size: 0.85rem;
    font-weight: 600;
}


.action-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    height: 100%;
    transition: all 0.3s ease;
}

.action-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.action-card .card-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #e9ecef;
    padding: 20px 25px;
    border-radius: 15px 15px 0 0;
    border-bottom: none;
}

.action-card .card-header h5 {
    margin: 0;
    color: #232c0c;
    font-weight: 600;
    font-size: 1.1rem;
}

.action-card .card-body {
    padding: 25px;
}


.quick-actions-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.action-btn {
    display: flex;
    align-items: center;
    padding: 18px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    background: #f8f9fa;
}

.action-btn:hover {
    transform: translateX(5px);
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.action-btn-primary:hover {
    background: #485a1a;
    color: white;
    border-color: #485a1a;
}

.action-btn-success:hover {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.action-btn-info:hover {
    background: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.action-btn-warning:hover {
    background: #ffc107;
    color: #212529;
    border-color: #ffc107;
}

.action-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    background: rgba(72, 90, 26, 0.1);
    color: #485a1a;
}

.action-btn:hover .action-icon {
    background: rgba(255, 255, 255, 0.2);
    color: inherit;
}

.action-icon i {
    font-size: 1.4rem;
}

.action-text {
    display: flex;
    flex-direction: column;
}

.action-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 2px;
    color: #232c0c;
}

.action-btn:hover .action-title {
    color: inherit;
}

.action-subtitle {
    font-size: 0.85rem;
    color: #6c757d;
    opacity: 0.8;
}

.action-btn:hover .action-subtitle {
    color: inherit;
    opacity: 0.9;
}


.activity-timeline {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.activity-icon i {
    font-size: 1rem;
    color: white;
}

.activity-content h6 {
    margin: 0 0 5px 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #232c0c;
}

.activity-content p {
    margin: 0 0 5px 0;
    font-size: 0.85rem;
    color: #6c757d;
}

.activity-content small {
    font-size: 0.75rem;
    color: #adb5bd;
}


.alert-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    height: 100%;
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.alert-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.alert-card.alert-warning {
    border-left-color: #ffc107;
}

.alert-card.alert-success {
    border-left-color: #28a745;
}

.alert-card.alert-info {
    border-left-color: #17a2b8;
}

.alert-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.alert-header i {
    font-size: 1.2rem;
    margin-right: 10px;
}

.alert-warning .alert-header i {
    color: #ffc107;
}

.alert-success .alert-header i {
    color: #28a745;
}

.alert-info .alert-header i {
    color: #17a2b8;
}

.alert-header h6 {
    margin: 0;
    font-weight: 600;
    color: #232c0c;
}

.alert-body p {
    margin-bottom: 15px;
    color: #6c757d;
    font-size: 0.9rem;
}

.alert-body .btn {
    font-size: 0.85rem;
    padding: 6px 15px;
}


@media (max-width: 991.98px) {
    .dashboard-content {
        padding: 20px 15px;
    }
    
    .quick-actions-grid {
        gap: 10px;
    }
    
    .action-btn {
        padding: 15px;
    }
    
    .stat-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 767.98px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .stat-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .stat-trend {
        align-items: center;
        margin-top: 10px;
    }
    
    .action-btn {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .action-icon {
        margin-right: 0;
        margin-bottom: 10px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    loadDashboardStats();
});

function loadDashboardStats() {
    // Simular carga de estadísticas con datos más realistas
    setTimeout(() => {
        // Animar los números
        animateNumber('totalInsumos', 0, 24, 1000);
        animateNumber('totalProveedores', 0, 8, 800);
        animateNumber('insumosLowStock', 0, 3, 600);
        
        // Actualizar valor total con formato de moneda
        document.getElementById('valorTotal').textContent = '$2,450,000';
    }, 300);
}

function animateNumber(elementId, start, end, duration) {
    const element = document.getElementById(elementId);
    const range = end - start;
    const startTime = performance.now();

    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.floor(start + (range * progress));
        
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        }
    }
    
    requestAnimationFrame(updateNumber);
}

function showComingSoon() {
    // Crear notificación elegante en lugar de alert
    showNotification('Esta funcionalidad estará disponible próximamente', 'info');
}

function checkLowStock() {
    // Simular verificación de stock bajo
    showNotification('Verificando productos con stock bajo...', 'warning');
    setTimeout(() => {
        window.location.href = "{{ route('dashboard') }}";
    }, 1000);
}

function checkSystemStatus() {
    showNotification('Sistema funcionando correctamente ✓', 'success');
}

function showTips() {
    showNotification('💡 Consejo: Configura alertas automáticas para el stock mínimo', 'info');
}

function showNotification(message, type) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${getNotificationIcon(type)} me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Estilos para la notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle',
        'danger': 'exclamation-circle'
    };
    return icons[type] || 'info-circle';
}
</script>
@endpush
