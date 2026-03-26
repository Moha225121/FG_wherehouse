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
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8f9fa; }
        .navbar { background-color: #0d6efd; }
        .navbar-brand, .nav-link { color: white !important; }
        .card-header { font-weight: bold; background-color: #e9ecef; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-car-side"></i> مستودع الزجاج
            </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>