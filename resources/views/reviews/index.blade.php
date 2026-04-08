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

    /* Header de respuesta con título + iconos alineados */
    .review-response-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    /* Iconos editar / eliminar */
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

    .response-icon-edit {
        border-color: #9ca3af;
        color: #374151;
    }
    .response-icon-edit:hover {
        background: #f3f4f6;
        border-color: #6b7280;
    }

    .response-icon-delete {
        border-color: #9f0505;
        color: #9f0505;
    }
    .response-icon-delete:hover {
        background: #fee2e2;
        border-color: #d81b1b;
        color: #d81b1b;
    }

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
    .char-counter.warn {
        color: #e18018;
    }
    .char-counter.limit {
        color: #dc2626;
    }
    .textarea-wrap {
        position: relative;
    }
    .textarea-wrap textarea {
        padding-top: 28px !important;
    }

    /* ── FILTROS ── */
    .reviews-filters-wrapper {
        margin-bottom: 20px;
    }
    .reviews-filters-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .reviews-filters-toggle:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }
    .reviews-filters-chevron {
        margin-left: 4px;
        transition: transform 0.2s ease;
        font-size: 11px;
    }
    .reviews-filters-toggle.open .reviews-filters-chevron {
        transform: rotate(180deg);
    }
    .reviews-filters-panel {
        border: 1.5px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 10px 10px;
        background: #fafafa;
        padding: 16px 20px;
        margin-top: -1px;
    }
    .reviews-filters-panel.hidden {
        display: none;
    }
    .reviews-filters-body {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 160px;
    }
    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .filter-group select {
        padding: 8px 12px;
        border: 1.5px solid #d1d5db;
        border-radius: 7px;
        font-size: 14px;
        color: #374151;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .filter-group select:hover,
    .filter-group select:focus {
        border-color: #915016;
        outline: none;
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

    {{-- FILTROS COLAPSABLES --}}
    <div class="reviews-filters-wrapper">
        <button class="reviews-filters-toggle" type="button" onclick="toggleFilters(this)">
            <i class="fas fa-sliders-h"></i>
            Filtros
            <i class="fas fa-chevron-down reviews-filters-chevron"></i>
        </button>
        <div class="reviews-filters-panel hidden" id="filtersPanel">
            <div class="reviews-filters-body">
                <div class="filter-group">
                    <label>Calificación</label>
                    <select name="rating">
                        <option value="">Todas</option>
                        <option value="5">5 estrellas</option>
                        <option value="4">4 estrellas</option>
                        <option value="3">3 estrellas</option>
                        <option value="2">2 estrellas</option>
                        <option value="1">1 estrella</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="">Todos</option>
                        <option value="positive">Positivas</option>
                        <option value="neutral">Neutras</option>
                        <option value="negative">Negativas</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Con respuesta</label>
                    <select name="respondida">
                        <option value="">Todas</option>
                        <option value="1">Con respuesta</option>
                        <option value="0">Sin respuesta</option>
                    </select>
                </div>
            </div>
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
                             onclick="openReviewModal(this, event)">

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
                                <div class="review-response-box review-response-box--orange">

                                    <div class="review-response-header">
                                        <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                        <div class="response-icon-actions" id="local-response-actions-{{ $item->local_review_id }}">
                                            <button type="button"
                                                    class="response-icon-btn response-icon-edit"
                                                    title="Editar respuesta"
                                                    onclick="editResponse('local', {{ $item->local_review_id }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('reviews.response.delete', $item->review->review_id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  class="delete-response-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="response-icon-btn response-icon-delete"
                                                        title="Eliminar respuesta"
                                                        onclick="confirmDeleteResponse(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="review-response-text" id="local-response-text-{{ $item->local_review_id }}">
                                        {{ $respuesta }}
                                    </div>

                                    <form action="{{ route('reviews.response.update', $item->review->review_id) }}"
                                          method="POST"
                                          class="reply-box hidden edit-response-form"
                                          id="local-edit-form-{{ $item->local_review_id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="local-counter-edit-{{ $item->local_review_id }}">{{ mb_strlen($respuesta) }}/1000</span>
                                            <textarea name="response"
                                                      placeholder="Editar respuesta..."
                                                      required
                                                      maxlength="1000"
                                                      oninput="updateCounter(this, 'local-counter-edit-{{ $item->local_review_id }}')">{{ $respuesta }}</textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button"
                                                    onclick="confirmSaveEdit(this)">Guardar cambios</button>
                                            <button type="button"
                                                    class="reply-btn"
                                                    onclick="cancelEditResponse('local', {{ $item->local_review_id }})">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            @else
                                <div class="review-action-area">
                                    <button class="reply-btn respond-toggle-btn" type="button" onclick="toggleReplyBox(this)">
                                        Responder
                                    </button>

                                    <form action="{{ route('reviews.respond', $item->local_review_id) }}" method="POST" class="reply-box hidden respond-form">
                                        @csrf
                                        <input type="hidden" name="review_type" value="local">
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="local-counter-new-{{ $item->local_review_id }}">0/1000</span>
                                            <textarea name="response"
                                                      placeholder="Escribir respuesta..."
                                                      required
                                                      maxlength="1000"
                                                      data-counter="local-counter-new-{{ $item->local_review_id }}"
                                                      oninput="updateCounter(this, 'local-counter-new-{{ $item->local_review_id }}')"></textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button"
                                                    onclick="confirmSaveResponse(this)">Guardar respuesta</button>
                                            <button class="reply-btn" type="button"
                                                    onclick="cancelNewResponse(this)">Cancelar</button>
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
                             onclick="openReviewModal(this, event)">

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
                                <div class="review-response-box review-response-box--orange">

                                    <div class="review-response-header">
                                        <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                        <div class="response-icon-actions" id="product-response-actions-{{ $item->product_review_id }}">
                                            <button type="button"
                                                    class="response-icon-btn response-icon-edit"
                                                    title="Editar respuesta"
                                                    onclick="editResponse('product', {{ $item->product_review_id }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('reviews.response.delete', $item->review->review_id) }}"
                                                  method="POST"
                                                  style="display:inline;"
                                                  class="delete-response-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="response-icon-btn response-icon-delete"
                                                        title="Eliminar respuesta"
                                                        onclick="confirmDeleteResponse(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="review-response-text" id="product-response-text-{{ $item->product_review_id }}">
                                        {{ $respuesta }}
                                    </div>

                                    <form action="{{ route('reviews.response.update', $item->review->review_id) }}"
                                          method="POST"
                                          class="reply-box hidden edit-response-form"
                                          id="product-edit-form-{{ $item->product_review_id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="product-counter-edit-{{ $item->product_review_id }}">{{ mb_strlen($respuesta) }}/1000</span>
                                            <textarea name="response"
                                                      placeholder="Editar respuesta..."
                                                      required
                                                      maxlength="1000"
                                                      oninput="updateCounter(this, 'product-counter-edit-{{ $item->product_review_id }}')">{{ $respuesta }}</textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button"
                                                    onclick="confirmSaveEdit(this)">Guardar cambios</button>
                                            <button type="button"
                                                    class="reply-btn"
                                                    onclick="cancelEditResponse('product', {{ $item->product_review_id }})">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>

                                </div>
                            @else
                                <div class="review-action-area">
                                    <button class="reply-btn respond-toggle-btn" type="button" onclick="toggleReplyBox(this)">
                                        Responder
                                    </button>

                                    <form action="{{ route('reviews.respond', $item->product_review_id) }}" method="POST" class="reply-box hidden respond-form">
                                        @csrf
                                        <input type="hidden" name="review_type" value="product">
                                        <div class="textarea-wrap">
                                            <span class="char-counter" id="product-counter-new-{{ $item->product_review_id }}">0/1000</span>
                                            <textarea name="response"
                                                      placeholder="Escribir respuesta..."
                                                      required
                                                      maxlength="1000"
                                                      data-counter="product-counter-new-{{ $item->product_review_id }}"
                                                      oninput="updateCounter(this, 'product-counter-new-{{ $item->product_review_id }}')"></textarea>
                                        </div>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="button"
                                                    onclick="confirmSaveResponse(this)">Guardar respuesta</button>
                                            <button class="reply-btn" type="button"
                                                    onclick="cancelNewResponse(this)">Cancelar</button>
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

<!-- MODAL DETALLES DE RESEÑA -->
<div id="reviewModal" class="review-modal hidden">
    <div class="review-modal-overlay"></div>
    <div class="review-modal-content">
        <button class="review-modal-close" onclick="closeReviewModal()">×</button>

        <div class="review-modal-header">
            <div class="review-modal-avatar" id="modalAvatar">CL</div>
            <div class="review-modal-info">
                <div class="review-modal-name" id="modalNombre">Cliente</div>
                <div class="review-modal-fecha" id="modalFecha">-</div>
            </div>
            <span class="review-modal-badge" id="modalBadge">Positiva</span>
        </div>

        <div class="review-modal-body">
            <div class="review-modal-producto" id="modalProducto" style="display: none;">
                <strong>PRODUCTO:</strong> <span id="modalProductoNombre"></span>
            </div>

            <div class="review-modal-calificacion-title">Calificación</div>
            <div class="review-modal-rating" id="modalRating">
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
            </div>

            <div id="modalComentarioContainer" style="display: none;">
                <div class="review-modal-comentario-title">Comentario</div>
                <div class="review-modal-comentario" id="modalComentario">Sin comentario.</div>
            </div>

            <div class="review-modal-respuesta" id="modalRespuestaContainer" style="display: none;">
                <div class="review-response-box review-response-box--orange">
                    <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                    <div class="review-response-text" id="modalRespuesta"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* MODAL */
    .review-modal {
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .review-modal.hidden {
        display: none;
        opacity: 0;
    }

    .review-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        cursor: pointer;
    }

    .review-modal-content {
        position: relative;
        background: white;
        border-radius: 16px;
        padding: 32px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .review-modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 40px;
        height: 40px;
        border: none;
        background: transparent;
        font-size: 32px;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }

    .review-modal-close:hover {
        color: #374151;
    }

    .review-modal-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .review-modal-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #915016, #c67c3f);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }

    .review-modal-info {
        flex: 1;
    }

    .review-modal-name {
        font-size: 18px;
        font-weight: 700;
        color: #181818;
        margin-bottom: 4px;
    }

    .review-modal-fecha {
        font-size: 14px;
        color: #9ca3af;
    }

    .review-modal-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .review-modal-badge.positive {
        background: #dcfce7;
        color: #166534;
    }

    .review-modal-badge.neutral {
        background: #fef3c7;
        color: #92400e;
    }

    .review-modal-badge.negative {
        background: #fee2e2;
        color: #991b1b;
    }

    .review-modal-body {
        margin-top: 16px;
    }

    .review-modal-producto {
        background: #f9fafb;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
        color: #374151;
    }

    .review-modal-calificacion-title {
        font-size: 14px;
        font-weight: 700;
        color: #3c3c3c;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .review-modal-comentario-title {
        font-size: 14px;
        font-weight: 700;
        color: #3c3c3c;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 16px;
    }

    .review-modal-rating {
        display: flex;
        gap: 4px;
        margin-bottom: 16px;
    }

    .review-modal-rating .star {
        font-size: 20px;
        color: #d1d5db;
    }

    .review-modal-rating .star.filled {
        color: #fbbf24;
    }

    .review-modal-comentario {
        font-size: 16px;
        line-height: 1.6;
        color: #374151;
        margin-bottom: 16px;
        background: #f9fafb;
        padding: 16px;
        border-radius: 8px;
    }

    .review-modal-respuesta {
        margin-top: 16px;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tabs
        const tabButtons = document.querySelectorAll('.reviews-tab-btn');
        const tabPanels = document.querySelectorAll('.reviews-tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const tab = this.getAttribute('data-tab');
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
            });
        });

        // Cerrar modal al hacer click en el overlay
        const modal = document.getElementById('reviewModal');
        const overlay = document.querySelector('.review-modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', closeReviewModal);
        }

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeReviewModal();
            }
        });

        // Mensajes de sesión via SweetAlert toast
        @if(session('success'))
        waitForSwal(() => {
            window.swToast && window.swToast.fire({
                icon: 'success',
                title: @json(session('success')),
                timer: 4000,
            });
        });
        @endif

        @if(session('error'))
        waitForSwal(() => {
            window.swToast && window.swToast.fire({
                icon: 'error',
                title: @json(session('error')),
                timer: 4000,
            });
        });
        @endif
    });

    function waitForSwal(cb) {
        if (typeof Swal !== 'undefined' && window.swToast) {
            cb();
        } else {
            setTimeout(() => waitForSwal(cb), 100);
        }
    }

    // Toggle filtros colapsables
    function toggleFilters(btn) {
        const panel = document.getElementById('filtersPanel');
        const isOpen = !panel.classList.contains('hidden');
        if (isOpen) {
            panel.classList.add('hidden');
            btn.classList.remove('open');
        } else {
            panel.classList.remove('hidden');
            btn.classList.add('open');
        }
    }

    // Abrir modal de detalles de reseña
    // Solo se abre si el click NO viene de un elemento interactivo interno
    function openReviewModal(element, event) {
        if (event.target.closest('button, a, input, textarea, select, form')) return;

        const modal = document.getElementById('reviewModal');

        // Recopilar datos del elemento
        const nombre = element.getAttribute('data-modal-nombre') || 'Cliente';
        const iniciales = element.getAttribute('data-modal-iniciales') || 'CL';
        const fecha = element.getAttribute('data-modal-fecha') || '-';
        const rating = parseInt(element.getAttribute('data-modal-rating')) || 0;
        const comentario = element.getAttribute('data-modal-comentario') || 'Sin comentario.';
        const tipoTexto = element.getAttribute('data-modal-tipo-texto') || 'Neutra';
        const tipoClase = element.getAttribute('data-modal-tipo-clase') || 'neutral';
        const respuesta = element.getAttribute('data-modal-respuesta') || '';
        const producto = element.getAttribute('data-modal-producto') || '';

        // Actualizar contenido del modal
        document.getElementById('modalAvatar').textContent = iniciales;
        document.getElementById('modalNombre').textContent = nombre;
        document.getElementById('modalFecha').textContent = fecha;
        document.getElementById('modalComentario').textContent = comentario;

        // Mostrar/ocultar comentario con su título
        const comentarioContainer = document.getElementById('modalComentarioContainer');
        if (comentario && comentario.trim() && comentario !== 'Sin comentario.') {
            comentarioContainer.style.display = 'block';
        } else {
            comentarioContainer.style.display = 'none';
        }

        // Actualizar badge
        const badge = document.getElementById('modalBadge');
        badge.textContent = tipoTexto;
        badge.className = 'review-modal-badge';
        badge.classList.add(tipoClase);

        // Actualizar rating (estrellas)
        const starsContainer = document.querySelector('.review-modal-rating');
        starsContainer.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('span');
            star.className = 'star' + (i <= rating ? ' filled' : '');
            star.textContent = '★';
            starsContainer.appendChild(star);
        }

        // Mostrar/ocultar producto
        const productContainer = document.getElementById('modalProducto');
        if (producto) {
            document.getElementById('modalProductoNombre').textContent = producto;
            productContainer.style.display = 'block';
        } else {
            productContainer.style.display = 'none';
        }

        // Mostrar/ocultar respuesta
        const respuestaContainer = document.getElementById('modalRespuestaContainer');
        if (respuesta) {
            document.getElementById('modalRespuesta').textContent = respuesta;
            respuestaContainer.style.display = 'block';
        } else {
            respuestaContainer.style.display = 'none';
        }

        // Mostrar modal
        modal.classList.remove('hidden');
    }

    // Cerrar modal de detalles de reseña
    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function updateCounter(textarea, counterId) {
        const counter = document.getElementById(counterId);
        if (!counter) return;
        const len = textarea.value.length;
        const max = parseInt(textarea.getAttribute('maxlength')) || 1000;
        counter.textContent = len + '/' + max;
        counter.classList.remove('warn', 'limit');
        if (len >= max) {
            counter.classList.add('limit');
        } else if (len >= max * 0.8) {
            counter.classList.add('warn');
        }
    }

    // Toggle reply box
    function toggleReplyBox(button) {
        const container = button.closest('.review-action-area');
        const box = container.querySelector('.reply-box');
        box.style.display = 'block';
        box.classList.remove('hidden');
        button.style.display = 'none';
    }

    // Cancelar nueva respuesta
    function cancelNewResponse(btn) {
        const container = btn.closest('.review-action-area');
        const box = container.querySelector('.reply-box');
        const respondBtn = container.querySelector('.respond-toggle-btn');
        box.style.display = 'none';
        box.classList.add('hidden');
        respondBtn.style.display = '';
        const textarea = box.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
            const counterId = textarea.getAttribute('data-counter');
            if (counterId) {
                const counter = document.getElementById(counterId);
                if (counter) counter.textContent = '0/1000';
            }
        }
    }

    // Edit response
    function editResponse(type, id) {
        document.getElementById(type + '-response-text-' + id).classList.add('hidden');
        document.getElementById(type + '-response-actions-' + id).classList.add('hidden');
        document.getElementById(type + '-edit-form-' + id).classList.remove('hidden');
    }

    function cancelEditResponse(type, id) {
        document.getElementById(type + '-response-text-' + id).classList.remove('hidden');
        document.getElementById(type + '-response-actions-' + id).classList.remove('hidden');
        document.getElementById(type + '-edit-form-' + id).classList.add('hidden');
    }

    // Confirmar guardar nueva respuesta
    function confirmSaveResponse(btn) {
        const form = btn.closest('form');
        const textarea = form.querySelector('textarea');
        if (!textarea.value.trim()) {
            window.swToast && window.swToast.fire({ icon: 'warning', title: 'Por favor escribe una respuesta.' });
            return;
        }
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas guardar esta respuesta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#915016',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }

    // Confirmar guardar edición
    function confirmSaveEdit(btn) {
        const form = btn.closest('form');
        const textarea = form.querySelector('textarea');
        if (!textarea.value.trim()) {
            window.swToast && window.swToast.fire({ icon: 'warning', title: 'Por favor escribe una respuesta.' });
            return;
        }
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas guardar los cambios de esta respuesta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#915016',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }

    // Confirmar eliminar respuesta — igual que proveedores
    function confirmDeleteResponse(btn) {
        const form = btn.closest('form');
        waitForSwal(() => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas eliminar esta respuesta?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'No, cancelar',
                confirmButtonColor: '#9f0505',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
            }).then(result => {
                if (result.isConfirmed) {
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