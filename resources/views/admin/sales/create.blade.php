@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-cart-shopping"></i> نقطة بيع الإدارة (كل الفروع)</h2>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pos') }}" class="row g-3">
            <div class="col-md-4">
                <select name="branchID" class="form-select">
                    <option value="">كل الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branchID') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="بحث برقم الرف أو نوع الزجاج" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-search"></i> بحث</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>الفرع</th>
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
                        <td><span class="badge bg-secondary">{{ $item->branch->name }}</span></td>
                        <td><span class="badge bg-dark">{{ $item->shelf_number ?? '-' }}</span></td>
                        <td>{{ $item->carModel->name }}</td>
                        <td>{{ $item->glassPosition->name }}</td>
                        <td>{{ $item->glass_type ?? '-' }}</td>
                        <td class="text-primary fw-bold">{{ number_format($item->retail_price, 2) }} د.ل</td>
                        <td>{{ $item->stock_quantity }}</td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#adminSellModal{{ $item->id }}">
                                <i class="fa-solid fa-check"></i> بيع
                            </button>

                            <div class="modal fade" id="adminSellModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.sales.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تأكيد البيع بواسطة الإدارة</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="itemID" value="{{ $item->id }}">

                                                <p><strong>الفرع:</strong> {{ $item->branch->name }}</p>
                                                <p><strong>الصنف:</strong> {{ $item->carModel->name }} - {{ $item->glassPosition->name }}</p>
                                                <p><strong>سعر النظام:</strong> {{ number_format($item->retail_price, 2) }} دينار</p>

                                                <div class="mb-3">
                                                    <label class="form-label">سعر البيع الفعلي (اختياري)</label>
                                                    <input type="number" step="0.01" name="sold_price" class="form-control" value="{{ $item->retail_price }}">
                                                    <small class="text-muted">إذا تُرك فارغًا، سيتم البيع بسعر النظام.</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">ملاحظة</label>
                                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-success">تأكيد البيع</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">لا توجد أصناف متوفرة مطابقة للبحث.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
