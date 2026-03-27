@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reviews.css') }}?v={{ time() }}">
<style>
    /* Override: response box naranja */
    .review-response-box--orange {
        border-left-color: #e18018 !important;
        background: #fff8f0 !important;
    }
    .review-response-box--orange .review-response-title {
        color: #e18018 !important;
    }

    .review-response-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .response-icon-actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .response-icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1.5px solid;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        transition: all 0.2s ease;
    }
    .response-icon-edit { border-color: #9ca3af; color: #374151; }
    .response-icon-edit:hover { background: #f3f4f6; border-color: #6b7280; }
    .response-icon-delete { border-color: #9f0505; color: #9f0505; }
    .response-icon-delete:hover { background: #fee2e2; border-color: #d81b1b; color: #d81b1b; }

    .char-counter {
        position: absolute;
        top: 8px;
        right: 10px;
        font-size: 11px;
        color: #9ca3af;
        pointer-events: none;
        background: rgba(255,255,255,0.85);
        padding: 1px 4px;
        border-radius: 4px;
        z-index: 2;
        font-weight: 500;
    }
    .char-counter.warn  { color: #e18018; }
    .char-counter.limit { color: #dc2626; }
    .textarea-wrap { position: relative; }
    .textarea-wrap textarea { padding-top: 28px !important; }

    /* Tarjeta clickeable */
    .review-card-clickable {
        cursor: pointer;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .review-card-clickable:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    /* ===== MODAL ===== */
    .review-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .review-modal-overlay.active { display: flex; }

    .review-modal {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 580px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalSlideIn 0.25s ease;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .review-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    .review-modal-title {
        font-size: 14px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .review-modal-close {
        background: none;
        border: none;
        font-size: 22px;
        color: #9ca3af;
        cursor: pointer;
        padding: 2px 8px;
        border-radius: 6px;
        transition: all 0.2s;
        line-height: 1;
    }
    .review-modal-close:hover { background: #f3f4f6; color: #374151; }

    .review-modal-body { padding: 24px; }

    .review-modal-user {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }
    .review-modal-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: #374151;
        flex-shrink: 0;
    }
    .review-modal-user-name {
        font-weight: 700;
        font-size: 16px;
        color: #111827;
    }
    .review-modal-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 4px;
        flex-wrap: wrap;
    }
    .review-modal-date { font-size: 13px; color: #9ca3af; }

    .review-modal-stars { display: flex; gap: 3px; margin-bottom: 16px; }
    .review-modal-stars .star { font-size: 22px; color: #d1d5db; }
    .review-modal-stars .star.filled { color: #f59e0b; }

    .review-modal-section-label {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .review-modal-comment {
        font-size: 15px;
        color: #374151;
        line-height: 1.6;
        background: #f9fafb;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }
    .review-modal-product-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff7ed;
        color: #c9690f;
        border: 1px solid #fed7aa;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .review-modal-response-box {
        background: #fff8f0;
        border-left: 4px solid #e18018;
        border-radius: 0 10px 10px 0;
        padding: 14px 16px;
    }
    .review-modal-response-label {
        font-size: 11px;
        font-weight: 700;
        color: #e18018;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .review-modal-response-text { font-size: 14px; color: #374151; line-height: 1.6; }
    .review-modal-no-response {
        text-align: center;
        padding: 16px;
        color: #9ca3af;
        font-size: 14px;
        background: #f9fafb;
        border-radius: 10px;
        font-style: italic;
    }
</style>
@endpush

@section('title', 'Reseñas')

@section('content')
<div class="reviews-page">

    <div class="reviews-module-header">
        <div>
            <h1 class="reviews-module-title">
                <i class="fas fa-star"></i>
                Gestión de reseñas y opiniones
            </h1>
            <p class="reviews-module-subtitle">
                Administra las reseñas de tu local y de tus productos.
            </p>
        </div>
    </div>

    <div class="reviews-tabs-wrapper">
        <div class="reviews-tabs-header">
            <button class="reviews-tab-btn active" type="button" data-tab="locales">Reseñas de local</button>
            <button class="reviews-tab-btn" type="button" data-tab="productos">Reseñas de producto</button>
        </div>

        {{-- TAB LOCALES --}}
        <div class="reviews-tab-panel active" id="tab-locales">

            <div class="reviews-section-top">
                <div class="review-stat-card">
                    <div class="review-stat-label">PROMEDIO</div>
                    <div class="review-stat-value">{{ number_format($localStats['average'], 1) }} <span>★</span></div>
                    <div class="review-stat-text">Sobre 5 estrellas</div>
                </div>
                <div class="review-stat-card">
                    <div class="review-stat-label">RESEÑAS</div>
                    <div class="review-stat-value">{{ $localStats['total'] }}</div>
                    <div class="review-stat-text">Este mes: {{ $localStats['month_total'] }}</div>
                </div>
                <div class="review-stat-card">
                    <div class="review-stat-label">DISTRIBUCIÓN</div>
                    @php $maxLocal = max($localStats['distribution']) > 0 ? max($localStats['distribution']) : 1; @endphp
                    @for($star = 5; $star >= 1; $star--)
                        @php $count = $localStats['distribution'][$star]; $width = ($count / $maxLocal) * 100; @endphp
                        <div class="distribution-row">
                            <span>{{ $star }}</span>
                            <div class="distribution-bar"><div class="distribution-fill" style="width: {{ $width }}%;"></div></div>
                            <strong>{{ $count }}</strong>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="reviews-toolbar">
                <h2 class="reviews-block-title">RESEÑAS DE LOCAL</h2>
            </div>

            @if($localReviews->count() > 0)
                <div class="reviews-grid">
                    @foreach($localReviews as $item)
                        @php
                            $nombre     = $item->user->full_name ?? 'Cliente';
                            $rating     = $item->review->rating ?? 0;
                            $comentario = $item->review->comment ?? 'Sin comentario.';
                            $fecha      = $item->review->date ?? '-';
                            $respuesta  = $item->review->response ?? null;
                            $partes = explode(' ', trim($nombre));
                            $iniciales = '';
                            foreach(array_slice($partes, 0, 2) as $p){ $iniciales .= strtoupper(substr($p, 0, 1)); }
                            if ($rating >= 4)     { $tipoTexto = 'Positiva'; $tipoClase = 'positive'; }
                            elseif ($rating == 3) { $tipoTexto = 'Neutra';   $tipoClase = 'neutral';  }
                            else                  { $tipoTexto = 'Negativa'; $tipoClase = 'negative'; }
                        @endphp

                        <div class="review-card review-card-clickable"
                             data-modal-nombre="{{ $nombre }}"
                             data-modal-iniciales="{{ $iniciales ?: 'CL' }}"
                             data-modal-fecha="{{ $fecha }}"
                             data-modal-rating="{{ $rating }}"
                             data-modal-comentario="{{ $comentario }}"
                             data-modal-tipo-texto="{{ $tipoTexto }}"
                             data-modal-tipo-clase="{{ $tipoClase }}"
                             data-modal-respuesta="{{ $respuesta ?? '' }}"
                             data-modal-producto=""
                             onclick="openReviewModal(this)">

                            <div class="review-card-header">
                                <div class="review-user-box">
                                    <div class="review-avatar">{{ $iniciales ?: 'CL' }}</div>
                                    <div>
                                        <div class="review-user-name">{{ $nombre }}</div>
                                        <div class="review-date">{{ $fecha }}</div>
                                    </div>
                                </div>
                                <span class="review-badge {{ $tipoClase }}">{{ $tipoTexto }}</span>
                            </div>

                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>

                            <div class="review-comment">{{ $comentario }}</div>

                            @if($respuesta)
                                <div class="review-response-box review-response-box--orange" onclick="event.stopPropagation()">
                                    <div class="review-response-header">
                                        <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                        <div class="response-icon-actions" id="local-response-actions-{{ $item->local_review_id }}">
                                            <button type="button" class="response-icon-btn response-icon-edit" title="Editar respuesta"
                                                    onclick="event.stopPropagation(); editResponse('local', {{ $item->local_review_id }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('reviews.response.delete', $item->review->review_id) }}"
                                                  method="POST" style="display:inline;" class="delete-response-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="response-icon-btn response-icon-delete" title="Eliminar respuesta"
                                                        onclick="event.stopPropagation(); confirmDeleteResponse(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="review-response-text" id="local-response-text-{{ $item->local_review_id }}">{{ $respuesta }}</div>
                                    <form action="{{ route('reviews.response.update', $item->review->review_id) }}"
                                          method="POST" class="reply-box hidden edit-response-form"
                                          id="local-edit-form-{{ $item->local_review_id }}" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('PUT')
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="local-counter-edit-{{ $item->local_review_id }}">{{ mb_strlen($respuesta) }}/1000</span>
                                            <textarea name="response" placeholder="Editar respuesta..." required maxlength="1000"
                                                      oninput="updateCounter(this, 'local-counter-edit-{{ $item->local_review_id }}')">{{ $respuesta }}</textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button" onclick="confirmSaveEdit(this)">Guardar cambios</button>
                                            <button type="button" class="reply-btn" onclick="cancelEditResponse('local', {{ $item->local_review_id }})">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="review-action-area" onclick="event.stopPropagation()">
                                    <button class="reply-btn respond-toggle-btn" type="button" onclick="toggleReplyBox(this)">Responder</button>
                                    <form action="{{ route('reviews.respond', $item->local_review_id) }}" method="POST" class="reply-box hidden respond-form">
                                        @csrf
                                        <input type="hidden" name="review_type" value="local">
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="local-counter-new-{{ $item->local_review_id }}">0/1000</span>
                                            <textarea name="response" placeholder="Escribir respuesta..." required maxlength="1000"
                                                      data-counter="local-counter-new-{{ $item->local_review_id }}"
                                                      oninput="updateCounter(this, 'local-counter-new-{{ $item->local_review_id }}')"></textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button" onclick="confirmSaveResponse(this)">Guardar respuesta</button>
                                            <button class="reply-btn" type="button" onclick="cancelNewResponse(this)">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="reviews-empty-state">No hay reseñas de local registradas para este gerente.</div>
            @endif
        </div>

        {{-- TAB PRODUCTOS --}}
        <div class="reviews-tab-panel" id="tab-productos">

            <div class="reviews-section-top">
                <div class="review-stat-card">
                    <div class="review-stat-label">PROMEDIO</div>
                    <div class="review-stat-value">{{ number_format($productStats['average'], 1) }} <span>★</span></div>
                    <div class="review-stat-text">Sobre 5 estrellas</div>
                </div>
                <div class="review-stat-card">
                    <div class="review-stat-label">RESEÑAS</div>
                    <div class="review-stat-value">{{ $productStats['total'] }}</div>
                    <div class="review-stat-text">Este mes: {{ $productStats['month_total'] }}</div>
                </div>
                <div class="review-stat-card">
                    <div class="review-stat-label">DISTRIBUCIÓN</div>
                    @php $maxProduct = max($productStats['distribution']) > 0 ? max($productStats['distribution']) : 1; @endphp
                    @for($star = 5; $star >= 1; $star--)
                        @php $count = $productStats['distribution'][$star]; $width = ($count / $maxProduct) * 100; @endphp
                        <div class="distribution-row">
                            <span>{{ $star }}</span>
                            <div class="distribution-bar"><div class="distribution-fill" style="width: {{ $width }}%;"></div></div>
                            <strong>{{ $count }}</strong>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="reviews-toolbar">
                <h2 class="reviews-block-title">RESEÑAS DE PRODUCTO</h2>
            </div>

            @if($productReviews->count() > 0)
                <div class="reviews-grid">
                    @foreach($productReviews as $item)
                        @php
                            $nombre         = $item->user->full_name ?? 'Cliente';
                            $rating         = $item->review->rating ?? 0;
                            $comentario     = $item->review->comment ?? 'Sin comentario.';
                            $fecha          = $item->review->date ?? '-';
                            $respuesta      = $item->review->response ?? null;
                            $nombreProducto = $item->product->name ?? ('Producto #' . $item->product_id);
                            $partes = explode(' ', trim($nombre));
                            $iniciales = '';
                            foreach(array_slice($partes, 0, 2) as $p){ $iniciales .= strtoupper(substr($p, 0, 1)); }
                            if ($rating >= 4)     { $tipoTexto = 'Positiva'; $tipoClase = 'positive'; }
                            elseif ($rating == 3) { $tipoTexto = 'Neutra';   $tipoClase = 'neutral';  }
                            else                  { $tipoTexto = 'Negativa'; $tipoClase = 'negative'; }
                        @endphp

                        <div class="review-card review-card-clickable"
                             data-modal-nombre="{{ $nombre }}"
                             data-modal-iniciales="{{ $iniciales ?: 'CL' }}"
                             data-modal-fecha="{{ $fecha }}"
                             data-modal-rating="{{ $rating }}"
                             data-modal-comentario="{{ $comentario }}"
                             data-modal-tipo-texto="{{ $tipoTexto }}"
                             data-modal-tipo-clase="{{ $tipoClase }}"
                             data-modal-respuesta="{{ $respuesta ?? '' }}"
                             data-modal-producto="{{ $nombreProducto }}"
                             onclick="openReviewModal(this)">

                            <div class="review-mini-label">Producto: {{ $nombreProducto }}</div>

                            <div class="review-card-header">
                                <div class="review-user-box">
                                    <div class="review-avatar">{{ $iniciales ?: 'CL' }}</div>
                                    <div>
                                        <div class="review-user-name">{{ $nombre }}</div>
                                        <div class="review-date">{{ $fecha }}</div>
                                    </div>
                                </div>
                                <span class="review-badge {{ $tipoClase }}">{{ $tipoTexto }}</span>
                            </div>

                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star {{ $i <= $rating ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>

                            <div class="review-comment">{{ $comentario }}</div>

                            @if($respuesta)
                                <div class="review-response-box review-response-box--orange" onclick="event.stopPropagation()">
                                    <div class="review-response-header">
                                        <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                        <div class="response-icon-actions" id="product-response-actions-{{ $item->product_review_id }}">
                                            <button type="button" class="response-icon-btn response-icon-edit" title="Editar respuesta"
                                                    onclick="event.stopPropagation(); editResponse('product', {{ $item->product_review_id }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('reviews.response.delete', $item->review->review_id) }}"
                                                  method="POST" style="display:inline;" class="delete-response-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="response-icon-btn response-icon-delete" title="Eliminar respuesta"
                                                        onclick="event.stopPropagation(); confirmDeleteResponse(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="review-response-text" id="product-response-text-{{ $item->product_review_id }}">{{ $respuesta }}</div>
                                    <form action="{{ route('reviews.response.update', $item->review->review_id) }}"
                                          method="POST" class="reply-box hidden edit-response-form"
                                          id="product-edit-form-{{ $item->product_review_id }}" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('PUT')
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="product-counter-edit-{{ $item->product_review_id }}">{{ mb_strlen($respuesta) }}/1000</span>
                                            <textarea name="response" placeholder="Editar respuesta..." required maxlength="1000"
                                                      oninput="updateCounter(this, 'product-counter-edit-{{ $item->product_review_id }}')">{{ $respuesta }}</textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button" onclick="confirmSaveEdit(this)">Guardar cambios</button>
                                            <button type="button" class="reply-btn" onclick="cancelEditResponse('product', {{ $item->product_review_id }})">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="review-action-area" onclick="event.stopPropagation()">
                                    <button class="reply-btn respond-toggle-btn" type="button" onclick="toggleReplyBox(this)">Responder</button>
                                    <form action="{{ route('reviews.respond', $item->product_review_id) }}" method="POST" class="reply-box hidden respond-form">
                                        @csrf
                                        <input type="hidden" name="review_type" value="product">
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="product-counter-new-{{ $item->product_review_id }}">0/1000</span>
                                            <textarea name="response" placeholder="Escribir respuesta..." required maxlength="1000"
                                                      data-counter="product-counter-new-{{ $item->product_review_id }}"
                                                      oninput="updateCounter(this, 'product-counter-new-{{ $item->product_review_id }}')"></textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button" onclick="confirmSaveResponse(this)">Guardar respuesta</button>
                                            <button class="reply-btn" type="button" onclick="cancelNewResponse(this)">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="reviews-empty-state">No hay reseñas de productos de este local.</div>
            @endif
        </div>
    </div>
</div>

{{-- ===== MODAL DETALLE DE RESEÑA ===== --}}
<div class="review-modal-overlay" id="reviewModalOverlay" onclick="closeReviewModal()">
    <div class="review-modal" onclick="event.stopPropagation()">
        <div class="review-modal-header">
            <span class="review-modal-title">Detalle de reseña</span>
            <button class="review-modal-close" onclick="closeReviewModal()" title="Cerrar">&times;</button>
        </div>
        <div class="review-modal-body">

            <div id="modalProductTag" class="review-modal-product-tag" style="display:none;">
                <i class="fas fa-box" style="font-size:12px;"></i>
                <span id="modalProductNombre"></span>
            </div>

            <div class="review-modal-user">
                <div class="review-modal-avatar" id="modalAvatar"></div>
                <div>
                    <div class="review-modal-user-name" id="modalNombre"></div>
                    <div class="review-modal-meta">
                        <span class="review-modal-date" id="modalFecha"></span>
                        <span class="review-badge" id="modalBadge"></span>
                    </div>
                </div>
            </div>

            <div class="review-modal-stars" id="modalStars"></div>

            <div class="review-modal-section-label">Comentario</div>
            <div class="review-modal-comment" id="modalComentario"></div>

            <div class="review-modal-section-label">Respuesta del gerente</div>
            <div id="modalRespuestaBox"></div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tabs
        document.querySelectorAll('.reviews-tab-btn').forEach(button => {
            button.addEventListener('click', function () {
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.reviews-tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.reviews-tab-panel').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
            });
        });

        // Cerrar modal con ESC
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeReviewModal(); });
    });

    function waitForSwal(cb) {
        if (typeof Swal !== 'undefined' && window.swToast) cb();
        else setTimeout(() => waitForSwal(cb), 100);
    }

    // ===== MODAL =====
    function openReviewModal(card) {
        const d        = card.dataset;
        const rating   = parseInt(d.modalRating) || 0;
        const respuesta= d.modalRespuesta || '';
        const producto = d.modalProducto  || '';

        // Producto tag
        const tagEl = document.getElementById('modalProductTag');
        if (producto) {
            tagEl.style.display = 'inline-flex';
            document.getElementById('modalProductNombre').textContent = producto;
        } else {
            tagEl.style.display = 'none';
        }

        document.getElementById('modalAvatar').textContent   = d.modalIniciales;
        document.getElementById('modalNombre').textContent   = d.modalNombre;
        document.getElementById('modalFecha').textContent    = d.modalFecha;

        const badge = document.getElementById('modalBadge');
        badge.textContent = d.modalTipoTexto;
        badge.className   = 'review-badge ' + d.modalTipoClase;

        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<span class="star ${i <= rating ? 'filled' : ''}">&#9733;</span>`;
        }
        document.getElementById('modalStars').innerHTML     = starsHtml;
        document.getElementById('modalComentario').textContent = d.modalComentario;

        const respBox = document.getElementById('modalRespuestaBox');
        if (respuesta) {
            respBox.innerHTML = `
                <div class="review-modal-response-box">
                    <div class="review-modal-response-label">RESPUESTA DEL GERENTE</div>
                    <div class="review-modal-response-text">${escapeHtml(respuesta)}</div>
                </div>`;
        } else {
            respBox.innerHTML = `<div class="review-modal-no-response">Aún no hay respuesta del gerente.</div>`;
        }

        document.getElementById('reviewModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        document.getElementById('reviewModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    // ===== CONTADOR =====
    function updateCounter(textarea, counterId) {
        const counter = document.getElementById(counterId);
        if (!counter) return;
        const len = textarea.value.length;
        const max = parseInt(textarea.getAttribute('maxlength')) || 1000;
        counter.textContent = len + '/' + max;
        counter.classList.remove('warn','limit');
        if (len >= max)           counter.classList.add('limit');
        else if (len >= max*0.8)  counter.classList.add('warn');
    }

    // ===== REPLY BOX =====
    function toggleReplyBox(button) {
        const container = button.closest('.review-action-area');
        const box = container.querySelector('.reply-box');
        box.style.display = 'block';
        box.classList.remove('hidden');
        button.style.display = 'none';
    }

    function cancelNewResponse(btn) {
        const container  = btn.closest('.review-action-area');
        const box        = container.querySelector('.reply-box');
        const respondBtn = container.querySelector('.respond-toggle-btn');
        box.style.display        = 'none';
        box.classList.add('hidden');
        respondBtn.style.display = '';
        const textarea = box.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
            const cid = textarea.getAttribute('data-counter');
            if (cid) { const c = document.getElementById(cid); if (c) c.textContent = '0/1000'; }
        }
    }

    // ===== EDIT =====
    function editResponse(type, id) {
        document.getElementById(type+'-response-text-'+id).classList.add('hidden');
        document.getElementById(type+'-response-actions-'+id).classList.add('hidden');
        document.getElementById(type+'-edit-form-'+id).classList.remove('hidden');
    }

    function cancelEditResponse(type, id) {
        document.getElementById(type+'-response-text-'+id).classList.remove('hidden');
        document.getElementById(type+'-response-actions-'+id).classList.remove('hidden');
        document.getElementById(type+'-edit-form-'+id).classList.add('hidden');
    }

    // ===== SWEETALERTS =====
    function confirmSaveResponse(btn) {
        const form = btn.closest('form');
        const textarea = form.querySelector('textarea');
        if (!textarea.value.trim()) {
            window.swToast && window.swToast.fire({ icon: 'warning', title: 'Por favor escribe una respuesta.' });
            return;
        }
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?', text: '¿Deseas guardar esta respuesta?', icon: 'question',
                showCancelButton: true, confirmButtonText: 'Sí, guardar', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#915016', cancelButtonColor: '#6b7280',
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    }

    function confirmSaveEdit(btn) {
        const form = btn.closest('form');
        const textarea = form.querySelector('textarea');
        if (!textarea.value.trim()) {
            window.swToast && window.swToast.fire({ icon: 'warning', title: 'Por favor escribe una respuesta.' });
            return;
        }
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?', text: '¿Deseas guardar los cambios de esta respuesta?', icon: 'question',
                showCancelButton: true, confirmButtonText: 'Sí, guardar', cancelButtonText: 'Cancelar',
                confirmButtonColor: '#915016', cancelButtonColor: '#6b7280',
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    }

    function confirmDeleteResponse(btn) {
        const form = btn.closest('form');
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?', text: '¿Deseas eliminar esta respuesta?', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'No, cancelar',
                confirmButtonColor: '#9f0505', cancelButtonColor: '#6b7280', reverseButtons: true,
            }).then(r => {
                if (r.isConfirmed) {
                    window.confirmWithUndo({
                        message: 'La respuesta será eliminada',
                        delayMs: 8000,
                        onConfirm: () => form.submit(),
                    });
                }
            });
        });
    }
</script>
@endpush
@endsection