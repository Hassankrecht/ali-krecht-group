<div class="order-status">
    <span class="badge @if($status == 'pending') bg-secondary @elseif($status == 'processing') bg-info @elseif($status == 'shipped') bg-success @elseif($status == 'cancelled') bg-danger @else bg-light @endif">{{ ucfirst($status) }}</span>
</div>