@extends('layouts.app')

@section('content')
<h2 class="mb-4">مرحباً بك، {{ Auth::guard('employee')->user()->name }}</h2>

<div class="row mb-4">
    <!-- إجمالي المبيعات اليومية -->
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 shadow">
            <div class="card-header">إجمالي إيرادات اليوم</div>
            <div class="card-body">
                <h3 class="card-title">{{ number_format($totalDailyRevenue, 2) }} دينار</h3>
                <p class="card-text">شاملة المبيعات العادية والخارجية</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 shadow">
            <div class="card-header">مبيعات الزجاج (اليوم)</div>
            <div class="card-body">
                <h3 class="card-title">{{ number_format($todaySales, 2) }} دينار</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-dark bg-warning mb-3 shadow">
            <div class="card-header">المبيعات الخارجية (اليوم)</div>
            <div class="card-body">
                <h3 class="card-title">{{ number_format($todayExternalSales, 2) }} دينار</h3>
            </div>
        </div>
    </div>
</div>

<!-- تنبيهات النواقص -->
<div class="card shadow">
    <div class="card-header bg-danger text-white">
        <i class="fa-solid fa-triangle-exclamation"></i> تنبيهات النواقص في المخزن (أقل من 3 قطع)
    </div>
    <div class="card-body table-responsive">
        @if($lowStockItems->count() > 0)
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>رقم الرف</th>
                        <th>نوع السيارة</th>
                        <th>موقع الزجاج</th>
                        <th>النوع</th>
                        <th>الكمية المتبقية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockItems as $item)
                        <tr>
                            <td>{{ $item->shelf_number ?? 'غير محدد' }}</td>
                            <td>{{ $item->carModel->name ?? 'غير محدد' }}</td>
                            <td>{{ $item->glassPosition->name ?? 'غير محدد' }}</td>
                            <td>{{ $item->glass_type ?? '-' }}</td>
                            <td class="text-danger fw-bold">{{ $item->stock_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-success fw-bold text-center mb-0">لا توجد نواقص في المخزن حالياً.</p>
        @endif
    </div>
</div>
@endsection