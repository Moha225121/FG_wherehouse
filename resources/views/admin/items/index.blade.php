@extends('layouts.app')

@section('content')
<h2 class="mb-4"><i class="fa-solid fa-warehouse"></i> المخزون المركزي وإدارة الأسعار</h2>

<div class="mb-3">
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> إضافة مخزون جديد
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-light fw-bold"><i class="fa-solid fa-filter"></i> بحث متقدم في المخزون المركزي</div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.items.index') }}" class="row g-3">
            <div class="col-lg-4 col-md-6">
                <label class="form-label">بحث شامل</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="السيارة، الرف، الفرع، موقع الزجاج أو النوع">
            </div>
            <div class="col-lg-2 col-md-3"><label class="form-label">الفرع</label><select name="branchID" class="form-select"><option value="">كل الفروع</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" {{ request('branchID') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-3"><label class="form-label">السيارة</label><select name="carModelID" class="form-select"><option value="">كل السيارات</option>@foreach($carModels as $model)<option value="{{ $model->id }}" {{ request('carModelID') == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-3"><label class="form-label">موقع الزجاج</label><select name="glassPositionID" class="form-select"><option value="">كل المواقع</option>@foreach($glassPositions as $position)<option value="{{ $position->id }}" {{ request('glassPositionID') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-3"><label class="form-label">حالة المخزون</label><select name="stock_status" class="form-select"><option value="">كل الحالات</option><option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>متوفر</option><option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>منخفض (1-2)</option><option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>نافد</option></select></div>
            <div class="col-12 d-flex gap-2"><button class="btn btn-primary"><i class="fa-solid fa-search"></i> تطبيق البحث</button><a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">مسح الفلاتر</a></div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>الفرع</th>
                    <th>السيارة</th>
                    <th>الموقع / النوع</th>
                    <th>الرف</th>
                    <th>المتوفر</th>
                    <th>التالف</th>
                    <th>سعر البيع (للموظف)</th>
                    <th>سعر الجملة (سري للإدارة)</th>
                    <th>إدارة المخزون</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $item->branch->name }}</span></td>
                        <td>{{ $item->carModel->name ?? 'غير محدد' }}</td>
                        <td>{{ $item->glassPosition->name ?? 'غير محدد' }} <br><small class="text-muted">{{ $item->glass_type }}</small></td>
                        <td>{{ $item->shelf_number }}</td>
                        <td class="fw-bold fs-5 {{ $item->stock_quantity == 0 ? 'text-danger' : 'text-success' }}">{{ $item->stock_quantity }}</td>
                        <td class="fw-bold fs-5 text-warning">{{ $item->damaged_quantity ?? 0 }}</td>
                        <td>
                            <form action="{{ route('admin.items.retail.update', $item->id) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="number" step="0.01" name="retail_price" class="form-control form-control-sm me-2 border-primary" value="{{ $item->retail_price }}" style="width: 100px;">
                                <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                            </form>
                        </td>
                        <td>
                            <!-- تحديث سعر الجملة -->
                            <form action="{{ route('admin.items.wholesale.update', $item->id) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="number" step="0.01" name="wholesale_price" class="form-control form-control-sm me-2 border-danger" value="{{ $item->wholesale_price }}" style="width: 100px;">
                                <button type="submit" class="btn btn-dark btn-sm">حفظ</button>
                            </form>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal{{ $item->id }}">
                                <i class="fa-solid fa-pen-to-square"></i> تعديل
                            </button>

                            <div class="modal fade" id="inventoryModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.items.inventory.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل المخزون</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">الكمية المتوفرة</label>
                                                    <input type="number" name="stock_quantity" min="0" class="form-control" value="{{ $item->stock_quantity }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">المخزون التالف</label>
                                                    <input type="number" name="damaged_quantity" min="0" class="form-control" value="{{ $item->damaged_quantity ?? 0 }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">مكان الطارمة / رقم الرف</label>
                                                    <input type="text" name="shelf_number" class="form-control" value="{{ $item->shelf_number }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
