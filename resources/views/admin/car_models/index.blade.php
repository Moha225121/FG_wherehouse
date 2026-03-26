@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white"><i class="fa-solid fa-car"></i> إضافة نوع سيارة</div>
            <div class="card-body">
                <form action="{{ route('admin.car-models.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">نوع السيارة *</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: هيونداي النترا 2020" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظة عامة على السيارة</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">حفظ</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">أنواع السيارات المسجلة</div>
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>السيارة</th><th>الملاحظة</th><th>إجراء</th></tr></thead>
                    <tbody>
                        @foreach($carModels as $car)
                            <tr>
                                <td class="fw-bold">{{ $car->name }}</td>
                                <td>{{ $car->note ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.car-models.destroy', $car->id) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection