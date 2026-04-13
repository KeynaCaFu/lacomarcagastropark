<section class="local-reviews-section" id="resenas">
    <div class="local-reviews-container">

        {{-- Encabezado --}}
        <div class="local-reviews-header">
            <div class="local-reviews-label">
                <span class="local-reviews-label-dot"></span>
                OPINIONES
            </div>
            <h2 class="local-reviews-title">Lo que dicen nuestros clientes</h2>
            <p class="local-reviews-subtitle">Experiencias reales de quienes ya visitaron este local</p>
        </div>

        {{-- Promedio general --}}
        @if(isset($localStats) && $localStats['total'] > 0)
        <div class="local-reviews-stat">
            <div class="local-reviews-stat-score">{{ number_format($localStats['average'], 1) }}</div>
            <div class="local-reviews-stat-right">
                <div class="local-reviews-stat-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="lrs {{ $i <= round($localStats['average']) ? 'lrs--filled' : '' }}">★</span>
                    @endfor
                </div>
                <div class="local-reviews-stat-count">
                    Basado en {{ $localStats['total'] }} {{ $localStats['total'] === 1 ? 'reseña' : 'reseñas' }}
                </div>
            </div>
        </div>
        @endif

        {{-- Carrusel de reseñas --}}
        @if(isset($reviews) && $reviews->count() > 0)
        <div class="lrc-wrapper">

            <button class="lrc-arrow lrc-arrow--prev" id="lrcPrev" aria-label="Anterior">&#8592;</button>

            <div class="lrc-viewport">
                <div class="lrc-track" id="lrcTrack">
                    @foreach($reviews as $item)
                        @php
                            $nombre    = $item->user->full_name ?? 'Cliente';
                            $rating    = $item->review->rating ?? 0;
                            $comment   = $item->review->comment ?? null;
                            $fecha     = $item->review->date ?? null;
                            $respuesta = $item->review->response ?? null;
                            $partes    = explode(' ', trim($nombre));
                            $iniciales = '';
                            foreach (array_slice($partes, 0, 2) as $p) {
                                $iniciales .= strtoupper(substr($p, 0, 1));
                            }
                        @endphp

                        <div class="lrc-slide">
                            <div class="local-review-card">
                                <div class="local-review-card__header">
                                    <div class="local-review-card__avatar">{{ $iniciales ?: 'CL' }}</div>
                                    <div class="local-review-card__user">
                                        <div class="local-review-card__name">{{ $nombre }}</div>
                                        @if($fecha)
                                            <div class="local-review-card__date">
                                                {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="local-review-card__stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="lrs {{ $i <= $rating ? 'lrs--filled' : '' }}">★</span>
                                    @endfor
                                </div>

                                @if($comment)
                                    <p class="local-review-card__comment">"{{ $comment }}"</p>
                                @endif

                                @if($respuesta)
                                    <div class="local-review-card__response">
                                        <div class="local-review-card__response-label">
                                            <i class="fas fa-reply"></i> Respuesta del local
                                        </div>
                                        <p class="local-review-card__response-text">{{ $respuesta }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="lrc-arrow lrc-arrow--next" id="lrcNext" aria-label="Siguiente">&#8594;</button>
        </div>

        <div class="lrc-dots" id="lrcDots"></div>

        @else
            <div class="local-reviews-empty">
                <div class="local-reviews-empty__icon">★</div>
                <p class="local-reviews-empty__text">
                    Aún no hay reseñas para este local.<br>
                    ¡Sé el primero en compartir tu experiencia!
                </p>
            </div>
        @endif

    </div>
</section>

<style>
.local-reviews-section {
    background: #0f0f0f;
    padding: 80px 0 90px;
    position: relative;
}
.local-reviews-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(225,128,24,0.35), transparent);
}
.local-reviews-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}
.local-reviews-header {
    text-align: center;
    margin-bottom: 48px;
}
.local-reviews-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.15em;
    color: #e18018;
    text-transform: uppercase;
    margin-bottom: 16px;
}
.local-reviews-label-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #e18018;
    display: inline-block;
}
.local-reviews-title {
    font-size: clamp(26px, 4vw, 40px);
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 12px;
    line-height: 1.2;
}
.local-reviews-subtitle {
    font-size: 15px;
    color: #9ca3af;
    margin: 0;
}
.local-reviews-stat {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 24px 32px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    max-width: 300px;
    margin: 0 auto 52px;
}
.local-reviews-stat-score {
    font-size: 52px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
}
.local-reviews-stat-right {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.local-reviews-stat-count {
    font-size: 13px;
    color: #6b7280;
}
.lrs { color: #3a3a3a; font-size: 16px; }
.lrs--filled { color: #e18018; }

/* ══ CARRUSEL ══ */
.lrc-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}
.lrc-viewport {
    overflow: hidden;
    flex: 1;
    border-radius: 14px;
}
.lrc-track {
    display: flex;
    gap: 20px;
    transition: transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    will-change: transform;
}
.lrc-slide {
    flex: 0 0 calc((100% - 40px) / 3);
    min-width: 0;
}
.lrc-arrow {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.12);
    background: rgba(255,255,255,0.05);
    color: #d1d5db;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.lrc-arrow:hover {
    background: rgba(225,128,24,0.2);
    border-color: rgba(225,128,24,0.5);
    color: #e18018;
}
.lrc-arrow:disabled {
    opacity: 0.25;
    cursor: not-allowed;
    pointer-events: none;
}
.lrc-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 28px;
}
.lrc-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}
.lrc-dot--active {
    background: #e18018;
    transform: scale(1.3);
}

