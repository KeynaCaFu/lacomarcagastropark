<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $local->name }} - La Comarca Gastro Park</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:       #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --primary-glow:  rgba(212,119,58,0.25);
            --bg:            #0A0908;
            --surface:       #111009;
            --card:          #161310;
            --card-hover:    #1D1914;
            --border:        #252018;
            --border-light:  #302820;
            --text:          #F5F0E8;
            --muted:         #7A7060;
            --radius:        14px;
            --radius-sm:     8px;
        }

        html, body { width: 100%; min-height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ── HEADER ─────────────────────────────── */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10,9,8,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--card);
            border: 1px solid var(--border-light);
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-back:hover { background: var(--card-hover); color: var(--text); }
        .btn-back i { font-size: 0.7rem; }

        .header-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: var(--text);
        }

        /* auth user menu */
        .user-menu-top { position: relative; }
        .user-menu-btn {
            background: none;
            border: 1px solid var(--border-light);
            cursor: pointer;
            padding: 7px 12px;
            color: var(--primary);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.75rem;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
        }
        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            min-width: 190px;
            z-index: 1000;
            display: none;
            overflow: hidden;
        }
        .user-menu-dropdown.open { display: block; }
        .dropdown-header {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
        }
        .dropdown-name { font-weight: 600; font-size: 0.8rem; color: var(--text); }
        .dropdown-email { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
            cursor: pointer;
            background: none;
            border-left: none;
            border-right: none;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: var(--card-hover); }
        .dropdown-item.danger { color: #e05c5c; }

        /* ── HERO ────────────────────────────────── */
        .local-hero {
            position: relative;
            height: 35vh;
            min-height: 280px;
            max-height: 380px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
        }
        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.55);
            transition: transform 8s ease;
        }
        .local-hero:hover .hero-bg img { transform: scale(1.04); }

        .hero-gradient {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to top,  var(--bg) 0%, rgba(10,9,8,0.7) 35%, transparent 70%),
                linear-gradient(to right, rgba(10,9,8,0.4) 0%, transparent 60%);
        }

        .hero-body {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px 20px;
            display: flex;
            align-items: flex-end;
            gap: 14px;
        }

        .hero-logo-ring {
            width: 64px;
            height: 64px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid rgba(212,119,58,0.6);
            box-shadow: 0 6px 24px rgba(0,0,0,0.5), 0 0 0 3px rgba(212,119,58,0.1);
            flex-shrink: 0;
        }
        .hero-logo-ring img { width: 100%; height: 100%; object-fit: cover; }

        .hero-text { flex: 1; min-width: 0; }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--primary-light);
            border: 1px solid var(--primary-glow);
            color: var(--primary);
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 3px 8px;
            border-radius: 999px;
            margin-bottom: 6px;
        }

        .hero-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.05;
            color: var(--text);
            margin-bottom: 4px;
        }

        .hero-desc {
            font-size: 0.75rem;
            color: rgba(245,240,232,0.55);
            line-height: 1.4;
        }

        /* ── CATEGORY BAR ────────────────────────── */
        .cat-strip {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            position: sticky;
            top: 57px;
            z-index: 90;
        }

        .cat-scroll {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .cat-scroll::-webkit-scrollbar { display: none; }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 500;
            border: 1px solid var(--border-light);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
            flex-shrink: 0;
        }
        .cat-pill:hover { color: var(--text); border-color: var(--border-light); background: var(--card); }
        .cat-pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        /* ── SECTION INTRO ───────────────────────── */
        .menu-section {
            padding: 52px 0 80px;
        }

        .menu-intro {
            margin-bottom: 40px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .menu-intro-left {}

        .section-eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .section-heading {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.1;
        }

        .section-heading em { font-style: italic; color: var(--primary); }

        .item-count {
            font-size: 0.78rem;
            color: var(--muted);
            padding: 6px 12px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 999px;
        }

        /* ── PRODUCT GRID ────────────────────────── */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2px;
        }

        @media (min-width: 640px)  { .products-grid { grid-template-columns: repeat(2, 1fr); gap: 2px; } }
        @media (min-width: 900px)  { .products-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }

        /* ── PRODUCT CARD ────────────────────────── */
        .p-card {
            position: relative;
            background: var(--card);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s;
            border: 1px solid var(--border);
        }

        .p-card:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 20px 48px rgba(0,0,0,0.55), 0 0 0 1px var(--primary-glow);
            z-index: 2;
        }

        .p-card-img {
            position: relative;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #0d0b08;
        }

        .p-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(.22,.68,0,1.2), filter 0.4s;
            filter: brightness(0.9);
        }

        .p-card:hover .p-card-img img {
            transform: scale(1.1);
            filter: brightness(1);
        }

        /* shine overlay on hover */
        .p-card-img::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s;
        }
        .p-card:hover .p-card-img::after { opacity: 1; }

        /* bottom gradient on image */
        .p-card-img-fade {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(10,9,8,0.85) 0%, transparent 100%);
            pointer-events: none;
        }

        /* category badge top-left */
        .p-card-cat {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 4px 9px;
            border-radius: 999px;
            background: rgba(10,9,8,0.75);
            border: 1px solid rgba(212,119,58,0.4);
            color: var(--primary);
            backdrop-filter: blur(8px);
        }

        .p-card-body {
            padding: 16px 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
        }

        /* top rule */
        .p-card-body::before {
            content: '';
            position: absolute;
            top: 0; left: 16px; right: 16px;
            height: 1px;
            background: linear-gradient(to right, var(--primary-glow), transparent);
        }

        .p-card-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .p-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .p-card-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 8px;
            align-items: center;
        }

        .p-card-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .p-card-price sup {
            font-size: 0.7em;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            vertical-align: super;
            margin-right: 1px;
            opacity: 0.8;
        }

        .p-card-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary-light);
            border: 1px solid var(--primary-glow);
            color: var(--primary);
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .p-card:hover .p-card-icon {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        /* ── FEATURED CARD (first item, full width) ── */
        .p-card.featured {
            grid-column: 1 / 3;
            display: flex;
            flex-direction: row;
        }

        @media (max-width: 640px) {
            .p-card.featured { 
                grid-column: 1 / -1;
                flex-direction: column; 
            }
        }

        .p-card.featured .p-card-img {
            flex: 0 0 40%;
            aspect-ratio: unset;
            min-height: 200px;
        }

        .p-card.featured .p-card-body {
            flex: 1;
            justify-content: center;
            padding: 20px 20px;
        }

        .p-card.featured .p-card-name {
            font-size: 1.4rem;
            -webkit-line-clamp: 2;
        }

        .p-card.featured .p-card-price {
            font-size: 1.6rem;
        }

        .featured-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .featured-desc {
            font-size: 0.85rem;
            color: var(--muted);
            line-height: 1.6;
            margin-top: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── EMPTY STATE ─────────────────────────── */
        .empty-wrap {
            text-align: center;
            padding: 80px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 3.5rem;
            opacity: 0.15;
            margin-bottom: 16px;
        }

        .empty-msg {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            opacity: 0.5;
        }

         /* ══════════════════════════════════════
           FOOTER — UPGRADED (Nature-inspired)
        ══════════════════════════════════════ */
        .footer-v2 {
            position: relative;
            background: #5a2e02;
            background-image: url('{{ asset("images/fondomadera1.webp") }}');
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        /* Dark overlay for text readability */
        .footer-v2::before {
            content: '';
            position: absolute; inset: 0;
            background: rgba(10, 9, 8, 0.75);
            pointer-events: none; z-index: 0;
        }

        /* Silhouette landscape SVG top */
        .footer-landscape {
            display: block;
            width: 100%;
            margin-bottom: 0;
            line-height: 0;
            position: relative; z-index: 1;
        }
        .footer-landscape svg {
            width: 100%; height: 120px;
            display: block;
        }

        .footer-main {
            padding: 56px 0 0;
            position: relative; z-index: 2;
        }

        /* Subtle texture overlay */
        .footer-v2::after {
            content: '';
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            opacity: 0.025; pointer-events: none; z-index: 1;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 52px;
            position: relative; z-index: 3;
        }
        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
        }
        @media (max-width: 600px) {
            .footer-grid { grid-template-columns: 1fr; gap: 28px; text-align: center; }
        }

        /* Brand column */
        .footer-brand-logo {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 18px;
        }
        @media (max-width: 600px) { .footer-brand-logo { justify-content: center; } }
        .footer-brand-logo img { height: 38px; width: auto; opacity: 0.92; }
        .footer-brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem; font-weight: 600;
            color: #F5F0E8; letter-spacing: 0.02em;
        }
        .footer-brand-desc {
            font-size: 0.82rem; color: rgba(245,240,232,0.75);
            line-height: 1.8; font-weight: 300; margin-bottom: 24px;
            max-width: 300px;
        }
        @media (max-width: 600px) { .footer-brand-desc { margin: 0 auto 24px; } }

        /* Newsletter input */
        .footer-newsletter {
            display: flex; gap: 0;
            border: 1px solid rgba(200,220,200,0.15);
            border-radius: var(--radius-sm); overflow: hidden;
            max-width: 320px;
        }
        @media (max-width: 600px) { .footer-newsletter { margin: 0 auto; } }
        .footer-newsletter input {
            flex: 1; background: rgba(255,255,255,0.06);
            border: none; padding: 10px 14px;
            font-size: 0.78rem; color: #F5F0E8;
            font-family: 'DM Sans', sans-serif; outline: none;
        }
        .footer-newsletter input::placeholder { color: rgba(245,240,232,0.4); }
        .footer-newsletter-btn {
            background: var(--primary); color: #F5F0E8;
            border: none; padding: 10px 16px;
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.07em;
            text-transform: uppercase; cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .footer-newsletter-btn:hover { background: #c06830; }

        /* Column headings */
        .footer-col-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.05rem; font-weight: 700;
            color: #F5F0E8;
            margin-bottom: 20px; letter-spacing: 0.01em;
        }

        /* Navigation links */
        .footer-nav { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        @media (max-width: 600px) { .footer-nav { align-items: center; } }
        .footer-nav a {
            font-size: 0.82rem; color: rgba(245,240,232,0.75);
            font-weight: 400; transition: color 0.2s;
            display: flex; align-items: center; gap: 6px;
        }
        .footer-nav a::before {
            content: '→'; font-size: 0.65rem;
            opacity: 0; transform: translateX(-4px);
            transition: all 0.2s; color: var(--primary);
        }
        .footer-nav a:hover { color: var(--primary); }
        .footer-nav a:hover::before { opacity: 1; transform: translateX(0); }

        /* Contact info */
        .footer-contact { display: flex; flex-direction: column; gap: 12px; }
        @media (max-width: 600px) { .footer-contact { align-items: center; } }
        .footer-contact-item {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 0.8rem; color: rgba(245,240,232,0.75); line-height: 1.5;
        }
        @media (max-width: 600px) { .footer-contact-item { align-items: center; } }
        .footer-contact-item i {
            color: var(--primary); font-size: 0.88rem; width: 16px;
            text-align: center; margin-top: 1px; flex-shrink: 0;
        }
        .footer-contact-item a { color: inherit; transition: color 0.2s; }
        .footer-contact-item a:hover { color: var(--primary); }

        /* Divider */
        .footer-divider {
            border: none; border-top: 1px solid rgba(245,240,232,0.1);
            margin-bottom: 28px; position: relative; z-index: 3;
        }

        /* Bottom bar */
        .footer-bottom {
            display: flex; align-items: center;
            justify-content: center;
            padding-bottom: 32px;
            flex-wrap: wrap; gap: 16px;
            position: relative; z-index: 3;
        }
        @media (max-width: 600px) { .footer-bottom { flex-direction: column; align-items: center; } }

        .footer-copy {
            font-size: 0.74rem; color: rgba(245,240,232,0.55); font-weight: 300;
        }

        .footer-socials { display: flex; gap: 10px; }
        .footer-social-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(245,240,232,0.15);
            color: rgba(245,240,232,0.65);
            font-size: 0.9rem;
            transition: all 0.22s;
        }
        .footer-social-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #F5F0E8;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212,119,58,0.35);
        }

        /* Horario badge */
        .footer-horario-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(212,119,58,0.15);
            border: 1px solid rgba(212,119,58,0.35);
            padding: 6px 14px; border-radius: 99px;
            font-size: 0.73rem; color: var(--primary); font-weight: 600;
            margin-bottom: 16px; letter-spacing: 0.04em;
        }
        .footer-horario-badge .pulsedot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--primary); animation: pulse 2s infinite;
        }
        .social-row {
            display: flex;
            gap: 12px;
            margin-top: 14px;
        }

        .social-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1rem;
            transition: all 0.2s;
            border: 1px solid var(--primary-glow);
        }
        .social-btn:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px var(--primary-glow);
        }

        .footer-copy {
            border-top: 1px solid var(--border);
            padding-top: 20px;
            text-align: center;
            font-size: 0.75rem;
            color: var(--muted);
        }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (max-width: 480px) {
            .hero-name { font-size: 1.8rem; }
            .section-heading { font-size: 1.6rem; }
            .p-card-name { font-size: 1rem; }
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-col { text-align: center; }
            .footer-col h4 { justify-content: center; }
            .footer-col p { justify-content: center; }
            .social-row { justify-content: center; }
        }
    </style>
