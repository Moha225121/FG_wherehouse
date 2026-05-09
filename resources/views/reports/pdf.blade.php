<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
            line-height: 1.6;
        }
        .header {
            border-bottom: 2px solid #222;
            margin-bottom: 14px;
            padding-bottom: 8px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .meta {
            margin-top: 8px;
            font-size: 12px;
        }
        .meta-row {
            margin: 2px 0;
        }
        .summary {
            margin: 12px 0;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            background: #fafafa;
        }
        .summary-row {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: right;
            vertical-align: top;
        }
        th {
            background: #f1f1f1;
            font-weight: bold;
        }
        .section-title {
            margin-top: 16px;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
        }
        .muted {
            color: #666;
        }
        .ltr {
            direction: ltr;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">{{ $reportTitle }}</p>
        <div class="meta">
            <div class="meta-row"><strong>تم الإنشاء بواسطة:</strong> {{ $generatedByName }}</div>
            <div class="meta-row"><strong>الدور:</strong> {{ $generatedByRole === 'admin' ? 'الإدارة' : 'موظف' }}</div>
            <div class="meta-row"><strong>تاريخ إنشاء التقرير:</strong> {{ $generatedAt->format('Y-m-d H:i') }}</div>
            @if($branchName)
                <div class="meta-row"><strong>الفرع:</strong> {{ $branchName }}</div>
            @endif
        </div>
    </div>

    <div class="summary">
        <div class="summary-row"><strong>إجمالي المبيعات:</strong> {{ number_format($grandTotal, 2) }} د.ل</div>
        <div class="summary-row"><strong>مبيعات الزجاج:</strong> {{ number_format($totalNormalSales, 2) }} د.ل</div>
        <div class="summary-row"><strong>مبيعات خارجية:</strong> {{ number_format($totalExternalSales, 2) }} د.ل</div>
        <div class="summary-row"><strong>إجمالي الخصومات:</strong> {{ number_format($totalDiscounts, 2) }} د.ل</div>
        <div class="summary-row"><strong>إجمالي الزيادة:</strong> {{ number_format($totalOverprice, 2) }} د.ل</div>
    </div>

    <div class="section-title">تفاصيل مبيعات الزجاج</div>
    <table>
        <thead>
            <tr>
                <th>الوقت</th>
                <th>السيارة</th>
                <th>القطعة</th>
                <th>سعر النظام</th>
                <th>سعر البيع</th>
                <th>الخصم</th>
                <th>الزيادة</th>
                <th>ملاحظة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td class="ltr">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $sale->item->carModel->name ?? 'غير معروف' }}</td>
                    <td>{{ $sale->item->glassPosition->name ?? 'غير محدد' }}</td>
                    <td>{{ number_format($sale->system_price, 2) }}</td>
                    <td>{{ number_format($sale->sold_price, 2) }}</td>
                    <td>{{ number_format($sale->discount, 2) }}</td>
                    <td>{{ number_format($sale->overprice, 2) }}</td>
                    <td>{{ $sale->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">لا توجد مبيعات زجاج خلال هذه الفترة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">تفاصيل المبيعات الخارجية</div>
    <table>
        <thead>
            <tr>
                <th>الوقت</th>
                <th>الوصف</th>
                <th>القيمة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($externalSales as $external)
                <tr>
                    <td class="ltr">{{ $external->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $external->sale_type ?? '-' }}</td>
                    <td>{{ number_format($external->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="muted">لا توجد مبيعات خارجية خلال هذه الفترة.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