/* ── Card ── */
.local-review-card {
    background: #1a1a1a;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    height: 100%;
    box-sizing: border-box;
    transition: border-color 0.2s ease, transform 0.2s ease;
}
.local-review-card:hover {
    border-color: rgba(225,128,24,0.35);
    transform: translateY(-2px);
}
.local-review-card__header {
    display: flex;
    align-items: center;
    gap: 12px;
}
.local-review-card__avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #915016, #e18018);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.local-review-card__name {
    font-size: 14px;
    font-weight: 700;
    color: #f3f4f6;
}
.local-review-card__date {
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
}
.local-review-card__stars { display: flex; gap: 2px; }
.local-review-card__comment {
    font-size: 14px;
    line-height: 1.65;
    color: #d1d5db;
    margin: 0;
    font-style: italic;
}
.local-review-card__response {
    background: rgba(225,128,24,0.08);
    border-left: 3px solid #e18018;
    border-radius: 0 8px 8px 0;
    padding: 12px 14px;
}
.local-review-card__response-label {
    font-size: 11px;
    font-weight: 700;
    color: #e18018;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}
.local-review-card__response-text {
    font-size: 13px;
    color: #9ca3af;
    margin: 0;
    line-height: 1.55;
}
.local-reviews-empty {
    text-align: center;
    padding: 60px 20px;
}
.local-reviews-empty__icon {
    font-size: 48px;
    color: #2a2a2a;
    margin-bottom: 16px;
}
.local-reviews-empty__text {
    font-size: 15px;
    color: #6b7280;
    line-height: 1.7;
    margin: 0;
}

@media (max-width: 900px) {
    .lrc-slide { flex: 0 0 calc((100% - 20px) / 2); }
}
@media (max-width: 600px) {
    .local-reviews-section { padding: 60px 0 70px; }
    .lrc-slide { flex: 0 0 100%; }
    .lrc-arrow { width: 36px; height: 36px; font-size: 14px; }
}
</style>

<script>
(function () {
    const track   = document.getElementById('lrcTrack');
    const dotsEl  = document.getElementById('lrcDots');
    const btnPrev = document.getElementById('lrcPrev');
    const btnNext = document.getElementById('lrcNext');

    if (!track) return;

    const slides = track.querySelectorAll('.lrc-slide');
    const total  = slides.length;
    let current  = 0;
    let autoPlay;
    let isTransitioning = false;

    function visibleCount() {
        if (window.innerWidth <= 600) return 1;
        if (window.innerWidth <= 900) return 2;
        return 3;
    }

    function buildDots() {
        dotsEl.innerHTML = '';
        for (let i = 0; i < total; i++) {
            const d = document.createElement('button');
            d.className = 'lrc-dot' + (i === current ? ' lrc-dot--active' : '');
            d.addEventListener('click', () => { goTo(i); resetAutoPlay(); });
            dotsEl.appendChild(d);
        }
    }

    function updateDots() {
        dotsEl.querySelectorAll('.lrc-dot').forEach((d, i) => {
            d.classList.toggle('lrc-dot--active', i === current);
        });
    }

    function goTo(index) {
        if (isTransitioning) return;
        current = ((index % total) + total) % total;
        const slideWidth = slides[0].offsetWidth + 20;

        // Si va del último al primero, anima suavemente hacia adelante
        if (index >= total) {
            isTransitioning = true;
            track.style.transition = 'transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            track.style.transform = `translateX(-${total * slideWidth}px)`;
            setTimeout(() => {
                track.style.transition = 'none';
                track.style.transform = `translateX(0px)`;
                current = 0;
                isTransitioning = false;
                updateDots();
            }, 450);
            return;
        }

        // Si va del primero al último (botón prev), anima hacia atrás suavemente
        if (index < 0) {
            isTransitioning = true;
            track.style.transition = 'none';
            track.style.transform = `translateX(-${total * slideWidth}px)`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    track.style.transition = 'transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    track.style.transform = `translateX(-${(total - visibleCount()) * slideWidth}px)`;
                    current = total - 1;
                    setTimeout(() => { isTransitioning = false; }, 450);
                });
            });
            updateDots();
            return;
        }

        track.style.transition = 'transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        track.style.transform = `translateX(-${current * slideWidth}px)`;
        updateDots();
    }

    function resetAutoPlay() {
        clearInterval(autoPlay);
        autoPlay = setInterval(() => goTo(current + 1), 5000);
    }

    btnPrev.addEventListener('click', () => { goTo(current - 1); resetAutoPlay(); });
    btnNext.addEventListener('click', () => { goTo(current + 1); resetAutoPlay(); });

    window.addEventListener('resize', () => { buildDots(); goTo(0); });

    buildDots();
    goTo(0);
    resetAutoPlay();
})();
</script>