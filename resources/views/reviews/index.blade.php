@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reviews.css') }}?v={{ time() }}">
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

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="reviews-tabs-wrapper">
        <div class="reviews-tabs-header">
            <button class="reviews-tab-btn active" type="button" data-tab="locales">
                Reseñas de local
            </button>
            <button class="reviews-tab-btn" type="button" data-tab="productos">
                Reseñas de producto
            </button>
        </div>

        {{-- TAB LOCALES --}}
        <div class="reviews-tab-panel active" id="tab-locales">

            <div class="reviews-section-top">
                <div class="review-stat-card">
                    <div class="review-stat-label">PROMEDIO</div>
                    <div class="review-stat-value">
                        {{ number_format($localStats['average'], 1) }} <span>★</span>
                    </div>
                    <div class="review-stat-text">Sobre 5 estrellas</div>
                </div>

                <div class="review-stat-card">
                    <div class="review-stat-label">RESEÑAS</div>
                    <div class="review-stat-value">{{ $localStats['total'] }}</div>
                    <div class="review-stat-text">Este mes: {{ $localStats['month_total'] }}</div>
                </div>

                <div class="review-stat-card">
                    <div class="review-stat-label">DISTRIBUCIÓN</div>
                    @php
                        $maxLocal = max($localStats['distribution']) > 0 ? max($localStats['distribution']) : 1;
                    @endphp
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $localStats['distribution'][$star];
                            $width = ($count / $maxLocal) * 100;
                        @endphp
                        <div class="distribution-row">
                            <span>{{ $star }}</span>
                            <div class="distribution-bar">
                                <div class="distribution-fill" style="width: {{ $width }}%;"></div>
                            </div>
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
                            $nombre = $item->user->full_name ?? 'Cliente';
                            $rating = $item->review->rating ?? 0;
                            $comentario = $item->review->comment ?? 'Sin comentario.';
                            $fecha = $item->review->date ?? '-';
                            $respuesta = $item->review->response ?? null;

                            $partes = explode(' ', trim($nombre));
                            $iniciales = '';
                            foreach(array_slice($partes, 0, 2) as $parte){
                                $iniciales .= strtoupper(substr($parte, 0, 1));
                            }

                            if ($rating >= 4) {
                                $tipoTexto = 'Positiva';
                                $tipoClase = 'positive';
                            } elseif ($rating == 3) {
                                $tipoTexto = 'Neutra';
                                $tipoClase = 'neutral';
                            } else {
                                $tipoTexto = 'Negativa';
                                $tipoClase = 'negative';
                            }
                        @endphp

                        <div class="review-card">
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
                                <div class="review-response-box">
                                    <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                    <div class="review-response-text">{{ $respuesta }}</div>
                                </div>
                            @else
                                <div class="review-action-area">
                                    <button class="reply-btn" type="button" onclick="toggleReplyBox(this)">
                                        Responder
                                    </button>

                                    <form action="{{ route('reviews.respond', $item->local_review_id) }}" method="POST" class="reply-box hidden">
                                        @csrf
                                        <input type="hidden" name="review_type" value="local">
                                        <textarea name="response" placeholder="Escribe una respuesta..." required></textarea>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="submit">Guardar respuesta</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="reviews-empty-state">
                    No hay reseñas de local registradas para este gerente.
                </div>
            @endif
        </div>

        {{-- TAB PRODUCTOS --}}
        <div class="reviews-tab-panel" id="tab-productos">

            <div class="reviews-section-top">
                <div class="review-stat-card">
                    <div class="review-stat-label">PROMEDIO</div>
                    <div class="review-stat-value">
                        {{ number_format($productStats['average'], 1) }} <span>★</span>
                    </div>
                    <div class="review-stat-text">Sobre 5 estrellas</div>
                </div>

                <div class="review-stat-card">
                    <div class="review-stat-label">RESEÑAS</div>
                    <div class="review-stat-value">{{ $productStats['total'] }}</div>
                    <div class="review-stat-text">Este mes: {{ $productStats['month_total'] }}</div>
                </div>

                <div class="review-stat-card">
                    <div class="review-stat-label">DISTRIBUCIÓN</div>
                    @php
                        $maxProduct = max($productStats['distribution']) > 0 ? max($productStats['distribution']) : 1;
                    @endphp
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $productStats['distribution'][$star];
                            $width = ($count / $maxProduct) * 100;
                        @endphp
                        <div class="distribution-row">
                            <span>{{ $star }}</span>
                            <div class="distribution-bar">
                                <div class="distribution-fill" style="width: {{ $width }}%;"></div>
                            </div>
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
                            $nombre = $item->user->full_name ?? 'Cliente';
                            $rating = $item->review->rating ?? 0;
                            $comentario = $item->review->comment ?? 'Sin comentario.';
                            $fecha = $item->review->date ?? '-';
                            $respuesta = $item->review->response ?? null;
                            $nombreProducto = $item->product->name ?? ('Producto #' . $item->product_id);

                            $partes = explode(' ', trim($nombre));
                            $iniciales = '';
                            foreach(array_slice($partes, 0, 2) as $parte){
                                $iniciales .= strtoupper(substr($parte, 0, 1));
                            }

                            if ($rating >= 4) {
                                $tipoTexto = 'Positiva';
                                $tipoClase = 'positive';
                            } elseif ($rating == 3) {
                                $tipoTexto = 'Neutra';
                                $tipoClase = 'neutral';
                            } else {
                                $tipoTexto = 'Negativa';
                                $tipoClase = 'negative';
                            }
                        @endphp

                        <div class="review-card">
                            <div class="review-mini-label">
                                Producto: {{ $nombreProducto }}
                            </div>

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
                                <div class="review-response-box">
                                    <div class="review-response-title">RESPUESTA DEL GERENTE</div>
                                    <div class="review-response-text">{{ $respuesta }}</div>
                                </div>
                            @else
                                <div class="review-action-area">
                                    <button class="reply-btn" type="button" onclick="toggleReplyBox(this)">
                                        Responder
                                    </button>

                                    <form action="{{ route('reviews.respond', $item->product_review_id) }}" method="POST" class="reply-box hidden">
                                        @csrf
                                        <input type="hidden" name="review_type" value="product">
                                        <textarea name="response" placeholder="Escribe una respuesta..." required></textarea>
                                        <div class="reply-actions">
                                            <button class="reply-save-btn" type="submit">Guardar respuesta</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="reviews-empty-state">
                    No hay reseñas de productos de este local.
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });

    function toggleReplyBox(button) {
        const container = button.closest('.review-action-area');
        const box = container.querySelector('.reply-box');
        box.classList.toggle('hidden');
    }
</script>
@endsection