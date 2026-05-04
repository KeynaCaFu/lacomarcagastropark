```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mis Reseñas - La Comarca</title>

    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,600&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        :root {
            --primary: #D4773A;
            --primary-glow: rgba(212,119,58,0.18);
            --primary-dark: #A9541F;
            --amber: #E8A838;
            --bg: #0A0805;
            --surface: #12100E;
            --surface2: #1C1814;
            --surface3: #231F1A;
            --border: rgba(255,255,255,0.06);
            --border-warm: rgba(212,119,58,0.22);
            --text: #F5EFE4;
            --text2: #C4B89A;
            --muted: #8A7D69;
            --danger: #e74c3c;
            --danger-muted: rgba(231,76,60,0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }

        /* ── HEADER ── */
        .page-header {
            background: rgba(18,16,14,0.88);
            border-bottom: 1px solid var(--border-warm);
            padding: 0 1.75rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(18px);
        }
        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 0.3px;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--primary); }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-brand img { height: 26px; opacity: 0.92; }
        .header-brand span {
            font-family: 'Cormorant Garamond', serif;
            font-size: 16px;
            font-weight: 400;
            color: var(--primary);
            letter-spacing: 0.5px;
        }

        /* ── PAGE WRAP ── */
        .page-wrap {
            max-width: 840px;
            margin: 0 auto;
            padding: 0 1.25rem 5rem;
            position: relative;
            z-index: 1;
        }

        /* ── HERO BANNER ── */
        .hero-banner {
            position: relative;
            padding: 3rem 2rem 2.5rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 80% at 10% 50%, rgba(212,119,58,0.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 90% 20%, rgba(232,168,56,0.07) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-deco {
            position: absolute;
            top: 50%;
            right: -30px;
            transform: translateY(-50%);
            font-size: 180px;
            opacity: 0.03;
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            color: var(--primary);
            pointer-events: none;
            user-select: none;
            line-height: 1;
        }
        .hero-inner {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
        }
        .avatar-ring {
            position: relative;
            flex-shrink: 0;
        }
        .avatar-ring::before {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, var(--primary), var(--amber), var(--primary-dark), var(--primary));
            animation: spin 8s linear infinite;
            opacity: 0.7;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .avatar-circle {
            position: relative;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            color: #fff;
            z-index: 1;
            border: 2px solid var(--bg);
        }
        .hero-text h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 36px;
            font-weight: 600;
            line-height: 1.1;
            color: var(--text);
        }
        .hero-text h1 em {
            color: var(--primary);
            font-style: italic;
        }
        .hero-text p {
            font-size: 13px;
            color: var(--muted);
            margin-top: 5px;
            letter-spacing: 0.2px;
        }

        /* ── DIVIDER ── */
        .fancy-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 1.75rem;
            opacity: 0.4;
        }
        .fancy-divider::before,
        .fancy-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }
        .fancy-divider i { color: var(--primary); font-size: 10px; }

        /* ── STATS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 1.75rem;
        }
        .stat-card {
            background: var(--surface2);
            border: 1px solid var(--border-warm);
            border-radius: 16px;
            padding: 1.1rem 1.2rem;
            transition: border-color 0.25s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            opacity: 0;
            transition: opacity 0.25s;
        }
        .stat-card:hover { border-color: rgba(212,119,58,0.45); transform: translateY(-2px); }
        .stat-card:hover::before { opacity: 1; }
        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            color: var(--primary);
            font-size: 13px;
        }
        .stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
        .stat-value { font-size: 28px; font-weight: 600; color: var(--text); line-height: 1; font-family: 'Cormorant Garamond', serif; }
        .stat-sub { font-size: 11px; color: var(--muted); margin-top: 4px; }

        /* ── TABS ── */
        .tabs-wrap {
            background: var(--surface2);
            border: 1px solid var(--border-warm);
            border-radius: 14px;
            padding: 5px;
            display: flex;
            gap: 4px;
            margin-bottom: 1.5rem;
        }
        .tab-btn {
            flex: 1;
            padding: 10px 16px;
            background: transparent;
            border: none;
            border-radius: 10px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .tab-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(212,119,58,0.35);
        }
        .tab-btn:not(.active):hover { color: var(--text); background: var(--surface3); }
        .tab-badge {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .tab-btn.active .tab-badge { background: rgba(255,255,255,0.25); }

        /* ── REVIEW CARD ── */
        .review-card {
            position: relative;
            background: var(--surface);
            border: 1px solid var(--border-warm);
            border-radius: 20px;
            padding: 0;
            margin-bottom: 14px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            animation: fadeUp 0.35s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .review-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.35), 0 0 0 1px rgba(212,119,58,0.22);
            border-color: rgba(212,119,58,0.4);
        }
        .card-accent {
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--primary), var(--amber), var(--primary-dark));
        }
        .card-body { padding: 1.25rem 1.35rem 1rem 1.5rem; }

        /* ── CARD HEADER ── */
        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }
        .reviewer-avatar {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(212,119,58,0.3);
        }
        .card-title-wrap { flex: 1; min-width: 0; }
        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-sub {
            font-size: 12px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .review-tag {
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid var(--border-warm);
            color: var(--primary);
            background: var(--primary-glow);
            white-space: nowrap;
            align-self: flex-start;
            letter-spacing: 0.3px;
            font-weight: 500;
        }

        /* ── STARS ── */
        .stars-row {
            display: flex;
            gap: 3px;
            margin-bottom: 10px;
            align-items: center;
        }
        .stars-row i { font-size: 13px; }
        .rating-num {
            font-size: 12px;
            color: var(--muted);
            margin-left: 6px;
        }

        /* ── COMMENT ── */
        .review-comment {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            font-style: normal;
            color: var(--text);
            line-height: 1.7;
            margin-bottom: 12px;
            padding-left: 12px;
            border-left: 2px solid rgba(212,119,58,0.25);
        }

        /* ── RESPONSE ── */
        .response-block {
            margin: 10px 0 12px;
            padding: 11px 14px;
            border-left: 2px solid var(--primary);
            border-radius: 0 10px 10px 0;
            background: rgba(212,119,58,0.06);
        }
        .response-label {
            font-size: 10px;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .response-text {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ── CARD FOOTER ── */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 1.35rem 10px 1.5rem;
            border-top: 1px solid var(--border);
            background: rgba(0,0,0,0.15);
        }
        .card-meta {
            font-size: 11px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.2px;
        }
        .btn-trash {
            background: transparent;
            border: 1px solid transparent;
            color: var(--danger-muted);
            cursor: pointer;
            font-size: 13px;
            padding: 5px 9px;
            border-radius: 8px;
            transition: all 0.2s;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-trash span { font-size: 11px; font-family: 'DM Sans', sans-serif; }
        .btn-trash:hover {
            color: var(--danger);
            background: rgba(231,76,60,0.08);
            border-color: rgba(231,76,60,0.2);
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 5rem 1rem 4rem;
            color: var(--muted);
        }
        .empty-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--surface2);
            border: 1px solid var(--border-warm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .empty-icon-wrap i { font-size: 26px; color: var(--muted); opacity: 0.5; }
        .empty-state p { font-size: 14px; line-height: 1.6; }
        .empty-state small { font-size: 12px; color: var(--muted); opacity: 0.7; }

        /* ── PAGINATION ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-top: 1.5rem;
        }
        .page-btn {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: 1px solid var(--border-warm);
            background: var(--surface2);
            color: var(--muted);
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s;
        }
        .page-btn:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-glow);
        }
        .page-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(212,119,58,0.4);
        }
        .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
        .page-sep { font-size: 12px; color: var(--muted); padding: 0 4px; }

        /* ── FLOATING DECORATION ── */
        .page-deco {
            position: fixed;
            pointer-events: none;
            z-index: 0;
        }
        .page-deco-1 {
            top: 15%;
            right: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212,119,58,0.06) 0%, transparent 70%);
        }
        .page-deco-2 {
            bottom: 20%;
            left: -100px;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232,168,56,0.04) 0%, transparent 70%);
        }

        .review-card:nth-child(1) { animation-delay: 0.05s; }
        .review-card:nth-child(2) { animation-delay: 0.1s; }
        .review-card:nth-child(3) { animation-delay: 0.15s; }
        .review-card:nth-child(4) { animation-delay: 0.2s; }
        .review-card:nth-child(5) { animation-delay: 0.25s; }

        @media (max-width: 600px) {
            .stats-row { grid-template-columns: repeat(3, 1fr); gap: 7px; }
            .stat-value { font-size: 22px; }
            .hero-text h1 { font-size: 27px; }
            .hero-deco { font-size: 120px; }
        }
    </style>
</head>
<body>

<div class="page-deco page-deco-1"></div>
<div class="page-deco page-deco-2"></div>

<div id="app">

    <!-- HEADER -->
    <header class="page-header">
        <a href="{{ route('plaza.index') }}" class="back-link">
            <i class="fas fa-chevron-left"></i> Volver a la plaza
        </a>
        <div class="header-brand">
            <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca">
            <span>La Comarca Gastropark</span>
        </div>
        <div style="width:110px;"></div>
    </header>

    <div class="page-wrap">

        <!-- HERO BANNER -->
        <div class="hero-banner">
            <div class="hero-deco">★</div>
            <div class="hero-inner">
                <div class="avatar-ring">
                    <div class="avatar-circle">{{ $iniciales }}</div>
                </div>
                <div class="hero-text">
                    <h1>Mis <em>Reseñas</em></h1>
                    <p>{{ auth()->user()->full_name ?? auth()->user()->name }} &nbsp;·&nbsp; {{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- DIVIDER -->
        <div class="fancy-divider">
            <i class="fas fa-utensils"></i>
        </div>

        <!-- STATS -->
        @php
            $todas = $resenasLocales->pluck('review.rating')
                ->merge($resenasProductos->pluck('review.rating'))
                ->filter();
            $promedio = $todas->count() ? round($todas->avg(), 1) : '—';
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-store"></i></div>
                <div class="stat-label">Locales</div>
                <div class="stat-value">{{ $resenasLocales->count() }}</div>
                <div class="stat-sub">reseñados</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-utensils"></i></div>
                <div class="stat-label">Productos</div>
                <div class="stat-value">{{ $resenasProductos->count() }}</div>
                <div class="stat-sub">reseñados</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-label">Promedio</div>
                <div class="stat-value">{{ $promedio }}</div>
                @if($todas->count())
                    <div class="stat-sub">de 5 estrellas</div>
                @endif
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs-wrap">
            <button class="tab-btn" :class="{ active: tab === 'locales' }" @click="cambiarTab('locales')">
                <i class="fas fa-store"></i>
                Por local
                <span class="tab-badge">@{{ resenasLocales.length }}</span>
            </button>
            <button class="tab-btn" :class="{ active: tab === 'productos' }" @click="cambiarTab('productos')">
                <i class="fas fa-bowl-food"></i>
                Por producto
                <span class="tab-badge">@{{ resenasProductos.length }}</span>
            </button>
        </div>

        <!-- TAB: LOCALES -->
        <div v-show="tab === 'locales'">
            <div v-if="resenasLocales.length === 0" class="empty-state">
                <div class="empty-icon-wrap"><i class="fas fa-store"></i></div>
                <p>Aún no has reseñado ningún local</p>
                <small>Visita un local y comparte tu experiencia</small>
            </div>
            <template v-else>
                <div
                    v-for="(r, i) in resenasLocalesPaginadas"
                    :key="r.local_review_id"
                    class="review-card"
                >
                    <div class="card-accent"></div>
                    <div class="card-body">
                        <div class="card-header">
                            <div class="reviewer-avatar">@{{ iniciales }}</div>
                            <div class="card-title-wrap">
                                <div class="card-title">@{{ r.local_name }}</div>
                                <div class="card-sub">
                                    <i class="fas fa-map-marker-alt" style="font-size:10px; color:var(--primary);"></i>
                                    Local reseñado
                                </div>
                            </div>
                            <span class="review-tag"><i class="fas fa-store" style="font-size:9px;"></i> Local</span>
                        </div>

                        <div class="stars-row">
                            <i v-for="s in 5" :key="s" class="fas fa-star"
                               :style="{ color: s <= r.rating ? '#E8A838' : 'rgba(232,168,56,0.14)' }"></i>
                            <span class="rating-num">@{{ r.rating }}/5</span>
                        </div>

                        <p class="review-comment">@{{ r.comment }}</p>

                        <div v-if="r.response" class="response-block">
                            <div class="response-label"><i class="fas fa-reply"></i> &nbsp;Respuesta del local</div>
                            <p class="response-text">@{{ r.response }}</p>
                        </div>
                    </div>

                    <div class="card-footer">
                        <span class="card-meta">
                            <i class="fas fa-clock" style="color:var(--primary); font-size:10px;"></i>
                            @{{ formatDate(r.date) }}
                        </span>
                        <button class="btn-trash" @click="eliminarLocal(r)" title="Eliminar reseña">
                            <i class="fas fa-trash-alt"></i>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>

                <div class="pagination-wrap" v-if="totalPagesLocales > 1">
                    <button class="page-btn" @click="pageLocales--" :disabled="pageLocales === 1">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template v-for="p in totalPagesLocales" :key="p">
                        <button class="page-btn" :class="{ active: pageLocales === p }" @click="pageLocales = p">
                            @{{ p }}
                        </button>
                    </template>
                    <button class="page-btn" @click="pageLocales++" :disabled="pageLocales === totalPagesLocales">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <span class="page-sep">@{{ pageLocales }} / @{{ totalPagesLocales }}</span>
                </div>
            </template>
        </div>

        <!-- TAB: PRODUCTOS -->
        <div v-show="tab === 'productos'">
            <div v-if="resenasProductos.length === 0" class="empty-state">
                <div class="empty-icon-wrap"><i class="fas fa-bowl-food"></i></div>
                <p>Aún no has reseñado ningún producto</p>
                <small>Prueba algo nuevo y cuéntanos qué te pareció</small>
            </div>
            <template v-else>
                <div
                    v-for="(r, i) in resenasProductosPaginadas"
                    :key="r.product_review_id"
                    class="review-card"
                >
                    <div class="card-accent"></div>
                    <div class="card-body">
                        <div class="card-header">
                            <div class="reviewer-avatar">@{{ iniciales }}</div>
                            <div class="card-title-wrap">
                                <div class="card-title">@{{ r.product_name }}</div>
                                <div class="card-sub">
                                    <i class="fas fa-store" style="font-size:10px; color:var(--primary);"></i>
                                    @{{ r.local_name }}
                                </div>
                            </div>
                            <span class="review-tag">@{{ r.category || 'Producto' }}</span>
                        </div>

                        <div class="stars-row">
                            <i v-for="s in 5" :key="s" class="fas fa-star"
                               :style="{ color: s <= r.rating ? '#E8A838' : 'rgba(232,168,56,0.14)' }"></i>
                            <span class="rating-num">@{{ r.rating }}/5</span>
                        </div>

                        <p class="review-comment">@{{ r.comment }}</p>

                        <div v-if="r.response" class="response-block">
                            <div class="response-label"><i class="fas fa-reply"></i> &nbsp;Respuesta del local</div>
                            <p class="response-text">@{{ r.response }}</p>
                        </div>
                    </div>

                    <div class="card-footer">
                        <span class="card-meta">
                            <i class="fas fa-clock" style="color:var(--primary); font-size:10px;"></i>
                            @{{ formatDate(r.date) }}
                        </span>
                        <button class="btn-trash" @click="eliminarProducto(r)" title="Eliminar reseña">
                            <i class="fas fa-trash-alt"></i>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>

                <div class="pagination-wrap" v-if="totalPagesProductos > 1">
                    <button class="page-btn" @click="pageProductos--" :disabled="pageProductos === 1">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template v-for="p in totalPagesProductos" :key="p">
                        <button class="page-btn" :class="{ active: pageProductos === p }" @click="pageProductos = p">
                            @{{ p }}
                        </button>
                    </template>
                    <button class="page-btn" @click="pageProductos++" :disabled="pageProductos === totalPagesProductos">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <span class="page-sep">@{{ pageProductos }} / @{{ totalPagesProductos }}</span>
                </div>
            </template>
        </div>

    </div>
</div>

@include('plaza.carrito._toast-notifications')

<script>
    const showToast = (config) => { if (window.showNotification) { window.showNotification(config); } };

    const initSwToast = () => {
        if (typeof Swal !== 'undefined' && !window.swToast) {
            window.swToast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 7000,
                timerProgressBar: true,
                background: '#161310',
                color: '#F5F0E8',
                customClass: {
                    container: 'custom-toast-container',
                    popup: 'custom-toast-popup',
                    title: 'custom-toast-title',
                    icon: 'custom-toast-icon'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }
    };
    if (typeof Swal !== 'undefined') initSwToast();

    const { createApp } = Vue;
    createApp({
        data() {
            return {
                tab: 'locales',
                iniciales: '{{ $iniciales }}',
                perPage: 5,
                pageLocales: 1,
                pageProductos: 1,

                resenasLocales: {!! json_encode($resenasLocales->map(function($lr) {
                    return [
                        'local_review_id' => $lr->local_review_id,
                        'local_id'        => $lr->local_id,
                        'local_name'      => $lr->local->name ?? 'Local',
                        'rating'          => $lr->review->rating ?? 0,
                        'comment'         => $lr->review->comment ?? '',
                        'response'        => $lr->review->response ?? null,
                        'date'            => $lr->review->date ?? $lr->created_at,
                    ];
                })) !!},

                resenasProductos: {!! json_encode($resenasProductos->map(function($pr) {
                    return [
                        'product_review_id' => $pr->product_review_id,
                        'product_id'        => $pr->product_id,
                        'product_name'      => $pr->product->name ?? 'Producto',
                        'local_name'        => optional($pr->product->locals->first())->name ?? '',
                        'category'          => $pr->product->category ?? '',
                        'rating'            => $pr->review->rating ?? 0,
                        'comment'           => $pr->review->comment ?? '',
                        'response'          => $pr->review->response ?? null,
                        'date'              => $pr->review->date ?? $pr->created_at,
                    ];
                })) !!},
            }
        },

        computed: {
            totalPagesLocales() {
                return Math.max(1, Math.ceil(this.resenasLocales.length / this.perPage));
            },
            resenasLocalesPaginadas() {
                const start = (this.pageLocales - 1) * this.perPage;
                return this.resenasLocales.slice(start, start + this.perPage);
            },
            totalPagesProductos() {
                return Math.max(1, Math.ceil(this.resenasProductos.length / this.perPage));
            },
            resenasProductosPaginadas() {
                const start = (this.pageProductos - 1) * this.perPage;
                return this.resenasProductos.slice(start, start + this.perPage);
            },
        },

        methods: {
            cambiarTab(t) { this.tab = t; },

            formatDate(dateString) {
                if (!dateString) return '—';
                return new Date(dateString).toLocaleDateString('es-CR', {
                    year: 'numeric', month: 'short', day: 'numeric'
                });
            },

            async eliminarLocal(resena) {
                const result = await Swal.fire({
                    title: '¿Eliminar reseña?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#14110f',
                    color: '#F5F0E8',
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#3a3530',
                });
                if (!result.isConfirmed) return;

                try {
                    const res = await fetch(`/plaza/${resena.local_id}/review/${resena.local_review_id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const idx = this.resenasLocales.findIndex(r => r.local_review_id === resena.local_review_id);
                        if (idx !== -1) this.resenasLocales.splice(idx, 1);
                        if (this.pageLocales > this.totalPagesLocales) this.pageLocales = this.totalPagesLocales;
                        showToast({ icon: 'success', title: 'Reseña eliminada', message: 'Tu reseña del local fue eliminada.', timer: 5500 });
                    } else {
                        showToast({ icon: 'error', title: 'Error', message: 'No se pudo eliminar la reseña.', timer: 5500 });
                    }
                } catch (e) {
                    showToast({ icon: 'error', title: 'Error de conexión', timer: 5000 });
                }
            },

            async eliminarProducto(resena) {
                const result = await Swal.fire({
                    title: '¿Eliminar reseña?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#14110f',
                    color: '#F5F0E8',
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#3a3530',
                });
                if (!result.isConfirmed) return;

                try {
                    const res = await fetch(`/plaza/producto/${resena.product_review_id}/resena`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const idx = this.resenasProductos.findIndex(r => r.product_review_id === resena.product_review_id);
                        if (idx !== -1) this.resenasProductos.splice(idx, 1);
                        if (this.pageProductos > this.totalPagesProductos) this.pageProductos = this.totalPagesProductos;
                        showToast({ icon: 'success', title: 'Reseña eliminada', message: 'Tu reseña del producto fue eliminada.', timer: 5500 });
                    } else {
                        showToast({ icon: 'error', title: 'Error', message: 'No se pudo eliminar la reseña.', timer: 5500 });
                    }
                } catch (e) {
                    showToast({ icon: 'error', title: 'Error de conexión', timer: 5000 });
                }
            }
        },

        mounted() { initSwToast(); }
    }).mount('#app');
</script>
</body>
</html>
