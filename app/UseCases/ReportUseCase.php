<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;

class ReportUseCase
{
    /**
     * Laporan pembayaran SPP per anak
     */
    public function getPaymentReport(array $filters)
    {
        $query = DB::table(DatabaseEntity::TBL_BILL_ITEMS . ' as bi')
            ->join(DatabaseEntity::TBL_BILLS . ' as b', 'bi.bill_id', '=', 'b.id')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'bi.student_id', '=', 's.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'b.academic_year_id', '=', 'ay.id')
            ->join(DatabaseEntity::TBL_PAYMENT_TYPES . ' as pt', 'bi.payment_type_id', '=', 'pt.id')
            ->leftJoin(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->leftJoin(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select(
                'ay.name as academic_year_name',
                's.nisn',
                's.name as student_name',
                'c.grade_level',
                'm.name as major_name',
                's.family_card_number',
                'b.month',
                'b.year',
                'pt.name as payment_type_name',
                'bi.amount as tagihan',
                'b.id as bill_id',
                'b.total_amount',
                'b.status'
            );

        // Filter: tahun ajaran
        if (!empty($filters['academic_year_id'])) {
            $query->where('b.academic_year_id', $filters['academic_year_id']);
        }

        // Filter: bulan-bulan (dari semester)
        if (!empty($filters['months']) && is_array($filters['months'])) {
            $query->whereIn('b.month', $filters['months']);
        }

        // Filter: bulan spesifik
        if (!empty($filters['month'])) {
            $query->where('b.month', $filters['month']);
        }

        // Filter: tahun
        if (!empty($filters['year'])) {
            $query->where('b.year', $filters['year']);
        }

        $results = $query
            ->orderBy('s.name', 'asc')
            ->orderBy('b.month', 'asc')
            ->get();

        // Hitung total dibayar per bill
        $billIds = $results->pluck('bill_id')->unique();
        $payments = DB::table(DatabaseEntity::TBL_PAYMENTS)
            ->whereIn('bill_id', $billIds)
            ->selectRaw('bill_id, SUM(amount) as total_paid')
            ->groupBy('bill_id')
            ->pluck('total_paid', 'bill_id');

        // Hitung proporsi bayar per anak
        foreach ($results as $row) {
            $billTotalPaid = $payments[$row->bill_id] ?? 0;
            // Proporsi: jika bill punya 2 anak (40rb total), anak 20rb bayar,
            // maka proporsi bayar anak ini = (20000/40000) * totalPaid
            if ($row->total_amount > 0) {
                $proportion = $row->tagihan / $row->total_amount;
                $row->dibayar = round($billTotalPaid * $proportion);
            } else {
                $row->dibayar = 0;
            }
            $row->sisa = $row->tagihan - $row->dibayar;
            $row->status_text = $row->sisa <= 0 ? 'Lunas' : ($row->dibayar > 0 ? 'Sebagian' : 'Belum Bayar');
        }

        return $results;
    }
}
