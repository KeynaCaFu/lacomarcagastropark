<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - La Comarca</title>

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:       #D4773A;
            --primary-light: rgba(212,119,58,0.15);
            --primary-glow:  rgba(212,119,58,0.28);
            --bg:            #0A0908;
            --surface:       #0F0D0B;
            --card:          #171410;
            --card-hover:    #1E1A14;
            --border:        #262018;
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
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }
        @media (max-width: 480px) {
            .container { padding: 0 16px; }
        }

        /* ══════════════════════════════════════
           KEYFRAMES
        ══════════════════════════════════════ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }
        @keyframes floatA {
            0%,100% { transform: translateY(-50%) translate(0,0) rotate(0deg); }
            33%      { transform: translateY(-50%) translate(-10px,-20px) rotate(1.5deg); }
            66%      { transform: translateY(-50%) translate(6px,-10px) rotate(-1deg); }
        }
        @keyframes floatB {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            40%      { transform: translate(-12px,-24px) rotate(-2deg); }
            70%      { transform: translate(8px,-12px) rotate(1.5deg); }
        }
        @keyframes floatC {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(6px,-16px) scale(1.04); }
        }
        @keyframes orbReveal {
            from { opacity: 0; transform: translateY(-50%) scale(0.75); }
            to   { opacity: 1; transform: translateY(-50%) scale(1); }
        }
        @keyframes orbReveal2 {
            from { opacity: 0; transform: scale(0.7) translateY(16px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        @keyframes glowPulse {
            0%,100% { box-shadow: 0 0 40px rgba(212,119,58,0.2); }
            50%      { box-shadow: 0 0 70px rgba(212,119,58,0.45), 0 0 120px rgba(212,119,58,0.15); }
        }
        @keyframes scanline {
            0%   { transform: translateY(-100%); }
            100% { transform: translateY(200vh); }
        }
        @keyframes particleDrift {
            0%,100% { transform: translate(0,0) scale(1); opacity: var(--op); }
            50%      { transform: translate(var(--dx),var(--dy)) scale(1.5); opacity: calc(var(--op) * 1.8); }
        }
        @keyframes scrollBounce {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(6px); }
        }
        @keyframes pulse {
            0%,100% { opacity: 1; }
            50%      { opacity: 0.55; }
        }

        /* ══════════════════════════════════════
           V-CLOAK - Hide Vue elements during compilation
        ══════════════════════════════════════ */
        [v-cloak] { display: none; }

        /* ══════════════════════════════════════
           HEADER
        ══════════════════════════════════════ */
        .plaza-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            background: rgba(10,9,8,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            height: 58px;
            transition: background 0.3s;
        }
        @media (max-width: 480px) {
            .plaza-header { padding: 10px 0; height: 54px; }
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo-img {
            height: 34px;
            width: auto;
            object-fit: contain;
        }

        .header-logo-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: 0.02em;
        }

        .header-auth { display: flex; align-items: center; gap: 8px; }

        .btn-auth {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-login {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        .btn-login:hover { background: var(--primary-light); }

        .user-menu-top { position: relative; }
        .user-menu-btn {
            background: none;
            border: 1px solid var(--border-light);
            cursor: pointer;
            padding: 7px 12px;
            color: var(--primary);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; gap: 8px;
            font-size: 0.78rem; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s;
        }
        .user-menu-btn:hover { border-color: var(--primary); }

        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px); right: 0;
            background: var(--card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            box-shadow: 0 12px 40px rgba(0,0,0,0.5);
            min-width: 210px; z-index: 1000;
            display: none; overflow: hidden;
        }
        .user-menu-dropdown.open { display: block; }
        .dropdown-header { padding: 12px 14px; border-bottom: 1px solid var(--border); }
        .dropdown-name { font-weight: 600; font-size: 0.82rem; color: var(--text); }
        .dropdown-email { font-size: 0.7rem; color: var(--muted); margin-top: 2px; }
        .dropdown-item {
            display: flex; align-items: center; gap: 9px;
            padding: 10px 14px; font-size: 0.78rem; color: var(--text);
            border-bottom: 1px solid var(--border);
            cursor: pointer; background: none;
            border-left: none; border-right: none;
            width: 100%; font-family: 'DM Sans', sans-serif;
            transition: background 0.15s;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: var(--card-hover); }
        .dropdown-item.danger { color: #e05c5c; }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px 0 70px;
            margin-top: 58px;
            overflow: hidden;
            isolation: isolate;
        }
        @media (max-width: 768px) {
            .hero { 
                padding: 60px 0 50px; 
                margin-top: 58px;
                min-height: auto;
            }
        }
        @media (max-width: 480px) {
            .hero { 
                padding: 50px 0 40px; 
                margin-top: 54px;
                min-height: auto;
            }
        }

        /* Bokeh bg */
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
        }
        .hero-bg img {
            width: 100%; height: 100%;
            object-fit: cover;
            opacity: 0.6;
            filter: brightness(0.65) saturate(1.15);
            transform: scale(1.06);
            transition: transform 14s ease;
            will-change: transform;
        }
        .hero:hover .hero-bg img { transform: scale(1.02); }

        /* Overlays */
        .hero-overlay {
            position: absolute; inset: 0; z-index: 1;
            background:
                radial-gradient(ellipse 80% 55% at 50% 115%, rgba(212,119,58,0.2) 0%, transparent 65%),
                radial-gradient(ellipse 120% 90% at 50% -10%, rgba(10,9,8,0.95) 0%, transparent 55%),
                linear-gradient(180deg, rgba(10,9,8,0.82) 0%, rgba(10,9,8,0.15) 38%, rgba(10,9,8,0.88) 100%);
        }
        .hero-scanline {
            position: absolute; inset: 0; z-index: 2; overflow: hidden; pointer-events: none;
        }
        .hero-scanline::after {
            content: '';
            position: absolute; left: 0; right: 0; height: 140px;
            background: linear-gradient(to bottom, transparent, rgba(212,119,58,0.035), transparent);
            animation: scanline 10s linear infinite;
        }
        .hero-grain {
            position: absolute; inset: 0; z-index: 3; pointer-events: none;
            opacity: 0.032;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 200px 200px;
        }

        /* ── Floating logo orbs ── */
        .hero-orbs {
            position: absolute; inset: 0; z-index: 4; pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            overflow: hidden;
            opacity: 0;
        }

        /* Orb grande — logo redondo con fondo negro */
        .orb-1 {
            width: 360px; height: 360px;
            right: -40px; top: 50%;
            transform: translateY(-50%);
            border: 1px solid rgba(212,119,58,0.22);
            box-shadow:
                0 0 60px rgba(212,119,58,0.22),
                0 0 140px rgba(212,119,58,0.08),
                inset 0 0 40px rgba(0,0,0,0.4);
            animation:
                orbReveal 1.3s cubic-bezier(.22,.68,0,1.25) 0.5s forwards,
                floatA 10s ease-in-out 1.8s infinite,
                glowPulse 5s ease-in-out 2s infinite;
        }
        @media (max-width: 1024px) {
            .orb-1 { width: 280px; height: 280px; right: -100px; }
        }
        @media (max-width: 768px) {
            .orb-1 { width: 200px; height: 200px; right: -80px; display: none; }
        }
        @media (max-width: 640px) {
            .orb-1 { 
                width: 140px; 
                height: 140px; 
                right: -30px; 
                bottom: 20%; 
                top: auto;
                transform: none;
                border: 1px solid rgba(212,119,58,0.15);
                box-shadow: 0 0 30px rgba(212,119,58,0.1);
                opacity: 0.3;
                animation: none;
                display: block;
            }
        }
        @media (max-width: 480px) {
            .orb-1 { width: 100px; height: 100px; right: -20px; opacity: 0.25; }
        }

        /* Orb mediano — logo blanco */
        .orb-2 {
            width: 130px; height: 130px;
            right: 310px; top: 10%;
            border: 1px solid rgba(212,119,58,0.18);
            box-shadow: 0 0 30px rgba(212,119,58,0.18);
            background: rgba(10,9,8,0.7);
            animation:
                orbReveal2 1s cubic-bezier(.22,.68,0,1.25) 1s forwards,
                floatB 12s ease-in-out 2s infinite;
        }
        .orb-2 img { object-fit: contain; padding: 10px; width: 100%; height: 100%; }
        @media (max-width: 1024px) {
            .orb-2 { width: 100px; height: 100px; right: 250px; }
        }
        @media (max-width: 768px) {
            .orb-2 { width: 80px; height: 80px; right: 80px; display: none; }
        }
        @media (max-width: 480px) {
            .orb-2 { display: none; }
        }

        /* Orb pequeño */
        .orb-3 {
            width: 68px; height: 68px;
            right: 140px; bottom: 20%;
            border: 1px solid rgba(212,119,58,0.28);
            box-shadow: 0 0 20px rgba(212,119,58,0.22);
            filter: brightness(0.65);
            animation:
                orbReveal2 1s cubic-bezier(.22,.68,0,1.25) 1.4s forwards,
                floatC 8s ease-in-out 2.4s infinite;
        }
        @media (max-width: 768px) {
            .orb-3 { width: 50px; height: 50px; right: 60px; }
        }
        @media (max-width: 480px) {
            .orb-3 { display: none; }
        }

        /* Arc decorativo */
        .orb-arc {
            position: absolute;
            right: 115px; top: 15%;
            width: 290px; height: 370px;
            border: 1px dashed rgba(212,119,58,0.1);
            border-radius: 50%;
            pointer-events: none;
            animation: fadeIn 2.5s ease 2.5s both;
        }
        @media (max-width: 768px) {
            .orb-arc { display: none; }
        }

        /* ── Particles ── */
        .hero-particles {
            position: absolute; inset: 0; z-index: 4; pointer-events: none;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            background: var(--primary);
            animation: particleDrift var(--dur) ease-in-out var(--delay) infinite;
        }

        /* ── Hero content ── */
        .hero-content {
            position: relative; z-index: 10;
            max-width: 620px;
            margin: 0 0 0 200px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        @media (max-width: 1024px) {
            .hero-content { margin: 0 0 0 100px; max-width: 550px; }
        }
        @media (max-width: 768px) {
            .hero-content { margin: 0; max-width: 100%; align-items: center; text-align: center; }
        }
        @media (max-width: 480px) {
            .hero-content { margin: 0; max-width: 100%; }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center; gap: 8px;
            background: rgba(212,119,58,0.1);
            border: 1px solid rgba(212,119,58,0.32);
            color: var(--primary);
            font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            padding: 7px 14px; border-radius: 999px;
            margin-bottom: 24px; width: fit-content;
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(.22,.68,0,1.2) 0.2s forwards;
        }
        @media (max-width: 480px) {
            .hero-badge { font-size: 0.6rem; padding: 6px 12px; margin-bottom: 16px; }
        }
        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--primary);
            animation: glowPulse 2s ease infinite;
        }

        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 5vw, 4.2rem);
            font-weight: 300;
            line-height: 1.08;
            color: var(--text);
            margin-bottom: 18px;
            letter-spacing: -0.01em;
            opacity: 0;
            animation: fadeUp 0.95s cubic-bezier(.22,.68,0,1.2) 0.42s forwards;
        }
        @media (max-width: 480px) {
            .hero-title { margin-bottom: 12px; }
        }
        .hero-title strong { font-weight: 700; display: block; }
        .hero-title em {
            font-style: italic; font-weight: 300;
            background: linear-gradient(90deg, #D4773A, #F0A060, #E08840, #D4773A);
            background-size: 220% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4.5s linear 1.5s infinite;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: rgba(245,240,232,0.5);
            line-height: 1.75;
            margin-bottom: 32px;
            max-width: 460px;
            font-weight: 300;
            opacity: 0;
            animation: fadeUp 0.95s cubic-bezier(.22,.68,0,1.2) 0.62s forwards;
        }
        @media (max-width: 768px) {
            .hero-subtitle { font-size: 0.85rem; max-width: 100%; }
        }
        @media (max-width: 480px) {
            .hero-subtitle { font-size: 0.75rem; margin-bottom: 24px; }
        }

        .hero-divider {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.78s forwards;
        }
        @media (max-width: 480px) {
            .hero-divider { margin-bottom: 20px; gap: 10px; }
        }
        .hero-divider-line {
            flex: 1; max-width: 56px; height: 1px;
            background: linear-gradient(to right, transparent, rgba(212,119,58,0.45));
        }
        .hero-divider-line.r { background: linear-gradient(to left, transparent, rgba(212,119,58,0.45)); }
        .hero-divider-icon { color: var(--primary); font-size: 0.78rem; opacity: 0.65; }
        @media (max-width: 480px) {
            .hero-divider-line { max-width: 40px; }
            .hero-divider-icon { font-size: 0.65rem; }
        }

        /* Search */
        .search-wrap {
            position: relative; margin-bottom: 36px;
            opacity: 0;
            animation: fadeUp 0.9s cubic-bezier(.22,.68,0,1.2) 0.88s forwards;
            width: 100%;
        }
        @media (max-width: 768px) {
            .search-wrap { margin-bottom: 28px; }
        }
        @media (max-width: 480px) {
            .search-wrap { margin-bottom: 20px; }
        }
        .search-icon {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); pointer-events: none; font-size: 0.9rem;
        }
        .search-input {
            width: 100%; height: 52px;
            background: rgba(20,17,13,0.9);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            padding: 0 20px 0 48px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem; font-weight: 300;
            color: var(--text);
            outline: none; -webkit-appearance: none;
            backdrop-filter: blur(10px);
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        @media (max-width: 480px) {
            .search-input { height: 44px; font-size: 0.8rem; padding: 0 16px 0 40px; }
        }
        .search-input::placeholder { color: rgba(122,112,96,0.55); }
        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(212,119,58,0.14), 0 8px 32px rgba(0,0,0,0.35);
        }

        /* Stats */
        .hero-stats {
            display: flex; align-items: center; gap: 24px; flex-wrap: wrap;
            opacity: 0;
            animation: fadeUp 0.9s cubic-bezier(.22,.68,0,1.2) 1.06s forwards;
            justify-content: flex-start;
        }
        @media (max-width: 768px) {
            .hero-stats { justify-content: center; gap: 16px; }
        }
        @media (max-width: 480px) {
            .hero-stats { gap: 12px; }
        }
        .stat-divider { width: 1px; height: 30px; background: rgba(38,32,24,0.9); }
        @media (max-width: 480px) {
            .stat-divider { height: 20px; }
        }
        .stat { display: flex; align-items: center; gap: 11px; }
        @media (max-width: 480px) {
            .stat { gap: 8px; }
        }
        .stat-icon {
            display: flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(212,119,58,0.1);
            border: 1px solid rgba(212,119,58,0.18);
            color: var(--primary); font-size: 0.88rem; flex-shrink: 0;
        }
        @media (max-width: 480px) {
            .stat-icon { width: 32px; height: 32px; font-size: 0.75rem; }
        }
        .stat strong {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem; font-weight: 700;
            color: var(--text); line-height: 1;
        }
        @media (max-width: 480px) {
            .stat strong { font-size: 1rem; }
        }
        .stat small {
            font-size: 0.66rem; color: var(--muted);
            margin-top: 2px; display: block;
            text-transform: uppercase; letter-spacing: 0.07em;
        }
        @media (max-width: 480px) {
            .stat small { font-size: 0.55rem; }
        }

        /* Scroll hint */
        .scroll-hint {
            position: absolute; bottom: 28px; left: 50%;
            transform: translateX(-50%); z-index: 10;
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            color: rgba(122,112,96,0.45); font-size: 0.62rem;
            letter-spacing: 0.11em; text-transform: uppercase;
            opacity: 0; animation: fadeIn 1s ease 2.2s forwards;
        }
        .scroll-mouse {
            width: 22px; height: 34px;
            border: 1px solid rgba(122,112,96,0.28);
            border-radius: 999px;
            display: flex; justify-content: center; padding-top: 7px;
        }
        .scroll-wheel {
            width: 3px; height: 7px;
            background: rgba(212,119,58,0.55);
            border-radius: 999px;
            animation: scrollBounce 1.9s ease infinite;
        }

        /* ══════════════════════════════════════
           CATEGORY BAR — UPGRADED
        ══════════════════════════════════════ */
        .category-bar {
            position: sticky; top: 58px; z-index: 150;
            padding: 0;
            background: rgba(10,9,8,0.96);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
        }
        @media (max-width: 480px) {
            .category-bar { top: 54px; }
        }
        .category-bar-inner {
            display: flex; align-items: center;
            gap: 0; overflow-x: auto;
            scrollbar-width: none; -ms-overflow-style: none;
            padding: 0 4px;
        }
        .category-bar-inner::-webkit-scrollbar { display: none; }

        .cat-pill {
            display: inline-flex; align-items: center; gap: 7px;
            white-space: nowrap;
            padding: 13px 20px;
            font-size: 0.77rem; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.03em;
            border: none; cursor: pointer;
            background: transparent; color: var(--muted);
            flex-shrink: 0;
            position: relative;
            transition: color 0.22s;
        }
        .cat-pill::after {
            content: '';
            position: absolute; bottom: 0; left: 20px; right: 20px;
            height: 2px; border-radius: 2px 2px 0 0;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.25s cubic-bezier(.22,.68,0,1.2);
        }
        .cat-pill:hover { color: var(--text); }
        .cat-pill.active { color: var(--primary); }
        .cat-pill.active::after { transform: scaleX(1); }
        .cat-pill i { font-size: 0.85rem; transition: transform 0.2s; }
        .cat-pill:hover i { transform: scale(1.2); }
        .cat-pill .cat-count {
            font-size: 0.62rem; background: var(--primary-light);
            color: var(--primary); padding: 1px 6px; border-radius: 99px;
            font-weight: 700; display: none;
        }
        .cat-pill.active .cat-count { display: inline-block; }

        /* ══════════════════════════════════════
           FILTERED PRODUCTS — UPGRADED
        ══════════════════════════════════════ */
        .filtered-section {
            padding: 60px 0;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .filtered-section::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-glow), transparent);
        }
        .filtered-header {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 32px; flex-wrap: wrap; gap: 12px;
        }
        .filtered-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.14em;
            text-transform: uppercase; color: var(--primary);
            display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
        }
        .filtered-label::before {
            content: ''; width: 20px; height: 1px;
            background: var(--primary); opacity: 0.6;
        }
        .filtered-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.7rem, 3vw, 2.2rem);
            font-weight: 700; color: var(--text);
            line-height: 1.1;
        }
        .filtered-count-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 99px;
            background: var(--card); border: 1px solid var(--border-light);
            font-size: 0.75rem; color: var(--muted); font-weight: 500;
        }
        .filtered-count-badge strong { color: var(--primary); }

        .grid-products-filtered {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
        @media (min-width: 640px) {
            .grid-products-filtered { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 900px) {
            .grid-products-filtered { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }

        /* Transition for filtered grid */
        .fade-slide-enter-active { transition: all 0.42s cubic-bezier(.22,.68,0,1.2); }
        .fade-slide-leave-active { transition: all 0.22s ease; position: absolute; }
        .fade-slide-enter-from { opacity: 0; transform: translateY(20px); }
        .fade-slide-leave-to   { opacity: 0; transform: translateY(-8px); }

        .product-card-v2 {
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.32s cubic-bezier(.22,.68,0,1.2), box-shadow 0.32s;
            cursor: pointer;
            position: relative;
        }
        .product-card-v2:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 0 1px var(--primary-glow);
        }
        .product-card-v2 .product-img {
            position: relative; aspect-ratio: 4/3; overflow: hidden;
            background: rgba(0,0,0,0.2);
        }
        .product-card-v2 .product-img img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.55s cubic-bezier(.22,.68,0,1.2);
        }
        .product-card-v2:hover .product-img img { transform: scale(1.1); }
        .product-img-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10,9,8,0.8) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.3s;
        }
        .product-card-v2:hover .product-img-overlay { opacity: 1; }
        .product-cat-chip {
            position: absolute; top: 10px; left: 10px;
            background: rgba(10,9,8,0.75); backdrop-filter: blur(8px);
            color: var(--primary); font-size: 0.6rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            padding: 4px 9px; border-radius: 99px;
            border: 1px solid rgba(212,119,58,0.3);
        }
        .product-quick-view {
            position: absolute; bottom: 10px; right: 10px;
            background: var(--primary); color: #fff;
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em;
            padding: 6px 12px; border-radius: var(--radius-sm);
            border: none; cursor: pointer; font-family: 'DM Sans', sans-serif;
            opacity: 0; transform: translateY(6px);
            transition: opacity 0.25s, transform 0.25s;
            display: flex; align-items: center; gap: 5px;
            text-decoration: none;
        }
        .product-card-v2:hover .product-quick-view {
            opacity: 1; transform: translateY(0);
        }
        .product-card-v2 .product-body { padding: 14px; }
        .product-card-v2 .product-local {
            font-size: 0.62rem; font-weight: 700;
            color: var(--primary); text-transform: uppercase;
            letter-spacing: 0.07em; margin-bottom: 5px;
        }
        .product-card-v2 .product-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.05rem; font-weight: 700; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 10px;
        }
        .product-card-v2 .product-footer {
            display: flex; align-items: center; justify-content: space-between;
        }
        .product-card-v2 .product-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.25rem; font-weight: 700; color: var(--primary);
        }

        /* Loading skeleton */
        .skeleton-grid {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
        @media (min-width: 640px) { .skeleton-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 900px) { .skeleton-grid { grid-template-columns: repeat(4, 1fr); gap: 20px; } }

        .skeleton-card {
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .skeleton-img {
            aspect-ratio: 4/3;
            background: linear-gradient(90deg, var(--card) 25%, var(--card-hover) 50%, var(--card) 75%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.4s infinite;
        }
        .skeleton-body { padding: 14px; }
        .skeleton-line {
            height: 10px; border-radius: 5px;
            background: linear-gradient(90deg, var(--card) 25%, var(--card-hover) 50%, var(--card) 75%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.4s infinite;
            margin-bottom: 8px;
        }
        .skeleton-line.w-60 { width: 60%; }
        .skeleton-line.w-80 { width: 80%; }
        .skeleton-line.w-40 { width: 40%; }
        @keyframes skeletonShimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ══════════════════════════════════════
           LOCALES SECTION — UPGRADED
        ══════════════════════════════════════ */
        .locales-section {
            padding: 72px 0;
            border-bottom: 1px solid var(--border);
        }
        .locales-header-row {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 40px; flex-wrap: wrap; gap: 16px;
        }
        .locales-eyebrow {
            font-size: 0.65rem; font-weight: 700;
            letter-spacing: 0.14em; text-transform: uppercase;
            color: var(--primary); margin-bottom: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .locales-eyebrow::before {
            content: ''; width: 20px; height: 1px;
            background: var(--primary); opacity: 0.6;
        }
        .locales-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            font-weight: 700; color: var(--text);
            line-height: 1.05; margin-bottom: 8px;
        }
        .locales-sub {
            font-size: 0.88rem; color: var(--muted); font-weight: 300; line-height: 1.6;
        }
        .locales-count {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem; font-weight: 300; color: var(--border-light);
            line-height: 1; letter-spacing: -0.03em;
        }
        .locales-count span { color: var(--primary); }

        .grid-locals-v2 {
            display: grid; gap: 20px;
            grid-template-columns: repeat(2, 1fr);
        }
        @media (min-width: 768px) {
            .grid-locals-v2 { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 1100px) {
            .grid-locals-v2 { grid-template-columns: repeat(3, 1fr); gap: 24px; }
        }

        .local-card-v2 {
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s;
            cursor: pointer;
            position: relative;
            display: flex; flex-direction: column;
        }
        .local-card-v2:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 60px rgba(0,0,0,0.5), 0 0 0 1px var(--primary-glow);
        }

        .local-img-wrap-v2 {
            position: relative;
            aspect-ratio: 3/2; overflow: hidden;
        }
        .local-img-v2 {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.55s cubic-bezier(.22,.68,0,1.2);
        }
        .local-card-v2:hover .local-img-v2 { transform: scale(1.09); }

        .local-img-gradient {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, transparent 35%, rgba(10,9,8,0.85) 100%);
        }
        .local-floating-status {
            position: absolute; top: 12px; right: 12px;
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(10,9,8,0.75); backdrop-filter: blur(8px);
            padding: 4px 10px; border-radius: 99px;
            font-size: 0.66rem; font-weight: 700;
            border: 1px solid rgba(74,222,128,0.25);
        }
        .local-floating-status.open { color: #4ade80; }
        .local-floating-status.closed { color: var(--muted); border-color: var(--border); }
        .local-floating-status .dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #4ade80;
            animation: pulse 2s infinite;
        }
        .local-floating-status.closed .dot {
            background: var(--muted); animation: none;
        }

        .local-body-v2 {
            padding: 18px; flex: 1;
            display: flex; flex-direction: column;
        }
        .local-name-v2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem; font-weight: 700;
            color: var(--text); margin-bottom: 6px;
            line-height: 1.2;
        }
        .local-desc-v2 {
            font-size: 0.76rem; color: var(--muted);
            line-height: 1.6; font-weight: 300;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
            margin-bottom: 14px; flex: 1;
        }
        .local-meta-v2 {
            display: flex; align-items: center;
            gap: 12px; margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .meta-chip {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.72rem; color: var(--muted); font-weight: 500;
        }
        .meta-chip i { color: var(--primary); font-size: 0.8rem; }

        .local-status {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.72rem; font-weight: 600; color: #4ade80;
        }
        .local-status::before {
            content: ''; display: inline-block;
            width: 7px; height: 7px; border-radius: 50%;
            background: #4ade80;
            animation: pulse 2s infinite;
        }
        .local-status.closed { color: var(--muted); }
        .local-status.closed::before { background: var(--muted); animation: none; }

        .btn-ver-menu-v2 {
            width: 100%; padding: 11px 0;
            background: transparent;
            color: var(--primary);
            font-size: 0.82rem; font-weight: 700;
            border: 1px solid var(--primary);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.22s, color 0.22s, box-shadow 0.22s, transform 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: 'DM Sans', sans-serif; letter-spacing: 0.04em;
            text-decoration: none;
        }
        .btn-ver-menu-v2:hover {
            background: var(--primary); color: #fff;
            box-shadow: 0 6px 20px var(--primary-glow);
        }
        .btn-ver-menu-v2:active { transform: scale(0.97); }
        .btn-ver-menu-v2 i { transition: transform 0.22s; }
        .btn-ver-menu-v2:hover i { transform: translateX(4px); }

        /* ══════════════════════════════════════
           PRODUCTOS DESTACADOS — UPGRADED
        ══════════════════════════════════════ */
        .destacados-section {
            padding: 72px 0;
            position: relative;
            border-bottom: 1px solid var(--border);
        }
        .destacados-section::before {
            content: ''; position: absolute; inset: 0;
            background: rgba(10,9,8,0.80);
            pointer-events: none; z-index: 0;
        }
        .destacados-section > .container { position: relative; z-index: 1; }

        .destacados-header {
            display: flex; align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 40px; flex-wrap: wrap; gap: 16px;
        }
        .destacados-fire-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(212,119,58,0.12);
            border: 1px solid rgba(212,119,58,0.35);
            color: var(--primary);
            font-size: 0.68rem; font-weight: 800;
            letter-spacing: 0.12em; text-transform: uppercase;
            padding: 6px 14px; border-radius: 99px;
            margin-bottom: 12px; width: fit-content;
        }
        .destacados-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            font-weight: 700; color: var(--text);
            line-height: 1.05; margin-bottom: 8px;
        }
        .destacados-sub {
            font-size: 0.88rem; color: rgba(245,240,232,0.45);
            font-weight: 300; line-height: 1.6;
        }

        .grid-destacados {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
        }
        @media (min-width: 640px) {
            .grid-destacados { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 900px) {
            .grid-destacados { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }

        .destacado-card {
            background: rgba(23,20,16,0.7);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(48,40,32,0.8);
            transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s;
            cursor: pointer;
        }
        .destacado-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 0 1px var(--primary-glow);
        }
        .destacado-img {
            position: relative; aspect-ratio: 1; overflow: hidden;
            background: rgba(0,0,0,0.25);
        }
        .destacado-img img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.55s cubic-bezier(.22,.68,0,1.2);
        }
        .destacado-card:hover .destacado-img img { transform: scale(1.1); }
        .destacado-img-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10,9,8,0.7) 0%, transparent 55%);
        }
        .destacado-popular-tag {
            position: absolute; top: 9px; right: 9px;
            background: var(--primary); color: #fff;
            font-size: 0.58rem; font-weight: 800; letter-spacing: 0.06em;
            text-transform: uppercase; padding: 3px 9px; border-radius: 99px;
        }
        .destacado-body { padding: 13px 14px; }
        .destacado-local {
            font-size: 0.6rem; font-weight: 700; color: var(--primary);
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 5px;
        }
        .destacado-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem; font-weight: 700; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 10px;
        }
        .destacado-footer {
            display: flex; align-items: center; justify-content: space-between;
        }
        .destacado-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem; font-weight: 700; color: var(--primary);
        }

        /* ══════════════════════════════════════
           FOOTER — UPGRADED (Nature-inspired)
        ══════════════════════════════════════ */
        .footer-v2 {
            position: relative;
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

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 900px) {
            .orb-1 { width: 260px; height: 260px; right: -70px; }
            .orb-2 { width: 100px; height: 100px; right: 205px; }
            .orb-3 { display: none; }
            .orb-arc { display: none; }
        }
        @media (max-width: 640px) {
            .hero-title { font-size: 2.4rem; }
            .orb-1 { 
                width: 120px; 
                height: 120px; 
                right: -20px; 
                top: 10%;
                bottom: auto;
                transform: none;
                opacity: 0.25 !important;
                border: 1px solid rgba(212,119,58,0.12);
                box-shadow: 0 0 25px rgba(212,119,58,0.1);
                animation: orbFloatMobile 4s ease-in-out infinite;
            }
            .orb-2 { display: none; }
            .hero-stats { gap: 14px; }
            .stat-divider { display: none; }
            .grid-locals-v2 { grid-template-columns: 1fr; }
            .locales-count { display: none; }
            .filtered-header { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
        @media (max-width: 480px) {
            .plaza-header { padding: 10px 0; }
            .orb-1 { 
                width: 90px; 
                height: 90px;
                right: -15px;
                top: 8%;
                opacity: 0.2 !important;
                animation: orbFloatMobile 5s ease-in-out infinite;
            }
            .grid-locals-v2 { grid-template-columns: 1fr; }
            .destacados-header { flex-direction: column; }
        }

        /* ── SMALL CONFIRMATION MODAL ──*/
        .swal-small-popup {
            max-width: 320px !important;
            width: 90% !important;
            padding: 24px 20px !important;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7), 0 0 20px rgba(212, 119, 58, 0.2) !important;
            animation: scaleIn 0.3s cubic-bezier(0.22, 0.68, 0, 1.2);
        }

        .swal-small-title {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            margin-bottom: 12px !important;
            font-family: 'DM Sans', sans-serif !important;
        }

        .swal-small-html {
            font-family: 'DM Sans', sans-serif !important;
            font-size: 0.85rem !important;
            line-height: 1.6 !important;
        }

        .swal2-confirm, .swal2-cancel {
            font-size: 0.85rem !important;
            padding: 8px 20px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.03em !important;
            min-width: 120px !important;
        }

        .swal2-confirm {
            background: #D4773A !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(212, 119, 58, 0.3) !important;
        }

        .swal2-confirm:hover {
            background: #c06830 !important;
            box-shadow: 0 6px 16px rgba(212, 119, 58, 0.4) !important;
        }

        .swal2-cancel {
            background: #3a3531 !important;
            border: 1px solid #4d4540 !important;
            color: #b0a099 !important;
        }

        .swal2-cancel:hover {
            background: #4d4540 !important;
            color: #d4c5ba !important;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.85);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* ── SWAL2 Z-INDEX FIX ──*/
        .swal2-container {
            z-index: 99999 !important;
        }

        .swal2-modal {
            z-index: 99999 !important;
        }
    </style>
</head>
<body>
<div id="plaza-app">

    <!-- ══ HEADER ══ -->
    <header class="plaza-header">
        <div class="container">
            <div class="header-inner">
                <div class="header-logo">
                    <img src="{{ asset('images/iconoblanco.png') }}" alt="La Comarca" class="header-logo-img">
                    <span class="header-logo-text">La Comarca Gastropark</span>
                </div>
                <div class="header-auth">
                    @auth
                        <button @click="openCartDrawer" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 7px 12px; border: 1px solid var(--border-light); border-radius: var(--radius-sm); font-size: 0.78rem; color: var(--primary); background: none; cursor: pointer; transition: all 0.2s;" :style="{ borderColor: showCartDrawer ? 'var(--primary)' : 'var(--border-light)' }">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cart-count">@{{ totalDrawerQty }}</span>
                        </button>
                        <div class="user-menu-top">
                            <button class="user-menu-btn" id="userMenuBtn">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">
                                @else
                                    <i class="fas fa-user-circle" style="font-size:18px;"></i>
                                @endif
                                <span style="color:var(--text);font-size:0.75rem;">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down" style="font-size:0.6rem;opacity:0.5;"></i>
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
                        <a href="{{ route('login') }}" class="btn-auth btn-login">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ══ ADMIN PREVIEW BANNER ══ -->
    @if(session('plaza_admin_preview'))
    <div style="background: linear-gradient(135deg, #c9690f 0%, #a85010 100%); color: white; padding: 13px 20px; border-bottom: 2px solid #f59e0b; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
        <div class="container">
            <div style="display: flex; align-items: center; gap: 12px; justify-content: space-between; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-eye" style="font-size: 18px;"></i>
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">Vista Previa - Administrador</div>
                        <div style="font-size: 12px; opacity: 0.9;">Estás viendo la plaza en modo preview. Tu sesión administrativa se mantiene activa en otro tab.</div>
                    </div>
                </div>
                <button onclick="window.history.back()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 500; transition: all 0.2s; white-space: nowrap;">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ══ HERO ══ -->
    <section class="hero" id="hero-section">

        <div class="hero-bg">
            <img src="{{ asset('images/fotoindex.webp') }}" alt="La Comarca de noche" id="heroBgImg">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-scanline"></div>
        <div class="hero-grain"></div>

        <!-- Orbs flotantes -->
        <div class="hero-orbs">
            <div class="orb orb-1" id="orb1">
                <img src="{{ asset('images/favicon.png') }}" alt="Comarca">
            </div>
            <div class="orb orb-2" id="orb2">
                <img src="{{ asset('images/iconoblanco.png') }}" alt="Comarca">
            </div>
            <div class="orb orb-3" id="orb3">
                <img src="{{ asset('images/favicon.png') }}" alt="Comarca">
            </div>
            <div class="orb-arc" id="orbArc"></div>
        </div>

        <!-- Partículas Vue -->
        <div class="hero-particles">
            <div
                v-for="p in particles" :key="p.id"
                class="particle"
                :style="{
                    left: p.x + '%', top: p.y + '%',
                    width: p.size + 'px', height: p.size + 'px',
                    '--dur': p.dur + 's',
                    '--delay': p.delay + 's',
                    '--dx': p.dx + 'px',
                    '--dy': p.dy + 'px',
                    '--op': p.op,
                    opacity: p.op,
                    animationDelay: p.delay + 's',
                    animationDuration: p.dur + 's',
                }">
            </div>
        </div>

        <!-- Contenido -->
        <div class="container hero-content">

            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                Guápiles, Limón &nbsp;·&nbsp; Costa Rica
            </div>

            <h1 class="hero-title">
                Descubre los<br>
                Mejores <em>Sabores</em><br>
                <strong>de La Comarca</strong>
            </h1>

            <p class="hero-subtitle">
                {{ $stats['total_locales'] }} Locales únicos te esperan. Platillos auténticos,
                ambiente inigualable y la magia de comer bajo las estrellas.
            </p>

            <div class="hero-divider">
                <span class="hero-divider-line"></span>
                <span class="hero-divider-icon"><i class="fas fa-utensils"></i></span>
                <span class="hero-divider-line r"></span>
            </div>

            <div class="search-wrap">
                <i class="fas fa-search search-icon"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Buscar local o platillo..."
                    class="search-input"
                >
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-store"></i></span>
                    <div>
                        <strong>{{ $stats['total_locales'] }}</strong>
                        <small>Locales</small>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-clock"></i></span>
                    <div>
                        <strong>{{ $stats['horario_apertura'] }}–{{ $stats['horario_cierre'] }}</strong>
                        <small>Horario</small>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat">
                    <span class="stat-icon"><i class="fas fa-star"></i></span>
                    <div>
                        <strong>4.8</strong>
                        <small>Calificación</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ CATEGORY BAR ══ -->
    <div class="category-bar" v-cloak>
        <div class="container">
            <div class="category-bar-inner">
                <button
                   @click="filtrarPorCategoria('todos')"
                   :class="['cat-pill', { active: categoriaSelect === 'todos' }]">
                    <i class="fas fa-border-all"></i> Todos
                </button>
                <button
                   v-for="cat in categorias"
                   :key="cat.slug"
                   @click="filtrarPorCategoria(cat.slug)"
                   :class="['cat-pill', { active: categoriaSelect === cat.slug }]">
                    <i :class="['fas', cat.icono]"></i>
                    @{{ cat.nombre }}
                </button>
            </div>
        </div>
    </div>

    <!-- ══ PRODUCTOS FILTRADOS ══ -->
    <section class="filtered-section" v-if="categoriaSelect !== 'todos'" v-cloak>
        <div class="container">
            <div class="filtered-header">
                <div>
                    <p class="filtered-label">
                        <i class="fas fa-filter"></i> Categoría Seleccionada
                    </p>
                    <h2 class="filtered-title">@{{ categoriaSelectNombre || 'Productos Filtrados' }}</h2>
                </div>
                <div class="filtered-count-badge" v-if="!cargandoProductos">
                    <strong>@{{ productosFiltrados.length }}</strong> resultados
                </div>
            </div>

            <!-- Loading skeletons -->
            <div v-if="cargandoProductos" class="skeleton-grid">
                <div class="skeleton-card" v-for="n in 8" :key="n">
                    <div class="skeleton-img"></div>
                    <div class="skeleton-body">
                        <div class="skeleton-line w-40"></div>
                        <div class="skeleton-line w-80"></div>
                        <div class="skeleton-line w-60"></div>
                    </div>
                </div>
            </div>

            <!-- No results -->
            <div v-if="!cargandoProductos && productosFiltrados.length === 0" class="empty-state">
                <i class="fas fa-bowl-food"></i>
                <p>No se encontraron productos en esta categoría</p>
            </div>

            <!-- Grid de productos filtrados -->
            <transition-group
                v-if="!cargandoProductos && productosFiltrados.length > 0"
                name="fade-slide"
                tag="div"
                class="grid-products-filtered">
                <div v-for="producto in productosFiltrados" :key="producto.id" class="product-card-v2">
                    <div class="product-img">
                        <img :src="producto.photo_url" :alt="producto.name" loading="lazy">
                        <div class="product-img-overlay"></div>
                        <span class="product-cat-chip">@{{ producto.category }}</span>
                        <a :href="'/plaza/' + producto.local_id" class="product-quick-view">
                            Ver <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="product-body">
                        <div class="product-local">@{{ producto.local }}</div>
                        <h3 class="product-name" :title="producto.name">@{{ producto.name }}</h3>
                        <div class="product-stars" style="margin: 8px 0; display: flex; gap: 2px;">
                            <i v-for="i in 5" :key="i" class="fas fa-star" :style="{color: i <= (producto.average_rating || 0) ? 'var(--primary)' : 'rgba(122,112,96,0.25)', fontSize: '0.65rem'}"></i>
                        </div>
                        <div class="product-footer">
                            <span class="product-price">₡@{{ producto.price }}</span>
                            <a :href="'/plaza/' + producto.local_id" style="color: var(--primary); font-size: 0.8rem; font-weight: 600;">
                                Ver <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>
    </section>

    <!-- ══ NUESTROS LOCALES ══ -->
    <section class="locales-section">
        <div class="container">
            <div class="locales-header-row">
                <div>
                    <p class="locales-eyebrow">Explora</p>
                    <h2 class="locales-title">Nuestros Locales</h2>
                    <p class="locales-sub">Los mejores restaurantes de la plaza, todos en un solo lugar</p>
                </div>
                <div class="locales-count" aria-hidden="true">
                    <span>{{ $stats['total_locales'] }}</span> locales
                </div>
            </div>

            @if($locales->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-store-slash"></i>
                    <p>No se encontraron locales disponibles</p>
                </div>
            @else
                <div class="grid-locals-v2">
                    @foreach($locales as $local)
                    <article class="local-card-v2">
                        <div class="local-img-wrap-v2">
                            <img src="{{ $local->logo_url ?? 'https://via.placeholder.com/400x225/171410/D4773A?text=' . urlencode($local->name) }}"
                                 alt="{{ $local->name }}" class="local-img-v2" loading="lazy">
                            <div class="local-img-gradient"></div>
                        </div>
                        <div class="local-body-v2">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 8px;">
                                <h3 class="local-name-v2">{{ $local->name }}</h3>
                                <span class="meta-chip" style="white-space: nowrap; font-size: 0.7rem; padding: 4px 8px;">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $local->isOpenNow ? '#4ade80' : '#ef4444' }}; margin-right: 4px;"></span>
                                    {{ $local->isOpenNow ? 'Abierto' : 'Cerrado' }}
                                </span>
                            </div>
                            <p class="local-desc-v2">{{ $local->description ?? 'Explora nuestro menú y descubre sabores únicos' }}</p>
                            <div class="local-stars" style="display: flex; gap: 4px; margin: 10px 0;">
                                @php
                                    $rating = round($local->average_rating ?? 0);
                                    for ($j = 1; $j <= 5; $j++):
                                @endphp
                                    <i class="fas fa-star" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.75rem;"></i>
                                @php
                                    endfor;
                                @endphp
                            </div>
                            <a href="{{ route('plaza.show', $local->local_id) }}" class="btn-ver-menu-v2">
                                Ver Menú <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ══ PRODUCTOS DESTACADOS ══ -->
    @if($productos->isNotEmpty())
    <section class="destacados-section">
        <div class="container">
            <div class="destacados-header">
                <div>
                    <div class="destacados-fire-badge">
                        <i class="fas fa-fire"></i> Lo Más Buscado
                    </div>
                    <h2 class="destacados-title">Platillos Destacados</h2>
                    <p class="destacados-sub">Los favoritos de nuestros clientes de todos los locales</p>
                </div>
            </div>

            <div class="grid-destacados">
                @foreach($productos as $producto)
                <div class="destacado-card">
                    <div class="destacado-img">
                        <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}"
                             alt="{{ $producto->name }}" loading="lazy">
                        <div class="destacado-img-overlay"></div>
                        <span class="destacado-popular-tag">Popular</span>
                    </div>
                    <div class="destacado-body">
                        <div class="destacado-local">
                            {{ $producto->locals->first()?->name ?? 'Local' }}
                        </div>
                        <h3 class="destacado-name" title="{{ $producto->name }}">
                            {{ $producto->name }}
                        </h3>
                        @php
                            $rating = round($producto->average_rating ?? 0);
                        @endphp
                        <div style="margin: 8px 0; display: flex; gap: 2px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="color: {{ $i <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.65rem;"></i>
                            @endfor
                        </div>
                        <div class="destacado-footer" style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="destacado-price">₡{{ number_format($producto->price, 2) }}</span>
                            <a href="{{ route('plaza.show', $producto->locals->first()?->local_id ?? '#') }}" style="color: var(--primary); font-size: 0.8rem; font-weight: 600;">
                                Ver <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

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

    <!-- ══ CART DRAWER ══ -->
    @include('plaza.carrito._cart_drawer')

</div>

<!-- ═══ TOAST NOTIFICATIONS (OUTSIDE TEMPLATE) ═══ -->
@include('plaza.carrito._toast-notifications')

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
    // Función helper para mostrar toasts personalizados
    const showToast = (config) => { if (window.showNotification) { window.showNotification(config); } };
    /* ── User menu ── */
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('userMenuBtn');
        const menuDrop = document.getElementById('userMenuDropdown');
        if (menuBtn && menuDrop) {
            menuBtn.addEventListener('click', e => {
                e.stopPropagation();
                menuDrop.classList.toggle('open');
            });
            document.addEventListener('click', () => menuDrop.classList.remove('open'));
        }
    });

    /* ── Vue App ── */
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                searchQuery: '',
                particles: [],
                categorias: {!! json_encode($categorias) !!},
                categoriaSelect: 'todos',
                categoriaSelectNombre: 'Todos',
                productosFiltrados: [],
                cargandoProductos: false,
                // Cart drawer data
                showCartDrawer: false,
                showConfirmOrder: false,
                showConfirmClear: false,
                drawerCart: [],
                isCheckingOut: false
            }
        },

        mounted() {
            this.buildParticles();
            document.addEventListener('mousemove', this.onMouseMove);
            window.addEventListener('scroll', this.onScroll, { passive: true });
            this.loadCartDrawer();
        },

        beforeUnmount() {
            document.removeEventListener('mousemove', this.onMouseMove);
            window.removeEventListener('scroll', this.onScroll);
        },

        methods: {
            buildParticles() {
                for (let i = 0; i < 20; i++) {
                    this.particles.push({
                        id: i,
                        x:     Math.random() * 100,
                        y:     Math.random() * 100,
                        size:  Math.random() * 3 + 1.5,
                        dur:   Math.random() * 9 + 6,
                        delay: Math.random() * 7,
                        dx:    (Math.random() - 0.5) * 70,
                        dy:    (Math.random() - 0.5) * 70,
                        op:    Math.random() * 0.35 + 0.08,
                    });
                }
            },

            onMouseMove(e) {
                const hero = document.getElementById('hero-section');
                if (!hero) return;
                const rect = hero.getBoundingClientRect();
                if (rect.bottom < 0) return;

                const cx = rect.width  / 2;
                const cy = rect.height / 2;
                const dx = (e.clientX - rect.left  - cx) / cx; // -1 → 1
                const dy = (e.clientY - rect.top   - cy) / cy;

                const o1 = document.getElementById('orb1');
                const o2 = document.getElementById('orb2');
                const o3 = document.getElementById('orb3');
                const arc = document.getElementById('orbArc');

                /* Profundidades distintas para sensación 3-D */
                if (o1)  o1.style.marginTop  = `${dy * -10}px`;
                if (o1)  o1.style.marginRight = `${dx * 10}px`;
                if (o2)  o2.style.transform  = `translate(${dx * -20}px, ${dy * -16}px)`;
                if (o3)  o3.style.transform  = `translate(${dx * -30}px, ${dy * -22}px)`;
                if (arc) arc.style.transform = `translate(${dx * -8}px, ${dy * -6}px)`;
            },

            onScroll() {
                const scrollY = window.scrollY;
                const bg = document.getElementById('heroBgImg');
                if (bg) bg.style.transform = `scale(1.06) translateY(${scrollY * 0.14}px)`;
            },

            filtrarPorCategoria(slug) {
                this.categoriaSelect = slug;

                // Actualizar el nombre de la categoría seleccionada
                if (slug === 'todos') {
                    this.categoriaSelectNombre = 'Todos';
                } else {
                    const cat = this.categorias.find(c => c.slug === slug);
                    this.categoriaSelectNombre = cat ? cat.nombre : 'Productos Filtrados';
                }

                // Obtener productos filtrados vía AJAX
                this.obtenerProductosFiltrados(slug);
            },

            async obtenerProductosFiltrados(categoria) {
                this.cargandoProductos = true;
                try {
                    const response = await fetch(`{{ route('plaza.get.productos') }}?categoria=${categoria}`);
                    const data = await response.json();

                    if (data.success) {
                        this.productosFiltrados = data.data;
                        // Scroll a la sección de productos filtrados
                        setTimeout(() => {
                            const section = document.querySelector('[v-if*="categoriaSelect"]');
                            if (section) {
                                section.scrollIntoView({ behavior: 'smooth' });
                            }
                        }, 100);
                    } else {
                        this.productosFiltrados = [];
                    }
                } catch (error) {
                    console.error('Error al obtener productos:', error);
                    this.productosFiltrados = [];
                } finally {
                    this.cargandoProductos = false;
                }
            },

            // ── DRAWER METHODS ──
            openCartDrawer() {
                this.showCartDrawer = true;
                this.loadCartDrawer();
            },
            closeCartDrawer() {
                this.showCartDrawer = false;
            },
            loadCartDrawer() {
                fetch('{{ route("plaza.cart.get") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.drawerCart = (data.cart || []).map(item => ({
                        ...item,
                        price: parseFloat(item.price),
                        quantity: parseInt(item.quantity)
                    }));
                })
                .catch(error => console.error('Error loading cart:', error));
            },
            updateItemQty(index, newQty) {
                if (newQty < 1) newQty = 1;
                if (this.drawerCart[index]) {
                    this.drawerCart[index].quantity = newQty;
                }
            },
            removeFromCart(index) {
                this.drawerCart.splice(index, 1);
            },
            goToClearCart() {
                this.showConfirmClear = true;
            },
            cancelClearCart() {
                this.showConfirmClear = false;
            },
            confirmClearCart() {
                this.drawerCart = [];
                this.showConfirmClear = false;
                showToast({ icon: 'success', title: '¡Carrito vaciado!', message: 'Todos los items han sido eliminados', timer: 5500 });
            },
            goToCheckout() {
                if (this.drawerCart.length === 0) {
                    showToast({ icon: 'warning', title: 'El carrito está vacío' });
                    return;
                }

                // Mostrar confirmación dentro del panel
                this.showConfirmOrder = true;
            },
            
            cancelConfirmOrder() {
                this.showConfirmOrder = false;
            },

            async processCheckout() {
                this.isCheckingOut = true;

                try {
                    const response = await fetch('{{ route("plaza.order.create") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ items: this.drawerCart })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast({ icon: 'success', title: '¡Orden confirmada!', message: 'Tu orden se ha procesado correctamente', timer: 6000 });
                        this.drawerCart = [];
                        this.showConfirmOrder = false;
                        this.showCartDrawer = false;
                    } else {
                        showToast({ icon: 'error', title: 'No se pudo procesar', message: data.message || 'Hubo un problema al confirmar tu orden', timer: 5500 });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast({ icon: 'error', title: 'Oops, algo salió mal', message: 'Hubo un problema de conexión. Intenta de nuevo', timer: 5500 });
                } finally {
                    this.isCheckingOut = false;
                }
            }
        },

        computed: {
            totalDrawerQty() {
                return this.drawerCart.length;
            },
            totalDrawerPrice() {
                return this.drawerCart.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
            }
        }
    }).mount('#plaza-app');
</script>

</body>
</html>