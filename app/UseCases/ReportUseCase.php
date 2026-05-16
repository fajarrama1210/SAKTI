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
                'bi.id as bill_item_id',
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

        // 1. Ambil semua Bill Item ID
        $billItemIds = $results->pluck('bill_item_id')->toArray();

        // 2. Ambil total alokasi pembayaran per item dari tabel payment_allocations
        $allocations = DB::table(DatabaseEntity::TBL_PAYMENT_ALLOCATIONS)
            ->whereIn('bill_item_id', $billItemIds)
            ->selectRaw('bill_item_id, SUM(amount) as total_allocated')
            ->groupBy('bill_item_id')
            ->pluck('total_allocated', 'bill_item_id');

        // 3. Mapping data ke hasil akhir
        foreach ($results as $row) {
            $row->dibayar = (int) ($allocations[$row->bill_item_id] ?? 0);
            $row->sisa = $row->tagihan - $row->dibayar;
            
            // Tentukan status teks yang akurat per item
            if ($row->sisa <= 0) {
                $row->status_text = 'Lunas';
            } elseif ($row->dibayar > 0) {
                $row->status_text = 'Sebagian';
            } else {
                $row->status_text = 'Belum Bayar';
            }
        }

        return $results;
    }
}
