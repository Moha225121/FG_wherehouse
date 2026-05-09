@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-cart-shopping"></i> نقطة البيع</h2>
</div>

<!-- نموذج البحث -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('employee.pos') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="ابحث برقم الرف (abc-123) أو نوع الزجاج..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search"></i> بحث</button>
            </div>
        </form>
    </div>
</div>

<!-- جدول الأصناف المتاحة للبيع -->
<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>رقم الرف</th>
                    <th>نوع السيارة</th>
                    <th>موقع الزجاج</th>
                    <th>النوع</th>
                    <th>سعر النظام</th>
                    <th>المتوفر</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->shelf_number }}</span></td>
                        <td>{{ $item->carModel->name ?? 'غير محدد' }}</td>
                        <td>{{ $item->glassPosition->name ?? 'غير محدد' }}</td>
                        <td>{{ $item->glass_type }}</td>
                        <td class="text-primary fw-bold">{{ number_format($item->retail_price, 2) }} د.ل</td>
                        <td>{{ $item->stock_quantity }}</td>
                        <td>
                            <!-- زر فتح نافذة تأكيد البيع -->
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#sellModal{{ $item->id }}">
                                <i class="fa-solid fa-check"></i> بيع
                            </button>

                            <!-- نافذة البيع (Modal) -->
                            <div class="modal fade" id="sellModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('employee.sales.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تأكيد عملية البيع</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="itemID" value="{{ $item->id }}">
                                                
                                                <p><strong>السيارة:</strong> {{ $item->carModel->name ?? 'غير محدد' }} - {{ $item->glassPosition->name ?? 'غير محدد' }}</p>
                                                <p><strong>سعر النظام:</strong> {{ number_format($item->retail_price, 2) }} دينار</p>

                                                <div class="alert alert-info">
                                                    <i class="fa-solid fa-lock"></i>
                                                    سعر البيع ثابت حسب إعداد الإدارة ولا يمكن تعديله من واجهة الموظف.
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">ملاحظة للبيع النهائي (اختياري)</label>
                                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-success">تأكيد البيع <i class="fa-solid fa-cart-arrow-down"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">لا توجد أصناف متوفرة مطابقة للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection