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
        $filters = request()->only(['academic_year_id', 'semester_id', 'month']);
        $filtered = false;

        if (!empty($filters['academic_year_id'])) {
            $semesters = $this->semesterUseCase->getByAcademicYear($filters['academic_year_id']);
            $filtered = true;

            $reportFilters = ['academic_year_id' => $filters['academic_year_id']];

            if (!empty($filters['semester_id'])) {
                $months = $this->semesterUseCase->getMonthRange($filters['semester_id']);
                $reportFilters['months'] = $months;
            }

            if (!empty($filters['month'])) {
                $reportFilters['month'] = $filters['month'];
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
        $filters = request()->only(['academic_year_id', 'semester_id', 'month', 'year', 'type']);
        $filtered = false;

        if (!empty($filters['year']) || !empty($filters['month']) || !empty($filters['semester_id'])) {
            $filtered = true;
            $reportFilters = [];

            if (!empty($filters['academic_year_id'])) {
                $semesters = $this->semesterUseCase->getByAcademicYear($filters['academic_year_id']);
            }

            if (!empty($filters['semester_id'])) {
                $months = $this->semesterUseCase->getMonthRange($filters['semester_id']);
                $reportFilters['months'] = $months;
            }

            if (!empty($filters['month'])) {
                $reportFilters['month'] = $filters['month'];
            }

            if (!empty($filters['year'])) {
                $reportFilters['year'] = $filters['year'];
            }

            if (!empty($filters['type'])) {
                $reportFilters['type'] = $filters['type'];
            }

            $data = $this->transactionUseCase->getReport($reportFilters);
        }

        $totalIncome = $data instanceof \Illuminate\Support\Collection ? $data->where('type', 'income')->sum('amount') : 0;
        $totalExpense = $data instanceof \Illuminate\Support\Collection ? $data->where('type', 'expense')->sum('amount') : 0;

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
        $input = request()->only(['academic_year_id', 'semester_id', 'month']);
        $filters = [];

        if (!empty($input['academic_year_id'])) {
            $filters['academic_year_id'] = $input['academic_year_id'];
        }
        if (!empty($input['semester_id'])) {
            $filters['months'] = $this->semesterUseCase->getMonthRange($input['semester_id']);
        }
        if (!empty($input['month'])) {
            $filters['month'] = $input['month'];
        }
        return $filters;
    }

    private function buildTransactionFilters(): array
    {
        $input = request()->only(['semester_id', 'month', 'year', 'type']);
        $filters = [];

        if (!empty($input['semester_id'])) {
            $filters['months'] = $this->semesterUseCase->getMonthRange($input['semester_id']);
        }
        if (!empty($input['month'])) {
            $filters['month'] = $input['month'];
        }
        if (!empty($input['year'])) {
            $filters['year'] = $input['year'];
        }
        if (!empty($input['type'])) {
            $filters['type'] = $input['type'];
        }
        return $filters;
    }
}
