<section class="local-reviews-section" id="resenas">
    <div class="local-reviews-container">

        {{-- Encabezado con botón a la derecha --}}
        <div class="local-reviews-header">
            <div class="lrh-top">
                <div class="lrh-left">
                    <div class="local-reviews-label">
                        <span class="local-reviews-label-dot"></span>
                        OPINIONES
                    </div>
                    <h2 class="local-reviews-title">Lo que dicen nuestros clientes</h2>
                    <p class="local-reviews-subtitle">Experiencias reales de quienes ya visitaron este local</p>
                </div>

                @auth
                @php
                    $yaReseno = \App\Models\LocalReview::where('user_id', auth()->id())
                        ->where('local_id', $local->local_id)
                        ->exists();
                @endphp
                <div class="resena-btn-wrap" style="display:flex; justify-content:center; margin-top:16px;">
                    @if(!$yaReseno)
                        <button id="btnAbrirResena"
    style="
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:12px 22px;
        border:none;
        border-radius:999px;
        background:#e67e22;
        color:#ffffff;
        font-family:'DM Sans', sans-serif;
        font-size:14px;
        font-weight:700;
        cursor:pointer;
        box-shadow:0 4px 18px rgba(230,126,34,0.35);
        transition:all .2s ease;
    "
    onmouseover="this.style.background='#d35400';this.style.transform='translateY(-1px)'"
    onmouseout="this.style.background='#e67e22';this.style.transform='translateY(0)'">
    <i class="fas fa-pen-to-square"></i>
    Escribir reseña
</button>
                    @else
                        <span class="resena-ya-hecha">
                            <i class="fas fa-circle-check"></i>
                            Ya compartiste tu experiencia
                        </span>
                    @endif
                </div>
                @endauth
            </div>
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

        {{-- Carrusel --}}
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

