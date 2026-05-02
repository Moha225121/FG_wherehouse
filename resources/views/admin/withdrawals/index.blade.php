@extends('layouts.app')

@section('content')
<h2 class="mb-4 text-primary"><i class="fa-solid fa-sack-dollar"></i> سحوبات الإدارة</h2>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المسؤول</th>
                    <th>المبلغ</th>
                    <th>ملاحظة</th>
                    <th>وقت السحب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $w)
                <tr>
                    <td>{{ $w->id }}</td>
                    <td>{{ $w->admin ? $w->admin->name : '—' }}</td>
                    <td>{{ number_format($w->amount, 2) }} دينار</td>
                    <td>{{ $w->note }}</td>
                    <td>{{ $w->withdrawn_at ?? $w->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $withdrawals->links() }}
    </div>
</div>

@endsection
