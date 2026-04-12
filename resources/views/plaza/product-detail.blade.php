<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - {{ $local->name }} - La Comarca Gastro Park</title>

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/comarca-favicon.ico') }}?v={{ time() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/plaza/product-detail-page.css') }}">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
<div id="product-detail-app" v-cloak>
    <!-- Header -->
    <header class="detail-header">
        <button class="btn-back" @click="goBack" title="Volver">
            <i class="fas fa-chevron-left"></i>
        </button>
        <span class="header-title">{{ $product->name }}</span>
    </header>

    <!-- Main Content -->
    <div class="product-detail-page">
        <div class="container">
            <!-- Gallery Section -->
            <section class="gallery-section">
                <div class="gallery-main">
                    <img :src="currentImage" :alt="product.name" loading="lazy">
                    <div class="gallery-counter" v-if="gallery.length > 1">
                        @{{ currentImageIndex + 1 }} / @{{ gallery.length }}
                    </div>
                    <button 
                        v-if="currentImageIndex > 0"
                        @click="currentImageIndex--" 
                        class="gallery-nav-btn gallery-nav-prev"
                        title="Foto anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button 
                        v-if="currentImageIndex < gallery.length - 1"
                        @click="currentImageIndex++" 
                        class="gallery-nav-btn gallery-nav-next"
                        title="Siguiente foto">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Thumbnails -->
                <div class="gallery-thumbnails" v-if="gallery.length > 1" ref="thumbnailsContainer">
                    <button 
                        v-for="(image, idx) in gallery" 
                        :key="idx"
                        @click="scrollToThumbnail(idx)"
                        :class="['thumb', { active: currentImageIndex === idx }]"
                        :title="`Foto ${idx + 1}`">
                        <img :src="image.image_url" :alt="`{{ $product->name }} - ${idx + 1}`" loading="lazy">
                    </button>
                </div>
            </section>

            <!-- Product Info -->
            <section class="info-section">
                <!-- Meta Badges -->
                <div class="product-meta">
                    <span class="meta-badge">
                        <i class="fas fa-store"></i> {{ $local->name }}
                    </span>
                    <span class="meta-badge" v-if="product.category">
                        <i class="fas fa-tag"></i> @{{ product.category }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="product-title">@{{ product.name }}</h1>

                <!-- Rating -->
                <div class="rating-section" v-if="product.average_rating > 0">
                    <div class="stars">
                        <i 
                            v-for="star in 5" 
                            :key="star"
                            class="fas fa-star" 
                            :class="{ active: star <= Math.round(product.average_rating) }">
                        </i>
                    </div>
                    <span class="rating-text">@{{ product.average_rating }}/5</span>
                </div>

                <!-- Price -->
                <div class="price-section">
                    <span class="price-label">Precio</span>
                    <div class="price">
                        <sup>₡</sup>@{{ formatPrice(product.price) }}
                    </div>
                </div>
            </section>

            <!-- Description -->
            <section class="description-section" v-if="product.description">
                <h2 class="section-title">Descripción</h2>
                <p class="description-text">@{{ product.description }}</p>
            </section>

            <!-- Local Info -->
            <section class="local-section">
                <div class="local-logo" v-if="local.image_logo">
                    <img :src="assetUrl(local.image_logo)" :alt="local.name" loading="lazy">
                </div>
                <div class="local-info">
                    <h3>@{{ local.name }}</h3>
                    <p v-if="local.description">@{{ local.description }}</p>
                    <p v-if="local.contact">
                        <i class="fas fa-phone"></i> @{{ local.contact }}
                    </p>
                </div>
            </section>

            <!-- Reviews Section -->
            <section class="reviews-section" v-if="reviews.length > 0">
                <h2 class="section-title">Reseñas (@{{ reviews.length }})</h2>
                <div v-for="review in reviews" :key="review.product_review_id" class="review-item">
                    <div class="review-header">
                        <span class="review-author">@{{ review.reviewer_name || 'Usuario' }}</span>
                        <span class="review-date">@{{ formatDate(review.created_at) }}</span>
                    </div>
                    <div class="review-rating">
                        <i 
                            v-for="star in 5" 
                            :key="star"
                            class="fas fa-star" 
                            :style="{ color: star <= review.rating ? 'var(--primary)' : 'rgba(212, 119, 58, 0.3)' }">
                        </i>
                    </div>
                    <p class="review-text">@{{ review.comment }}</p>
                </div>
            </section>

            <div v-else class="reviews-section reviews-empty">
                <i class="fas fa-comments"></i>
                <p>Sin reseñas aún. ¡Sé el primero en reseñar este producto!</p>
            </div>
        </div>
    </div>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                product: {!! json_encode([
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'category' => $product->category,
                    'photo_url' => $product->photo_url,
                    'price' => $product->price,
                    'average_rating' => $product->average_rating,
                ]) !!},
                local: {!! json_encode([
                    'local_id' => $local->local_id,
                    'name' => $local->name,
                    'description' => $local->description,
                    'contact' => $local->contact,
                    'image_logo' => $local->image_logo,
                ]) !!},
                gallery: {!! json_encode($gallery->map(fn($g) => ['image_url' => $g->image_url])->toArray()) !!},
                reviews: {!! json_encode($reviews->map(fn($r) => [
                    'product_review_id' => $r->product_review_id,
                    'reviewer_name' => $r->reviewer_name,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->toIso8601String(),
                ])->toArray()) !!},
                currentImageIndex: 0,
            }
        },
        computed: {
            currentImage() {
                if (this.gallery.length === 0) {
                    return this.product.photo_url || '{{ asset("images/product-placeholder.png") }}';
                }
                return this.gallery[this.currentImageIndex]?.image_url;
            }
        },
        methods: {
            assetUrl(path) {
                if (!path) return '{{ asset("images/product-placeholder.png") }}';
                return path.startsWith('http') ? path : '{{ asset("") }}' + path;
            },
            formatPrice(price) {
                return (price || 0).toLocaleString('es-CR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },
            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-CR', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },
            goBack() {
                window.history.back();
            },
            scrollToThumbnail(index) {
                this.currentImageIndex = index;
                this.$nextTick(() => {
                    const container = this.$refs.thumbnailsContainer;
                    const thumbs = container.querySelectorAll('.thumb');
                    const activeThumb = thumbs[index];
                    if (activeThumb) {
                        activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }
                });
            }
        },
        mounted() {
            console.log('Producto cargado:', this.product.name);
            // Agregar navegación por teclado
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft' && this.currentImageIndex > 0) {
                    this.currentImageIndex--;
                } else if (e.key === 'ArrowRight' && this.currentImageIndex < this.gallery.length - 1) {
                    this.currentImageIndex++;
                }
            });
        }
    }).mount('#product-detail-app');
</script>
</body>
</html>
