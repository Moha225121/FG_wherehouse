@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header text-center fs-5 text-primary">
                <i class="fa-solid fa-user-lock"></i> تسجيل الدخول للنظام
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fs-5">دخول <i class="fa-solid fa-arrow-left"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection