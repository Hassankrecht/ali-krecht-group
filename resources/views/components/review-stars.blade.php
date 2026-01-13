<div class="review-stars">
    @php $full = floor($rating ?? 0); $half = ($rating - $full) >= 0.5; @endphp
    @for($i=1;$i<=5;$i++)
        @if($i <= $full)
            <i class="fas fa-star text-warning"></i>
        @elseif($i === $full + 1 && $half)
            <i class="fas fa-star-half-alt text-warning"></i>
        @else
            <i class="far fa-star text-muted"></i>
        @endif
    @endfor
    <span class="sr-only">{{ $rating ?? 0 }}/5</span>
</div>