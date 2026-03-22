{{-- Breadcrumb para módulo Local --}}
<nav class="local-breadcrumb" aria-label="Breadcrumb">
    <ol>
        <li>
            <a href="{{ route('local.index') }}" class="breadcrumb-home">
                <i class="fas fa-store"></i> Mi Local
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
