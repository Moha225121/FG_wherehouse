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
                    <th>اسم المستخدم</th>
                    <th>الفرع</th>
                    <th>المرتب</th>
                    <th>حد السحب</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr>
                        <td class="fw-bold">{{ $employee->name }}</td>
                        <td><span class="badge bg-light text-dark">{{ $employee->username }}</span></td>
                        <td>{{ $employee->branch->name }}</td>
                        <td class="text-success fw-bold">{{ number_format($employee->salary, 2) }} د.ل</td>
                        <td class="text-primary">{{ number_format($employee->daily_withdrawal_limit, 2) }} د.ل</td>
                        <td>
                            @if($employee->status == 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">موقوف</span>
                            @endif
                        </td>
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