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

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-primary shadow">
            <div class="card-header bg-primary text-white">بيانات المرتب والسحب</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>المرتب الكلي:</span>
                    <span class="fw-bold">{{ number_format(Auth::guard('employee')->user()->salary, 2) }} د.ل</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>الرصيد المتبقي:</span>
                    <span class="fw-bold text-success">{{ number_format(Auth::guard('employee')->user()->remaining_salary, 2) }} د.ل</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>حد السحب اليومي:</span>
                    <span class="fw-bold text-danger">{{ number_format(Auth::guard('employee')->user()->daily_withdrawal_limit, 2) }} د.ل</span>
                </div>
                <button class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fa-solid fa-money-bill-transfer"></i> سحب سلفة من المرتب
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal السحب -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('employee.withdrawals.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">طلب سحب سلفة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ المطلوب (د.ل)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="1">
                        <small class="text-muted">سيتم خصم هذا المبلغ من رصيد مرتبك فوراً.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظة</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد السحب</button>
                </div>
            </div>
        </form>
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