@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-warning">
            <div class="card-header bg-warning text-dark fs-5 fw-bold">
                <i class="fa-solid fa-file-invoice-dollar"></i> إضافة مبيعات خارجية
            </div>
            <div class="card-body p-4">
                <form action="{{ route('employee.external-sales.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">نوع البيع الخارجي / البيان *</label>
                        <input type="text" name="sale_type" class="form-control" placeholder="مثال: بيع سيليكون، خدمة تركيب خارجية..." required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">قيمة البيع (دينار) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold fs-5">
                        تسجيل البيع الخارجي <i class="fa-solid fa-check"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection