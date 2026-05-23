<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Entities\DatabaseEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrincipalController extends Controller
{
    public function dashboard()
    {
        // 1. Card Stats
        $totalStudents = DB::table('students')->count();
        $totalClassrooms = DB::table('classrooms')->count();
        $totalMajors = DB::table('majors')->count();
        
        $totalBilled = DB::table('bills')->sum('total_amount');
        $totalPaid = DB::table('payments')->sum('amount');
        $totalOutstanding = max(0, $totalBilled - $totalPaid);

        // 2. Chart 1: Financial Performance (Last 6 Months)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = \Carbon\Carbon::now()->subMonths($i);
            $month = $monthDate->month;
            $year = $monthDate->year;
            $monthName = $monthDate->translatedFormat('M Y');

            $chartLabels[] = $monthName;

            $income = DB::table('transactions')
                ->where('type', 'income')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');
            $chartIncome[] = $income;

            $expense = DB::table('transactions')
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');
            $chartExpense[] = $expense;
        }

        // 3. Chart 2: SPP Bills Status (Paid vs Unpaid)
        $paidBillsCount = DB::table('bills')->where('status', 'paid')->count();
        $unpaidBillsCount = DB::table('bills')->whereIn('status', ['unpaid', 'partial'])->count();

        // 4. Chart 3: Student Distribution per Class
        $classroomStats = DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->select('c.name', DB::raw('count(s.id) as total'))
            ->groupBy('c.id', 'c.name')
            ->get();
        $classroomNames = $classroomStats->pluck('name')->toArray();
        $classroomStudentCounts = $classroomStats->pluck('total')->toArray();

        // 5. Chart 4: Payment Methods Distribution
        $methodStats = DB::table('payments')
            ->select('payment_method', DB::raw('count(*) as total'))
            ->groupBy('payment_method')
            ->get();
        $paymentMethods = $methodStats->pluck('payment_method')->toArray();
        $paymentMethodCounts = $methodStats->pluck('total')->toArray();

        // 6. Recent Payments (Last 5)
        $recentPayments = DB::table('payments as p')
            ->join('bills as b', 'p.bill_id', '=', 'b.id')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->select('p.*', 's.family_card_number', 's.name as student_name')
            ->orderBy('p.payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('kepala_sekolah.dashboard', compact(
            'totalStudents',
            'totalClassrooms',
            'totalMajors',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'paidBillsCount',
            'unpaidBillsCount',
            'classroomNames',
            'classroomStudentCounts',
            'paymentMethods',
            'paymentMethodCounts',
            'recentPayments'
        ));
    }

    public function students(Request $request)
    {
        $search      = $request->input('search');
        $classroomId = $request->input('classroom_id');
        $status      = $request->input('status');

        // ── Summary Stats ────────────────────────────────────────────
        $totalStudents = DB::table('students')->count();

        $gradeStats = DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->selectRaw('c.grade_level, COUNT(s.id) as total')
            ->groupBy('c.grade_level')
            ->orderBy('c.grade_level')
            ->get();

        $statusStats = DB::table('students')
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // ── Filter Options ───────────────────────────────────────────
        $classroomOptions = DB::table('classrooms as c')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('c.id', 'c.name', 'c.grade_level', 'm.name as major_name')
            ->orderBy('c.grade_level')
            ->orderBy('m.name')
            ->get();

        // ── Main Query ───────────────────────────────────────────────
        $query = DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.*', 'c.name as classroom_name', 'm.name as major_name', 'c.grade_level');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%{$search}%")
                  ->orWhere('s.nisn', 'like', "%{$search}%")
                  ->orWhere('s.id_number', 'like', "%{$search}%")
                  ->orWhere('s.family_card_number', 'like', "%{$search}%");
            });
        }

        if ($classroomId) {
            $query->where('s.classroom_id', $classroomId);
        }

        if ($status) {
            $query->where('s.status', $status);
        }

        $students = $query->orderBy('c.grade_level')->orderBy('s.name')->paginate(15)->withQueryString();

        return view('kepala_sekolah.students', compact(
            'students', 'search', 'classroomId', 'status',
            'totalStudents', 'gradeStats', 'statusStats', 'classroomOptions'
        ));
    }

    public function transactions(Request $request)
    {
        $search  = $request->input('search');
        $type    = $request->input('type');    // income | expense | ''
        $month   = $request->input('month');   // YYYY-MM  | ''

        // ── Summary Stats (always global) ────────────────────────────
        $summaryStats = DB::table('transactions')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN type = "income"  THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) as total_expense,
                COUNT(*) as total_count
            ')
            ->first();

        $netCash = ($summaryStats->total_income ?? 0) - ($summaryStats->total_expense ?? 0);

        // ── Main Query ───────────────────────────────────────────────
        $query = DB::table('transactions as t')
            ->leftJoin('users as u', 't.recorded_by', '=', 'u.id')
            ->select('t.*', 'u.name as recorder_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('t.description', 'like', "%{$search}%")
                  ->orWhere('t.category', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('t.type', $type);
        }

        if ($month) {
            $query->whereRaw('DATE_FORMAT(t.date, "%Y-%m") = ?', [$month]);
        }

        $transactions = $query
            ->orderBy('t.date', 'desc')
            ->orderBy('t.id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kepala_sekolah.transactions', compact(
            'transactions', 'search', 'type', 'month',
            'summaryStats', 'netCash'
        ));
    }

    public function bills(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        // ── Summary Stats (always global, not filtered) ──────────────
        $summaryStats = DB::table('bills as b')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->selectRaw('
                COUNT(*) as total_bills,
                SUM(b.total_amount) as total_amount,
                COALESCE(SUM((SELECT SUM(p.amount) FROM payments p WHERE p.bill_id = b.id)), 0) as total_paid,
                SUM(CASE WHEN b.status = "paid" THEN 1 ELSE 0 END) as count_paid,
                SUM(CASE WHEN b.status = "partial" THEN 1 ELSE 0 END) as count_partial,
                SUM(CASE WHEN b.status = "unpaid" THEN 1 ELSE 0 END) as count_unpaid
            ')
            ->first();

        $totalOutstanding = max(0, ($summaryStats->total_amount ?? 0) - ($summaryStats->total_paid ?? 0));

        // ── Main Query ───────────────────────────────────────────────
        $query = DB::table('bills as b')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->select(
                'b.*',
                's.name as student_name',
                's.nisn',
                'c.name as classroom_name',
                DB::raw('COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.bill_id = b.id), 0) as paid_amount')
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%{$search}%")
                  ->orWhere('s.nisn', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('b.status', $status);
        }

        $bills = $query->orderBy('b.year', 'desc')->orderBy('b.month', 'desc')->paginate(15)->withQueryString();

        return view('kepala_sekolah.bills', compact(
            'bills', 'search', 'status',
            'summaryStats', 'totalOutstanding'
        ));
    }
}
