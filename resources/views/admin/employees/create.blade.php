@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white fs-5">
                <i class="fa-solid fa-user-plus"></i> تسجيل موظف جديد
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.employees.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الموظف بالكامل *</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-primary">الفرع التابع له *</label>
                            <select name="branchID" class="form-select" required>
                                <option value="" disabled selected>-- حدد الفرع --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branchID') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">اسم المستخدم (للدخول) *</label>
                            <input type="text" name="username" class="form-control" required value="{{ old('username') }}" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">كلمة المرور *</label>
                            <input type="password" name="password" class="form-control" required minlength="6" dir="ltr">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">حالة الحساب *</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط (يسمح بالدخول)</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>موقوف (يمنع الدخول)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fs-5">
                        حفظ بيانات الموظف <i class="fa-solid fa-save"></i>
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary w-100 mt-2">إلغاء والعودة</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection