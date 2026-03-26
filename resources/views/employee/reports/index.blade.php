@extends('layouts.app')

@section('content')
@php
    $isAdmin = Auth::guard('admin')->check();
    $indexRoute = $isAdmin ? 'admin.reports.index' : 'employee.reports.index';
    $pdfRoute = $isAdmin ? 'admin.reports.pdf' : 'employee.reports.pdf';
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-chart-line"></i> {{ $reportTitle }}</h2>

    <div class="d-flex gap-2">
        <!-- فلاتر التقرير -->
        <form action="{{ route($indexRoute) }}" method="GET" class="d-flex">
            <select name="period" class="form-select me-2" onchange="this.form.submit()">
                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>يومي</option>
                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>شهري</option>
            </select>
        </form>

        <a href="{{ route($pdfRoute, ['period' => $period]) }}" class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i> استخراج PDF
        </a>
    </div>
</div>

<!-- ملخص الأرقام -->
<div class="row mb-4 text-center">
    <div class="col-md-3">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <h5>إجمالي المبيعات</h5>
                <h3 class="fw-bold">{{ number_format($grandTotal, 2) }} د.ل</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <h5>مبيعات الزجاج فقط</h5>
                <h3 class="fw-bold">{{ number_format($totalNormalSales, 2) }} د.ل</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white shadow">
            <div class="card-body">
                <h5>إجمالي الخصومات</h5>
                <h3 class="fw-bold">{{ number_format($totalDiscounts, 2) }} د.ل</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                <h5>الزيادة (أكثر من السعر)</h5>
                <h3 class="fw-bold">{{ number_format($totalOverprice, 2) }} د.ل</h3>
            </div>
        </div>
    </div>
</div>

<!-- جدول التفاصيل -->
<div class="card shadow mb-4">
    <div class="card-header bg-dark text-white fw-bold">تفاصيل مبيعات الزجاج</div>
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>الوقت</th>
                    <th>السيارة</th>
                    <th>القطعة</th>
                    <th>السعر الافتراضي</th>
                    <th>سعر البيع الفعلي</th>
                    <th>الفرق (خصم / زيادة)</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td dir="ltr" class="text-end">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $sale->item->carModel->name }}</td>
                        <td>{{ $sale->item->glassPosition->name }}</td>
                        <td>{{ number_format($sale->system_price, 2) }}</td>
                        <td class="fw-bold">{{ number_format($sale->sold_price, 2) }}</td>
                        <td>
                            @if($sale->discount > 0)
                                <span class="text-danger">خصم: {{ number_format($sale->discount, 2) }}</span>
                            @elseif($sale->overprice > 0)
                                <span class="text-success">زيادة: {{ number_format($sale->overprice, 2) }}</span>
                            @else
                                <span class="text-muted">السعر مطابق</span>
                            @endif
                        </td>
                        <td>{{ $sale->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">لا توجد مبيعات في هذه الفترة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection