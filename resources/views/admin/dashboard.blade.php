@extends('layouts.app')

@section('content')
<h2 class="mb-4 text-primary"><i class="fa-solid fa-chess-king"></i> لوحة تحكم الإدارة (المركزية)</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 shadow border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-coins"></i> إجمالي إيرادات اليوم (كل الفروع)</h5>
                <h2 class="fw-bold">{{ number_format($totalDailyRevenue, 2) }} دينار</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 shadow border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-code-branch"></i> الفروع النشطة</h5>
                <h2 class="fw-bold">{{ $branchesCount }} فروع</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-dark bg-warning mb-3 shadow border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-truck-fast"></i> إجمالي المبيعات الخارجية اليوم</h5>
                <h2 class="fw-bold">{{ number_format($todayExternalSales, 2) }} دينار</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-dark w-100 py-3 mb-3 fs-5 fw-bold shadow-sm">
            <i class="fa-solid fa-clock-rotate-left"></i> إدارة المبيعات والتراجع عن البيع
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('admin.items.index') }}" class="btn btn-outline-primary w-100 py-3 mb-3 fs-5 fw-bold shadow-sm">
            <i class="fa-solid fa-boxes-stacked"></i> مراقبة المخزون المركزي وسعر الجملة
        </a>
    </div>
</div>
@endsection