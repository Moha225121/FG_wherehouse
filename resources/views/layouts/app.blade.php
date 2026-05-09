<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة مستودع زجاج السيارات</title>
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Google Fonts: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Mobile App Meta -->
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: #f8f9fa; 
            padding-bottom: 70px; /* Space for bottom nav on mobile */
        }
        @media (min-width: 992px) {
            body { padding-bottom: 0; }
        }
        
        .navbar { background-color: #0d6efd; border-bottom: 3px solid #0a58ca; }
        .navbar-brand, .nav-link { color: white !important; }
        .card { border-radius: 12px; border: none; }
        .card-header { font-weight: bold; background-color: #e9ecef; border-bottom: 1px solid #dee2e6; }
        
        /* Bottom Navigation for Mobile */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1030;
            border-top: 1px solid #eee;
        }
        .bottom-nav-item {
            text-align: center;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.75rem;
            flex: 1;
        }
        .bottom-nav-item i {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 2px;
        }
        .bottom-nav-item.active {
            color: #0d6efd;
        }
        
        @media (min-width: 992px) {
            .bottom-nav { display: none; }
        }

        /* Tom Select RTL Fix */
        .ts-wrapper.rtl .ts-control { text-align: right; }
        .ts-dropdown { text-align: right; }
        
        /* General Mobile Optimizations */
        @media (max-width: 576px) {
            .container { padding-left: 10px; padding-right: 10px; }
            .btn { padding: 10px 15px; } /* Larger touch targets */
            .card-body { padding: 15px; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-car-side"></i> مستودع الزجاج
            </a>
            <div class="ms-3 text-white small d-none d-lg-block">
                <!-- Display server date & time -->
                <i class="fa-regular fa-clock"></i>
                {{ now()->format('Y-m-d H:i:s') }}
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(Auth::guard('employee')->check())
                        <li class="nav-item"><a class="nav-link" href="{{ route('employee.dashboard') }}">الرئيسية</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('employee.pos') }}">نقطة البيع</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('employee.items.index') }}">المخزون</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('employee.external-sales.index') }}">مبيعات خارجية</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('employee.reports.index') }}">التقارير</a></li>
                    @elseif(Auth::guard('admin')->check())
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.pos') }}">نقطة البيع (كل الفروع)</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.sales.index') }}">المبيعات والتراجع</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.items.index') }}">المخزون المركزي</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.items.create') }}">إضافة مخزون</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports.index') }}">التقارير</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.expenses.index') }}">السحوبات والمصروفات</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.branches.index') }}">الفروع</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.employees.index') }}">الموظفين</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.car-models.index') }}">أنواع السيارات</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.glass-positions.index') }}">مواقع الزجاج</a></li>
@endif
                </ul>
                <ul class="navbar-nav">
                    @if(Auth::guard('admin')->check() || Auth::guard('employee')->check())
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm mt-1">تسجيل الخروج <i class="fa-solid fa-sign-out-alt"></i></button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- رسائل النجاح والخطأ -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    @if(Auth::guard('employee')->check() || Auth::guard('admin')->check())
        <div class="bottom-nav">
            @php
                $prefix = Auth::guard('admin')->check() ? 'admin' : 'employee';
            @endphp
            <a href="{{ route($prefix . '.dashboard') }}" class="bottom-nav-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                الرئيسية
            </a>
            <a href="{{ route($prefix . '.pos') }}" class="bottom-nav-item {{ request()->routeIs('*.pos') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i>
                نقطة البيع
            </a>
            <a href="{{ route($prefix . '.items.index') }}" class="bottom-nav-item {{ request()->routeIs('*.items.index') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i>
                المخزون
            </a>
            <a href="{{ route($prefix . '.reports.index') }}" class="bottom-nav-item {{ request()->routeIs('*.reports.index') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                التقارير
            </a>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.searchable-select').forEach(function(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });
        });
    </script>
</body>

</html>