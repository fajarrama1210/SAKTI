<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Data for cards
        $totalStudents = DB::table(DatabaseEntity::TBL_STUDENTS)->count();
        $totalClassrooms = DB::table(DatabaseEntity::TBL_CLASSROOMS)->count();
        $totalMajors = DB::table(DatabaseEntity::TBL_MAJORS)->count();

        $activeAcademicYear = DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('is_active', true)->first();
        $activeYearName = $activeAcademicYear ? $activeAcademicYear->name : 'Belum diatur';

        // Finance overview for current month & year
        $currentMonth = date('n');
        $currentYear = date('Y');

        // Income & Expense this month
        $incomeThisMonth = DB::table(DatabaseEntity::TBL_TRANSACTIONS)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $expenseThisMonth = DB::table(DatabaseEntity::TBL_TRANSACTIONS)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // Chart Data (Last 6 Months)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = \Carbon\Carbon::now()->subMonths($i);
            $month = $monthDate->month;
            $year = $monthDate->year;
            $monthName = $monthDate->translatedFormat('M Y');

            $chartLabels[] = $monthName;

            $income = DB::table(DatabaseEntity::TBL_TRANSACTIONS)
                ->where('type', 'income')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');
            $chartIncome[] = $income;

            $expense = DB::table(DatabaseEntity::TBL_TRANSACTIONS)
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');
            $chartExpense[] = $expense;
        }

        // Recent Payments (Last 5)
        $recentPayments = DB::table(DatabaseEntity::TBL_PAYMENTS . ' as p')
            ->join(DatabaseEntity::TBL_BILLS . ' as b', 'p.bill_id', '=', 'b.id')
            ->select('p.*', 'b.family_card_number')
            ->orderBy('p.payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('_admin.dashboard.index', compact(
            'totalStudents',
            'totalClassrooms',
            'totalMajors',
            'activeYearName',
            'incomeThisMonth',
            'expenseThisMonth',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'recentPayments'
        ));
    }
}
