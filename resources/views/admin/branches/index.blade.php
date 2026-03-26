@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Form to Add Branch -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white"><i class="fa-solid fa-plus"></i> إضافة فرع جديد</div>
            <div class="card-body">
                <form action="{{ route('admin.branches.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم الفرع *</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: فرع طرابلس الرئيسي" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الموقع / العنوان</label>
                        <input type="text" name="location" class="form-control" placeholder="اختياري">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">حفظ الفرع</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Table of Branches -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white"><i class="fa-solid fa-code-branch"></i> الفروع المسجلة</div>
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الفرع</th>
                            <th>الموقع</th>
                            <th>عدد الموظفين</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>
                                <td class="fw-bold">{{ $branch->name }}</td>
                                <td>{{ $branch->location ?? '-' }}</td>
                                <td><span class="badge bg-info text-dark fs-6">{{ $branch->employees_count }}</span></td>
                                <td>
                                    <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> حذف</button>
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