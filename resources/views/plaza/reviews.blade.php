<section class="local-reviews-section" id="resenas">
    <div class="local-reviews-container">

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
                <div class="resena-btn-wrap" style="display:flex; justify-content:center; margin-top:16px;">
                    <button id="btnAbrirResena"
                        style="
                            display:inline-flex; align-items:center; gap:8px;
                            padding:12px 22px; border:none; border-radius:999px;
                            background:#e67e22; color:#ffffff;
                            font-family:'DM Sans', sans-serif; font-size:14px;
                            font-weight:700; cursor:pointer;
                            box-shadow:0 4px 18px rgba(230,126,34,0.35);
                            transition:all .2s ease;
                        "
                        onmouseover="this.style.background='#d35400';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.background='#e67e22';this.style.transform='translateY(0)'">
                        <i class="fas fa-pen-to-square"></i>
                        Escribir reseña
                    </button>
                </div>
                @endauth

            </div>{{-- cierra lrh-top --}}
        </div>{{-- cierra local-reviews-header --}}

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
                            
    <div class="local-review-card" style="position:relative;">
    @auth
    @if((int)$item->user_id === (int)auth()->id())
    <button onclick="eliminarResenaLocal({{ $item->local_review_id }}, {{ $local->local_id }}, this)"
            style="position:absolute; bottom:12px; right:12px; background:transparent; border:none; color:rgba(231,76,60,0.6); cursor:pointer; font-size:16px; padding:4px; transition:color .2s;"
            onmouseover="this.style.color='#e74c3c'"
            onmouseout="this.style.color='rgba(231,76,60,0.6)'"
            title="Eliminar reseña">
        <i class="fas fa-trash-alt"></i>
    </button>
    @endif
    @endauth
   
    
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
                                    <p class="local-review-card__comment" style="word-break:break-word; overflow-wrap:break-word;">"{{ $comment }}"</p>
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

{{-- MODAL RESEÑA --}}
<div id="modalResena" style="display:none; position:fixed; inset:0; z-index:9999;
     background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
    <div style="background:#1a1714; border-radius:16px; padding:32px;
                width:90%; max-width:480px; position:relative;">
        <button onclick="cerrarModalResena()" style="position:absolute; top:16px; right:16px;
                background:none; border:none; color:#F5F0E8; font-size:20px; cursor:pointer;">
            <i class="fas fa-times"></i>
        </button>
        <h3 style="color:#F5F0E8; font-family:'Cormorant Garamond',serif;
                   font-size:1.5rem; margin-bottom:20px;">Tu reseña</h3>

        <div id="starSelector" style="display:flex; gap:8px; margin-bottom:16px;">
            @for($s = 1; $s <= 5; $s++)
            <span data-val="{{ $s }}" onclick="seleccionarEstrella({{ $s }})"
                  style="font-size:28px; cursor:pointer; color:rgba(122,112,96,0.3);
                         transition:color .15s;" class="star-opt">★</span>
            @endfor
        </div>
        <input type="hidden" id="resenaRating" value="0">

        <textarea id="resenaComment" rows="4" maxlength="500"
            placeholder="Describe tu experiencia (mínimo 10 caracteres)..."
            style="width:100%; padding:12px; border-radius:10px; border:1px solid rgba(245,240,232,0.15);
                   background:rgba(255,255,255,0.05); color:#F5F0E8; font-family:'DM Sans',sans-serif;
                   font-size:14px; resize:vertical; box-sizing:border-box;"></textarea>

        <div id="resenaError" style="color:#e74c3c; font-size:13px; margin-top:8px; display:none;"></div>

        <button onclick="enviarResena()" id="btnEnviarResena"
            style="margin-top:16px; width:100%; padding:13px; border:none; border-radius:999px;
                   background:#e67e22; color:#fff; font-weight:700; font-size:15px;
                   cursor:pointer; transition:background .2s;">
            Publicar reseña
        </button>
    </div>
</div>

<script>
const LOCAL_ID_RESENA = {{ $local->local_id }};
const RESENA_URL = `/plaza/${LOCAL_ID_RESENA}/review`;

setTimeout(function() {
    const btn = document.getElementById('btnAbrirResena');
    if (btn) {
        btn.addEventListener('click', () => {
            document.getElementById('modalResena').style.display = 'flex';
        });
    }
}, 500);

function cerrarModalResena() {
    document.getElementById('modalResena').style.display = 'none';
    document.getElementById('resenaRating').value = 0;
    document.getElementById('resenaComment').value = '';
    document.getElementById('resenaError').style.display = 'none';
    document.querySelectorAll('.star-opt').forEach(s => s.style.color = 'rgba(122,112,96,0.3)');
}

