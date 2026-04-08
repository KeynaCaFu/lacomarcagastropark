@extends('layouts.app')

@section('title', 'Historial de Órdenes')

@push('styles')
    <style>
        .history-container {
            padding: 20px;
            min-height: 100vh;
        }

        .history-header {
            margin-bottom: 25px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-right: 79px;
            margin-left: 52px;
        }

        .history-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .breadcrumb-nav {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0;
        }

        .breadcrumb-link {
            color: #e18018;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.3s ease;
        }

        .breadcrumb-link:hover {
            color: #c9690f;
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: #ccc;
            display: inline-flex;
            align-items: center;
            font-size: 11px;
        }

        .breadcrumb-current {
            color: #666;
            font-weight: 500;
        }

        .history-subtitle {
            font-size: 12px;
            color: #e18018;
            margin-top: 5px;
            font-weight: 500;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            /* background: white; */
            padding: 15px 20px;
            /* border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); */
            margin-left: 39px;
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
            margin-right: 83px;
            margin-left: 59px;
        }

        .section-title {
            background: linear-gradient(135deg, #e18018, #c9690f);
            color: white;
            padding: 9px 20px;
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
            border: 2px solid;
            background: transparent;
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
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-download:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-resend {
            border-color: #10b981;
            color: #10b981;
        }

        .btn-resend:hover {
            background: #10b981;
            color: white;
        }

        .btn-regenerate {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .btn-regenerate:hover {
            background: #f59e0b;
            color: white;
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
            background: #e6f3ed;;
            color: #065f46;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .results-info {
            padding: 12px 16px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            font-size: 12px;
            color: #1e40af;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .default-info {
            padding: 12px 18px;
            border-radius: 4px;
            font-size: 12px;
            color: #92400e;
            margin-bottom: -4px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 46px;
        }

        /* Paginación */
        .pagination-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            padding: 15px 20px;
            background: white;
            border-radius: 0 0 8px 8px;
            margin-bottom: 20px;
        }

        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            transition: all 0.2s ease;
            min-width: 40px;
            text-align: center;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #e18018;
            background: #fff8f0;
            color: #e18018;
        }

        .pagination-btn.active {
            background: #e18018;
            color: white;
            border-color: #e18018;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-info {
            font-size: 13px;
            color: #e18018;
            padding: 0 15px;
            font-weight: 600;
        }

        .pagination-summary {
            font-size: 13px;
            color: #666;
            padding: 0;
            font-weight: 500;
            margin-top: 8px;
            text-align: center;
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
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav" style="margin-bottom: 20px;">
        <a href="{{ route('orders.index') }}" class="breadcrumb-link">
            <i class="fas fa-list"></i> Órdenes
        </a>
        <span class="breadcrumb-separator">
            <i class="fas fa-chevron-right"></i>
        </span>
        <span class="breadcrumb-current">Historial de Hoy</span>
    </div>

    <!-- Header -->
    <div class="history-header" style="gap: 15px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 0;">
            <h1 class="history-title">Órdenes de Hoy</h1>
            <p class="history-subtitle"><i class="fas fa-calendar-day"></i> <span id="todayDate"></span></p>
        </div>
    </div>

    <!-- Información por Defecto -->
    <div id="defaultInfo" class="default-info">
        <i class="fas fa-info-circle"></i>
        <span><strong>Solo muestra:</strong> Órdenes entregadas y canceladas del día de hoy</span>
    </div>

    <!-- Información de Búsqueda/Filtros Activos -->
    <div class="results-info" id="resultsInfo" style="display: none;">
        <i class="fas fa-check-circle"></i>
        <span id="resultsCount"></span>
    </div>

    <!-- Tabs para Elegir Vista -->
    <div class="tab-buttons" style="flex-wrap: wrap; justify-content: flex-start;">
        <button class="tab-btn active" data-tab="delivered" style="flex-shrink: 0;">
            <i class="fas fa-check-circle"></i> <span>Entregadas</span>
        </button>
        <button class="tab-btn" data-tab="cancelled" style="flex-shrink: 0;">
            <i class="fas fa-times-circle"></i> <span>Canceladas</span>
        </button>
    </div>

    <!-- Órdenes Entregadas -->
    <div id="tab-delivered" class="history-section">
        <div class="section-title">
            <span class="section-icon"><i class="fas fa-box-open"></i></span>
            <span>Órdenes Entregadas</span>
            <span class="accordion-count" id="deliveredCount">0</span>
        </div>
        <div class="orders-wrapper" id="deliveredContent">
            @if($deliveredOrders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <p class="empty-state-text">No hay órdenes entregadas hoy</p>
                </div>
            @else
                @foreach($deliveredOrders as $date => $orders)
                    @foreach($orders as $order)
                        <div class="order-row" data-order-id="{{ $order->order_id }}" data-order-number="{{ $order->order_number }}" data-customer="{{ $order->user->first()?->full_name ?? 'N/A' }}" data-status="delivered">
                            <div class="order-col-number">
                                <span>{{ $order->order_number }}</span>
                                <span class="status-badge badge-delivered">
                                    <i class="fas fa-check"></i> Entregada
                                </span>
                                <span id="receipt-badge-{{ $order->order_id }}">
                                    @php
                                        $hasPdf = false;
                                        if($order->receipts && count($order->receipts) > 0) {
                                            foreach($order->receipts as $receipt) {
                                                if($receipt->pdf_path !== null) {
                                                    $hasPdf = true;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($hasPdf)
                                        <span class="status-badge" style=" color: #065f46; margin-left: 5px;">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </span>
                                    @else
                                        <span class="status-badge" style=" color: #991b1b; margin-left: 5px;">
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
                @endforeach
            @endif
        </div>
        <!-- Paginación Entregadas -->
        <div class="pagination-container" id="deliveredPagination"></div>
    </div>

    <!-- Órdenes Canceladas -->
    <div id="tab-cancelled" class="history-section" style="display: none;">
        <div class="section-title">
            <span class="section-icon"><i class="fas fa-trash-alt"></i></span>
            <span>Órdenes Canceladas</span>
            <span class="accordion-count" id="cancelledCount">0</span>
        </div>
        <div class="orders-wrapper" id="cancelledContent">
            @if($cancelledOrders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">🚫</div>
                    <p class="empty-state-text">No hay órdenes canceladas hoy</p>
                </div>
            @else
                @foreach($cancelledOrders as $date => $orders)
                    @foreach($orders as $order)
                        <div class="order-row" data-order-id="{{ $order->order_id }}" data-order-number="{{ $order->order_number }}" data-customer="{{ $order->user->first()?->full_name ?? 'N/A' }}" data-status="cancelled">
                            <div class="order-col-number">
                                <span>{{ $order->order_number }}</span>
                                <span class="status-badge badge-cancelled">
                                    <i class="fas fa-ban"></i> Cancelada
                                </span>
                                <span id="receipt-badge-{{ $order->order_id }}">
                                    @php
                                        $hasPdf = false;
                                        if($order->receipts && count($order->receipts) > 0) {
                                            foreach($order->receipts as $receipt) {
                                                if($receipt->pdf_path !== null) {
                                                    $hasPdf = true;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($hasPdf)
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
                @endforeach
            @endif
        </div>
        <!-- Paginación Canceladas -->
        <div class="pagination-container" id="cancelledPagination"></div>
    </div>
</div>

<!-- Modal para datos del cliente (sin cliente registrado) -->
<div id="clientDataModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 3000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 450px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <h3 style="margin: 0 0 10px; color: #111; font-size: 18px;">
            <i class="fas fa-user-plus" style="margin-right: 8px; color: #e18018;"></i>
            Datos del Cliente
        </h3>
        <p style="margin: 0 0 20px; color: #666; font-size: 13px; line-height: 1.6;">
            Esta orden no tiene un cliente registrado. Por favor proporciona los datos del cliente para crear y enviar el comprobante.
        </p>
        
        <form id="clientDataForm" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 13px;">
                    Nombre del Cliente <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="modalClientName" placeholder="Ej: Juan Pérez" required style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333; font-size: 13px;">
                    Correo Electrónico <span style="color: #ef4444;">*</span>
                </label>
                <input type="email" id="modalClientEmail" placeholder="Ej: cliente@email.com" required style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 10px;">
                <button type="button" id="cancelClientModal" style="flex: 1; padding: 10px 16px; border: 2px solid #e5e7eb; background: white; color: #666; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    Cancelar
                </button>
                <button type="submit" style="flex: 1; padding: 10px 16px; background: linear-gradient(135deg, #e18018, #c9690f); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Variable global para almacenar los datos del cliente temporal y la acción pendiente
let clientDataTemp = {
    pendingAction: null,
    orderId: null,
    customerName: null,
    customerEmail: null
};

// Mostrar fecha actual
document.getElementById('todayDate').textContent = new Date().toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
});

// Sistema de Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Actualizar tabs activos
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Mostrar/ocultar secciones
        document.getElementById('tab-delivered').style.display = tab === 'delivered' ? 'block' : 'none';
        document.getElementById('tab-cancelled').style.display = tab === 'cancelled' ? 'block' : 'none';
    });
});

// Contar órdenes
function updateOrderCounts() {
    const deliveredCount = document.querySelectorAll('#deliveredContent .order-row').length;
    const cancelledCount = document.querySelectorAll('#cancelledContent .order-row').length;
    
    document.getElementById('deliveredCount').textContent = deliveredCount;
    document.getElementById('cancelledCount').textContent = cancelledCount;
}

// Acordeón
function toggleAccordion(header) {
    const content = header.nextElementSibling;
    const toggle = header.querySelector('.accordion-toggle');

    content.classList.toggle('collapsed');
    toggle.classList.toggle('collapsed');
}

// Paginación
const ITEMS_PER_PAGE = 5;
let currentPageDelivered = 1;
let currentPageCancelled = 1;

function renderPagination(containerId, currentPage, totalItems) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '';
    
    if (totalItems === 0) return;
    
    const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
    
    // Crear contenedor para los botones
    const btnContainer = document.createElement('div');
    btnContainer.style.display = 'flex';
    btnContainer.style.justifyContent = 'center';
    btnContainer.style.alignItems = 'center';
    btnContainer.style.gap = '4px';
    
    if (totalPages > 1) {
        // Botón Anterior
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.textContent = '‹';
        prevBtn.disabled = currentPage === 1;
        prevBtn.style.fontSize = '18px';
        prevBtn.style.padding = '8px 10px';
        prevBtn.onclick = () => {
            if (containerId === 'deliveredPagination' && currentPageDelivered > 1) {
                currentPageDelivered--;
                updatePaginationDisplay();
            } else if (containerId === 'cancelledPagination' && currentPageCancelled > 1) {
                currentPageCancelled--;
                updatePaginationDisplay();
            }
        };
        btnContainer.appendChild(prevBtn);
        
        // Números de página
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);
        
        if (startPage > 1) {
            const firstBtn = document.createElement('button');
            firstBtn.className = 'pagination-btn';
            firstBtn.textContent = '1';
            firstBtn.onclick = () => changePage(1, containerId);
            btnContainer.appendChild(firstBtn);
            
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'pagination-info';
                dots.textContent = '...';
                btnContainer.appendChild(dots);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => changePage(i, containerId);
            btnContainer.appendChild(btn);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'pagination-info';
                dots.textContent = '...';
                btnContainer.appendChild(dots);
            }
            
            const lastBtn = document.createElement('button');
            lastBtn.className = 'pagination-btn';
            lastBtn.textContent = totalPages;
            lastBtn.onclick = () => changePage(totalPages, containerId);
            btnContainer.appendChild(lastBtn);
        }
        
        // Botón Siguiente
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.textContent = '›';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.style.fontSize = '18px';
        nextBtn.style.padding = '8px 10px';
        nextBtn.onclick = () => {
            if (containerId === 'deliveredPagination' && currentPageDelivered < totalPages) {
                currentPageDelivered++;
                updatePaginationDisplay();
            } else if (containerId === 'cancelledPagination' && currentPageCancelled < totalPages) {
                currentPageCancelled++;
                updatePaginationDisplay();
            }
        };
        btnContainer.appendChild(nextBtn);
    }
    
    container.appendChild(btnContainer);
    
    // Agregar texto de resumen
    const pageStart = (currentPage - 1) * ITEMS_PER_PAGE + 1;
    const pageEnd = Math.min(currentPage * ITEMS_PER_PAGE, totalItems);
    const summaryText = document.createElement('div');
    summaryText.className = 'pagination-summary';
    const label = containerId === 'deliveredPagination' ? 'órdenes entregadas' : 'órdenes canceladas';
    summaryText.textContent = `Mostrando ${pageStart} a ${pageEnd} de ${totalItems} ${label}`;
    container.appendChild(summaryText);
}

function changePage(pageNumber, containerId) {
    if (containerId === 'deliveredPagination') currentPageDelivered = pageNumber;
    if (containerId === 'cancelledPagination') currentPageCancelled = pageNumber;
    updatePaginationDisplay();
}

function updatePaginationDisplay() {
    // Actualizar órdenes entregadas
    const allDeliveredRows = Array.from(document.querySelectorAll('#deliveredContent .order-row'));
    const visibleBySearchDelivered = allDeliveredRows.filter(row => row.dataset.searchVisible !== 'false');
    const totalDelivered = visibleBySearchDelivered.length;
    
    allDeliveredRows.forEach((row) => {
        const visibleIndex = visibleBySearchDelivered.indexOf(row);
        const pageStart = (currentPageDelivered - 1) * ITEMS_PER_PAGE;
        const pageEnd = pageStart + ITEMS_PER_PAGE;
        
        const isVisibleBySearch = row.dataset.searchVisible !== 'false';
        const isInPage = isVisibleBySearch && visibleIndex >= pageStart && visibleIndex < pageEnd;
        
        row.style.display = isInPage ? '' : 'none';
    });
    
    renderPagination('deliveredPagination', currentPageDelivered, totalDelivered);
    
    // Actualizar órdenes canceladas
    const allCancelledRows = Array.from(document.querySelectorAll('#cancelledContent .order-row'));
    const visibleBySearchCancelled = allCancelledRows.filter(row => row.dataset.searchVisible !== 'false');
    const totalCancelled = visibleBySearchCancelled.length;
    
    allCancelledRows.forEach((row) => {
        const visibleIndex = visibleBySearchCancelled.indexOf(row);
        const pageStart = (currentPageCancelled - 1) * ITEMS_PER_PAGE;
        const pageEnd = pageStart + ITEMS_PER_PAGE;
        
        const isVisibleBySearch = row.dataset.searchVisible !== 'false';
        const isInPage = isVisibleBySearch && visibleIndex >= pageStart && visibleIndex < pageEnd;
        
        row.style.display = isInPage ? '' : 'none';
    });
    
    renderPagination('cancelledPagination', currentPageCancelled, totalCancelled);
}

// Filtros en TIEMPO REAL usando el buscador del top-navbar
const topSearchInput = document.getElementById('topSearchInput');
if (topSearchInput) {
    topSearchInput.addEventListener('input', applyFiltersFromTopBar);
}

function applyFiltersFromTopBar() {
    const searchValue = document.getElementById('topSearchInput').value.toLowerCase().trim();
    applyFilters(searchValue);
}

function applyFilters(searchValue = '') {
    let visibleCount = 0;

    // Filtrar órdenes entregadas
    document.querySelectorAll('#deliveredContent .order-row').forEach(row => {
        const rowOrderNumber = row.dataset.orderNumber.toLowerCase();
        const rowCustomer = row.dataset.customer.toLowerCase();
        let visible = true;

        if (searchValue && !rowOrderNumber.includes(searchValue) && !rowCustomer.includes(searchValue)) {
            visible = false;
        }

        row.dataset.searchVisible = visible ? 'true' : 'false';
        if (visible) visibleCount++;
    });

    // Filtrar órdenes canceladas
    document.querySelectorAll('#cancelledContent .order-row').forEach(row => {
        const rowOrderNumber = row.dataset.orderNumber.toLowerCase();
        const rowCustomer = row.dataset.customer.toLowerCase();
        let visible = true;

        if (searchValue && !rowOrderNumber.includes(searchValue) && !rowCustomer.includes(searchValue)) {
            visible = false;
        }

        row.dataset.searchVisible = visible ? 'true' : 'false';
    });

    // Resetear a página 1 cuando se filtra
    currentPageDelivered = 1;
    currentPageCancelled = 1;

    // Actualizar UI
    if (searchValue) {
        document.getElementById('resultsInfo').style.display = 'flex';
        document.getElementById('defaultInfo').style.display = 'none';
        document.getElementById('resultsCount').textContent = `Se encontraron ${visibleCount} órdenes en el día actual`;
    } else {
        document.getElementById('resultsInfo').style.display = 'none';
        document.getElementById('defaultInfo').style.display = 'flex';
    }
    
    // Actualizar paginación
    updatePaginationDisplay();
}

// Funciones de Acciones para Comprobantes
// Manejadores del modal de datos de cliente
document.getElementById('cancelClientModal').addEventListener('click', () => {
    document.getElementById('clientDataModal').style.display = 'none';
    clientDataTemp = { pendingAction: null, orderId: null, customerName: null, customerEmail: null };
});

document.getElementById('clientDataForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const customerName = document.getElementById('modalClientName').value.trim();
    const customerEmail = document.getElementById('modalClientEmail').value.trim();
    
    if (!customerName || !customerEmail) {
        alert('Por favor completa todos los campos');
        return;
    }
    
    // Guardar datos y ejecutar acción pendiente
    clientDataTemp.customerName = customerName;
    clientDataTemp.customerEmail = customerEmail;
    
    // Cerrar modal
    document.getElementById('clientDataModal').style.display = 'none';
    
    // Ejecutar acción pendiente
    if (clientDataTemp.pendingAction === 'resend') {
        proceedResendReceipt(clientDataTemp.orderId, customerEmail, customerName);
    } else if (clientDataTemp.pendingAction === 'download') {
        proceedDownloadReceipt(clientDataTemp.orderId);
    } else if (clientDataTemp.pendingAction === 'regenerate') {
        proceedRegenerateReceipt(clientDataTemp.orderId, customerEmail, customerName);
    }
    
    // Limpiar formulario
    document.getElementById('clientDataForm').reset();
    clientDataTemp = { pendingAction: null, orderId: null, customerName: null, customerEmail: null };
});

