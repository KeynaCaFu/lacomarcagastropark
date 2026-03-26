@extends('layouts.app')

@section('title', 'Órdenes')

@push('styles')
    <link href="{{ asset('css/order.css') }}" rel="stylesheet">
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
        <a href="{{ route('orders.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #e18018, #c9690f); border: none;">
            <i class="fas fa-plus"></i> Nueva Orden
        </a>
    </div>

    <!-- Estadísticas de órdenes -->
    <div class="order-stats-grid">
        <div class="order-stat-card">
            <div class="order-stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="order-stat-number">{{ $counts['total'] ?? 0 }}</div>
            <div class="order-stat-label">Total de Órdenes</div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #f59e0b;">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div class="order-stat-number">{{ $counts['Pending'] ?? 0 }}</div>
            <div class="order-stat-label">Pendientes</div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #ef4444;">
                <i class="fas fa-fire"></i>
            </div>
            <div class="order-stat-number">{{ $counts['En Preparación'] ?? 0 }}</div>
            <div class="order-stat-label">En Preparación</div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="order-stat-number">{{ $counts['Listo'] ?? 0 }}</div>
            <div class="order-stat-label">Listas</div>
        </div>

        <div class="order-stat-card">
            <div class="order-stat-icon" style="color: #3b82f6;">
                <i class="fas fa-truck"></i>
            </div>
            <div class="order-stat-number">{{ $counts['Delivered'] ?? 0 }}</div>
            <div class="order-stat-label">Entregadas</div>
        </div>
    </div>

    <!-- Filtros y opciones -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="buscar" class="form-label">Buscar orden...</label>
                    <input type="text" id="buscar" name="buscar" class="form-control" 
                           placeholder="Número de orden" value="{{ request('buscar') }}">
                </div>

                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" 
                           value="{{ request('fecha') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #e18018, #c9690f); border: none;">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor de órdenes -->
    @if($orders->count() > 0)
        <div class="orders-container">
            <!-- Barra lateral con lista de órdenes -->
            <div class="orders-sidebar">
                <div class="orders-list-header">
                    <i class="fas fa-list"></i>
                    Órdenes Pendientes ({{ $counts['Pending'] ?? 0 }})
                </div>

                <div class="orders-list-search">
                    <input type="text" class="form-control" placeholder="Buscar orden..." id="listSearch">
                </div>

                <div class="orders-list">
                    @foreach($orders as $order)
                        <div class="order-list-item" data-order-id="{{ $order->order_id }}">
                            <div class="order-list-item-header">
                                <span class="order-list-number">{{ $order->order_number }}</span>
                                <span class="order-list-time">{{ $order->time }}</span>
                            </div>
                            <div style="margin-bottom: 6px;">
                                <span class="status-badge {{ $order->getStatusColorClass() }}">
                                    <i class="{{ $order->getStatusIcon() }}"></i>
                                    {{ $statuses[$order->status] ?? 'Desconocido' }}
                                </span>
                            </div>
                            <div class="order-list-amount">
                                ₡{{ number_format($order->total_amount, 2) }}
                            </div>
                            @if($order->additional_notes)
                                <div class="order-list-customer" style="font-size: 11px; color: #888;">
                                    <i class="fas fa-sticky-note"></i> {{ Str::limit($order->additional_notes, 30) }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Panel de detalle de orden (será llenado por JavaScript) -->
            <div class="order-details-panel">
                <div style="text-align: center; padding: 60px 20px; color: #999;">
                    <i class="fas fa-arrow-left" style="font-size: 32px; margin-bottom: 15px; display: block;"></i>
                    <p>Selecciona una orden para ver los detalles</p>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="order-details-panel empty-state">
            <i class="fas fa-inbox empty-state-icon"></i>
            <h3 class="empty-state-title">No hay órdenes</h3>
            <p class="empty-state-text">Comienza creando una nueva orden</p>
            <a href="{{ route('orders.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #e18018, #c9690f); border: none;">
                <i class="fas fa-plus"></i> Crear Primera Orden
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar detalles de orden al hacer clic
    const orderItems = document.querySelectorAll('.order-list-item');
    const detailsPanel = document.querySelector('.order-details-panel');

    orderItems.forEach(item => {
        item.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            
            // Actualizar item activo
            document.querySelectorAll('.order-list-item').forEach(el => el.classList.remove('active'));
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
                const panel = document.querySelector('.order-details-panel');
                panel.innerHTML = '<div class="order-details-panel">' + html + '</div>';
                
                // Reasignar eventos a los botones de estado
                setupStatusButtons();
                setupDeleteButton();
            })
            .catch(error => console.error('Error:', error));
    }

    function setupStatusButtons() {
        const statusButtons = document.querySelectorAll('[data-status]');
        statusButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                const status = this.dataset.status;
                changeOrderStatus(orderId, status);
            });
        });
    }

    function setupDeleteButton() {
        const deleteBtn = document.querySelector('[data-delete]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                if (confirm('¿Estás seguro de que deseas eliminar esta orden?')) {
                    deleteOrder(orderId);
                }
            });
        }
    }

    function changeOrderStatus(orderId, status) {
        fetch(`{{ url('ordenes') }}/${orderId}/cambiar-estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Erro al cambiar estado'));
            }
        });
    }

    function deleteOrder(orderId) {
        fetch(`{{ url('ordenes') }}/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(() => {
            location.reload();
        });
    }

    // Búsqueda en lista
    const searchInput = document.getElementById('listSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.order-list-item').forEach(item => {
                const orderNumber = item.querySelector('.order-list-number').textContent.toLowerCase();
                const notes = item.querySelector('.order-list-customer')?.textContent.toLowerCase() || '';
                
                if (orderNumber.includes(searchTerm) || notes.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Cargar primera orden por defecto
    if (orderItems.length > 0) {
        orderItems[0].click();
    }
});
</script>
@endpush
@endsection
