@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-money-bill-wave"></i> السحوبات والمصروفات</h2>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.expenses.reset') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary" onclick="return confirm('هل تريد تصفير مرتبات الموظفين الذين تاريخ تصفيتهم هو اليوم؟')">
                <i class="fa-solid fa-arrows-rotate"></i> تصفير المرتبات المستحقة اليوم
            </button>
        </form>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fa-solid fa-plus"></i> إضافة سحب / مصروف
        </button>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">النوع</label>
                <select name="type" class="form-select">
                    <option value="">الكل</option>
                    <option value="salary" {{ request('type') == 'salary' ? 'selected' : '' }}>سحب من مرتب</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>مصروفات محل</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">الموظف (إذا كان سحب مرتب)</label>
                <select name="employeeID" class="form-select searchable-select">
                    <option value="">الكل</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employeeID') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">فلترة</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>المستلم / الموظف</th>
                    <th>المبلغ</th>
                    <th>ملاحظات</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                    <tr>
                        <td dir="ltr">{{ $w->date }}</td>
                        <td>
                            @if($w->type == 'salary')
                                <span class="badge bg-info text-dark">سحب من مرتب</span>
                            @else
                                <span class="badge bg-warning text-dark">مصروفات محل</span>
                            @endif
                        </td>
                        <td>
                            @if($w->type == 'salary')
                                {{ $w->employee->name ?? 'موظف محذوف' }}
                            @else
                                <span class="text-muted">مصروف عام</span>
                            @endif
                        </td>
                        <td class="fw-bold text-danger">{{ number_format($w->amount, 2) }} د.ل</td>
                        <td>{{ $w->note ?? '-' }}</td>
                        <td>
                            <form action="{{ route('admin.expenses.destroy', $w->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟ سيتم حذف السجل وإرجاع المبلغ للرصيد إذا كان سحب مرتب.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">لا توجد سجلات حالياً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>

<!-- Modal إضافة سحب -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.expenses.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل سحب جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع السحب *</label>
                        <select name="type" class="form-select" id="typeSelect" required>
                            <option value="salary">سحب من مرتب (سلفة)</option>
                            <option value="expense">مصروفات محل (إيجار، كهرباء، إلخ)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="employeeSelectDiv">
                        <label class="form-label">الموظف *</label>
                        <select name="employeeID" class="form-select searchable-select">
                            <option value="">-- اختر الموظف --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} (الرصيد: {{ number_format($emp->remaining_salary, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المبلغ (د.ل) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">التاريخ *</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظة</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حفظ السجل</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('typeSelect').addEventListener('change', function() {
        if (this.value === 'expense') {
            document.getElementById('employeeSelectDiv').style.display = 'none';
        } else {
            document.getElementById('employeeSelectDiv').style.display = 'block';
        }
    });
</script>
@endpush
@endsection
