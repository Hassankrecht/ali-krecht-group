<div class="notification-bell">
    <a href="{{ route('notifications.index') }}" class="nav-link position-relative">
        <i class="fas fa-bell"></i>
        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $unreadCount }}</span>
        @endif
    </a>
</div>