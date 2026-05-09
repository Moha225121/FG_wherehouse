@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark fs-5 fw-bold">
                <i class="fa-solid fa-user-pen"></i> تعديل بيانات الموظف: {{ $employee->name }}
            </div>
            <div class="card-body p-4">
                <!-- استخدام PUT للتعديل -->
                <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الموظف بالكامل *</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $employee->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">الفرع التابع له *</label>
                            <select name="branchID" class="form-select searchable-select" required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branchID', $employee->branchID) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">اسم المستخدم (للدخول) *</label>
                            <input type="text" name="username" class="form-control" required value="{{ old('username', $employee->username) }}" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">كلمة المرور الجديدة</label>
                            <!-- حقل كلمة المرور غير مطلوب في التعديل -->
                            <input type="password" name="password" class="form-control" minlength="6" dir="ltr">
                            <small class="text-muted">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">حالة الحساب *</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>موقوف</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-success">المرتب الشهري (د.ل) *</label>
                            <input type="number" step="0.01" name="salary" class="form-control" required value="{{ old('salary', $employee->salary) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">حد السحب اليومي (د.ل) *</label>
                            <input type="number" step="0.01" name="daily_withdrawal_limit" class="form-control" required value="{{ old('daily_withdrawal_limit', $employee->daily_withdrawal_limit) }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fs-5 fw-bold">
                        تحديث بيانات الموظف <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary w-100 mt-2">إلغاء والعودة</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection