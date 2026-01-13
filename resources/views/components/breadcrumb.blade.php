<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        @foreach($crumbs as $crumb)
            @if(!$loop->last)
                <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>