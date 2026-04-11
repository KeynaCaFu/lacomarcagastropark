<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - La Comarca Gastro Park</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary:       #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --bg:            #0A0908;
            --surface:       #111009;
            --card:          #161310;
            --card-hover:    #1D1914;
            --border:        #252018;
            --text:          #F5F0E8;
            --muted:         #7A7060;
            --radius:        14px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }

        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,9,8,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .header-inner {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: var(--radius);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-back:hover { background: var(--card-hover); color: var(--text); }

        .header-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 600;
        }

        .cart-empty {
            text-align: center;
            padding: 60px 20px;
        }

        .cart-empty-icon {
            font-size: 3rem;
            color: var(--muted);
            margin-bottom: 16px;
        }

        .cart-empty h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: var(--text);
        }

        .cart-empty p {
            color: var(--muted);
            margin-bottom: 24px;
        }

        .btn-shop {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
        }
        .btn-shop:hover { background: #c06830; }

        .cart-list {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .cart-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            border-bottom: 1px solid var(--border);
            align-items: flex-start;
        }
        .cart-item:last-child { border-bottom: none; }

        .cart-item-image {
            width: 80px;
            height: 80px;
            background: var(--surface);
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text);
        }

        .cart-item-meta {
            font-size: 0.75rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .cart-item-customization {
            font-size: 0.75rem;
            background: var(--surface);
            padding: 6px 10px;
            border-radius: 4px;
            max-height: 40px;
            overflow: hidden;
            color: var(--muted);
            border-left: 2px solid var(--primary);
        }

        .cart-item-qty {
            text-align: right;
            min-width: 100px;
        }

        .cart-item-qty p {
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .cart-item-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }

        .cart-summary {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            margin-top: 24px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .summary-row.total {
            border-top: 1px solid var(--border);
            padding-top: 12px;
            margin-top: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }

        .checkout-section {
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }

        .btn-checkout {
            flex: 1;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
        }
        .btn-checkout:hover { background: #c06830; }

        .btn-continue {
            flex: 1;
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--border);
            padding: 12px 20px;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-continue:hover { background: var(--card); }

        .customer-data {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            margin-top: 24px;
            font-size: 0.85rem;
        }

        .customer-data h4 {
            font-size: 0.9rem;
            margin-bottom: 12px;
            color: var(--text);
        }

        .customer-data-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 8px;
        }

        .customer-data-field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .customer-data-label {
            color: var(--muted);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .customer-data-value {
            color: var(--text);
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="{{ route('plaza.index') }}" class="btn-back">
            <i class="fas fa-arrow-left" style="font-size: 0.7rem;"></i>
            Volver a Plaza
        </a>
        <h1 class="header-label">Mi Carrito</h1>
        <div style="width: 100px;"></div>
    </div>
</header>

<div class="container">
    @if(empty($cart))
        <div class="cart-empty">
            <div class="cart-empty-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2>Tu carrito está vacío</h2>
            <p>Agrega algunos productos para comenzar</p>
            <a href="{{ route('plaza.index') }}" class="btn-shop">Ir a la Plaza</a>
        </div>
    @else
        <div class="cart-list">
            @foreach($cart as $item)
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="{{ $item['photo_url'] }}" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('images/product-placeholder.png') }}'">
                </div>
                <div class="cart-item-details">
                    <div class="cart-item-name">{{ $item['name'] }}</div>
                    <div class="cart-item-meta">Local ID: {{ $item['local_id'] }} | Producto ID: {{ $item['product_id'] }}</div>
                    @if($item['customization'])
                    <div class="cart-item-customization">{{ $item['customization'] }}</div>
                    @endif
                </div>
                <div class="cart-item-qty">
                    <p>Cantidad: <strong>{{ $item['quantity'] }}</strong></p>
                    <p>Unitario: ₡{{ number_format($item['price'], 2) }}</p>
                    <div class="cart-item-price">₡{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- DATOS DEL CLIENTE (del primer item) -->
        @if(isset($cart[0]) && $cart[0]['customer_name'])
        <div class="customer-data">
            <h4><i class="fas fa-user" style="margin-right: 6px;"></i> Datos de Entrega</h4>
            <div class="customer-data-row">
                <div class="customer-data-field">
                    <span class="customer-data-label">Nombre</span>
                    <span class="customer-data-value">{{ $cart[0]['customer_name'] }}</span>
                </div>
                <div class="customer-data-field">
                    <span class="customer-data-label">Email</span>
                    <span class="customer-data-value">{{ $cart[0]['customer_email'] }}</span>
                </div>
            </div>
            <div class="customer-data-row">
                <div class="customer-data-field">
                    <span class="customer-data-label">Teléfono</span>
                    <span class="customer-data-value">{{ $cart[0]['customer_phone'] }}</span>
                </div>
                @if($cart[0]['delivery_address'])
                <div class="customer-data-field">
                    <span class="customer-data-label">Dirección</span>
                    <span class="customer-data-value">{{ $cart[0]['delivery_address'] }}</span>
                </div>
                @endif
            </div>
            @if($cart[0]['additional_notes'])
            <div class="customer-data-row">
                <div class="customer-data-field" style="grid-column: 1/-1;">
                    <span class="customer-data-label">Notas Adicionales</span>
                    <span class="customer-data-value">{{ $cart[0]['additional_notes'] }}</span>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- RESUMEN -->
        <div class="cart-summary">
            <div class="summary-row">
                <span>Items:</span>
                <strong>{{ count($cart) }}</strong>
            </div>
            <div class="summary-row">
                <span>Total Unidades:</span>
                <strong>{{ array_sum(array_column($cart, 'quantity')) }}</strong>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>₡{{ number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $cart)), 2) }}</span>
            </div>
        </div>

        <div class="checkout-section">
            <button class="btn-checkout" onclick="alert('🚀 Checkout coming soon! (en la siguiente historia)')">
                <i class="fas fa-credit-card"></i> Proceder al Pago
            </button>
            <a href="{{ route('plaza.index') }}" class="btn-continue">
                <i class="fas fa-plus"></i> Agregar Más
            </a>
        </div>
    @endif
</div>

</body>
</html>
