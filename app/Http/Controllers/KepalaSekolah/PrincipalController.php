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
        $search = $request->input('search');
        
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

        $students = $query->orderBy('s.id', 'desc')->paginate(10)->withQueryString();

        return view('kepala_sekolah.students', compact('students', 'search'));
    }

    public function transactions(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('transactions');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('kepala_sekolah.transactions', compact('transactions', 'search'));
    }

    public function bills(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = DB::table('bills as b')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->select('b.*', 's.name as student_name', 's.nisn', 'c.name as classroom_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%{$search}%")
                  ->orWhere('s.nisn', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('b.status', $status);
        }

        $bills = $query->orderBy('b.year', 'desc')->orderBy('b.month', 'desc')->paginate(10)->withQueryString();

        return view('kepala_sekolah.bills', compact('bills', 'search', 'status'));
    }
}