</head>
<body>
<div id="plaza-app">

    <!-- ── HEADER ── -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="{{ route('plaza.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i> Atrás
                </a>
                <span class="header-label">Menú</span>
                <div>
                    @auth
                        <div class="user-menu-top">
                            <button class="user-menu-btn" id="userMenuBtn">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="" style="width:18px;height:18px;border-radius:50%;object-fit:cover;">
                                @else
                                    <i class="fas fa-user-circle" style="font-size:17px;"></i>
                                @endif
                                <span style="color:var(--text);font-size:0.72rem;">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                            </button>
                            <div class="user-menu-dropdown" id="userMenuDropdown">
                                <div class="dropdown-header">
                                    <div class="dropdown-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                                    <div class="dropdown-email">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('client.profile.edit') }}" class="dropdown-item">
                                    <i class="fas fa-user-edit" style="color:var(--muted);"></i> Editar perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border:1px solid var(--border-light);border-radius:var(--radius-sm);font-size:0.78rem;color:var(--primary);">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ── HERO ── -->
    <section class="local-hero">
        <div class="hero-bg">
            <img src="{{ $local->logo_url ?? 'https://via.placeholder.com/1200x600/111009/D4773A?text=' . urlencode($local->name) }}" alt="{{ $local->name }}">
        </div>
        <div class="hero-gradient"></div>
        <div class="hero-body container">
            @if($local->logo_url)
            <div class="hero-logo-ring">
                <img src="{{ $local->logo_url }}" alt="{{ $local->name }}">
            </div>
            @endif
            <div class="hero-text">
                <div class="hero-tag"><i class="fas fa-utensils"></i> La Comarca Gastro Park</div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
                    <div>
                        <h1 class="hero-name">{{ $local->name }}</h1>
                        @if($local->description)
                            <p class="hero-desc">{{ $local->description }}</p>
                        @endif
                    </div>
                    @if($horarioHoy)
                        <div style="display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 2px solid rgba(212,119,58,0.4); min-width: fit-content;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.75rem; color: rgba(245,240,232,0.6); margin-bottom: 4px; font-weight: 600;">{{ $diaActual }}</div>
                                @if($horarioHoy->status)
                                    <div style="font-size: 0.95rem; color: var(--primary); font-weight: 700;">
                                        {{ $horarioHoy->opening_time?->format('H:i') ?? 'N/A' }} - {{ $horarioHoy->closing_time?->format('H:i') ?? 'N/A' }}
                                    </div>
                                    <div style="font-size: 0.65rem; margin-top: 2px; font-weight: 600;">
                                        @if($estaAbierto)
                                            <span style="color: #4ade80;"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Abierto</span>
                                        @else
                                            <span style="color: var(--muted);"><i class="fas fa-circle" style="font-size: 0.5rem; color: #ff1100;"></i> Cerrado</span>
                                        @endif
                                    </div>
                                @else
                                    <div style="font-size: 0.85rem; color: #ff1100; font-weight: 600;">
                                        Cerrado hoy
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ── CATEGORY STRIP ── -->
    @if($categorias->isNotEmpty())
    <div class="cat-strip">
        <div class="container">
            <div class="cat-scroll">
                <button class="cat-pill" :class="{ active: activeCategory === null }" @click="activeCategory = null">
                    <i class="fas fa-border-all"></i> Todos
                </button>
                @foreach($categorias as $cat)
                <button
                    class="cat-pill"
                    :class="{ active: activeCategory === '{{ $cat['slug'] }}' }"
                    @click="activeCategory = '{{ $cat['slug'] }}'">
                    <i class="fas {{ $cat['icono'] }}"></i>
                    {{ $cat['nombre'] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- ── PRODUCTS ── -->
    <main class="menu-section">
        <div class="container">
            <div class="menu-intro">
                <div class="menu-intro-left">
                    <p class="section-eyebrow">Nuestro Menú</p>
                    <h2 class="section-heading">Lo Mejor de <em>{{ $local->name }}</em></h2>
                </div>
                @if($productos->isNotEmpty())
                <span class="item-count">{{ $productos->count() }} platillos</span>
                @endif
            </div>

            @if($productos->isEmpty())
                <div class="empty-wrap">
                    <div class="empty-icon"><i class="fas fa-bowl-food"></i></div>
                    <p class="empty-msg">No hay platillos disponibles por el momento</p>
                </div>
            @else
                <div class="products-grid">
                    @foreach($productos as $i => $producto)
                    <div class="p-card {{ $i === 0 ? 'featured' : '' }}"
                         v-show="activeCategory === null || '{{ Str::slug($producto->category) }}' === activeCategory">

                        <div class="p-card-img">
                            <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}"
                                 alt="{{ $producto->name }}"
                                 loading="{{ $i < 4 ? 'eager' : 'lazy' }}">
                            <div class="p-card-img-fade"></div>
                            @if($producto->category)
                            <span class="p-card-cat">{{ $producto->category }}</span>
                            @endif
                        </div>

                        <div class="p-card-body">
                            @if($i === 0)
                                <p class="featured-label"><i class="fas fa-crown"></i> &nbsp;Destacado</p>
                            @endif
                            <h3 class="p-card-name">{{ $producto->name }}</h3>
                            @if($i === 0 && $producto->description)
                                <p class="featured-desc">{{ $producto->description }}</p>
                            @endif
                            <div class="p-card-stars">
                                @php
                                    $rating = round($producto->average_rating ?? 0);
                                    for ($j = 1; $j <= 5; $j++):
                                @endphp
                                    <i class="fas fa-star" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.7rem;"></i>
                                @php
                                    endfor;
                                @endphp
                            </div>
                            <div class="p-card-footer">
                                <span class="p-card-price">
                                    <sup>₡</sup>{{ number_format($producto->price, 2) }}
                                </span>
                                <span class="p-card-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

     <!-- ══ FOOTER ══ -->
    <footer class="footer-v2">
        <!-- Mountains silhouette - separator -->
        <div class="footer-landscape">
            <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,40 C80,100 160,20 240,60 C280,80 300,30 340,50 C380,70 400,35 440,55
                         C480,75 510,25 560,65 C600,95 630,30 680,70 C720,105 750,40 800,75
                         C840,105 870,45 920,80 C960,110 990,50 1040,85 C1080,115 1110,55 1160,90
                         C1200,120 1240,65 1280,95 C1320,120 1360,70 1400,100 L1440,120 L1440,0 L0,0 Z"
                      fill="#0A0908" stroke="none"/>
            </svg>
        </div>

        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- Brand column -->
                    <div>
                        <div class="footer-brand-logo">
                            <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca">
                            <span class="footer-brand-name">La Comarca Gastropark</span>
                        </div>
                        <p class="footer-brand-desc">
                            Un espacio gastronómico único en Guápiles, Limón. Sabores auténticos, ambiente inigualable y la magia de comer bajo las estrellas.
                        </p>
                        <div class="footer-horario-badge">
                            <span class="pulsedot"></span>
                            Mar–Dom &nbsp;·&nbsp; 12:00 MD – 10:00 PM
                        </div>
                    </div>

                    <!-- Contacto column -->
                    <div>
                        <h3 class="footer-col-title">Contáctanos</h3>
                        <div class="footer-contact">
                            <div class="footer-contact-item">
                                <i class="fas fa-location-dot"></i>
                                <span>
                                    <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener">La Comarca Gastro Park</a><br>
                                    Guápiles, Limón, Costa Rica
                                </span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-phone"></i>
                                <span>+506 8888 8888</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><a href="mailto:info@lacomarcagastropark.com">info@lacomarcagastropark.com</a></span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-clock"></i>
                                <span>Lunes: Cerrado<br>Mar–Dom: 12:00 MD – 10:00 PM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Síguenos column -->
                    <div>
                        <h3 class="footer-col-title">Síguenos</h3>
                        <p style="font-size:0.8rem;color:rgba(200,220,200,0.4);font-weight:300;line-height:1.7;margin-bottom:20px;">
                            Conecta con nosotros en redes sociales y mantente al día con nuestros eventos y promociones especiales.
                        </p>
                        <div class="footer-socials">
                            <a href="https://www.facebook.com/share/1CYem5AGeo/" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/la.comarcagastropark?igsh=bW43MHB0OG9yMG8y" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.tiktok.com/@la.comarcagastropark?_t=ZM-8z8TOSBnnGv&_r=1" target="_blank" rel="noopener"
                               class="footer-social-btn" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="footer-divider">

                <div class="footer-bottom">
                    <p class="footer-copy">&copy; 2026 La Comarca Gastro Park. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

</div>

<script>
    // User menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('userMenuBtn');
        const menuDropdown = document.getElementById('userMenuDropdown');

        if (menuBtn && menuDropdown) {
            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                menuDropdown.classList.toggle('open');
            });
            document.addEventListener('click', () => {
                menuDropdown.classList.remove('open');
            });
        }
    });

    // Vue app
    const { createApp } = Vue;
    createApp({
        data() {
            return { activeCategory: null };
        }
    }).mount('#plaza-app');
</script>
</body>
</html>