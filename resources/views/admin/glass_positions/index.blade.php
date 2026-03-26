@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white"><i class="fa-solid fa-border-all"></i> إضافة جهة/موقع زجاج</div>
            <div class="card-body">
                <form action="{{ route('admin.glass-positions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">موقع الزجاج *</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: أمامي، مثلث خلفي..." required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">حفظ</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">مواقع الزجاج المسجلة</div>
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead><tr><th>موقع الزجاج</th><th>إجراء</th></tr></thead>
                    <tbody>
                        @foreach($positions as $pos)
                            <tr>
                                <td class="fw-bold">{{ $pos->name }}</td>
                                <td>
                                    <form action="{{ route('admin.glass-positions.destroy', $pos->id) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟');">
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