// Función para validar cliente y abrir modal si es necesario
function checkClientAndProceed(orderId, action) {
    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/validar-cliente`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json().then(data => ({
            ok: response.ok,
            status: response.status,
            data: data
        })))
        .then(({ ok, status, data }) => {
            if (ok && data.hasValidClient) {
                // Cliente válido, proceder directamente sin datos temporales
                if (action === 'resend') {
                    proceedResendWithoutModal(orderId);
                } else if (action === 'download') {
                    proceedDownloadReceipt(orderId);
                } else if (action === 'regenerate') {
                    proceedRegenerateReceipt(orderId);
                }
            } else if (ok && !data.hasValidClient) {
                // No hay cliente válido, pedir datos
                clientDataTemp.pendingAction = action;
                clientDataTemp.orderId = orderId;
                
                // Mostrar modal
                document.getElementById('clientDataModal').style.display = 'flex';
                document.getElementById('modalClientName').focus();
            } else {
                Swal.fire('Error', 'Error al validar los datos del cliente', 'error');
            }
        })
        .catch(error => {
            console.error('Error validando cliente:', error);
            Swal.fire('Error', 'No se pudo validar los datos del cliente', 'error');
        });
}

// Función para reenviar sin datos modales (cliente válido)
function proceedResendWithoutModal(orderId) {
    Swal.fire({
        title: 'Procesando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(response => response.json().then(data => ({
            ok: response.ok,
            status: response.status,
            data: data
        })))
        .then(({ ok, status, data }) => {
            if (ok && data.success) {
                swToast({icon: 'success', title: data.message});
            } else {
                Swal.fire('Error', data.message || 'Error al reenviar el comprobante', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo reenviar el comprobante', 'error');
        });
}

// Función para reenviar CON datos modales (cliente inválido)
function proceedResendReceipt(orderId, customerEmail, customerName) {
    Swal.fire({
        title: 'Procesando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const payload = {
        customer_email: customerEmail,
        customer_name: customerName
    };

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(response => response.json().then(data => ({
            ok: response.ok,
            status: response.status,
            data: data
        })))
        .then(({ ok, status, data }) => {
            if (ok && data.success) {
                swToast({icon: 'success', title: data.message});
            } else {
                Swal.fire('Error', data.message || 'Error al reenviar el comprobante', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo reenviar el comprobante', 'error');
        });
}

function proceedDownloadReceipt(orderId) {
    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/descargar`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.blob();
        })
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
            Swal.fire('Error', 'No se pudo descargar el comprobante. Intenta regenerarlo primero.', 'error');
        });
}

