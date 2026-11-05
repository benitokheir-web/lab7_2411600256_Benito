<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Hardware Inventory' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>\n<div class="container my-2 d-flex gap-3 flex-wrap"><a href="{{ route('dashboard') }}">Dashboard</a><a href="{{ route('products.index') }}">Products</a><a href="{{ route('transactions.index') }}">Transactions</a><a href="{{ route('suppliers.index') }}">Suppliers</a><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a><a href="{{ route('reports.index') }}">Reports</a></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white" href="{{ route('dashboard') }}">
            <i class="bi bi-box-seam-fill me-2"></i>Hardware Inventory
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Reports</a></li>
            </ul>
            <div class="dropdown me-3">
                <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i> Alerts
                    @if(auth()->user()->unreadNotifications()->count())
                        <span class="badge bg-danger">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width:300px;max-width:360px">
                    <strong class="d-block px-2 py-1">Low-stock notifications</strong>
                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                        <div class="border-top p-2 small">
                            {{ $notification->data['message'] ?? 'Inventory alert' }}
                            @if(isset($notification->data['product_id']))
                                <a class="d-block" href="{{ route('products.show', $notification->data['product_id']) }}">View product</a>
                            @endif
                            @if(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf
                                    <button class="btn btn-sm btn-link p-0">Mark as read</button>
                                </form>
                            @endif
                        </div>
                    @empty <div class="small text-muted p-2">No notifications yet.</div>
                    @endforelse
                </div>
            </div>
            <span class="text-white me-3"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>

<main class="container-fluid py-4 px-lg-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the form.</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
