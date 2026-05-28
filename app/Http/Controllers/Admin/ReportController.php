<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\ReportUseCase;
use App\UseCases\AcademicYearUseCase;
use App\UseCases\SemesterUseCase;
use App\UseCases\TransactionUseCase;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentReportExport;
use App\Exports\TransactionReportExport;

class ReportController extends Controller
{
    protected $reportUseCase;
    protected $academicYearUseCase;
    protected $semesterUseCase;
    protected $transactionUseCase;

    public function __construct(
        ReportUseCase $reportUseCase,
        AcademicYearUseCase $academicYearUseCase,
        SemesterUseCase $semesterUseCase,
        TransactionUseCase $transactionUseCase
    ) {
        $this->reportUseCase = $reportUseCase;
        $this->academicYearUseCase = $academicYearUseCase;
        $this->semesterUseCase = $semesterUseCase;
        $this->transactionUseCase = $transactionUseCase;
    }

    /**
     * Halaman Laporan Pembayaran SPP
     */
    public function paymentReport()
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $semesters = collect();
        $data = collect();
        $filters = request()->only(['academic_year_id', 'semester_id', 'month', 'search', 'per_page']);
        $filtered = false;

        if (!empty($filters['academic_year_id'])) {
            $semesters = $this->semesterUseCase->getByAcademicYear($filters['academic_year_id']);
            $filtered = true;

            $reportFilters = $filters; // Include search and per_page

            if (!empty($filters['semester_id'])) {
                $months = $this->semesterUseCase->getMonthRange($filters['semester_id']);
                $reportFilters['months'] = $months;
            }

            $data = $this->reportUseCase->getPaymentReport($reportFilters);
        }

        return view('_admin.report.payment', compact('academicYears', 'semesters', 'data', 'filters', 'filtered'));
    }

    /**
     * Export Laporan Pembayaran ke Excel
     */
    public function paymentExportExcel()
    {
        $filters = $this->buildPaymentFilters();
        $data = $this->reportUseCase->getPaymentReport($filters);
        return Excel::download(new PaymentReportExport($data), 'laporan_pembayaran_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Export Laporan Pembayaran ke PDF
     */
    public function paymentExportPdf()
    {
        $filters = $this->buildPaymentFilters();
        $data = $this->reportUseCase->getPaymentReport($filters);

        $pdf = Pdf::loadView('_admin.report.payment_pdf', compact('data'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('laporan_pembayaran_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Halaman Laporan Transaksi Keuangan Umum
     */
    public function transactionReport()
    {
        $academicYears = $this->academicYearUseCase->getAll();
        $semesters = collect();
        $data = collect();
        $filters = request()->only(['academic_year_id', 'semester_id', 'month', 'year', 'type', 'search', 'per_page']);
        $filtered = false;
        $totalIncome = 0;
        $totalExpense = 0;

        if (!empty($filters['year']) || !empty($filters['month']) || !empty($filters['semester_id']) || !empty($filters['type']) || !empty($filters['search'])) {
            $filtered = true;
            $reportFilters = $filters;

            if (!empty($filters['academic_year_id'])) {
                $semesters = $this->semesterUseCase->getByAcademicYear($filters['academic_year_id']);
            }

            if (!empty($filters['semester_id'])) {
                $months = $this->semesterUseCase->getMonthRange($filters['semester_id']);
                $reportFilters['months'] = $months;
            }

            $data = $this->transactionUseCase->getReport($reportFilters);
            $totals = $this->transactionUseCase->getReportTotals($reportFilters);
            $totalIncome = $totals['income'];
            $totalExpense = $totals['expense'];
        }

        return view('_admin.report.transaction', compact(
            'academicYears',
            'semesters',
            'data',
            'filters',
            'filtered',
            'totalIncome',
            'totalExpense'
        ));
    }

    /**
     * Export Laporan Transaksi ke Excel
     */
    public function transactionExportExcel()
    {
        $filters = $this->buildTransactionFilters();
        $data = $this->transactionUseCase->getReport($filters);
        return Excel::download(new TransactionReportExport($data), 'laporan_transaksi_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Export Laporan Transaksi ke PDF
     */
    public function transactionExportPdf()
    {
        $filters = $this->buildTransactionFilters();
        $data = $this->transactionUseCase->getReport($filters);

        $totalIncome = $data->where('type', 'income')->sum('amount');
        $totalExpense = $data->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('_admin.report.transaction_pdf', compact('data', 'totalIncome', 'totalExpense'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('laporan_transaksi_' . now()->format('Ymd_His') . '.pdf');
    }

    // === Private Helpers ===

    private function buildPaymentFilters(): array
    {
        $input = request()->only(['academic_year_id', 'semester_id', 'month', 'search']);
        $filters = $input;
        $filters['is_export'] = true;

        if (!empty($input['semester_id'])) {
            $filters['months'] = $this->semesterUseCase->getMonthRange($input['semester_id']);
        }
        
        return $filters;
    }

    private function buildTransactionFilters(): array
    {
        $input = request()->only(['semester_id', 'month', 'year', 'type', 'search']);
        $filters = $input;
        $filters['is_export'] = true;

        if (!empty($input['semester_id'])) {
            $filters['months'] = $this->semesterUseCase->getMonthRange($input['semester_id']);
        }
        
        return $filters;
    }
}
