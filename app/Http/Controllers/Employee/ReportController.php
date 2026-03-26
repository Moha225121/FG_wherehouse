<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\ExternalSale;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        $reportData = $this->buildReportData($period);

        return view('employee.reports.index', $reportData);
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'daily');
        $reportData = $this->buildReportData($period);

        $employee = Auth::guard('employee')->user();
        $admin = Auth::guard('admin')->user();

        $generatedByName = $employee?->name ?? $admin?->name ?? 'غير معروف';
        $generatedByRole = $admin ? 'admin' : 'employee';
        $branchName = null;

        if (! $admin && $employee) {
            $branchName = Branch::find($employee->branchID)?->name;
        }

        $html = view('reports.pdf', array_merge($reportData, [
            'generatedByName' => $generatedByName,
            'generatedByRole' => $generatedByRole,
            'generatedAt' => Carbon::now(),
            'branchName' => $branchName,
        ]))->render();

        $mpdfClass = '\\Mpdf\\Mpdf';

        $pdf = new $mpdfClass([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans',
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->SetDirectionality('rtl');
        $pdf->WriteHTML($html);

        $fileName = 'report-' . $period . '-' . Carbon::now()->format('Ymd-His') . '.pdf';

        return response($pdf->Output($fileName, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function buildReportData(string $period): array
    {
        $employee = Auth::guard('employee')->user();
        $admin = Auth::guard('admin')->user();

        $salesQuery = Sale::with(['item.carModel', 'item.glassPosition'])
            ->where('status', 'completed');
        $externalSalesQuery = ExternalSale::query();

        // Employee reports are branch-scoped, admin reports are global.
        if ($employee && ! $admin) {
            $salesQuery->where('branchID', $employee->branchID);
            $externalSalesQuery->where('branchID', $employee->branchID);
        }

        if ($period === 'weekly') {
            $salesQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $externalSalesQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $reportTitle = 'تقرير المبيعات الأسبوعية';
        } elseif ($period === 'monthly') {
            $salesQuery->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
            $externalSalesQuery->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
            $reportTitle = 'تقرير المبيعات الشهرية';
        } else {
            $period = 'daily';
            $salesQuery->whereDate('created_at', Carbon::today());
            $externalSalesQuery->whereDate('created_at', Carbon::today());
            $reportTitle = 'تقرير المبيعات اليومية';
        }

        $sales = $salesQuery->get();
        $externalSales = $externalSalesQuery->get();

        $totalNormalSales = $sales->sum('sold_price');
        $totalDiscounts = $sales->sum('discount');
        $totalOverprice = $sales->sum('overprice');
        $totalExternalSales = $externalSales->sum('amount');
        $grandTotal = $totalNormalSales + $totalExternalSales;

        return [
            'sales' => $sales,
            'externalSales' => $externalSales,
            'totalNormalSales' => $totalNormalSales,
            'totalDiscounts' => $totalDiscounts,
            'totalOverprice' => $totalOverprice,
            'totalExternalSales' => $totalExternalSales,
            'grandTotal' => $grandTotal,
            'period' => $period,
            'reportTitle' => $reportTitle,
        ];
    }
}