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
            max-height: 500px;
            overflow-y: auto;
        }

        /* ===== RESPONSIVE STYLES ===== */
        .orders-dashboard {
            padding: 15px;
        }

        /* Header responsivo */
        .orders-dashboard > .d-flex {
            flex-direction: column;
            gap: 15px;
        }

        .orders-dashboard > .d-flex > div:first-child {
            width: 100%;
        }

        .orders-dashboard > .d-flex > div:last-child {
            width: 100%;
            justify-content: flex-start !important;
            flex-wrap: wrap;
        }

        .orders-dashboard h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .orders-dashboard p {
            font-size: 13px;
        }

        /* Botón Nueva Orden responsivo */
        #newOrderBtn {
            width: 100%;
            justify-content: center;
            padding: 12px 16px !important;
            font-size: 13px;
        }

        /* Grid de estadísticas responsivo */
        .order-stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .order-stat-card {
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            border: 1px solid #e5e7eb;
        }

        .order-stat-icon {
            font-size: 24px;
            min-width: 40px;
            text-align: center;
        }

        .order-stat-number {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .order-stat-label {
            font-size: 11px;
            color: #9ca3af;
        }

        /* Tabs header responsivo */
        .orders-tabs-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .orders-tabs-header > div:first-child {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            flex: 1;
            min-width: 0;
        }

        .order-tab {
            padding: 8px 12px;
            font-size: 12px;
            white-space: nowrap;
            border-radius: 6px;
        }

        /* Main layout responsivo */
        .orders-main-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .orders-list-container,
        .order-details-panel-large {
            min-height: 300px;
            border-radius: 8px;
            background: white;
            border: 1px solid #e5e7eb;
        }

        .orders-list-search {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .orders-list-search input {
            font-size: 13px;
            padding: 8px 12px !important;
        }

        .orders-grid-list {
            max-height: 400px;
        }

        .order-card-item {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .order-card-number {
            font-weight: 600;
            color: #1f2937;
            font-size: 13px;
        }

        .order-card-customer {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .order-card-time {
            font-size: 11px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .order-card-status {
            margin: 8px 0;
        }

        .order-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #f3f4f6;
        }

        .order-card-amount {
            font-weight: 600;
            color: #e18018;
            font-size: 13px;
        }

        .order-card-items {
            font-size: 11px;
            color: #9ca3af;
        }

        .order-details-panel-large {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            min-height: 400px;
        }

        /* ===== TABLET (768px+) ===== */
        @media (min-width: 768px) {
            .orders-dashboard {
                padding: 20px;
            }

            .orders-dashboard > .d-flex {
                flex-direction: row;
            }

            .orders-dashboard > .d-flex > div:first-child {
                width: auto;
            }

            .orders-dashboard > .d-flex > div:last-child {
                width: auto;
                justify-content: flex-end !important;
            }

            #newOrderBtn {
                width: auto;
                padding: 10px 20px !important;
            }

            .order-stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .orders-main-layout {
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .orders-grid-list {
                max-height: 500px;
            }

            .orders-list-container {
                min-height: 500px;
            }

            .order-details-panel-large {
                min-height: 500px;
                padding: 30px 20px;
            }

            .order-tab {
                padding: 10px 16px;
                font-size: 13px;
            }
        }

        /* ===== DESKTOP (1024px+) ===== */
        @media (min-width: 1024px) {
            .orders-dashboard {
                padding: 25px;
            }

            .order-stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }

            .order-stat-card {
                padding: 15px;
            }

            .order-stat-icon {
                font-size: 28px;
            }

            .order-stat-number {
                font-size: 18px;
            }

            .order-tab {
                padding: 12px 20px;
                font-size: 14px;
            }

            .orders-main-layout {
                grid-template-columns: 1.2fr 1fr;
                gap: 25px;
            }

            .orders-list-container {
                min-height: 600px;
            }

            .order-details-panel-large {
                min-height: 600px;
                padding: 40px;
                margin-right: 10px;
                margin-left: -118px;
            }

            .orders-grid-list {
                max-height: 550px;
            }
        }

        /* ===== LARGE DESKTOP (1440px+) ===== */
        @media (min-width: 1440px) {
            .orders-main-layout {
                grid-template-columns: 1.3fr 1fr;
            }
        }

        /* ===== MOBILE PEQUEÑO (<480px) ===== */
        @media (max-width: 479px) {
            .orders-dashboard {
                padding: 10px;
            }

            .orders-dashboard h1 {
                font-size: 20px;
            }

            .order-stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .order-stat-card {
                padding: 10px;
                gap: 10px;
            }

            .order-stat-icon {
                font-size: 20px;
                min-width: 30px;
            }

            .order-stat-number {
                font-size: 14px;
            }

            .order-stat-label {
                font-size: 10px;
            }

            .orders-list-search input {
                font-size: 12px;
            }

            .order-card-item {
                padding: 10px;
            }

            .order-card-number {
                font-size: 12px;
            }

            .btn-history-responsive {
                padding: 6px 10px !important;
                font-size: 11px !important;
            }

            .btn-history-responsive span {
                display: none;
            }

            .btn-history-responsive i {
                margin: 0 !important;
            }

            .order-details-panel-large {
                padding: 20px 15px;
                min-height: auto;
            }

            .orders-grid-list {
                max-height: 300px;
            }
        }

        @media (min-width: 480px) and (max-width: 767px) {
            .btn-history-responsive {
                padding: 7px 12px !important;
                font-size: 12px !important;
            }
        }

        /* Paginación Responsive */
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
        }

        @media (max-width: 480px) {
            .pagination a,
            .pagination span {
                padding: 6px 10px;
                font-size: 12px;
                min-width: 32px;
            }

            .d-flex.justify-content-center {
                margin-top: 15px;
            }
        }

        /* Modal Responsive */
        #createOrderModal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: none !important;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 10px;
        }

        #createOrderModal.show {
            display: flex !important;
        }

        .create-order-modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 90vw;
            max-height: 90vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 480px) {
            .create-order-modal-content {
                max-width: 600px;
            }
        }

        @media (min-width: 768px) {
            .create-order-modal-content {
                max-width: 800px;
            }
        }

        @media (min-width: 1024px) {
            .create-order-modal-content {
                max-width: 1000px;
            }
        }

        /* Ajustes generales para touch/mobile */
        @media (hover: none) and (pointer: coarse) {
            button,
            a {
                min-height: 44px;
                min-width: 44px;
                padding: 12px !important;
            }

            .order-tab {
                min-height: 40px;
                min-width: 70px;
            }
        }

        /* Scroll suave en mobile */
        @media (max-width: 767px) {
            .orders-grid-list,
            .order-details-panel-large {
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Status badge responsive */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Dropdown responsive */
        .status-dropdown {
            min-width: 160px !important;
            z-index: 1000;
        }

        @media (max-width: 640px) {
            .status-dropdown {
                min-width: 140px !important;
            }

            .status-dropdown-item {
                font-size: 12px;
                padding: 8px 10px !important;
            }
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
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-warning" id="newOrderBtn" style="background: linear-gradient(135deg, #e18018, #c9690f); border: none; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Nueva Orden
            </button>
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
    <div class="orders-container-tabs">
        <!-- Tabs de estados -->
        <div class="orders-tabs-header" style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; flex: 1; min-width: 0;">
                @foreach($statuses as $key => $label)
                    <button class="order-tab {{ $loop->first ? 'active' : '' }}" data-status="{{ $key }}">
                        <span class="order-tab-label">{{ $label }}</span>
                        <span class="order-tab-count">{{ $counts[$key] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>
            <a href="{{ route('orders.receipt.history') }}" class="btn-history-responsive" style="background: linear-gradient(135deg, #e18018, #c9690f); color: white; padding: 7px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none; font-size: 13px; white-space: nowrap; transition: transform 0.2s ease, box-shadow 0.2s ease; flex-shrink: 0;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(225, 128, 24, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <i class="fas fa-history"></i> <span>Ver Historial</span>
            </a>
        </div>

        <!-- Contenedor con lista central y detalles -->
        <div class="orders-main-layout">
            <!-- Lista de órdenes (Centro-Izquierda) -->
            <div class="orders-list-container">
                @if($orders->count() > 0)
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
                                    @php
                                        // Flujo de estados permitidos
                                        $statusFlow = [
                                            'Pending' => ['Preparing', 'Cancelled'],
                                            'Preparing' => ['Ready', 'Cancelled'],
                                            'Ready' => ['Delivered', 'Cancelled'],
                                            'Delivered' => [],
                                            'Cancelled' => []
                                        ];
                                        $allowedStatuses = $statusFlow[$order->status] ?? [];
                                        $hasAllowedStatuses = count($allowedStatuses) > 0;
                                    @endphp
                                    <span class="status-badge {{ $order->getStatusColorClass() }} status-badge-clickable" 
                                          data-order-id="{{ $order->order_id }}"
                                          style="cursor: {{ $hasAllowedStatuses ? 'pointer' : 'default' }}; position: relative; {{ !$hasAllowedStatuses ? 'opacity: 0.7;' : '' }}">
                                        <i class="{{ $order->getStatusIcon() }}"></i>
                                        {{ $statuses[$order->status] ?? 'Desconocido' }}
                                        @if($hasAllowedStatuses)
                                            <i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 10px;"></i>
                                        @endif
                                        
                                        <!-- Dropdown de estados -->
                                        <div class="status-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 180px;">
                                            @php
                                                // Flujo de estados permitidos
                                                $statusFlow = [
                                                    'Pending' => ['Preparing', 'Cancelled'],
                                                    'Preparing' => ['Ready', 'Cancelled'],
                                                    'Ready' => ['Delivered', 'Cancelled'],
                                                    'Delivered' => [],
                                                    'Cancelled' => []
                                                ];
                                                $allowedStatuses = $statusFlow[$order->status] ?? [];
                                            @endphp
                                            @foreach($statuses as $statusKey => $statusLabel)
                                                @if(in_array($statusKey, $allowedStatuses))
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
                @else
                    <div style="text-align: center; padding: 60px 20px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; display: block; opacity: 0.3;"></i>
                        <p style="font-size: 14px;">No hay órdenes en esta categoría</p>
                    </div>
                @endif
            </div>

            <!-- Panel de detalle de orden (Derecha) -->
            <div class="order-details-panel-large">
                @if($orders->count() > 0)
                    <div style="text-align: center; padding: 80px 40px; color: #999;">
                        <i class="fas fa-arrow-left" style="font-size: 48px; margin-bottom: 20px; display: block; opacity: 0.5;"></i>
                        <p style="font-size: 16px;">Selecciona una orden para ver los detalles</p>
                    </div>
                @else
                    <div style="text-align: center; padding: 80px 40px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; display: block; opacity: 0.3;"></i>
                        <p style="font-size: 16px;">No hay órdenes para mostrar</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Paginación -->
    @if($orders->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>

<!-- Incluir modal para crear orden -->
@include('orders._create_order_modal')

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
                
                // Agregar botones de comprobante si la orden está Entregada
                setupReceiptButtons(orderId);
            })
            .catch(error => console.error('Error:', error));
    }

    function setupReceiptButtons(orderId) {
        // Obtener el elemento de la orden actual
        const orderCard = document.querySelector(`.order-card-item[data-order-id="${orderId}"]`);
        if (!orderCard || orderCard.dataset.status !== 'Delivered') {
            return;
        }

        // Buscar donde agregar los botones (en el panel de detalles)
        const detailsWrapper = document.querySelector('.order-details-wrapper');
        if (!detailsWrapper) return;

        // Crear contenedor de botones si no existe
        let receiptButtonsContainer = detailsWrapper.querySelector('.receipt-buttons-container');
        if (!receiptButtonsContainer) {
            receiptButtonsContainer = document.createElement('div');
            receiptButtonsContainer.className = 'receipt-buttons-container';
            receiptButtonsContainer.style.cssText = 'margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; flex-wrap: wrap;';
            
            // Agregar después del último elemento del panel
            detailsWrapper.appendChild(receiptButtonsContainer);
        } else {
            receiptButtonsContainer.innerHTML = '';
        }

        // Botón de Descargar Comprobante
        const downloadBtn = document.createElement('button');
        downloadBtn.className = 'btn btn-info';
        downloadBtn.style.cssText = 'flex: 1; min-width: 150px; background: #3b82f6; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px;';
        downloadBtn.innerHTML = '<i class="fas fa-download"></i> Descargar Comprobante';
        downloadBtn.addEventListener('click', () => downloadReceipt(orderId));

        // Botón de Reenviar Comprobante
        const resendBtn = document.createElement('button');
        resendBtn.className = 'btn btn-warning';
        resendBtn.style.cssText = 'flex: 1; min-width: 150px; background: #10b981; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px;';
        resendBtn.innerHTML = '<i class="fas fa-envelope"></i> Reenviar al Email';
        resendBtn.addEventListener('click', (e) => resendReceipt(orderId, e.target.closest('button')));

        // Botón Ver Historial
        const historyBtn = document.createElement('a');
        historyBtn.className = 'btn btn-secondary';
        historyBtn.href = '{{ route("orders.receipt.history") }}';
        historyBtn.style.cssText = 'flex: 1; min-width: 150px; background: #8b5cf6; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none;';
        historyBtn.innerHTML = '<i class="fas fa-history"></i> Ver Historial';

        receiptButtonsContainer.appendChild(downloadBtn);
        receiptButtonsContainer.appendChild(resendBtn);
        receiptButtonsContainer.appendChild(historyBtn);
    }

    function downloadReceipt(orderId) {
        fetch(`{{ url('ordenes') }}/${orderId}/comprobante/descargar`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al descargar el comprobante');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Comprobante_${orderId}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                a.remove();

                Swal.fire({
                    icon: 'success',
                    title: '¡Descargado!',
                    text: 'El comprobante ha sido descargado exitosamente',
                    confirmButtonColor: '#c9690f'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No fue posible descargar el comprobante. Asegúrate de que exista.',
                    confirmButtonColor: '#c9690f'
                });
            });
    }

    function resendReceipt(orderId, btn) {
        Swal.fire({
            icon: 'question',
            title: '¿Reenviar Comprobante?',
            text: 'Se reenviará el comprobante al correo del cliente',
            showCancelButton: true,
            confirmButtonText: 'Sí, reenviar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
        }).then((result) => {
            if (result.isConfirmed) {
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

                fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Enviado!',
                                text: data.message,
                                confirmButtonColor: '#c9690f'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No fue posible reenviar el comprobante',
                                confirmButtonColor: '#c9690f'
                            });
                        }
                    })
                    .catch(error => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al reenviar el comprobante',
                            confirmButtonColor: '#c9690f'
                        });
                    });
            }
        });
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
            
            // Obtener el dropdown
            const dropdown = badge.querySelector('.status-dropdown');
            if (!dropdown) return;
            
            // Contar items en el dropdown
            const items = dropdown.querySelectorAll('.status-dropdown-item');
            if (items.length === 0) {
                // No hay estados permitidos, no abrir dropdown
                return;
            }
            
            // Cerrar otros dropdowns primero
            ordersList.querySelectorAll('.status-dropdown').forEach(d => {
                if (d !== dropdown) {
                    d.style.display = 'none';
                }
            });
            
            // Toggle este dropdown
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
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
        let swalPromise;
        
        if (status === 'Cancelled') {
            // Para cancelación, pedir motivo
            swalPromise = Swal.fire({
                title: 'Cancelar Orden',
                text: 'Por favor, indique su nombre y el motivo de la cancelación:',
                input: 'textarea',
                inputPlaceholder: 'Ej: Cliente lo solicita, error en pedido, etc...',
                inputAttributes: {
                    maxlength: 500
                },
                showCancelButton: true,
                confirmButtonText: 'Cancelar orden',
                cancelButtonText: 'Atrás',
                confirmButtonColor: '#e18018',
                cancelButtonColor: '#6b7280',
                icon: 'warning',
                reverseButtons: true,
                allowOutsideClick: false,
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Debe ingresar un motivo para cancelar'
                    }
                }
            });
        } else if (status === 'Delivered') {
            // Para DELIVERED, pedir método de pago y número de comprobante
            swalPromise = Swal.fire({
                title: 'Registrar Pago y Comprobante',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333;">Método de Pago</label>
                        <select id="paymentMethod" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <option value="">-- Seleccionar --</option>
                            <option value="Efectivo">💵 Efectivo</option>
                            <option value="Tarjeta de Crédito">💳 Tarjeta de Crédito</option>
                            <option value="Tarjeta de Débito">🏧 Tarjeta de Débito</option>
                            <option value="Transferencia Bancaria">🏦 Transferencia Bancaria</option>
                            <option value="Billetera Digital">📱 Billetera Digital</option>
                            <option value="Cheque">📄 Cheque</option>
                        </select>
                        
                        <label style="display: block; margin: 15px 0 10px 0; font-weight: 600; color: #333;">Número de Comprobante/Factura</label>
                        <input type="text" id="receiptReference" placeholder="Ej: FAC-001-2026, 12345678, etc..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Marcar como Entregado',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e18018',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                allowOutsideClick: false,
                didOpen: () => {
                    // Enfocar el campo de método de pago
                    document.getElementById('paymentMethod').focus();
                },
                preConfirm: () => {
                    const paymentMethod = document.getElementById('paymentMethod').value;
                    const receiptReference = document.getElementById('receiptReference').value;
                    
                    if (!paymentMethod) {
                        Swal.showValidationMessage('Debe seleccionar un método de pago');
                        return false;
                    }
                    if (!receiptReference || !receiptReference.trim()) {
                        Swal.showValidationMessage('Debe ingresar un número de comprobante/factura');
                        return false;
                    }
                    
                    return {
                        paymentMethod: paymentMethod,
                        receiptReference: receiptReference.trim()
                    };
                }
            });
        } else {
            // Para otros estados, confirmación simple
            swalPromise = Swal.fire({
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
            });
        }
        
        swalPromise.then((result) => {
            if (result.isConfirmed) {
                // Preparar payload
                const payload = { status };
                
                if (status === 'Cancelled' && result.value) {
                    payload.cancellation_reason = result.value.trim();
                    
                    // Mostrar confirmación final antes de cancelar
                    return Swal.fire({
                        title: '⚠️ ¿Estás seguro?',
                        text: 'Esta acción cancelará la orden. ¿Deseas continuar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cancelar orden',
                        cancelButtonText: 'No, atrás',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true,
                        allowOutsideClick: false
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            return { shouldProceed: true, payload };
                        }
                        return { shouldProceed: false };
                    });
                }
                
                if (status === 'Delivered' && result.value) {
                    // Agregar datos de pago para Delivered
                    payload.payment_method = result.value.paymentMethod;
                    payload.receipt_reference = result.value.receiptReference;
                }
                
                return { shouldProceed: true, payload };
            }
            return { shouldProceed: false };
        }).then((result) => {
            if (!result || !result.shouldProceed) return;
            
            const payload = result.payload;
            
            // Hacer el fetch
            fetch(`{{ url('ordenes') }}/${orderId}/cambiar-estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json().then(data => ({
                        ok: response.ok,
                        data: data
                    }));
                })
                .then(({ ok, data }) => {
                    console.log('Response data:', data);
                    
                    if (!ok || !data.success) {
                        // Error del servidor
                        Swal.fire({
                            title: 'Error',
                            text: data.error || data.message || 'Error al cambiar el estado',
                            icon: 'error',
                            confirmButtonColor: '#e18018'
                        });
                        return;
                    }
                    
                    // Éxito - procesar actualización del DOM
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
                            
                            // Obtener estados permitidos según el nuevo estado
                            const allowedNextStatuses = getNextStatuses(status);
                            const statusLabelsMap = {
                                'Pending': 'Pendiente',
                                'Preparing': 'En Preparación',
                                'Ready': 'Listo',
                                'Delivered': 'Entregado',
                                'Cancelled': 'Cancelada'
                            };
                            
                            // Construir el dropdown dinámicamente
                            let dropdownHTML = '<div class="status-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; min-width: 180px;">';
                            
                            allowedNextStatuses.forEach(statusKey => {
                                dropdownHTML += `
                                    <button type="button" class="status-dropdown-item status-dropdown-item-${statusKey}" data-status="${statusKey}">
                                        <i class="fas ${statusIcons[statusKey]}"></i>
                                        ${statusLabelsMap[statusKey]}
                                    </button>
                                `;
                            });
                            
                            dropdownHTML += '</div>';
                            
                            // Actualizar el contenido del badge
                            statusBadge.innerHTML = `
                                <i class="fas ${statusIcons[status]}"></i>
                                ${statusNames[status]}
                                ${allowedNextStatuses.length > 0 ? '<i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 10px;"></i>' : ''}
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
        });
    }

    // Función para obtener los estados permitidos según el estado actual
    function getNextStatuses(currentStatus) {
        const statusFlow = {
            'Pending': ['Preparing', 'Cancelled'],
            'Preparing': ['Ready', 'Cancelled'],
            'Ready': ['Delivered', 'Cancelled'],
            'Delivered': [], // No puede cambiar desde Entregado
            'Cancelled': [] // No puede cambiar desde Cancelado
        };
        
        return statusFlow[currentStatus] || [];
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

    // ========== CREAR NUEVA ORDEN ==========
    
    // Datos de la orden en construcción
    let orderInProgress = {
        items: {},
        customerId: null
    };

    // Productos disponibles en memoria
    let allProducts = [];
    let selectedCategory = 'all';

    // Abrir modal
    document.getElementById('newOrderBtn').addEventListener('click', function() {
        document.getElementById('createOrderModal').classList.add('show');
        document.body.style.overflow = 'hidden';
        loadLocalProducts();
        loadAllCustomers(); // Cargar clientes
    });

    // Cerrar modal
    function closeModal() {
        document.getElementById('createOrderModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        resetOrderForm();
    }

    document.getElementById('closeOrderModal').addEventListener('click', closeModal);
    document.getElementById('cancelOrderBtn').addEventListener('click', closeModal);

    // Cerrar modal al hacer clic fuera de él
    document.getElementById('createOrderModal').addEventListener('click', function(e) {
        // Si el click fue en el overlay (no en el contenido del modal)
        if (e.target === this) {
            closeModal();
        }
    });

    // Cargar productos del local
    async function loadLocalProducts() {
        try {
            const response = await fetch('{{ route("orders.local-products") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            allProducts = data.products || [];

            if (allProducts.length === 0) {
                document.getElementById('productsContainer').innerHTML = '<p style="color: #999; grid-column: 1/-1; text-align: center; padding: 20px;">No hay productos disponibles</p>';
                return;
            }

            // Extraer categorías únicas
            const categories = ['all', ...new Set(allProducts.map(p => p.category || 'Sin categoría'))];
            setupCategoryTabs(categories);
            
            // Mostrar todos los productos
            displayProducts(allProducts);
            updateProductCount(allProducts.length);

            // Agregar listeners para búsqueda y categorías
            setupProductFilters();
        } catch (error) {
            console.error('Error cargando productos:', error);
        }
    }

    // Configurar tabs de categorías
    function setupCategoryTabs(categories) {
        const tabsContainer = document.getElementById('categoryTabs');
        tabsContainer.innerHTML = '';

        categories.forEach((category, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'category-tab' + (index === 0 ? ' active' : '');
            button.dataset.category = category;
            button.textContent = category === 'all' ? 'Todas' : category;
            button.style.padding = '8px 16px';
            button.style.background = index === 0 ? '#e18018' : '#f3f4f6';
            button.style.color = index === 0 ? 'white' : '#666';
            button.style.border = index === 0 ? 'none' : '1px solid #e5e7eb';
            button.style.borderRadius = '20px';
            button.style.fontWeight = '600';
            button.style.cursor = 'pointer';
            button.style.whiteSpace = 'nowrap';
            button.style.fontSize = '13px';

            button.addEventListener('click', function() {
                // Actualizar tab activo
                document.querySelectorAll('.category-tab').forEach(t => {
                    t.classList.remove('active');
                    t.style.background = '#f3f4f6';
                    t.style.color = '#666';
                    t.style.border = '1px solid #e5e7eb';
                });
                this.classList.add('active');
                this.style.background = '#e18018';
                this.style.color = 'white';
                this.style.border = 'none';

                selectedCategory = category;
                filterProducts();
            });

            tabsContainer.appendChild(button);
        });
    }

    // Configurar listeners para búsqueda
    function setupProductFilters() {
        const searchInput = document.getElementById('productSearch');
        if (searchInput) {
            searchInput.addEventListener('input', filterProducts);
        }
    }

    // Filtrar productos por búsqueda y categoría
    function filterProducts() {
        const searchTerm = document.getElementById('productSearch').value.toLowerCase();
        
        let filtered = allProducts.filter(product => {
            const matchCategory = selectedCategory === 'all' || product.category === selectedCategory;
            const matchSearch = product.name.toLowerCase().includes(searchTerm) || 
                              (product.category || '').toLowerCase().includes(searchTerm);
            return matchCategory && matchSearch;
        });

        displayProducts(filtered);
        updateProductCount(filtered.length);
    }

    // Mostrar productos en el grid
    function displayProducts(products) {
        const container = document.getElementById('productsContainer');
        container.innerHTML = '';

        if (products.length === 0) {
            container.innerHTML = '<p style="color: #999; grid-column: 1/-1; text-align: center; padding: 20px;">No hay productos que coincidan</p>';
            return;
        }

        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            if (orderInProgress.items[product.product_id]) {
                card.classList.add('selected');
            }

            card.innerHTML = `
                ${product.photo ? `<img src="${product.photo}" alt="${product.name}">` : '<div style="width: 100%; height: 80px; background: #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;"><i class="fas fa-image" style="color: #999; font-size: 24px;"></i></div>'}
                <div class="product-card-name" title="${product.name}">${product.name}</div>
                <div class="product-card-price">₡${parseFloat(product.price).toFixed(2)}</div>
                <input type="number" class="product-quantity-input" value="${orderInProgress.items[product.product_id]?.quantity || 1}" min="1" max="99" data-product-id="${product.product_id}" data-product-name="${product.name}" data-product-price="${product.price}">
            `;

            card.addEventListener('click', function() {
                card.classList.toggle('selected');
                if (card.classList.contains('selected')) {
                    orderInProgress.items[product.product_id] = {
                        product_id: product.product_id,
                        name: product.name,
                        price: product.price,
                        quantity: 1
                    };
                } else {
                    delete orderInProgress.items[product.product_id];
                }
                updateOrderSummary();
            });

            // Cambiar cantidad
            const quantityInput = card.querySelector('.product-quantity-input');
            quantityInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            quantityInput.addEventListener('change', function(e) {
                const quantity = parseInt(e.target.value) || 1;
                if (orderInProgress.items[product.product_id]) {
                    orderInProgress.items[product.product_id].quantity = quantity;
                    updateOrderSummary();
                }
            });

            container.appendChild(card);
        });
    }

    // Actualizar contador de productos
    function updateProductCount(count) {
        const countElement = document.getElementById('productCount');
        if (countElement) {
            countElement.textContent = count;
        }
    }

    // Actualizar resumen de orden
    function updateOrderSummary() {
        const items = Object.values(orderInProgress.items);
        const tbody = document.getElementById('orderItemsSummary');
        let total = 0;

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 12px 8px; color: #999; font-size: 12px;">Sin productos seleccionados</td></tr>';
            document.getElementById('orderTotal').textContent = '0.00';
            return;
        }

        tbody.innerHTML = items.map(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            return `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>₡${subtotal.toFixed(2)}</td>
                </tr>
            `;
        }).join('');

        document.getElementById('orderTotal').textContent = total.toFixed(2);
    }

    // Búsqueda de clientes
    const customerSearch = document.getElementById('customerSearch');
    const toggleCustomerDropdown = document.getElementById('toggleCustomerDropdown');
    let allCustomers = [];
    let customersLoaded = false;

    // Cargar todos los clientes al abrir el modal
    async function loadAllCustomers() {
        try {
            const response = await fetch(`{{ route('orders.search-customers') }}?query=`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            allCustomers = data.customers || [];
            customersLoaded = true;
            console.log('Clientes cargados:', allCustomers.length);
        } catch (error) {
            console.error('Error cargando clientes:', error);
            allCustomers = [];
        }
    }

    // Mostrar dropdown al hacer clic o focus en el input
    customerSearch.addEventListener('focus', async function() {
        // Si no están cargados, cargar
        if (!customersLoaded) {
            await loadAllCustomers();
        }
        
        if (!customerSearch.value && document.getElementById('customerId').value === '') {
            displayCustomerResults(allCustomers);
        }
    });

    // También al hacer clic
    customerSearch.addEventListener('click', async function() {
        if (!customersLoaded) {
            await loadAllCustomers();
        }
        
        if (!customerSearch.value && document.getElementById('customerId').value === '') {
            displayCustomerResults(allCustomers);
        }
    });

    // Botón toggle para dropdown
    document.getElementById('toggleCustomerDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('customerSearch').value = '';
        document.getElementById('customerId').value = '';
        document.getElementById('customerResults').style.display = 'none';
        toggleCustomerDropdown.style.display = 'none';
        customerSearch.focus();
    });

    customerSearch.addEventListener('input', async function() {
        const search = this.value.trim();
        
        if (search.length === 0) {
            // Si está vacío, mostrar todos
            displayCustomerResults(allCustomers);
            return;
        }

        if (search.length < 2) {
            document.getElementById('customerResults').style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`{{ route('orders.search-customers') }}?query=${encodeURIComponent(search)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            displayCustomerResults(data.customers || []);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('customerResults').style.display = 'none';
        }
    });

    // Mostrar resultados de búsqueda de clientes
    function displayCustomerResults(customers) {
        const resultsDiv = document.getElementById('customerResults');
        
        if (customers.length === 0) {
            resultsDiv.innerHTML = '<div style="padding: 12px; color: #999; text-align: center; font-size: 13px;">No se encontraron clientes</div>';
            resultsDiv.style.display = 'block';
            return;
        }

        resultsDiv.innerHTML = customers.map((customer, index) => `
            <div class="customer-result-item" data-customer-id="${customer.user_id}" data-customer-name="${customer.full_name}" data-customer-email="${customer.email}" style="padding: 10px 12px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.2s;">
                <div style="font-weight: 500; color: #111; font-size: 14px;">${customer.full_name}</div>
                <div style="font-size: 12px; color: #666;">${customer.email}</div>
                ${customer.phone ? `<div style="font-size: 11px; color: #999;">📞 ${customer.phone}</div>` : ''}
            </div>
        `).join('');

        resultsDiv.style.display = 'block';

        // Agregar event listeners a los resultados
        resultsDiv.querySelectorAll('.customer-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const customerId = this.dataset.customerId;
                const customerName = this.dataset.customerName;
                const customerEmail = this.dataset.customerEmail;

                // Llenar campos
                document.getElementById('customerSearch').value = `${customerName} (${customerEmail})`;
                document.getElementById('customerId').value = customerId;
                document.getElementById('customerResults').style.display = 'none';
                toggleCustomerDropdown.style.display = 'block';
            });

            item.addEventListener('mouseenter', function() {
                this.style.background = '#f9fafb';
            });

            item.addEventListener('mouseleave', function() {
                this.style.background = 'white';
            });
        });
    }

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#customerSearch') && !e.target.closest('#customerResults') && !e.target.closest('#toggleCustomerDropdown')) {
            document.getElementById('customerResults').style.display = 'none';
        }
    });

    // Enviar formulario
    document.getElementById('createOrderForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const items = Object.values(orderInProgress.items);
        if (items.length === 0) {
            swAlert({ icon: 'warning', title: 'Advertencia', text: 'Debes seleccionar al menos un producto' });
            return;
        }

        const preparationTime = document.getElementById('preparationTime').value;
        const additionalNotes = document.getElementById('additionalNotes').value;
        const userId = document.getElementById('customerId').value;
        const itemsCount = items.reduce((sum, item) => sum + item.quantity, 0);

        // Mostrar confirmación antes de guardar
        swConfirm({
            title: '¿Confirmar nueva orden?',
            html: `
                <div style="text-align: left; margin-top: 15px;">
                    <p><strong>Resumen de la orden:</strong></p>
                    <ul style="list-style: none; padding: 0; font-size: 14px;">
                        <li><i class="fas fa-box" style="margin-right: 8px; color: #e18018;"></i> <strong>${itemsCount}</strong> producto${itemsCount > 1 ? 's' : ''}</li>
                        <li><i class="fas fa-clock" style="margin-right: 8px; color: #e18018;"></i> Tiempo: <strong>${preparationTime} min</strong></li>
                        ${userId ? `<li><i class="fas fa-user" style="margin-right: 8px; color: #e18018;"></i> Cliente: <strong>Seleccionado</strong></li>` : `<li><i class="fas fa-user" style="margin-right: 8px; color: #999;"></i> Cliente: <em>No asignado</em></li>`}
                    </ul>
                </div>
            `,
            confirmButtonText: 'Sí, crear orden',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (!result.isConfirmed) return;

            const payload = {
                user_id: userId || null,
                items: items.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    customization: null
                })),
                preparation_time: parseInt(preparationTime),
                additional_notes: additionalNotes
            };

            try {
                const response = await fetch('{{ route("orders.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    // Cerrar modal primero
                    closeModal();
                    
                    // Mostrar notificación de éxito (toast con tiempo)
                    if (window.swToast) {
                        window.swToast.fire({
                            icon: 'success',
                            title: `Orden ${data.order.order_number} creada exitosamente`
                        });
                    } else {
                        showNotification('success', `Orden ${data.order.order_number} creada exitosamente`);
                    }
                    
                    // Refrescar la página después de 1.5 segundos para que aparezca la nueva orden
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    swAlert({ icon: 'error', title: 'Error', text: data.error || 'Error al crear la orden' });
                }
            } catch (error) {
                console.error('Error:', error);
                swAlert({ icon: 'error', title: 'Error', text: error.message });
            }
        });
    });

    function resetOrderForm() {
        orderInProgress = {
            items: {},
            customerId: null
        };
        document.getElementById('createOrderForm').reset();
        document.querySelectorAll('.product-card').forEach(card => card.classList.remove('selected'));
        document.getElementById('customerId').value = '';
        document.getElementById('customerSearch').value = '';
        document.getElementById('productSearch').value = '';
        document.getElementById('toggleCustomerDropdown').style.display = 'none';
        document.getElementById('customerResults').style.display = 'none';
        
        // Resetear categoría a "Todas"
        selectedCategory = 'all';
        document.querySelectorAll('.category-tab').forEach((tab, index) => {
            if (index === 0) {
                tab.classList.add('active');
                tab.style.background = '#e18018';
                tab.style.color = 'white';
                tab.style.border = 'none';
            } else {
                tab.classList.remove('active');
                tab.style.background = '#f3f4f6';
                tab.style.color = '#666';
                tab.style.border = '1px solid #e5e7eb';
            }
        });
        
        updateOrderSummary();
    }
});
</script>
@endpush
@endsection
