<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $student = DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.*', 'c.name as classroom_name', 'm.name as major_name', 'c.grade_level')
            ->where('s.id', $studentId)
            ->first();

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        // Hitung total tagihan dan statusnya
        $totalBillsCount = DB::table('bills')->where('student_id', $studentId)->count();

        // Total tunggakan (belum lunas dan lunas sebagian)
        $unpaidBills = DB::table('bills')
            ->where('student_id', $studentId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();
        
        $totalOutstanding = 0;
        foreach ($unpaidBills as $bill) {
            $paidAmount = DB::table('payments')->where('bill_id', $bill->id)->sum('amount');
            $totalOutstanding += ($bill->total_amount - $paidAmount);
        }

        // Total sudah dibayar
        $totalPaid = DB::table('payments as p')
            ->join('bills as b', 'p.bill_id', '=', 'b.id')
            ->where('b.student_id', $studentId)
            ->sum('p.amount');

        // Histori pembayaran terbaru
        $recentPayments = DB::table('payments as p')
            ->join('bills as b', 'p.bill_id', '=', 'b.id')
            ->select('p.*', 'b.month', 'b.year')
            ->where('b.student_id', $studentId)
            ->orderBy('p.payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'totalBillsCount',
            'totalOutstanding',
            'totalPaid',
            'recentPayments'
        ));
    }

    public function bills()
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $student = DB::table('students')->where('id', $studentId)->first();

        $bills = DB::table('bills as b')
            ->join('academic_years as ay', 'b.academic_year_id', '=', 'ay.id')
            ->join('semesters as sm', 'b.semester_id', '=', 'sm.id')
            ->select('b.*', 'ay.name as academic_year_name', 'sm.name as semester_name')
            ->where('b.student_id', $studentId)
            ->orderBy('b.year', 'desc')
            ->orderBy('b.month', 'desc')
            ->get();

        foreach ($bills as $bill) {
            $bill->items = DB::table('bill_items as bi')
                ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
                ->select('bi.*', 'pt.name as payment_type_name')
                ->where('bi.bill_id', $bill->id)
                ->get();

            $bill->payments = DB::table('payments')
                ->where('bill_id', $bill->id)
                ->orderBy('payment_date', 'desc')
                ->get();

            $bill->paid_amount = $bill->payments->sum('amount');
        }

        return view('student.bills', compact('student', 'bills'));
    }

    public function schedules()
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $student = DB::table('students')->where('id', $studentId)->first();

        $schedules = DB::table('schedules as sc')
            ->join('classrooms as cl', 'sc.classroom_id', '=', 'cl.id')
            ->select('sc.*', 'cl.name as classroom_name')
            ->where('sc.classroom_id', $student->classroom_id)
            ->orderBy('sc.start_time', 'asc')
            ->get();

        // Petakan hari dari database
        $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $daysMapping = [
            'monday'    => 'Senin',
            'tuesday'   => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday'  => 'Kamis',
            'friday'    => 'Jumat',
            'saturday'  => 'Sabtu',
        ];

        // Kelompokkan jadwal berdasarkan hari
        $schedulesByDay = [];
        foreach ($orderedDays as $day) {
            $schedulesByDay[$daysMapping[$day]] = $schedules->filter(function ($item) use ($day) {
                return strtolower($item->day) === $day;
            });
        }

        return view('student.schedules', compact('student', 'schedulesByDay'));
    }

    public function profile()
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $student = DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.*', 'c.name as classroom_name', 'm.name as major_name', 'c.grade_level')
            ->where('s.id', $studentId)
            ->first();

        $user = Auth::user();

        return view('student.profile', compact('student', 'user'));
    }

    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        DB::table('users')
            ->where('id', Auth::id())
            ->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'updated_at' => now(),
            ]);

        return redirect()->route('student.profile')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