{{-- MODAL --}}
<div id="modalResena"
    style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.72); backdrop-filter:blur(7px);">

    <div id="modalCajaResena"
        style="
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%, -50%);
            width:92%;
            max-width:760px;
            background:#171717;
            border:1px solid #2c2c2c;
            border-radius:24px;
            box-shadow:0 30px 70px rgba(0,0,0,0.6);
            overflow:hidden;
            font-family:'DM Sans', sans-serif;
        ">

        <button id="btnCerrarResena"
            type="button"
            aria-label="Cerrar"
            style="
                position:absolute;
                top:16px;
                right:16px;
                width:42px;
                height:42px;
                border-radius:50%;
                border:1px solid #3a3a3a;
                background:#111;
                color:#fff;
                cursor:pointer;
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:5;
            ">
            <i class="fas fa-times"></i>
        </button>

        <div style="padding:30px 30px 18px 30px; border-bottom:1px solid #2c2c2c;">
            <h2 style="margin:0; font-size:38px; font-weight:700; color:#f5f0e8;">Tu reseña</h2>
            <p style="margin:8px 0 0 0; font-size:18px; color:#aaa;">Contanos cómo fue tu experiencia</p>
        </div>

        <form id="formResena" style="padding:26px 30px 30px 30px; display:flex; flex-direction:column; gap:22px;">
            @csrf

            <div>
                <label style="display:block; margin-bottom:10px; font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#9a9a9a;">
                    ¿Cómo calificás tu experiencia?
                </label>

                <div id="starRating" style="display:flex; gap:10px;">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="rm-star"
                            data-value="{{ $i }}"
                            style="font-size:42px; line-height:1; color:#343434; cursor:pointer; user-select:none; transition:all .12s ease;">
                            ★
                        </span>
                    @endfor
                </div>

                <input type="hidden" id="ratingInput" value="0">

                <p id="errorRating"
                    style="display:none; margin-top:10px; color:#e05252; font-size:13px; line-height:1.4;">
                    <i class="fas fa-triangle-exclamation"></i>
                    Seleccioná una calificación.
                </p>
            </div>

            <div>
                <label style="display:block; margin-bottom:10px; font-size:13px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#9a9a9a;">
                    Comentario
                </label>

                <div style="position:relative;">
                    <textarea
                        id="commentInput"
                        rows="6"
                        maxlength="500"
                        placeholder="¿Qué fue lo que más te gustó?"
                        style="
                            width:100%;
                            min-height:185px;
                            background:#111;
                            border:1px solid #2e2e2e;
                            border-radius:16px;
                            padding:18px 18px 46px 18px;
                            color:#f0ebe0;
                            font-family:'DM Sans', sans-serif;
                            font-size:16px;
                            resize:none;
                            outline:none;
                            box-sizing:border-box;
                            line-height:1.55;
                        "></textarea>

                    <span style="position:absolute; right:16px; bottom:14px; color:#666; font-size:12px;">
                        <span id="charCount">0</span>/500
                    </span>
                </div>

                <p id="errorComment"
                    style="display:none; margin-top:10px; color:#e05252; font-size:13px; line-height:1.4;">
                    <i class="fas fa-triangle-exclamation"></i>
                    Mínimo 10 caracteres.
                </p>
            </div>

            <div style="display:flex; gap:14px; margin-top:2px;">
                <button type="button" id="btnDismiss"
                    style="
                        padding:14px 22px;
                        border-radius:14px;
                        border:1px solid #333;
                        background:transparent;
                        color:#d0d0d0;
                        font-family:'DM Sans', sans-serif;
                        font-size:15px;
                        font-weight:600;
                        cursor:pointer;
                    ">
                    Cancelar
                </button>

                <button type="submit" id="btnSubmitResena"
                    style="
                        flex:1;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        gap:8px;
                        padding:14px 22px;
                        border:none;
                        border-radius:14px;
                        background:#e67e22;
                        color:#fff;
                        font-family:'DM Sans', sans-serif;
                        font-size:15px;
                        font-weight:700;
                        cursor:pointer;
                        box-shadow:0 6px 20px rgba(230,126,34,0.35);
                    ">
                    <i class="fas fa-paper-plane"></i>
                    Publicar reseña
                </button>
            </div>

            <p id="errorGeneral"
                style="display:none; margin-top:4px; color:#e05252; font-size:13px; text-align:center; line-height:1.4;"></p>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toastResena"
    style="display:none; position:fixed; bottom:2rem; right:2rem; z-index:99999; align-items:center; gap:.6rem; padding:.85rem 1.4rem; background:#0e2418; border:1px solid #1e4a2e; border-radius:14px; color:#6aad7a; font-size:.875rem; font-weight:600; font-family:'DM Sans', sans-serif; box-shadow:0 8px 32px rgba(0,0,0,0.55);">
    <i class="fas fa-circle-check"></i>
    ¡Reseña publicada con éxito!
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('modalResena');
    const btnAbrir = document.getElementById('btnAbrirResena');
    const btnCerrar = document.getElementById('btnCerrarResena');
    const btnDismiss = document.getElementById('btnDismiss');
    const form = document.getElementById('formResena');
    const ratingInput = document.getElementById('ratingInput');
    const stars = document.querySelectorAll('.rm-star');
    const toast = document.getElementById('toastResena');
    const commentInput = document.getElementById('commentInput');
    const charCount = document.getElementById('charCount');

    if (commentInput && charCount) {
        commentInput.addEventListener('input', () => {
            charCount.textContent = commentInput.value.length;
        });
    }

    function abrirModal() {
        if (!overlay) return;
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function cerrarModal() {
        if (!overlay) return;
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    btnAbrir?.addEventListener('click', function (e) {
        e.preventDefault();
        abrirModal();
    });

    btnCerrar?.addEventListener('click', cerrarModal);
    btnDismiss?.addEventListener('click', cerrarModal);

    overlay?.addEventListener('click', function (e) {
        if (e.target === overlay) cerrarModal();
    });

    function resaltarEstrellas(valor) {
        stars.forEach(function (s) {
            const active = parseInt(s.dataset.value) <= valor;
            s.style.color = active ? '#e67e22' : '#343434';
            s.style.transform = active ? 'scale(1.10)' : 'scale(1)';
        });
    }

    stars.forEach(function (star) {
        star.addEventListener('mouseover', function () {
            resaltarEstrellas(parseInt(star.dataset.value));
        });

        star.addEventListener('mouseout', function () {
            resaltarEstrellas(parseInt(ratingInput.value || 0));
        });

        star.addEventListener('click', function () {
            ratingInput.value = parseInt(star.dataset.value);
            resaltarEstrellas(parseInt(ratingInput.value));
        });
    });

    function agregarNuevaResenaAlCarrusel(review) {
        const track = document.getElementById('lrcTrack');
        const emptyState = document.querySelector('.local-reviews-empty');
        const container = document.querySelector('.local-reviews-container');

        const nombre = review.name || 'Cliente';
        const partes = nombre.trim().split(' ');
        let iniciales = '';
        partes.slice(0, 2).forEach(p => {
            iniciales += p.charAt(0).toUpperCase();
        });

        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<span class="lrs ${i <= review.rating ? 'lrs--filled' : ''}">★</span>`;
        }

        const nuevaResenaHtml = `
            <div class="lrc-slide">
                <div class="local-review-card">
                    <div class="local-review-card__header">
                        <div class="local-review-card__avatar">${iniciales || 'CL'}</div>
                        <div class="local-review-card__user">
                            <div class="local-review-card__name">${nombre}</div>
                            <div class="local-review-card__date">${review.date}</div>
                        </div>
                    </div>

                    <div class="local-review-card__stars">
                        ${starsHtml}
                    </div>

                    <p class="local-review-card__comment">"${review.comment}"</p>
                </div>
            </div>
        `;

        if (track) {
            track.insertAdjacentHTML('afterbegin', nuevaResenaHtml);
            return;
        }

        if (emptyState && container) {
            emptyState.remove();

            const wrapper = document.createElement('div');
            wrapper.className = 'lrc-wrapper';
            wrapper.innerHTML = `
                <button class="lrc-arrow lrc-arrow--prev" id="lrcPrev" aria-label="Anterior">&#8592;</button>
                <div class="lrc-viewport">
                    <div class="lrc-track" id="lrcTrack">
                        ${nuevaResenaHtml}
                    </div>
                </div>
                <button class="lrc-arrow lrc-arrow--next" id="lrcNext" aria-label="Siguiente">&#8594;</button>
            `;

            const dotsDiv = document.createElement('div');
            dotsDiv.className = 'lrc-dots';
            dotsDiv.id = 'lrcDots';

            container.appendChild(wrapper);
            container.appendChild(dotsDiv);
        }
    }

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();

        document.getElementById('errorRating').style.display = 'none';
        document.getElementById('errorComment').style.display = 'none';
        document.getElementById('errorGeneral').style.display = 'none';

        const rating = parseInt(ratingInput.value);
        const comment = commentInput.value.trim();
        let valido = true;

        if (!rating || rating < 1) {
            document.getElementById('errorRating').style.display = 'block';
            valido = false;
        }

        if (comment.length < 10) {
            document.getElementById('errorComment').style.display = 'block';
            valido = false;
        }

        if (!valido) return;

        const btn = document.getElementById('btnSubmitResena');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publicando...';

        try {
            const response = await fetch(`{{ route('plaza.review.store', $local->local_id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rating, comment })
            });

            const data = await response.json();

            if (response.ok) {
                cerrarModal();
                form.reset();
                ratingInput.value = 0;
                resaltarEstrellas(0);
                if (charCount) charCount.textContent = '0';

                if (btnAbrir) {
                    const wrap = btnAbrir.closest('.resena-btn-wrap');
                    if (wrap) {
                        wrap.innerHTML = `
                            <span class="resena-ya-hecha">
                                <i class="fas fa-circle-check"></i>
                                Ya compartiste tu experiencia
                            </span>
                        `;
                    }
                }

                if (data.review) {
                    agregarNuevaResenaAlCarrusel(data.review);
                }

                if (toast) {
                    toast.style.display = 'flex';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 3500);
                }
            } else {
                const err = document.getElementById('errorGeneral');
                err.textContent = data.error || data.message || 'Ocurrió un error.';
                err.style.display = 'block';
            }
        } catch (err) {
            const errEl = document.getElementById('errorGeneral');
            errEl.textContent = 'Error de conexión. Intentá de nuevo.';
            errEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Publicar reseña';
        }
    });
});
</script>