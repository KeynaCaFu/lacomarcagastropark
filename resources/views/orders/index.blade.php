@extends('layouts.app')

@section('title', 'Órdenes')

@push('styles')
    <link href="{{ asset('css/order.css') }}" rel="stylesheet">
    <style>
        /* Scrollbar personalizado para la lista de órdenes */
        .orders-grid-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .orders-grid-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .orders-grid-list::-webkit-scrollbar-thumb {
            background: #e18018;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        
        .orders-grid-list::-webkit-scrollbar-thumb:hover {
            background: #d97c13;
        }
        
        /* Para Firefox */
        .orders-grid-list {
            scrollbar-width: thin;
            scrollbar-color: #e18018 #f1f5f9;
        }
    </style>
@endpush

@section('content')
<div class="orders-dashboard">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-shopping-bag"></i> Órdenes
            </h1>
            <p class="text-muted mb-0">Gestión de órdenes del establecimiento</p>
        </div>
    </div>

    <!-- Estadísticas de órdenes -->
    <div class="order-stats-grid">
        <div class="order-stat-card">
            <div class="order-stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <div class="order-stat-number">{{ $counts['total'] ?? 0 }}</div>
                <div class="order-stat-label">Total de Órdenes</div>
            </div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #f59e0b;">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div>
                <div class="order-stat-number">{{ $counts['Pending'] ?? 0 }}</div>
                <div class="order-stat-label">Pendientes</div>
            </div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #f59e0b;">
                <i class="fas fa-fire"></i>
            </div>
            <div>
                <div class="order-stat-number">{{ $counts['Preparing'] ?? 0 }}</div>
                <div class="order-stat-label">En Preparación</div>
            </div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #f59e0b;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="order-stat-number">{{ $counts['Ready'] ?? 0 }}</div>
                <div class="order-stat-label">Listas</div>
            </div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #f59e0b;">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <div class="order-stat-number">{{ $counts['Delivered'] ?? 0 }}</div>
                <div class="order-stat-label">Entregadas</div>
            </div>
        </div>
    </div>

    <!-- Contenedor de órdenes con Tabs -->
    @if($orders->count() > 0)
        <div class="orders-container-tabs">
            <!-- Tabs de estados -->
            <div class="orders-tabs-header">
                @foreach($statuses as $key => $label)
                    <button class="order-tab {{ $loop->first ? 'active' : '' }}" data-status="{{ $key }}">
                        <span class="order-tab-label">{{ $label }}</span>
                        <span class="order-tab-count">{{ $counts[$key] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Contenedor con lista central y detalles -->
            <div class="orders-main-layout">
                <!-- Lista de órdenes (Centro-Izquierda) -->
                <div class="orders-list-container">
                    <div class="orders-list-search">
                        <input type="text" class="form-control" placeholder="Buscar orden..." id="listSearch">
                    </div>

                    <div class="orders-grid-list">
                        @foreach($orders as $order)
                            <div class="order-card-item" data-order-id="{{ $order->order_id }}" data-status="{{ $order->status }}">
                                <div class="order-card-header">
                                    <div>
                                        <div class="order-card-number">{{ $order->order_number }}</div>
                                        <div class="order-card-customer">Cliente</div>
                                    </div>
                                    <div class="order-card-time">{{ $order->time }}</div>
                                </div>
                                <div class="order-card-status">
                                    <span class="status-badge {{ $order->getStatusColorClass() }} status-badge-clickable" 
                                          data-order-id="{{ $order->order_id }}"
                                          style="cursor: pointer; position: relative;">
                                        <i class="{{ $order->getStatusIcon() }}"></i>
                                        {{ $statuses[$order->status] ?? 'Desconocido' }}
                                        <i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 10px;"></i>
                                        
                                        <!-- Dropdown de estados -->
                                        <div class="status-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 180px;">
                                            @foreach($statuses as $statusKey => $statusLabel)
                                                @if($statusKey !== $order->status)
                                                    <button type="button" class="status-dropdown-item status-dropdown-item-{{ $statusKey }}" data-status="{{ $statusKey }}">
                                                        <i class="fas {{ match($statusKey) {
                                                            'Pending' => 'fa-hourglass-start',
                                                            'Preparing' => 'fa-fire',
                                                            'Ready' => 'fa-check-circle',
                                                            'Delivered' => 'fa-truck',
                                                            'Cancelled' => 'fa-times-circle',
                                                            default => 'fa-info-circle'
                                                        } }}"></i>
                                                        {{ $statusLabel }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </span>
                                </div>
                                <div class="order-card-footer">
                                    <div class="order-card-amount">₡{{ number_format($order->total_amount, 2) }}</div>
                                    <div class="order-card-items">{{ $order->items()->count() }} items</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Panel de detalle de orden (Derecha) -->
                <div class="order-details-panel-large">
                    <div style="text-align: center; padding: 80px 40px; color: #999;">
                        <i class="fas fa-arrow-left" style="font-size: 48px; margin-bottom: 20px; display: block; opacity: 0.5;"></i>
                        <p style="font-size: 16px;">Selecciona una orden para ver los detalles</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="card mt-4">
            <div class="order-details-panel empty-state">
                <i class="fas fa-inbox empty-state-icon"></i>
                <h3 class="empty-state-title">No hay órdenes</h3>
                <p class="empty-state-text">Aún no hay órdenes registradas</p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tabs de filtrado
    const tabs = document.querySelectorAll('.order-tab');
    const orderCards = document.querySelectorAll('.order-card-item');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.dataset.status;
            
            // Actualizar tab activo
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Filtrar órdenes
            orderCards.forEach(card => {
                if (card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Cargar detalles de orden al hacer clic
    orderCards.forEach(card => {
        card.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            
            // Actualizar orden activa
            document.querySelectorAll('.order-card-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            // Cargar detalles
            loadOrderDetails(orderId);
        });
    });

    function loadOrderDetails(orderId) {
        fetch(`{{ url('ordenes') }}/${orderId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                const panel = document.querySelector('.order-details-panel-large');
                panel.innerHTML = '<div class="order-details-wrapper">' + html + '</div>';
            })
            .catch(error => console.error('Error:', error));
    }

    function setupStatusButtons() {
        // Usa delegación para clicks en badges de estado en las tarjetas
        const ordersList = document.querySelector('.orders-grid-list');
        
        if (!ordersList) return;
        
        // Delegado para clicks en status badge
        ordersList.addEventListener('click', function(e) {
            const badge = e.target.closest('.status-badge-clickable');
            if (!badge) return;
            
            e.stopPropagation();
            
            // Cerrar otros dropdowns primero
            ordersList.querySelectorAll('.status-dropdown').forEach(d => {
                if (d !== badge.querySelector('.status-dropdown')) {
                    d.style.display = 'none';
                }
            });
            
            // Toggle este dropdown
            const dropdown = badge.querySelector('.status-dropdown');
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            }
        });

        // Delegado para items del dropdown
        ordersList.addEventListener('click', function(e) {
            if (!e.target.closest('.status-dropdown-item')) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const item = e.target.closest('.status-dropdown-item');
            const badge = item.closest('.status-badge-clickable');
            
            if (badge) {
                const orderId = badge.dataset.orderId;
                const newStatus = item.dataset.status;
                
                changeOrderStatus(orderId, newStatus);
            }
        });
    }

    // Event listener global para cerrar dropdowns (una sola vez)
    if (!window.dropdownCloserAttached) {
        document.addEventListener('click', function(e) {
            // Solo cerrar si NO es un status badge o dropdown
            if (!e.target.closest('.status-badge-clickable') && !e.target.closest('.status-dropdown')) {
                document.querySelectorAll('.status-dropdown').forEach(d => d.style.display = 'none');
            }
        });
        window.dropdownCloserAttached = true;
    }

    function changeOrderStatus(orderId, status) {
        console.log('Cambiando estado - Order:', orderId, 'Nuevo status:', status);
        
        // Mapear nombres de estado para mensajes amigables
        const statusNames = {
            'Pending': 'Pendiente',
            'Preparing': 'En Preparación',
            'Ready': 'Listo',
            'Delivered': 'Entregado',
            'Cancelled': 'Cancelada'
        };
        
        const statusIcons = {
            'Pending': 'fa-hourglass-start',
            'Preparing': 'fa-fire',
            'Ready': 'fa-check-circle',
            'Delivered': 'fa-truck',
            'Cancelled': 'fa-times-circle'
        };
        
        // Mapear clases CSS según estado (debe coincidir con getStatusColorClass() del modelo)
        const statusColorClasses = {
            'Pending': 'status-pending',
            'Preparing': 'status-preparation',
            'Ready': 'status-ready',
            'Delivered': 'status-delivered',
            'Cancelled': 'status-cancelled'
        };
        
        // Mostrar confirmación con SweetAlert
        Swal.fire({
            title: '¿Cambiar estado de la orden?',
            html: `<p style="margin-bottom: 16px; color: #666;">¿Deseas cambiar el estado de la orden a:</p>
                   <div style="display: inline-block; padding: 10px 20px; background: #fff7ed; border-radius: 8px; border: 2px solid #e18018;">
                       <i class="fas ${statusIcons[status]}" style="color: #e18018; margin-right: 8px; font-size: 18px;"></i>
                       <strong style="color: #e18018; font-size: 18px;">${statusNames[status]}</strong>
                   </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e18018',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Hacer el fetch
                fetch(`{{ url('ordenes') }}/${orderId}/cambiar-estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success || data.success !== false) {
                        
                        // Actualizar el estado de la tarjeta en el DOM
                        const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                        if (orderCard) {
                            // Obtener el estado anterior
                            const previousStatus = orderCard.dataset.status;
                            
                            // Actualizar el atributo data-status
                            orderCard.dataset.status = status;
                            
                            // Actualizar contadores en los tabs
                            const previousTab = document.querySelector(`[data-status="${previousStatus}"]`);
                            const newTab = document.querySelector(`[data-status="${status}"]`);
                            
                            if (previousTab) {
                                const previousCount = previousTab.querySelector('.order-tab-count');
                                if (previousCount) {
                                    let count = parseInt(previousCount.textContent) || 0;
                                    previousCount.textContent = Math.max(0, count - 1);
                                }
                            }
                            
                            if (newTab) {
                                const newCount = newTab.querySelector('.order-tab-count');
                                if (newCount) {
                                    let count = parseInt(newCount.textContent) || 0;
                                    newCount.textContent = count + 1;
                                }
                            }
                            
                            // Actualizar contadores en las tarjetas de estadísticas
                            const statCards = document.querySelectorAll('.order-stat-card');
                            statCards.forEach(card => {
                                const label = card.querySelector('.order-stat-label');
                                if (!label) return;
                                
                                if (label.textContent.includes('Pendientes') && previousStatus === 'Pending') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = Math.max(0, count - 1);
                                    }
                                }
                                if (label.textContent.includes('Pendientes') && status === 'Pending') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = count + 1;
                                    }
                                }
                                if (label.textContent.includes('En Preparación') && previousStatus === 'Preparing') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = Math.max(0, count - 1);
                                    }
                                }
                                if (label.textContent.includes('En Preparación') && status === 'Preparing') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = count + 1;
                                    }
                                }
                                if (label.textContent.includes('Listas') && previousStatus === 'Ready') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = Math.max(0, count - 1);
                                    }
                                }
                                if (label.textContent.includes('Listas') && status === 'Ready') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = count + 1;
                                    }
                                }
                                if (label.textContent.includes('Entregadas') && previousStatus === 'Delivered') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = Math.max(0, count - 1);
                                    }
                                }
                                if (label.textContent.includes('Entregadas') && status === 'Delivered') {
                                    const statNumber = card.querySelector('.order-stat-number');
                                    if (statNumber) {
                                        let count = parseInt(statNumber.textContent) || 0;
                                        statNumber.textContent = count + 1;
                                    }
                                }
                            });
                            
                            // Actualizar el badge de estado con las clases CSS correctas
                            const statusBadge = orderCard.querySelector('.status-badge-clickable');
                            if (statusBadge) {
                                // Remover todas las clases de color antiguas
                                statusBadge.classList.remove('status-pending', 'status-preparation', 'status-ready', 'status-delivered', 'status-cancelled');
                                // Agregar la nueva clase de color
                                statusBadge.classList.add(statusColorClasses[status]);
                                
                                // Obtener todos los estados disponibles desde el HTML
                                const allStatuses = ['Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled'];
                                const statusLabelsMap = {
                                    'Pending': 'Pendiente',
                                    'Preparing': 'En Preparación',
                                    'Ready': 'Listo',
                                    'Delivered': 'Entregado',
                                    'Cancelled': 'Cancelada'
                                };
                                
                                // Construir el dropdown dinámicamente
                                let dropdownHTML = '<div class="status-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 180px;">';
                                
                                allStatuses.forEach(statusKey => {
                                    if (statusKey !== status) {
                                        dropdownHTML += `
                                            <button type="button" class="status-dropdown-item status-dropdown-item-${statusKey}" data-status="${statusKey}">
                                                <i class="fas ${statusIcons[statusKey]}"></i>
                                                ${statusLabelsMap[statusKey]}
                                            </button>
                                        `;
                                    }
                                });
                                
                                dropdownHTML += '</div>';
                                
                                // Actualizar el contenido del badge
                                statusBadge.innerHTML = `
                                    <i class="fas ${statusIcons[status]}"></i>
                                    ${statusNames[status]}
                                    <i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 10px;"></i>
                                    ${dropdownHTML}
                                `;
                            }
                            
                            // Detectar el estado filtrado actualmente
                            const activeTab = document.querySelector('.order-tab.active');
                            if (activeTab) {
                                const activeStatus = activeTab.dataset.status;
                                
                                // Si la orden cambió de estado y no coincide con el estado activo, ocultarla
                                if (status !== activeStatus) {
                                    orderCard.style.display = 'none';
                                }
                            }
                        }
                        
                        // Mostrar mensaje de éxito con toast
                        swToast.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            html: `Estado cambiado a <strong style="color: #e18018;">${statusNames[status]}</strong>`
                        });
                    } else {
                        // Error en la respuesta
                        Swal.fire({
                            title: 'Error',
                            text: data.error || data.message || 'Error al cambiar el estado',
                            icon: 'error',
                            confirmButtonColor: '#e18018'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error en la solicitud: ' + error.message,
                        icon: 'error',
                        confirmButtonColor: '#e18018'
                    });
                });
            }
        });
    }

    // Búsqueda en lista
    const searchInput = document.getElementById('listSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.order-card-item').forEach(item => {
                if (item.style.display === 'none') return;
                
                const orderNumber = item.querySelector('.order-card-number').textContent.toLowerCase();
                const customer = item.querySelector('.order-card-customer').textContent.toLowerCase();
                
                if (orderNumber.includes(searchTerm) || customer.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Cargar primera orden por defecto
    if (orderCards.length > 0) {
        // Hacer clic en la primera pestaña para filtrar por estado inicial (Pendiente)
        const firstTab = document.querySelector('.order-tab.active');
        if (firstTab) {
            firstTab.click();
        }
    }

    // Inicializar eventos de cambio de estado
    setupStatusButtons();
});
</script>
@endpush
@endsection
