@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-boxes-stacked"></i> مخزون الفرع</h2>
    <a href="{{ route('employee.items.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> إدخال صنف جديد
    </a>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>رقم الطارمة (الرف)</th>
                    <th>نوع السيارة</th>
                    <th>موقع الزجاج</th>
                    <th>النوع / الجودة</th>
                    <th>سعر البيع</th>
                    <th>الكمية المتوفرة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><span class="badge bg-secondary fs-6">{{ $item->shelf_number ?? 'بدون رقم' }}</span></td>
                        <td>{{ $item->carModel->name }}</td>
                        <td>{{ $item->glassPosition->name }}</td>
                        <td>{{ $item->glass_type ?? 'عادي' }}</td>
                        <td class="text-success fw-bold">{{ number_format($item->retail_price, 2) }} د.ل</td>
                        <td>
                            @if($item->stock_quantity > 0)
                                <span class="badge bg-success fs-6">{{ $item->stock_quantity }}</span>
                            @else
                                <span class="badge bg-danger fs-6">نفذت الكمية</span>
                            @endif
                        </td>
                        <td>
                            <!-- زر تسجيل التالف -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#damagedModal{{ $item->id }}">
                                <i class="fa-solid fa-crack"></i> تسجيل تالف
                            </button>

                            <!-- نافذة تسجيل التالف (Modal) -->
                            <div class="modal fade" id="damagedModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('employee.items.damaged', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title text-dark">تسجيل زجاج تالف / مكسور</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>الصنف:</strong> {{ $item->carModel->name }} - {{ $item->glassPosition->name }}</p>
                                                <p><strong>المتوفر حالياً:</strong> {{ $item->stock_quantity }} قطع</p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label text-danger fw-bold">الكمية التالفة *</label>
                                                    <input type="number" name="quantity" class="form-control" min="1" max="{{ $item->stock_quantity }}" required>
                                                    <small class="text-muted">سيتم خصم هذه الكمية من المخزون وتسجيلها كتالف.</small>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">ملاحظة أو سبب التلف (اختياري)</label>
                                                    <textarea name="note" class="form-control" rows="2" placeholder="مثال: كسر أثناء النقل..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-danger">تأكيد الخصم <i class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">لا توجد أصناف مسجلة في هذا الفرع حتى الآن.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection