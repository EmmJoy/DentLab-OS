<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmileCare Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #2c3e50;
        }
        .brand-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        .brand-logo {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .user-info {
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.1);
            margin: 10px;
            border-radius: 10px;
        }
        .alert {
            border: none;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fas fa-tooth"></i>
            </div>
            <h4 class="mb-0">SmileCare</h4>
            <small class="opacity-75">Dental Management</small>
        </div>

        <div class="user-info">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fa-2x me-3"></i>
                <div>
                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                    <small class="opacity-75">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
                </div>
            </div>
        </div>

        <ul class="nav flex-column">
            @if(Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.patients*') ? 'active' : '' }}" href="{{ route('admin.patients.index') }}">
                        <i class="fas fa-users me-2"></i> Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                        <i class="fas fa-boxes me-2"></i> Inventory
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
                        <i class="fas fa-credit-card me-2"></i> Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.lab-technicians*') ? 'active' : '' }}" href="{{ route('admin.lab-technicians.index') }}">
                        <i class="fas fa-user-md me-2"></i> Lab Technicians
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.production*') ? 'active' : '' }}" href="{{ route('admin.production.index') }}">
                        <i class="fas fa-industry me-2"></i> Production Field
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}" href="{{ route('lab.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lab.patients*') ? 'active' : '' }}" href="{{ route('lab.patients.index') }}">
                        <i class="fas fa-users me-2"></i> View Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lab.inventory*') ? 'active' : '' }}" href="{{ route('lab.inventory.index') }}">
                        <i class="fas fa-boxes me-2"></i> Check Inventory
                    </a>
                </li>
                @php
                    $labProductsMenuOpen = request()->routeIs('lab.products*') || request()->routeIs('lab.cart.index') || request()->routeIs('lab.cart.confirmed');
                @endphp
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $labProductsMenuOpen ? 'active' : '' }}" data-bs-toggle="collapse" href="#labProductsMenu" role="button" aria-expanded="{{ $labProductsMenuOpen ? 'true' : 'false' }}" aria-controls="labProductsMenu">
                        <span><i class="fas fa-shopping-bag me-2"></i> Products</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse {{ $labProductsMenuOpen ? 'show' : '' }}" id="labProductsMenu">
                        <ul class="nav flex-column ms-3 my-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lab.products*') ? 'active' : '' }}" href="{{ route('lab.products.index') }}">
                                    <i class="fas fa-boxes me-2"></i> Browse Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('lab.cart.index') ? 'active' : '' }}" href="{{ route('lab.cart.index') }}">
                                    <i class="fas fa-shopping-cart me-2"></i> My Cart
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('lab.production*') ? 'active' : '' }}" href="{{ route('lab.production.index') }}">
                        <i class="fas fa-industry me-2"></i> Production Field
                    </a>
                </li>
            @endif
            
            <li class="nav-item mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
