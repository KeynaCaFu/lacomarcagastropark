@extends('layouts.app')

@section('title', 'Historial de Órdenes')

@push('styles')
    <style>
        .history-container {
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .history-header {
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .history-subtitle {
            font-size: 13px;
            color: #999;
            margin-top: 5px;
        }

        .btn-back {
            background: #e18018;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-back:hover {
            background: #c9690f;
            color: white;
        }

        /* Filtros */
        .filter-panel {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .filter-title {
            font-size: 14px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }

        .filter-input {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            width: 100%;
            box-sizing: border-box;
        }

        .filter-input:focus {
            outline: none;
            border-color: #e18018;
            box-shadow: 0 0 0 2px rgba(225, 128, 24, 0.1);
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tab-btn {
            padding: 8px 16px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            color: #666;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #e18018, #c9690f);
            color: white;
            border-color: #e18018;
        }

        .tab-btn:hover {
            border-color: #e18018;
        }

        .history-section {
            margin-bottom: 20px;
        }

        .section-title {
            background: linear-gradient(135deg, #e18018, #c9690f);
            color: white;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            font-size: 20px;
        }

        .orders-wrapper {
            background: white;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Acordeón */
        .accordion-item {
            border-bottom: 1px solid #e0e0e0;
        }

        .accordion-item:last-child {
            border-bottom: none;
        }

        .accordion-header {
            padding: 15px 20px;
            background: #f5f5f5;
            border-left: 4px solid #e18018;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
            user-select: none;
        }

        .accordion-header:hover {
            background: #efefef;
        }

        .accordion-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .accordion-count {
            background: #e18018;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .accordion-toggle {
            transition: transform 0.3s ease;
            color: #e18018;
        }

        .accordion-toggle.collapsed {
            transform: rotate(-90deg);
        }

        .accordion-content {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .accordion-content.collapsed {
            max-height: 0;
        }

        .order-row {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: center;
            transition: background 0.2s ease;
        }

        .order-row:hover {
            background: #fafafa;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .order-col-number {
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .order-col-client {
            font-size: 12px;
            color: #666;
        }

        .order-col-meta {
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        .order-col-total {
            font-size: 14px;
            font-weight: 700;
            color: #e18018;
        }

        .order-col-actions {
            display: flex;
            gap: 6px;
        }

        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-download {
            background: #3b82f6;
            color: white;
        }

        .btn-download:hover {
            background: #2563eb;
        }

        .btn-resend {
            background: #10b981;
            color: white;
        }

        .btn-resend:hover {
            background: #059669;
        }

        .btn-regenerate {
            background: #f59e0b;
            color: white;
        }

        .btn-regenerate:hover {
            background: #d97706;
        }

        .empty-state {
            text-align: center;
            padding: 60px 40px;
            background: white;
            border-radius: 8px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state-text {
            font-size: 14px;
            color: #999;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-delivered {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .results-info {
            padding: 10px 20px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            font-size: 12px;
            color: #1e40af;
            margin-bottom: 15px;
        }

        /* TABLET - Large (1024px+) */
        @media (min-width: 1024px) and (max-width: 1399px) {
            .history-container {
                padding: 18px;
            }

            .history-header {
                padding: 18px;
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .history-title {
                font-size: 22px;
            }

            .btn-back {
                align-self: flex-end;
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-row {
                grid-template-columns: 1.5fr 1.2fr 1fr auto;
                gap: 12px;
                padding: 12px 15px;
            }

            .order-col-number {
                flex-direction: column;
                gap: 6px;
            }

            .order-col-meta {
                font-size: 11px;
            }

            .order-col-total {
                font-size: 13px;
            }

            .action-btn {
                padding: 5px 8px;
                font-size: 9px;
                min-height: 36px;
            }

            .tab-btn {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        /* TABLET - Medium (768px - 1023px) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .history-container {
                padding: 15px;
            }

            .history-header {
                padding: 15px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                margin-bottom: 20px;
            }

            .history-title {
                font-size: 20px;
            }

            .history-subtitle {
                font-size: 12px;
            }

            .btn-back {
                align-self: flex-end;
                padding: 8px 12px;
                font-size: 12px;
            }

            .btn-back span {
                display: inline;
            }

            .filter-panel {
                padding: 15px;
                margin-bottom: 15px;
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .filter-label {
                font-size: 11px;
            }

            .filter-input {
                padding: 8px 10px;
                font-size: 12px;
            }

            .tab-buttons {
                padding: 12px 15px;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 15px;
            }

            .tab-btn {
                padding: 6px 12px;
                font-size: 12px;
                border-radius: 5px;
                flex: 1;
                min-width: 130px;
                white-space: nowrap;
            }

            .tab-btn span {
                display: inline;
            }

            .section-title {
                padding: 12px 15px;
                font-size: 15px;
            }

            .section-icon {
                font-size: 18px;
            }

            .order-row {
                grid-template-columns: 1fr 1fr auto;
                gap: 10px;
                padding: 12px 15px;
                align-items: start;
            }

            .order-col-number {
                grid-column: 1 / -1;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
                font-size: 13px;
                margin-bottom: 8px;
            }

            .order-col-client {
                font-size: 11px;
                padding: 8px;
                background: #f9f9f9;
                border-radius: 4px;
            }

            .order-col-meta {
                font-size: 11px;
                background: #f9f9f9;
                padding: 8px;
                border-radius: 4px;
                text-align: left;
            }

            .order-col-total {
                font-size: 13px;
                font-weight: 700;
                background: #fffbf0;
                padding: 8px;
                border-radius: 4px;
                text-align: right;
            }

            .order-col-actions {
                grid-column: 1 / -1;
                width: 100%;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 6px;
                padding-top: 8px;
                border-top: 1px solid #f0f0f0;
            }

            .action-btn {
                padding: 6px 10px;
                font-size: 10px;
                min-height: 36px;
                min-width: 36px;
                flex: 0 1 calc(33.333% - 4px);
                justify-content: center;
            }

            .accordion-header {
                padding: 12px 15px;
                font-size: 12px;
            }

            .accordion-header-left {
                font-size: 12px;
                gap: 8px;
            }

            .accordion-count {
                padding: 1px 6px;
                font-size: 10px;
            }

            .results-info {
                padding: 8px 15px;
                font-size: 11px;
                margin-bottom: 12px;
            }

            .empty-state {
                padding: 40px 20px;
            }

            .empty-state-icon {
                font-size: 36px;
                margin-bottom: 10px;
            }

            .empty-state-text {
                font-size: 13px;
            }
        }

        /* MOBILE - Large (480px - 767px) */
        @media (min-width: 480px) and (max-width: 767px) {
            .history-container {
                padding: 12px;
            }

            .history-header {
                padding: 12px;
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 15px;
            }

            .history-title {
                font-size: 18px;
            }

            .btn-back {
                align-self: flex-end;
                padding: 6px 10px;
                font-size: 11px;
            }

            .filter-panel {
                padding: 12px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .filter-label {
                font-size: 10px;
            }

            .filter-input {
                padding: 8px;
                font-size: 12px;
            }

            .tab-buttons {
                padding: 10px 12px;
                gap: 6px;
                flex-wrap: wrap;
            }

            .tab-btn {
                padding: 6px 10px;
                font-size: 11px;
                flex: 1 1 calc(50% - 3px);
                min-width: 100px;
            }

            .order-row {
                grid-template-columns: 1fr;
                gap: 0;
                padding: 12px;
            }

            .order-col-number {
                grid-column: 1;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 10px;
                font-size: 12px;
            }

            .order-col-client {
                font-size: 11px;
                padding: 8px;
                background: #f9f9f9;
                border-radius: 4px;
                margin-bottom: 8px;
            }

            .order-col-meta {
                font-size: 10px;
                background: #f9f9f9;
                padding: 6px 8px;
                border-radius: 4px;
                margin-bottom: 8px;
            }

            .order-col-total {
                font-size: 12px;
                background: #fffbf0;
                padding: 8px;
                border-radius: 4px;
                margin-bottom: 8px;
                text-align: right;
            }

            .order-col-actions {
                width: 100%;
                flex-wrap: wrap;
                gap: 6px;
                padding-top: 8px;
                border-top: 1px solid #f0f0f0;
            }

            .action-btn {
                padding: 6px 8px;
                font-size: 9px;
                min-height: 36px;
                flex: 1 1 calc(33.333% - 4px);
                justify-content: center;
            }

            .empty-state {
                padding: 30px 15px;
            }

            .empty-state-icon {
                font-size: 32px;
            }

            .empty-state-text {
                font-size: 12px;
            }
        }

        /* MOBILE - Small (max-width: 479px) */
        @media (max-width: 479px) {
            .history-container {
                padding: 10px;
            }

            .history-header {
                padding: 10px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .history-title {
                font-size: 16px;
            }

            .history-subtitle {
                font-size: 11px;
            }

            .btn-back {
                align-self: flex-end;
                padding: 5px 8px;
                font-size: 10px;
                gap: 4px;
            }

            .filter-panel {
                padding: 10px;
                margin-bottom: 10px;
            }

            .filter-title {
                font-size: 12px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .filter-label {
                font-size: 9px;
            }

            .filter-input {
                padding: 7px;
                font-size: 11px;
            }

            .tab-buttons {
                padding: 8px 10px;
                gap: 5px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .tab-btn {
                padding: 5px 8px;
                font-size: 10px;
                flex: 1 1 calc(50% - 2.5px);
                min-width: 80px;
            }

            .section-title {
                padding: 10px 12px;
                font-size: 13px;
            }

            .section-icon {
                font-size: 16px;
            }

            .order-row {
                grid-template-columns: 1fr;
                gap: 0;
                padding: 10px;
            }

            .order-col-number {
                grid-column: 1;
                flex-wrap: wrap;
                gap: 5px;
                margin-bottom: 8px;
                font-size: 11px;
            }

            .order-col-client {
                font-size: 10px;
                padding: 6px;
                background: #f9f9f9;
                border-radius: 3px;
                margin-bottom: 6px;
                line-height: 1.3;
            }

            .order-col-meta {
                font-size: 9px;
                background: #f9f9f9;
                padding: 5px 6px;
                border-radius: 3px;
                margin-bottom: 6px;
            }

            .order-col-total {
                font-size: 11px;
                background: #fffbf0;
                padding: 6px;
                border-radius: 3px;
                text-align: right;
                margin-bottom: 6px;
                font-weight: 700;
            }

            .order-col-actions {
                width: 100%;
                flex-wrap: wrap;
                gap: 4px;
            }

            .action-btn {
                padding: 5px 6px;
                font-size: 8px;
                min-height: 32px;
                flex: 1 1 calc(33.333% - 3px);
                justify-content: center;
            }

            .accordion-header {
                padding: 10px 12px;
                font-size: 11px;
            }

            .accordion-header-left {
                font-size: 11px;
                gap: 6px;
            }

            .accordion-count {
                padding: 0 5px;
                font-size: 9px;
            }

            .results-info {
                padding: 6px 10px;
                font-size: 10px;
                margin-bottom: 10px;
            }

            .empty-state {
                padding: 25px 15px;
            }

            .empty-state-icon {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .empty-state-text {
                font-size: 11px;
            }

            .status-badge {
                padding: 2px 6px;
                font-size: 8px;
            }

            /* Touch optimization */
            @media (hover: none) and (pointer: coarse) {
                button, a {
                    min-height: 44px;
                    min-width: 44px;
                }

                .action-btn {
                    min-height: 36px;
                    padding: 8px;
                }

                .tab-btn {
                    min-height: 40px;
                }
            }
        }

        /* TABLET - Optimización adicional para responsive */
        @media (min-width: 768px) and (max-width: 1023px) {
            /* Ocultar algunos textos en tablet si es necesario para mejor layout */
            .history-header {
                display: flex !important;
            }

            /* Asegurar que los tabs no causen overflow */
            .tab-btn {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Optimizar espacios en orden row */
            .order-row {
                padding: 12px;
            }

            /* Asegurar que el filter grid sea siempre responsive */
            .filter-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }

            /* Garantizar que las acciones tengan espacio adecuado */
            .order-col-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }
        }

        /* MOBILE - Optimización pequeñas pantallas */
        @media (min-width: 480px) and (max-width: 767px) {
            /* Asegurar textos legibles */
            .order-col-number span:not(.status-badge) {
                font-weight: 600;
            }

            /* Buttons responsive */
            #searchBtn span,
            #clearBtn span {
                display: inline;
            }

            /* Garantizar que status badges se vean bien */
            .status-badge {
                display: inline-block;
                margin: 2px;
            }
        }

        @media (max-width: 479px) {
            /* Buttons solo con IconO en pantallas muy pequeñas */
            #searchBtn span,
            #clearBtn span {
                display: none;
            }

            .tab-btn span {
                display: none;
            }

            .action-btn {
                padding: 5px 6px;
            }

            .status-badge {
                display: inline-block;
                padding: 2px 4px;
                font-size: 7px;
            }
        }
    </style>
@endpush

@section('content')
<div class="history-container">
    <!-- Header -->
    <div class="history-header" style="gap: 15px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 0;">
            <h1 class="history-title">Historial de Órdenes</h1>
            <p class="history-subtitle">Órdenes entregadas y canceladas con búsqueda y filtros</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn-back" style="flex-shrink: 0; align-self: center;">
            <i class="fas fa-arrow-left"></i> <span>Volver</span>
        </a>
    </div>

    <!-- Tabs de Filtro -->
    <div class="tab-buttons" style="flex-wrap: wrap; justify-content: flex-start;">
        <button class="tab-btn" data-tab="all" style="flex-shrink: 0;">
            <i class="fas fa-list"></i> <span>Todas</span>
        </button>
        <button class="tab-btn active" data-tab="delivered" style="flex-shrink: 0;">
            <i class="fas fa-check-circle"></i> <span>Entregadas</span>
        </button>
        <button class="tab-btn" data-tab="cancelled" style="flex-shrink: 0;">
            <i class="fas fa-times-circle"></i> <span>Canceladas</span>
        </button>
    </div>

    <!-- Panel de Filtros -->
    <div class="filter-panel">
        <div class="filter-title">
            <i class="fas fa-filter" style="margin-right: 8px;"></i> Filtrar y Buscar
        </div>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Número de Orden</label>
                <input type="text" class="filter-input" id="searchOrderNumber" placeholder="Ej: ORD-3405">
            </div>
            <div class="filter-group">
                <label class="filter-label">Nombre del Cliente</label>
                <input type="text" class="filter-input" id="searchCustomer" placeholder="Buscar por nombre">
            </div>
            <div class="filter-group">
                <label class="filter-label">Desde (Fecha)</label>
                <input type="date" class="filter-input" id="filterDateFrom">
            </div>
            <div class="filter-group">
                <label class="filter-label">Hasta (Fecha)</label>
                <input type="date" class="filter-input" id="filterDateTo">
            </div>
        </div>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0; display: flex; gap: 10px; flex-wrap: wrap;">
            <button id="searchBtn" class="btn-back" style="background: #ecb133; flex: 1; min-width: 150px; justify-content: center;">
                <i class="fas fa-search"></i> <span>Buscar en Historial</span>
            </button>
            <button id="clearBtn" class="btn-back" style="background: #6b7280; display: none; flex: 1; min-width: 150px; justify-content: center;">
                <i class="fas fa-times"></i> <span>Limpiar Filtros</span>
            </button>
        </div>
    </div>

    <!-- Información de Resultados -->
    <div class="results-info" id="resultsInfo" style="display: none;">
        <i class="fas fa-info-circle"></i> <span id="resultsCount"></span> <button style="background: none; border: none; color: #1e40af; cursor: pointer; text-decoration: underline; margin-left: 10px;" onclick="clearLocalFilters()">Limpiar</button>
    </div>

    <!-- Información de Alcance Por Defecto -->
    <div class="results-info" id="defaultInfo" style="background: #fef3c7; border-left-color: #f59e0b; color: #92400e;">
        <i class="fas fa-calendar"></i> <strong>Mostrando:</strong> Órdenes de hoy y últimos 5 días. Completa fechas fuera de este rango o busca en el historial completo.
    </div>

    <!-- Órdenes Entregadas (TAB 1 y 3) -->
    <div id="tab-delivered" class="history-section">
        <div class="section-title">
            <span class="section-icon"><i class="fas fa-check-circle"></i></span>
            Órdenes Entregadas
        </div>

        <div class="orders-wrapper" id="deliveredContent">
            @if($deliveredOrders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <p class="empty-state-text">No hay órdenes entregadas</p>
                </div>
            @else
                @foreach($deliveredOrders as $date => $orders)
                    <div class="accordion-item" data-section="delivered">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div class="accordion-header-left">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $date }}
                                <span class="accordion-count">{{ count($orders) }}</span>
                            </div>
                            <div class="accordion-toggle collapsed">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        <div class="accordion-content collapsed">
                            @foreach($orders as $order)
                                <div class="order-row" data-order-id="{{ $order->order_id }}" data-order-number="{{ $order->order_number }}" data-customer="{{ $order->user->first()?->full_name ?? '' }}" data-status="delivered">
                                    <div class="order-col-number">
                                        <span>{{ $order->order_number }}</span>
                                        <span class="status-badge badge-delivered">
                                            <i class="fas fa-check"></i> Entregada
                                        </span>
                                        <span id="receipt-badge-{{ $order->order_id }}">
                                            @if($order->receipts && count($order->receipts) > 0)
                                                <span class="status-badge" style="background: #d1fae5; color: #065f46; margin-left: 5px;">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </span>
                                            @else
                                                <span class="status-badge" style="background: #f0eeec;  color: #de7528;  margin-left: 5px;">
                                                    <i class="fas fa-exclamation-circle"></i> Sin PDF
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="order-col-client">
                                        <div style="font-weight: 600;">{{ $order->user->first()?->full_name ?? 'N/A' }}</div>
                                        <div style="font-size: 11px; color: #999;">{{ $order->local?->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="order-col-meta">
                                        {{ $order->updated_at->format('H:i') }}<br>
                                        <span style="font-size: 11px;">{{ $order->items->count() }} prod.</span>
                                    </div>
                                    <div class="order-col-total">
                                        ₡{{ number_format($order->total_amount, 2) }}
                                    </div>
                                    <div class="order-col-meta">
                                        <span style="font-size: 11px; color: #666;">{{ $order->quantity }} unid.</span>
                                    </div>
                                    <div class="order-col-actions">
                                        <button class="action-btn btn-download" onclick="downloadReceipt({{ $order->order_id }})" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="action-btn btn-regenerate" onclick="regenerateReceipt({{ $order->order_id }})" title="Regenerar">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                        <button class="action-btn btn-resend" onclick="resendReceiptMail({{ $order->order_id }})" title="Reenviar">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Órdenes Canceladas (TAB 1 y 2) -->
    <div id="tab-cancelled" class="history-section">
        <div class="section-title">
            <span class="section-icon"><i class="fas fa-times-circle"></i></span>
            Órdenes Canceladas
        </div>

        <div class="orders-wrapper" id="cancelledContent">
            @if($cancelledOrders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">🚫</div>
                    <p class="empty-state-text">No hay órdenes canceladas</p>
                </div>
            @else
                @foreach($cancelledOrders as $date => $orders)
                    <div class="accordion-item" data-section="cancelled">
                        <div class="accordion-header" onclick="toggleAccordion(this)">
                            <div class="accordion-header-left">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $date }}
                                <span class="accordion-count">{{ count($orders) }}</span>
                            </div>
                            <div class="accordion-toggle collapsed">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        <div class="accordion-content collapsed">
                            @foreach($orders as $order)
                                <div class="order-row" data-order-id="{{ $order->order_id }}" data-order-number="{{ $order->order_number }}" data-customer="{{ $order->user->first()?->full_name ?? '' }}" data-status="cancelled">
                                    <div class="order-col-number">
                                        <span>{{ $order->order_number }}</span>
                                        <span class="status-badge badge-cancelled">
                                            <i class="fas fa-ban"></i> Cancelada
                                        </span>
                                        <span id="receipt-badge-{{ $order->order_id }}">
                                            @if($order->receipts && count($order->receipts) > 0)
                                                <span class="status-badge" style="background: #d1fae5; color: #065f46; margin-left: 5px;">
                                                    <i class="fas fa-file-pdf"></i> PDF
                                                </span>
                                            @else
                                                <span class="status-badge" style="background: #fee2e2; color: #991b1b; margin-left: 5px;">
                                                    <i class="fas fa-exclamation-circle"></i> Sin PDF
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="order-col-client">
                                        <div style="font-weight: 600;">{{ $order->user->first()?->full_name ?? 'N/A' }}</div>
                                        <div style="font-size: 11px; color: #999;">{{ $order->local?->name ?? 'N/A' }}</div>
                                        @if($order->cancellation_reason)
                                            <div style="font-size: 10px; color: #d32f2f; margin-top: 4px;">
                                                <strong>Razón:</strong> {{ $order->cancellation_reason }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="order-col-meta">
                                        {{ $order->updated_at->format('H:i') }}<br>
                                        <span style="font-size: 11px;">{{ $order->items->count() }} prod.</span>
                                    </div>
                                    <div class="order-col-total">
                                        ₡{{ number_format($order->total_amount, 2) }}
                                    </div>
                                    <div class="order-col-meta">
                                        <span style="font-size: 11px; color: #666;">{{ $order->quantity }} unid.</span>
                                    </div>
                                    <div class="order-col-actions">
                                        <span style="color: #999; font-size: 11px;">-</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let isSearchActive = false;
let isLocalFilterActive = false;
const fiveDaysAgo = new Date();
fiveDaysAgo.setDate(fiveDaysAgo.getDate() - 5);

// Sistema de Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Actualizar tabs activos
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Mostrar/ocultar secciones
        showTab(tab);
    });
});

function showTab(tab) {
    const delivered = document.getElementById('tab-delivered');
    const cancelled = document.getElementById('tab-cancelled');

    if (tab === 'all') {
        delivered.style.display = 'block';
        cancelled.style.display = 'block';
    } else if (tab === 'delivered') {
        delivered.style.display = 'block';
        cancelled.style.display = 'none';
    } else if (tab === 'cancelled') {
        delivered.style.display = 'none';
        cancelled.style.display = 'block';
    }
}

// Inicializar mostrando las órdenes entregadas
showTab('delivered');

// Acordeón
function toggleAccordion(header) {
    const content = header.nextElementSibling;
    const toggle = header.querySelector('.accordion-toggle');

    content.classList.toggle('collapsed');
    toggle.classList.toggle('collapsed');
}

// FILTROS EN TIEMPO REAL (Hoy + 5 días)
document.getElementById('searchOrderNumber').addEventListener('input', applyLocalFilters);
document.getElementById('searchCustomer').addEventListener('input', applyLocalFilters);
document.getElementById('filterDateFrom').addEventListener('change', handleDateFilter);
document.getElementById('filterDateTo').addEventListener('change', handleDateFilter);

function applyLocalFilters() {
    const orderNumber = document.getElementById('searchOrderNumber').value.toLowerCase().trim();
    const customerName = document.getElementById('searchCustomer').value.toLowerCase().trim();

    let visibleCount = 0;

    document.querySelectorAll('.order-row').forEach(row => {
        const rowOrderNumber = row.dataset.orderNumber.toLowerCase();
        const rowCustomer = row.dataset.customer.toLowerCase();
        let visible = true;

        // Aplicar filtros
        if (orderNumber && !rowOrderNumber.includes(orderNumber)) {
            visible = false;
        }
        if (customerName && !rowCustomer.includes(customerName)) {
            visible = false;
        }

        row.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });

    // Actualizar UI
    if (orderNumber || customerName) {
        isLocalFilterActive = true;
        document.getElementById('resultsInfo').style.display = 'block';
        document.getElementById('defaultInfo').style.display = 'none';
        document.getElementById('resultsCount').textContent = `Se encontraron ${visibleCount} órdenes en los últimos 5 días`;
    } else {
        isLocalFilterActive = false;
        document.getElementById('resultsInfo').style.display = 'none';
        document.getElementById('defaultInfo').style.display = 'block';
    }
}

function handleDateFilter() {
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    if (!dateFrom && !dateTo) {
        applyLocalFilters();
        return;
    }

    // Verificar si alguna fecha está fuera del rango de 5 días
    let isOutOfRange = false;

    if (dateFrom) {
        const fromDate = new Date(dateFrom);
        if (fromDate < fiveDaysAgo) {
            isOutOfRange = true;
        }
    }

    if (dateTo) {
        const toDate = new Date(dateTo);
        if (toDate < fiveDaysAgo) {
            isOutOfRange = true;
        }
    }

    // Si está fuera de rango, mostrar mensaje y habilitar búsqueda
    if (isOutOfRange) {
        document.getElementById('resultsInfo').style.display = 'block';
        document.getElementById('defaultInfo').style.display = 'none';
        document.getElementById('resultsCount').innerHTML = `
            <span style="color: #1e40af;">Las fechas seleccionadas están fuera del rango de 5 días. 
            <button style="background: none; border: none; color: #3b82f6; cursor: pointer; text-decoration: underline; margin-left: 5px; font-weight: 600;" onclick="triggerAdvancedSearch()">Busca en historial completo</button></span>
        `;
    } else {
        applyLocalFilters();
    }
}

function clearLocalFilters() {
    document.getElementById('searchOrderNumber').value = '';
    document.getElementById('searchCustomer').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';

    isLocalFilterActive = false;
    isSearchActive = false;

    document.getElementById('resultsInfo').style.display = 'none';
    document.getElementById('defaultInfo').style.display = 'block';

    // Mostrar todas las órdenes
    document.querySelectorAll('.order-row').forEach(row => {
        row.style.display = '';
    });
}

// BÚSQUEDA AVANZADA (Historial Completo)
document.getElementById('searchBtn').addEventListener('click', function() {
    const orderNumber = document.getElementById('searchOrderNumber').value.trim();
    const customerName = document.getElementById('searchCustomer').value.trim();
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    // Validar que al menos un filtro esté completo
    if (!orderNumber && !customerName && !dateFrom && !dateTo) {
        Swal.fire({
            icon: 'warning',
            title: 'Filtro Requerido',
            text: 'Debes completar al menos un filtro (número, cliente o rango de fechas)',
            confirmButtonColor: '#e18018'
        });
        return;
    }

    performSearch(orderNumber, customerName, dateFrom, dateTo);
});

function triggerAdvancedSearch() {
    const orderNumber = document.getElementById('searchOrderNumber').value.trim();
    const customerName = document.getElementById('searchCustomer').value.trim();
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;

    performSearch(orderNumber, customerName, dateFrom, dateTo);
}

// Botón de limpiar
document.getElementById('clearBtn').addEventListener('click', function() {
    clearLocalFilters();
    document.getElementById('searchBtn').style.display = 'inline-flex';
    document.getElementById('clearBtn').style.display = 'none';
    location.reload();
});

// Realizar búsqueda AJAX
function performSearch(orderNumber, customerName, dateFrom, dateTo) {
    Swal.fire({
        title: 'Buscando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`{{ route('orders.receipt.search') }}?orderNumber=${orderNumber}&customerName=${customerName}&dateFrom=${dateFrom}&dateTo=${dateTo}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
        .then(response => response.json())
        .then(data => {
            Swal.close();

            if (!data.success) {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            if (data.count === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin Resultados',
                    text: 'No se encontraron órdenes que coincidan con los filtros',
                    confirmButtonColor: '#e18018'
                });
                return;
            }

            // Mostrar resultados
            renderSearchResults(data);
            isSearchActive = true;
            
            // Mostrar/ocultar botones
            document.getElementById('searchBtn').style.display = 'none';
            document.getElementById('clearBtn').style.display = 'inline-flex';
            document.getElementById('resultsInfo').style.display = 'block';
            document.getElementById('defaultInfo').style.display = 'none';
            document.getElementById('resultsCount').innerHTML = `Se encontraron ${data.count} órdenes en el historial completo`;

            // Hacer scroll al resultado
            setTimeout(() => {
                document.getElementById('tab-delivered').scrollIntoView({ behavior: 'smooth' });
            }, 300);
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al buscar órdenes', 'error');
        });
}

// Renderizar resultados de búsqueda
function renderSearchResults(data) {
    // Limpiar contenido previo
    const deliveredContent = document.getElementById('deliveredContent');
    const cancelledContent = document.getElementById('cancelledContent');

    // Separar por estado
    const delivered = data.orders.filter(o => o.status === 'Delivered');
    const cancelled = data.orders.filter(o => o.status === 'Cancelled');

    // Renderizar entregadas
    if (delivered.length === 0) {
        deliveredContent.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p class="empty-state-text">No hay órdenes entregadas que coincidan</p>
            </div>
        `;
    } else {
        deliveredContent.innerHTML = renderOrderGroups(delivered);
    }

    // Renderizar canceladas
    if (cancelled.length === 0) {
        cancelledContent.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🚫</div>
                <p class="empty-state-text">No hay órdenes canceladas que coincidan</p>
            </div>
        `;
    } else {
        cancelledContent.innerHTML = renderOrderGroups(cancelled);
    }
}

// Agrupar y renderizar órdenes
function renderOrderGroups(orders) {
    const grouped = {};
    
    orders.forEach(order => {
        const dateStr = new Date(order.updated_at).toLocaleDateString('es-ES');
        const today = new Date().toLocaleDateString('es-ES');
        const dateLabel = dateStr === today ? 'Hoy' : dateStr;
        
        if (!grouped[dateLabel]) {
            grouped[dateLabel] = [];
        }
        grouped[dateLabel].push(order);
    });

    let html = '';
    Object.entries(grouped).forEach(([date, dateOrders]) => {
        html += `
            <div class="accordion-item" data-section="${orders[0].status === 'Delivered' ? 'delivered' : 'cancelled'}">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <div class="accordion-header-left">
                        <i class="fas fa-calendar-alt"></i>
                        ${date}
                        <span class="accordion-count">${dateOrders.length}</span>
                    </div>
                    <div class="accordion-toggle collapsed">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                <div class="accordion-content collapsed">
                    ${dateOrders.map(order => renderOrderRow(order)).join('')}
                </div>
            </div>
        `;
    });

    return html;
}

// Renderizar fila de orden
function renderOrderRow(order) {
    const status = order.status === 'Delivered' ? 'delivered' : 'cancelled';
    const statusClass = status === 'delivered' ? 'badge-delivered' : 'badge-cancelled';
    const statusText = status === 'delivered' ? 'Entregada' : 'Cancelada';
    const statusIcon = status === 'delivered' ? 'fa-check' : 'fa-ban';
    const customerName = order.user && order.user.length > 0 ? order.user[0].full_name : 'N/A';
    const localName = order.local ? order.local.name : 'N/A';
    const hour = new Date(order.updated_at).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
    
    // Verificar si tiene comprobante
    const hasReceipt = order.receipts && order.receipts.length > 0;
    const receiptBadge = hasReceipt 
        ? '<span class="status-badge" style="background: #d1fae5; color: #065f46; margin-left: 5px;"><i class="fas fa-file-pdf"></i> PDF</span>'
        : '<span class="status-badge" style="background: #fee2e2; color: #991b1b; margin-left: 5px;"><i class="fas fa-exclamation-circle"></i> Sin PDF</span>';
    
    let actions = '';
    if (status === 'delivered') {
        actions = `
            <button class="action-btn btn-download" onclick="downloadReceipt(${order.order_id})" title="Descargar">
                <i class="fas fa-download"></i>
            </button>
            <button class="action-btn btn-regenerate" onclick="regenerateReceipt(${order.order_id})" title="Regenerar">
                <i class="fas fa-redo"></i>
            </button>
            <button class="action-btn btn-resend" onclick="resendReceiptMail(${order.order_id})" title="Reenviar">
                <i class="fas fa-envelope"></i>
            </button>
        `;
    } else {
        actions = '<span style="color: #999; font-size: 11px;">-</span>';
    }

    return `
        <div class="order-row" data-order-id="${order.order_id}" data-order-number="${order.order_number}" data-customer="${customerName}" data-status="${status}">
            <div class="order-col-number">
                <span>${order.order_number}</span>
                <span class="status-badge ${statusClass}">
                    <i class="fas ${statusIcon}"></i> ${statusText}
                </span>
                <span id="receipt-badge-${order.order_id}">${receiptBadge}</span>
            </div>
            <div class="order-col-client">
                <div style="font-weight: 600;">${customerName}</div>
                <div style="font-size: 11px; color: #999;">${localName}</div>
                ${order.cancellation_reason ? `<div style="font-size: 10px; color: #d32f2f; margin-top: 4px;"><strong>Razón:</strong> ${order.cancellation_reason}</div>` : ''}
            </div>
            <div class="order-col-meta">
                ${hour}<br>
                <span style="font-size: 11px;">${order.items.length} prod.</span>
            </div>
            <div class="order-col-total">
                ₡${parseFloat(order.total_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}
            </div>
            <div class="order-col-meta">
                <span style="font-size: 11px; color: #666;">${order.quantity} unid.</span>
            </div>
            <div class="order-col-actions">
                ${actions}
            </div>
        </div>
    `;
}

// Funciones de Acciones
function downloadReceipt(orderId) {
    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/descargar`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `comprobante_${orderId}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo descargar el comprobante', 'error');
        });
}

function regenerateReceipt(orderId) {
    Swal.fire({
        icon: 'question',
        title: '¿Regenerar Comprobante?',
        text: 'Se generará un nuevo comprobante para esta orden',
        showCancelButton: true,
        confirmButtonText: 'Sí, regenerar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('ordenes') }}/${orderId}/comprobante/regenerar`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success');
                        // Actualizar el badge del comprobante
                        updateReceiptBadge(orderId);
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo regenerar el comprobante', 'error');
                });
        }
    });
}

function resendReceiptMail(orderId) {
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
            fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo reenviar el comprobante', 'error');
                });
        }
    });
}

// Actualizar badge de comprobante por AJAX
function updateReceiptBadge(orderId) {
    const badgeElement = document.getElementById(`receipt-badge-${orderId}`);
    if (!badgeElement) return;

    // Actualizar el badge inmediatamente a verde (PDF)
    badgeElement.innerHTML = '<span class="status-badge" style="background: #d1fae5; color: #065f46; margin-left: 5px;"><i class="fas fa-file-pdf"></i> PDF</span>';
}
</script>
@endpush

@endsection
