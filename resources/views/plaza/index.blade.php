<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Plaza Gastronómica - La Comarca</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

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
            transition: background 0.3s;
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
            overflow: hidden;
            isolation: isolate;
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
        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--primary);
            animation: glowPulse 2s ease infinite;
        }

        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.8rem, 6vw, 4.6rem);
            font-weight: 300;
            line-height: 1.06;
            color: var(--text);
            margin-bottom: 18px;
            letter-spacing: -0.01em;
            opacity: 0;
            animation: fadeUp 0.95s cubic-bezier(.22,.68,0,1.2) 0.42s forwards;
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

        .hero-divider {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeUp 0.8s ease 0.78s forwards;
        }
        .hero-divider-line {
            flex: 1; max-width: 56px; height: 1px;
            background: linear-gradient(to right, transparent, rgba(212,119,58,0.45));
        }
        .hero-divider-line.r { background: linear-gradient(to left, transparent, rgba(212,119,58,0.45)); }
        .hero-divider-icon { color: var(--primary); font-size: 0.78rem; opacity: 0.65; }

        /* Search */
        .search-wrap {
            position: relative; margin-bottom: 36px;
            opacity: 0;
            animation: fadeUp 0.9s cubic-bezier(.22,.68,0,1.2) 0.88s forwards;
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
        }
        .stat-divider { width: 1px; height: 30px; background: rgba(38,32,24,0.9); }
        .stat { display: flex; align-items: center; gap: 11px; }
        .stat-icon {
            display: flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(212,119,58,0.1);
            border: 1px solid rgba(212,119,58,0.18);
            color: var(--primary); font-size: 0.88rem; flex-shrink: 0;
        }
        .stat strong {
            display: block;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem; font-weight: 700;
            color: var(--text); line-height: 1;
        }
        .stat small {
            font-size: 0.66rem; color: var(--muted);
            margin-top: 2px; display: block;
            text-transform: uppercase; letter-spacing: 0.07em;
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
           CATEGORY BAR
        ══════════════════════════════════════ */
        .category-bar {
            background: rgba(10,9,8,0.96);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(18px);
            position: sticky; top: 0; z-index: 150;
            padding: 10px 0;
        }
        .categories-scroll {
            display: flex; align-items: center; gap: 8px;
            overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;
            padding: 4px 0;
        }
        .categories-scroll::-webkit-scrollbar { display: none; }
        .cat-btn {
            display: inline-flex; align-items: center; gap: 6px;
            white-space: nowrap;
            padding: 7px 16px; border-radius: 999px;
            font-size: 0.78rem; font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            border: 1px solid var(--border); cursor: pointer;
            transition: all 0.2s;
            background: transparent; color: var(--muted);
            flex-shrink: 0;
        }
        .cat-btn:hover { background: var(--card); color: var(--text); }
        .cat-btn.active {
            background: var(--primary); border-color: var(--primary);
            color: #fff; box-shadow: 0 4px 16px var(--primary-glow);
        }

        /* ══════════════════════════════════════
           SECTIONS
        ══════════════════════════════════════ */
        .section {
            padding: 56px 0;
            border-bottom: 1px solid var(--border);
            position: relative;
        }
        .section-wood {
            background-image: url('{{ asset("images/fondomadera1.webp") }}');
            background-size: cover;
            background-position: center;
        }
        .section-wood::before {
            content: '';
            position: absolute; inset: 0;
            background: rgba(10,9,8,0.72);
            pointer-events: none; z-index: 0;
        }
        .section-wood > .container { position: relative; z-index: 1; }

        .section-header { margin-bottom: 36px; }
        .section-eyebrow {
            font-size: 0.68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.12em;
            color: var(--primary); margin-bottom: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-eyebrow::before {
            content: ''; width: 24px; height: 1px;
            background: var(--primary); opacity: 0.6;
        }
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(1.8rem, 3vw, 2.4rem);
            font-weight: 700; color: var(--text); margin-bottom: 8px;
            line-height: 1.1;
        }
        .section-sub { color: var(--muted); font-size: 0.88rem; line-height: 1.6; font-weight: 300; }

        /* Section badge (products) */
        .section-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--primary-light); color: var(--primary);
            font-size: 0.72rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            padding: 5px 12px; border-radius: 999px; margin-bottom: 10px;
        }

        /* ══════════════════════════════════════
           LOCAL CARDS
        ══════════════════════════════════════ */
        .grid-locals {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, 1fr);
        }
        @media (min-width: 768px) {
            .grid-locals { grid-template-columns: repeat(3, 1fr); gap: 20px; }
        }

        .local-card {
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.32s cubic-bezier(.22,.68,0,1.2), box-shadow 0.32s;
        }
        .local-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.45), 0 0 0 1px var(--primary-glow);
        }

        .local-img-wrap {
            position: relative;
            aspect-ratio: 16/9; overflow: hidden;
        }
        .local-img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(.22,.68,0,1.2);
        }
        .local-card:hover .local-img { transform: scale(1.07); }

        .local-img-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(10,9,8,0.7) 0%, transparent 55%);
        }

        .local-body { padding: 16px; }

        .local-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem; font-weight: 700;
            color: var(--text); margin-bottom: 5px;
        }
        .local-desc {
            font-size: 0.75rem; color: var(--muted);
            line-height: 1.5; font-weight: 300;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
            margin-bottom: 12px;
        }
        .local-meta {
            display: flex; align-items: center;
            justify-content: space-between;
            font-size: 0.73rem; color: var(--muted);
            margin-bottom: 12px; flex-wrap: wrap; gap: 8px;
        }
        .meta-item { display: flex; align-items: center; gap: 4px; }
        .meta-item i { color: var(--primary); font-size: 0.85rem; }
        .local-stars { display: flex; gap: 2px; align-items: center; }

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

        .btn-ver-menu {
            width: 100%; padding: 10px 0;
            background: var(--primary); color: #fff;
            font-size: 0.82rem; font-weight: 600;
            border: none; border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-ver-menu:hover { background: #c06830; box-shadow: 0 4px 14px var(--primary-glow); }
        .btn-ver-menu:active { transform: scale(0.97); }

        /* ══════════════════════════════════════
           PRODUCT CARDS
        ══════════════════════════════════════ */
        .grid-products {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
        @media (min-width: 640px) {
            .grid-products { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 900px) {
            .grid-products { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }

        .product-card {
            background: var(--card);
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.45);
        }
        .product-img {
            position: relative; aspect-ratio: 1; overflow: hidden;
            background: rgba(0,0,0,0.15);
        }
        .product-img img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s;
        }
        .product-card:hover .product-img img { transform: scale(1.09); }
        .product-badge {
            position: absolute; top: 8px; right: 8px;
            background: var(--primary); color: #fff;
            font-size: 0.62rem; font-weight: 700;
            padding: 3px 8px; border-radius: 999px;
            letter-spacing: 0.04em;
        }
        .product-body { padding: 12px; }
        .product-local {
            font-size: 0.62rem; font-weight: 700;
            color: var(--primary); text-transform: uppercase;
            letter-spacing: 0.07em; margin-bottom: 4px;
        }
        .product-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem; font-weight: 600; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 8px;
        }
        .product-footer { display: flex; align-items: center; justify-content: space-between; }
        .product-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem; font-weight: 700; color: var(--primary);
        }
        .product-stars { display: flex; gap: 2px; margin-bottom: 8px; align-items: center; }
        .product-stars-filtered { display: flex; gap: 2px; margin-bottom: 8px; align-items: center; }

        /* Grid Productos Filtrados */
        .grid-products-filtered {
            display: grid; gap: 16px;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            animation: fadeUp 0.5s cubic-bezier(.22,.68,0,1.2);
        }
        @media (min-width: 640px) {
            .grid-products-filtered { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 900px) {
            .grid-products-filtered { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }

        /* Botón Ver Producto Filtrado */
        .btn-product-view {
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            padding: 6px 10px; background: var(--primary); color: #fff;
            font-size: 0.7rem; font-weight: 600; border-radius: var(--radius-sm);
            transition: background 0.2s, transform 0.15s;
            text-decoration: none;
            border: none; cursor: pointer; font-family: 'DM Sans', sans-serif;
        }
        .btn-product-view:hover { background: #c06830; transform: translateX(2px); }

        /* Spinner de carga */
        .spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ══════════════════════════════════════
           EMPTY STATE
        ══════════════════════════════════════ */
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--muted);
        }
        .empty-state i { font-size: 3rem; opacity: 0.2; margin-bottom: 14px; display: block; }

        /* ══════════════════════════════════════
           FOOTER
        ══════════════════════════════════════ */
        .footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 56px 0 24px;
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 36px; margin-bottom: 36px;
        }
        .footer-section h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem; font-weight: 700; color: var(--text);
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 7px;
        }
        .footer-section h3 i { color: var(--primary); font-size: 0.9rem; }
        .footer-section p {
            font-size: 0.82rem; color: var(--muted);
            line-height: 1.9; font-weight: 300;
            display: flex; align-items: center; gap: 8px;
        }
        .footer-section p i { color: var(--primary); width: 14px; text-align: center; }
        .social-icons { display: flex; gap: 12px; margin-top: 14px; flex-wrap: wrap; }
        .social-icons a {
            display: inline-flex; align-items: center; justify-content: center;
            width: 38px; height: 38px; border-radius: var(--radius-sm);
            background: var(--primary-light); color: var(--primary);
            border: 1px solid rgba(212,119,58,0.2);
            transition: all 0.2s; font-size: 1rem;
        }
        .social-icons a:hover {
            background: var(--primary); color: #fff;
            transform: translateY(-2px); box-shadow: 0 6px 16px var(--primary-glow);
        }
        .copyright {
            border-top: 1px solid var(--border); padding-top: 20px;
            text-align: center; font-size: 0.75rem; color: var(--muted); font-weight: 300;
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
            .orb-1 { width: 190px; height: 190px; right: -75px; opacity: 0.45 !important; }
            .orb-2 { display: none; }
            .hero-stats { gap: 14px; }
            .stat-divider { display: none; }
            .footer-content { grid-template-columns: 1fr; gap: 24px; }
            .footer-section { text-align: center; }
            .footer-section h3 { justify-content: center; }
            .footer-section p { justify-content: center; }
            .social-icons { justify-content: center; }
        }
        @media (max-width: 480px) {
            .plaza-header { padding: 10px 0; }
            .grid-locals { grid-template-columns: 1fr; }
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
    <div class="category-bar">
        <div class="container">
            <div class="categories-scroll">
                <button
                   @click="filtrarPorCategoria('todos')"
                   :class="['cat-btn', { active: categoriaSelect === 'todos' }]">
                    <i class="fas fa-border-all"></i> Todos
                </button>
                <button
                   v-for="cat in categorias"
                   :key="cat.slug"
                   @click="filtrarPorCategoria(cat.slug)"
                   :class="['cat-btn', { active: categoriaSelect === cat.slug }]">
                    <i :class="['fas', cat.icono]"></i>
                    @{{ cat.nombre }}
                </button>
            </div>
        </div>
    </div>

    <!-- ══ PRODUCTOS FILTRADOS ══ -->
    <section class="section" v-if="categoriaSelect !== 'todos'">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Categoría Seleccionada</p>
                <h2 class="section-title">@{{ categoriaSelectNombre || 'Productos Filtrados' }}</h2>
                <p class="section-sub" v-if="!cargandoProductos">Se encontraron <strong>@{{ productosFiltrados.length }}</strong> productos</p>
            </div>

            <!-- No results -->
            <div v-if="!cargandoProductos && productosFiltrados.length === 0" class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No se encontraron productos en esta categoría</p>
            </div>

            <!-- Grid de productos filtrados -->
            <div v-if="!cargandoProductos && productosFiltrados.length > 0" class="grid-products-filtered">
                <div v-for="producto in productosFiltrados" :key="producto.id" class="product-card">
                    <div class="product-img">
                        <img :src="producto.photo_url || '{{ asset('images/product-placeholder.png') }}'" :alt="producto.name" loading="lazy">
                        <span class="product-badge">@{{ producto.category }}</span>
                    </div>
                    <div class="product-body">
                        <div class="product-local">
                            @{{ producto.local }}
                        </div>
                        <h3 class="product-name" :title="producto.name">
                            @{{ producto.name }}
                        </h3>
                        <div class="product-stars-filtered">
                            <i v-for="j in 5" :key="j" class="fas fa-star" :style="{ color: j <= producto.average_rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)', fontSize: '0.75rem' }"></i>
                        </div>
                        <div class="product-footer">
                            <span class="product-price">₡@{{ producto.price }}</span>
                            <a :href="'/plaza/' + producto.local_id" class="btn-product-view">
                                Ver <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ NUESTROS LOCALES ══ -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <p class="section-eyebrow">Explora</p>
                <h2 class="section-title">Nuestros Locales</h2>
                <p class="section-sub">Los mejores restaurantes de la plaza, todos en un solo lugar</p>
            </div>

            @if($locales->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No se encontraron locales disponibles</p>
                </div>
            @else
                <div class="grid-locals">
                    @foreach($locales as $local)
                    <article class="local-card">
                        <div class="local-img-wrap">
                            <img src="{{ $local->logo_url ?? 'https://via.placeholder.com/400x225/171410/D4773A?text=' . urlencode($local->name) }}"
                                 alt="{{ $local->name }}" class="local-img">
                            <div class="local-img-overlay"></div>
                        </div>
                        <div class="local-body">
                            <h3 class="local-name">{{ $local->name }}</h3>
                            <p class="local-desc">{{ $local->description ?? 'Explora nuestro menú' }}</p>
                            <div class="local-meta">
                                <div class="local-stars">
                                    @php
                                        $rating = round($local->average_rating ?? 0);
                                        for ($j = 1; $j <= 5; $j++):
                                    @endphp
                                        <i class="fas fa-star" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.75rem;"></i>
                                    @php
                                        endfor;
                                    @endphp
                                </div>
                                <span class="local-status {{ $local->status === 'Active' ? '' : 'closed' }}">
                                    {{ $local->status === 'Active' ? 'Abierto' : 'Cerrado' }}
                                </span>
                            </div>
                            <a href="{{ route('plaza.show', $local->local_id) }}" class="btn-ver-menu">
                                Ver Menú <i class="fas fa-chevron-right"></i>
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
    <section class="section section-wood">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">
                    <i class="fas fa-fire"></i> Lo Más Buscado
                </div>
                <h2 class="section-title">Platillos Destacados</h2>
                <p class="section-sub">Los favoritos de nuestros clientes de todos los locales</p>
            </div>

            <div class="grid-products">
                @foreach($productos as $producto)
                <div class="product-card">
                    <div class="product-img">
                        <img src="{{ $producto->photo_url ?? asset('images/product-placeholder.png') }}"
                             alt="{{ $producto->name }}" loading="lazy">
                        <span class="product-badge">Popular</span>
                    </div>
                    <div class="product-body">
                        <div class="product-local">
                            {{ $producto->locals->first()?->name ?? 'Local' }}
                        </div>
                        <h3 class="product-name" title="{{ $producto->name }}">
                            {{ $producto->name }}
                        </h3>
                        <div class="product-stars">
                            @php
                                $rating = round($producto->average_rating ?? 0);
                                for ($j = 1; $j <= 5; $j++):
                            @endphp
                                <i class="fas fa-star" style="color: {{ $j <= $rating ? 'var(--primary)' : 'rgba(122,112,96,0.25)' }}; font-size: 0.75rem;"></i>
                            @php
                                endfor;
                            @endphp
                        </div>
                        <div class="product-footer">
                            <span class="product-price">₡{{ number_format($producto->price, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ══ FOOTER ══ -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-clock"></i> Horario</h3>
                    <p><strong>Lunes</strong></p>
                    <p>Cerrado</p>
                    <p><strong>Martes a Domingo</strong></p>
                    <p>12:00 MD – 10:00 PM</p>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Ubicación</h3>
                    <p>
                        <i class="fas fa-location-dot"></i>
                        <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener"
                           style="color:var(--primary);text-decoration:underline;">La Comarca Gastro Park</a>
                    </p>
                    <p>
                        <i class="fas fa-map"></i>
                        <a href="https://maps.app.goo.gl/UYkQZhrKbVnTKgWj8?g_st=aw" target="_blank" rel="noopener"
                           style="color:var(--muted);text-decoration:underline;">Guápiles, Limón, Costa Rica</a>
                    </p>
                    <p style="margin-top:8px;"><i class="fas fa-phone"></i> +506 8888 8888</p>
                    <p><i class="fas fa-envelope"></i> info@lacomarcagastropark.com</p>
                </div>
                <div class="footer-section">
                    <h3><i class="fas fa-share-alt"></i> Síguenos</h3>
                    <p style="margin-bottom:4px;font-weight:300;">Conecta con nosotros en redes sociales</p>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/share/1CYem5AGeo/" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/la.comarcagastropark?igsh=bW43MHB0OG9yMG8y" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@la.comarcagastropark?_t=ZM-8z8TOSBnnGv&_r=1" target="_blank" rel="noopener" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2026 La Comarca Gastro Park. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

</div>

<script>
    /* ── User menu ── */
    const menuBtn = document.getElementById('userMenuBtn');
    const menuDrop = document.getElementById('userMenuDropdown');
    if (menuBtn && menuDrop) {
        menuBtn.addEventListener('click', e => {
            e.stopPropagation();
            menuDrop.classList.toggle('open');
        });
        document.addEventListener('click', () => menuDrop.classList.remove('open'));
    }

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
            };
        },

        mounted() {
            this.buildParticles();
            document.addEventListener('mousemove', this.onMouseMove);
            window.addEventListener('scroll', this.onScroll, { passive: true });
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
        }
    }).mount('#plaza-app');
</script>
</body>
</html>