function seleccionarEstrella(val) {
    document.getElementById('resenaRating').value = val;
    document.querySelectorAll('.star-opt').forEach(s => {
        s.style.color = parseInt(s.dataset.val) <= val
            ? 'var(--primary, #D4773A)'
            : 'rgba(122,112,96,0.3)';
    });
}

async function enviarResena() {
    const rating  = parseInt(document.getElementById('resenaRating').value);
    const comment = document.getElementById('resenaComment').value.trim();
    const errEl   = document.getElementById('resenaError');
    const btn     = document.getElementById('btnEnviarResena');

    errEl.style.display = 'none';

    if (rating < 1) {
        errEl.textContent = 'Selecciona una calificación de estrellas.';
        errEl.style.display = 'block';
        return;
    }
    if (comment.length < 10) {
        errEl.textContent = 'El comentario debe tener al menos 10 caracteres.';
        errEl.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Publicando...';

    try {
        const res = await fetch(RESENA_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ rating, comment }),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            cerrarModalResena();
            agregarResenaAlCarrusel(data.review);
            if (window.showNotification) {
                window.showNotification({ icon: 'success', title: '¡Reseña publicada!', timer: 4000 });
            }
        } else {
            errEl.textContent = data.error || 'No se pudo guardar la reseña.';
            errEl.style.display = 'block';
        }
    } catch (e) {
        errEl.textContent = 'Error de conexión. Intenta de nuevo.';
        errEl.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Publicar reseña';
    }
}

function agregarResenaAlCarrusel(r) {
    const track = document.getElementById('lrcTrack');
    const emptyEl = document.querySelector('.local-reviews-empty');

    const fecha = new Date(r.date).toLocaleDateString('es-ES', {
        day: 'numeric', month: 'long', year: 'numeric'
    });

    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<span class="lrs ${i <= r.rating ? 'lrs--filled' : ''}">★</span>`;
    }

    if (!track) {
        const wrapper = document.querySelector('.local-reviews-container');
        if (emptyEl) emptyEl.style.display = 'none';
        wrapper.insertAdjacentHTML('beforeend', `
            <div class="lrc-wrapper">
                <button class="lrc-arrow lrc-arrow--prev" id="lrcPrev">&#8592;</button>
                <div class="lrc-viewport">
                    <div class="lrc-track" id="lrcTrack"></div>
                </div>
                <button class="lrc-arrow lrc-arrow--next" id="lrcNext">&#8594;</button>
            </div>
            <div class="lrc-dots" id="lrcDots"></div>
        `);
    }

    const trackEl = document.getElementById('lrcTrack');
    const slide = document.createElement('div');
    slide.className = 'lrc-slide';
    slide.innerHTML = `
        <div class="local-review-card" style="position:relative;">
            <button onclick="eliminarResenaLocal(${r.local_review_id}, ${r.local_id}, this)"
                    style="position:absolute; bottom:12px; right:12px; background:transparent; border:none; color:rgba(231,76,60,0.6); cursor:pointer; font-size:16px; padding:4px; transition:color .2s;"
                    onmouseover="this.style.color='#e74c3c'"
                    onmouseout="this.style.color='rgba(231,76,60,0.6)'"
                    title="Eliminar reseña">
                <i class="fas fa-trash-alt"></i>
            </button>
            <div class="local-review-card__header">
                <div class="local-review-card__avatar">${r.iniciales}</div>
                <div class="local-review-card__user">
                    <div class="local-review-card__name">${r.nombre}</div>
                    <div class="local-review-card__date">${fecha}</div>
                </div>
            </div>
            <div class="local-review-card__stars">${starsHtml}</div>
            <p class="local-review-card__comment">"${r.comment}"</p>
        </div>`;

    trackEl.insertBefore(slide, trackEl.firstChild);

   if (typeof window.reiniciarCarrusel === 'function') {
        window.reiniciarCarrusel();
    }
}

function toggleMenuResena(btn) {
    const dropdown = btn.nextElementSibling;
    document.querySelectorAll('.resena-menu-dropdown').forEach(d => {
        if (d !== dropdown) d.style.display = 'none';
    });
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.resena-menu-wrap')) {
        document.querySelectorAll('.resena-menu-dropdown').forEach(d => d.style.display = 'none');
    }
});

async function eliminarResenaLocal(localReviewId, localId, btn) {
    const confirmResult = await Swal.fire({
        title: '¿Eliminar reseña?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#14110f',
        color: '#F5F0E8'
    });
    if (!confirmResult.isConfirmed) return;

    const res = await fetch(`/plaza/${localId}/review/${localReviewId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    });

    const data = await res.json();
    if (data.success) {
        btn.closest('.lrc-slide').remove();
        if (typeof window.reiniciarCarrusel === 'function') window.reiniciarCarrusel();
        if (window.showNotification) window.showNotification({ icon: 'success', title: 'Reseña eliminada' });
    }
}

</script>