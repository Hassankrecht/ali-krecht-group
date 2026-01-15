{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Ali Krecht Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #0f0f0f; color: #fff; }
        .sidebar {
            width: 240px;
            background: #1a1a1a;
            position: fixed;
            top: 0; bottom: 0;
            padding: 20px 0;
            border-right: 1px solid #292929;
        }
        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #ddd;
            text-decoration: none;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #2b1e0f;
            color: #fff;
        }
        .content {
            margin-left: 240px;
            padding: 25px 25px 60px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .card-dark {
            background: #171717;
            border: 1px solid #2b1e0f;
            border-radius: 14px;
        }
    </style>
    @stack('head')
</head>
<body>

<div class="sidebar">
    <div class="px-4 mb-4">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 3, 2025, 08_00_27 AM.png') }}" style="height:46px" alt="">
        <div class="small text-muted mt-2">Ali Krecht Group</div>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
    <a href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam me-2"></i>Products</a>
    <a href="{{ route('admin.projects.index') }}"><i class="bi bi-building me-2"></i>Projects</a>
    <a href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt me-2"></i>Orders</a>
    <a href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated me-2"></i>Coupons</a>
    <a href="{{ route('admin.income.index') }}"><i class="bi bi-cash-coin me-2"></i>Income</a>
    <a href="{{ route('admin.reports.orders') }}"><i class="bi bi-graph-up-arrow me-2"></i>Order Reports</a>
    <a href="{{ route('admin.reviews.index') }}"><i class="bi bi-chat-heart me-2"></i>Testimonials</a>
    <a href="{{ route('admin.home-settings') }}"><i class="bi bi-gear me-2"></i>Home Settings</a>
    <a href="{{ route('admin.admin-users.index') }}"><i class="bi bi-person-badge me-2"></i>Admin Users</a>
    <form action="{{ route('admin.logout') }}" method="POST" class="mt-4 px-3">
        @csrf
        <button class="btn btn-sm btn-outline-light w-100">Logout</button>
    </form>
</div>

<div class="content">
    <div class="topbar">
        <h4 class="mb-0">Admin Dashboard</h4>
        <div class="small text-muted">Logged in as {{ auth('admin')->user()->name ?? 'Admin' }}</div>
    </div>

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