function proceedRegenerateReceipt(orderId, customerEmail = null, customerName = null) {
    Swal.fire({
        title: 'Procesando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const payload = {
        customer_email: customerEmail || null,
        customer_name: customerName || null
    };

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/regenerar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(response => response.json().then(data => ({
            ok: response.ok,
            status: response.status,
            data: data
        })))
        .then(({ ok, status, data }) => {
            if (ok && data.success) {
                swToast({icon: 'success', title: data.message});
                updateReceiptBadge(orderId);
            } else {
                Swal.fire('Error', data.message || 'Error al regenerar el comprobante', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo regenerar el comprobante', 'error');
        });
}

function downloadReceipt(orderId) {
    // Primero validar que haya cliente, si no, pedir datos
    checkClientAndProceed(orderId, 'download');
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
            // Primero validar que haya cliente, si no, pedir datos
            checkClientAndProceed(orderId, 'regenerate');
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
            // Primero validar que haya cliente, si no, pedir datos
            checkClientAndProceed(orderId, 'resend');
        }
    });
}


function updateReceiptBadge(orderId) {
    const badgeElement = document.getElementById(`receipt-badge-${orderId}`);
    if (!badgeElement) return;
    badgeElement.innerHTML = '<span class="status-badge" style="background: #d1fae5; color: #065f46; margin-left: 5px;"><i class="fas fa-file-pdf"></i> PDF</span>';
}

// Inicializar conteos y paginación
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar data-search-visible en todas las órdenes
    document.querySelectorAll('#deliveredContent .order-row').forEach(row => {
        row.dataset.searchVisible = 'true';
    });
    document.querySelectorAll('#cancelledContent .order-row').forEach(row => {
        row.dataset.searchVisible = 'true';
    });
    
    updateOrderCounts();
    updatePaginationDisplay();
});
</script>
@endpush

@endsection
