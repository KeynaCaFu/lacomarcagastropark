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