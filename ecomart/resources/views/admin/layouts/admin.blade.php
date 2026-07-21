<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','EcoMart Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{margin:0;background:#f4f6f8;font-family:Arial,sans-serif}
        .admin-wrapper{display:flex;min-height:100vh}
        .sidebar{width:280px;background:#154b35;color:#fff;padding:20px 15px}
        .logo-box{display:flex;align-items:center;gap:10px;margin-bottom:35px}
        .logo-box img{height:45px;background:#fff;padding:5px;border-radius:5px}
        .admin-badge{background:#ffc107;color:#000;padding:6px 14px;border-radius:8px;font-weight:700}
        .sidebar a{display:block;color:#d8f5e5;text-decoration:none;padding:14px 18px;border-radius:8px;margin-bottom:10px;font-weight:600}
        .sidebar a:hover,.sidebar a.active{background:#35b779;color:#fff}
        .main{flex:1}
        .topbar{background:#154b35;color:#fff;padding:20px 30px;display:flex;justify-content:flex-end;align-items:center;gap:15px}
        .content{padding:35px}
        .stat-card,.panel,.action-card{background:#fff;border-radius:12px;box-shadow:0 5px 18px rgba(0,0,0,.08)}
        .stat-card{padding:25px;border-left:5px solid #28c76f}
        .stat-card h2{color:#28c76f;font-weight:800}
        .action-card{text-align:center;padding:35px;text-decoration:none;color:#111;display:block}
        .action-card i{font-size:42px;color:#168a55}
        .panel-header{padding:16px 20px;border-bottom:1px solid #ddd;color:#28c76f;font-size:22px;font-weight:700}
        .btn-green{background:#28c76f;color:white;border:none}
        .btn-green:hover{background:#1fa75b;color:white}
        .table th{background:#e8f8ef}
    </style>
</head>

<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('assets/images/logo.jpg') }}">
            <span class="admin-badge">ADMIN</span>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products') ? 'active' : '' }}">
            <i class="bi bi-box"></i> Products
        </a>

        <a href="{{ route('admin.products.create') }}" class="{{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>

        <a href="{{ route('admin.categories') }}" class="{{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
        </a>

        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>

        <a href="{{ route('admin.messages') }}" class="{{ request()->routeIs('admin.messages') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> Messages
        </a>

        <hr>

        <a href="{{ url('/') }}">
            <i class="bi bi-shop"></i> View Storefront
        </a>
    </aside>

    <main class="main">

        <div class="topbar">
            <strong><i class="bi bi-person-square"></i> System Administrator</strong>
            <span class="badge bg-secondary">admin</span>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

        <div class="content">
            @yield('content')
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>