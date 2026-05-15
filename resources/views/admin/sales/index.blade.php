@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0"><i class="fa-solid fa-file-invoice"></i> سجل المبيعات الشامل</h2>
    </div>
    <div class="col-md-6">
        <form action="{{ route('admin.sales.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="بحث برقم الفاتورة، الفرع، الموظف أو نوع السيارة..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>الفرع / الموظف</th>
                    <th>السيارة والزجاج</th>
                    <th>سعر البيع</th>
                    <th>الخصم/الزيادة</th>
                    <th>الحالة</th>
                    <th>إجراء الإدارة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr class="{{ $sale->status == 'refunded' ? 'table-danger text-muted' : '' }}">
                        <td>#{{ $sale->id }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $sale->branch->name }}</span><br>
                            @if($sale->employee)
                                <small>الموظف: {{ $sale->employee->name }}</small>
                            @elseif($sale->admin)
                                <small>الإدارة: {{ $sale->admin->name }}</small>
                            @else
                                <small class="text-muted">غير محدد</small>
                            @endif
                        </td>
                        <td>{{ $sale->item->carModel->name ?? 'غير معروف' }} - {{ $sale->item->glassPosition->name ?? 'غير معروف' }}</td>
                        <td class="fw-bold">{{ number_format($sale->sold_price, 2) }}</td>
                        <td>
                            @if($sale->discount > 0) <span class="text-danger">خصم: {{ $sale->discount }}</span>
                            @elseif($sale->overprice > 0) <span class="text-success">زيادة: {{ $sale->overprice }}</span>
                            @else - @endif
                        </td>
                        <td>
                            @if($sale->status == 'completed')
                                <span class="badge bg-success">مكتمل</span>
                            @else
                                <span class="badge bg-danger">تم التراجع (مسترجع)</span>
                            @endif
                        </td>
                        <td>
                            @if($sale->status == 'completed')
                                <form action="{{ route('admin.sales.undo', $sale->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من التراجع عن هذا البيع؟ سيتم إرجاع الصنف للمخزن.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-rotate-left"></i> تراجع عن البيع
                                    </button>
                                </form>
                            @else
                                <span class="text-muted"><i class="fa-solid fa-ban"></i> ملغي</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection