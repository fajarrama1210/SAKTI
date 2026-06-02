<?php

namespace App\Http\Controllers;

use App\Entities\DatabaseEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Tampilkan invoice untuk satu payment.
     * Siswa hanya bisa lihat invoice miliknya sendiri.
     * Admin bisa lihat semua invoice.
     */
    public function show(int $paymentId)
    {
        $user = Auth::user();

        // Ambil data payment beserta relasi yang dibutuhkan
        $payment = DB::table(DatabaseEntity::TBL_PAYMENTS . ' as p')
            ->join(DatabaseEntity::TBL_BILLS . ' as b', 'p.bill_id', '=', 'b.id')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'b.student_id', '=', 's.id')
            ->join('academic_years as ay', 'b.academic_year_id', '=', 'ay.id')
            ->leftJoin('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->leftJoin('majors as m', 'c.major_id', '=', 'm.id')  // major via classroom, bukan langsung dari students
            ->select(
                'p.id as payment_id',
                'p.amount',
                'p.payment_date',
                'p.payment_method',
                'p.reference_number',
                'p.notes',
                'p.created_at as paid_at',
                'b.id as bill_id',
                'b.month',
                'b.year',
                'b.total_amount as bill_total',
                'b.status as bill_status',
                'ay.name as academic_year_name',
                's.id as student_id',
                's.name as student_name',
                's.nisn',
                's.id_number',
                'c.name as classroom_name',
                'c.grade_level',
                'm.name as major_name'
            )
            ->where('p.id', $paymentId)
            ->first();

        if (!$payment) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        // Otorisasi: siswa hanya boleh lihat invoice miliknya sendiri
        if ($user->role === 'student') {
            if ($user->student_id !== $payment->student_id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        }

        // Ambil detail item tagihan yang dibayar pada payment ini (menggunakan alokasi)
        $billItems = DB::table('payment_allocations as pa')
            ->join(DatabaseEntity::TBL_BILL_ITEMS . ' as bi', 'pa.bill_item_id', '=', 'bi.id')
            ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
            ->select('pa.amount', 'pt.name as type_name')
            ->where('pa.payment_id', $paymentId)
            ->get();

        if ($billItems->isEmpty()) {
            // Fallback ke seluruh item tagihan jika alokasi tidak ditemukan (untuk data lama)
            $billItems = DB::table(DatabaseEntity::TBL_BILL_ITEMS . ' as bi')
                ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
                ->select('bi.amount', 'pt.name as type_name')
                ->where('bi.bill_id', $payment->bill_id)
                ->get();
        }

        // Hitung total yang sudah dibayar untuk tagihan ini (untuk cek lunas/sebagian)
        $totalPaidForBill = DB::table(DatabaseEntity::TBL_PAYMENTS)
            ->where('bill_id', $payment->bill_id)
            ->sum('amount');

        // Nomor invoice: INV-{payment_id}-{tahun}{bulan}
        $invoiceNumber = 'INV-' . str_pad($paymentId, 6, '0', STR_PAD_LEFT)
            . '-' . $payment->year . str_pad($payment->month, 2, '0', STR_PAD_LEFT);

        return view('invoice.show', compact(
            'payment',
            'billItems',
            'totalPaidForBill',
            'invoiceNumber'
        ));
    }
}
