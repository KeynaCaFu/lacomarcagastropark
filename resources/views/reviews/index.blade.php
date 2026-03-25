@extends('layouts.app')

@section('title', 'Reseñas')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/reviews.css') }}">
@endsection


@section('content')
<div class="products-container reviews-page">

    <div class="reviews-header">
        <h2><i class="fas fa-star"></i> Reseñas</h2>
        <p class="reviews-subtitle">
            Gestiona las reseñas de tu local
        </p>
    </div>

    @if(isset($reviews) && count($reviews) > 0)

        <div class="reviews-grid">
            @foreach($reviews as $item)

                @php
                    $nombre = $item->user->full_name ?? 'Cliente';
                    $rating = $item->review->rating ?? 0;
                    $comentario = $item->review->comment ?? '';
                    $fecha = $item->review->date ?? '';
                    $respuesta = $item->review->response ?? null;

                    $tipo = $rating >= 4 ? 'positive' : ($rating == 3 ? 'neutral' : 'negative');
                @endphp

                <div class="review-card">

                    <div class="review-top">
                        <div class="review-user">
                            <div class="review-avatar">
                                {{ strtoupper(substr($nombre, 0, 1)) }}
                            </div>

                            <div>
                                <div class="review-name">{{ $nombre }}</div>
                                <div class="review-date">{{ $fecha }}</div>
                            </div>
                        </div>

                        <span class="review-badge {{ $tipo }}">
                            {{ ucfirst($tipo) }}
                        </span>
                    </div>

                    <div class="review-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $rating ? 'filled' : '' }}">★</span>
                        @endfor
                    </div>

                    <div class="review-comment">
                        {{ $comentario }}
                    </div>

                    @if($respuesta)
                        <div class="review-response">
                            <div class="review-response-title">
                                RESPUESTA DEL GERENTE
                            </div>
                            <div class="review-response-text">
                                {{ $respuesta }}
                            </div>
                        </div>
                    @else
                        <form action="{{ route('reviews.respond', $item->local_review_id) }}" method="POST" class="review-form">
                            @csrf
                            <textarea name="response" placeholder="Responder..."></textarea>
                            <button type="submit" class="review-btn">Responder</button>
                        </form>
                    @endif

                </div>

            @endforeach
        </div>

    @else
        <p>No hay reseñas</p>
    @endif

</div>
@endsection