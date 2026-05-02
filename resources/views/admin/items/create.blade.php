@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white fs-5">
                <i class="fa-solid fa-plus-circle"></i> إضافة صنف للمخزون (صلاحية الإدارة)
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.items.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">الفرع *</label>
                            <select name="branchID" class="form-select searchable-select" required>
                                <option value="" disabled selected>-- اختر الفرع --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">نوع السيارة *</label>
                            <select name="carModelID" class="form-select searchable-select" required>
                                <option value="" disabled selected>-- اختر نوع السيارة --</option>
                                @foreach($carModels as $car)
                                    <option value="{{ $car->id }}">{{ $car->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">جهة / موقع الزجاج *</label>
                            <select name="glassPositionID" class="form-select searchable-select" required>
                                <option value="" disabled selected>-- اختر موقع الزجاج --</option>
                                @foreach($glassPositions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">نوع الزجاج / الجودة</label>
                            <input type="text" name="glass_type" class="form-control" placeholder="مثال: أصلي، تجاري...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم الطارمة / الرف</label>
                            <input type="text" name="shelf_number" class="form-control" placeholder="مثال: ABC-123">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-success">سعر البيع للموظف *</label>
                            <input type="number" step="0.01" name="retail_price" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">سعر الجملة *</label>
                            <input type="number" step="0.01" name="wholesale_price" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary">الكمية المدخلة *</label>
                            <input type="number" name="stock_quantity" class="form-control" min="1" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fs-5">
                        حفظ في المخزن <i class="fa-solid fa-save"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
