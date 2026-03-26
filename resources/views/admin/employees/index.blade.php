@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-users-gear"></i> إدارة الموظفين</h2>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> إضافة موظف جديد
    </a>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>الاسم</th>
                    <th>اسم المستخدم (للدخول)</th>
                    <th>الفرع التابع له</th>
                    <th>الحالة</th>
                    <th>تاريخ الإضافة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td class="fw-bold">{{ $employee->name }}</td>
                        <td><span class="badge bg-secondary fs-6">{{ $employee->username }}</span></td>
                        <td>{{ $employee->branch->name }}</td>
                        <td>
                            @if($employee->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">موقوف</span>
                            @endif
                        </td>
                        <td dir="ltr" class="text-end">{{ $employee->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-pen"></i> تعديل
                            </a>
                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف نهائياً؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection