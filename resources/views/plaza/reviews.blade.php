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

        {{-- Cards de reseñas --}}
        @if(isset($reviews) && $reviews->count() > 0)
            <div class="local-reviews-grid">
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
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($reviews->hasPages())
                <div class="local-reviews-pagination">
                    @if($reviews->onFirstPage())
                        <span class="lrp-btn lrp-btn--disabled">&#8592;</span>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}#resenas" class="lrp-btn">&#8592;</a>
                    @endif

                    @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                        @if($page == $reviews->currentPage())
                            <span class="lrp-btn lrp-btn--active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}#resenas" class="lrp-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}#resenas" class="lrp-btn">&#8594;</a>
                    @else
                        <span class="lrp-btn lrp-btn--disabled">&#8594;</span>
                    @endif
                </div>
            @endif

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
    width: 6px;
    height: 6px;
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

.lrs {
    color: #3a3a3a;
    font-size: 16px;
}
.lrs--filled {
    color: #e18018;
}

.local-reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 48px;
}

.local-review-card {
    background: #1a1a1a;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
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
    width: 44px;
    height: 44px;
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

.local-review-card__stars {
    display: flex;
    gap: 2px;
}

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

.local-reviews-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.lrp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 10px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    color: #d1d5db;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    transition: all 0.2s ease;
}

.lrp-btn:hover {
    background: rgba(225,128,24,0.15);
    border-color: rgba(225,128,24,0.4);
    color: #e18018;
}

.lrp-btn--active {
    background: #e18018;
    border-color: #e18018;
    color: #fff;
    cursor: default;
}

.lrp-btn--disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

@media (max-width: 640px) {
    .local-reviews-section { padding: 60px 0 70px; }
    .local-reviews-grid { grid-template-columns: 1fr; }
    .local-reviews-stat { padding: 18px 20px; }
}
</style>