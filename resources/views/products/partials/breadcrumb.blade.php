{{-- Breadcrumb para módulo de Productos --}}
<nav class="product-breadcrumb" aria-label="Breadcrumb">
    <ol>
        <li>
            <a href="{{ route('products.index') }}">
                <i class="fas fa-boxes"></i> Productos
            </a>
        </li>
        @foreach($crumbs as $crumb)
            <li>
                <i class="fas fa-chevron-right breadcrumb-separator"></i>
                @if(isset($crumb['url']))
                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                @else
                    <span class="current">{{ $crumb['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
