<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillUseCase
{
    protected $paymentRateUseCase;

    public function __construct(PaymentRateUseCase $paymentRateUseCase)
    {
        $this->paymentRateUseCase = $paymentRateUseCase;
    }

    /**
     * Ambil semua tagihan bulan ini (untuk halaman rekap)
     */
    public function getPaginated($perPage = 15, $filters = [])
    {
        $query = DB::table(DatabaseEntity::TBL_BILLS . ' as b')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'b.student_id', '=', 's.id')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->join(DatabaseEntity::TBL_SEMESTERS . ' as sm', 'b.semester_id', '=', 'sm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'b.academic_year_id', '=', 'ay.id')
            ->select(
                'b.id',
                's.name as student_name',
                's.nisn',
                's.family_card_number',
                'c.grade_level',
                'm.name as major_name',
                'ay.name as academic_year_name',
                'sm.name as semester_name',
                'b.month',
                'b.year',
                'b.total_amount',
                'b.status',
                'b.due_date'
            );

        // Filter bulan
        if (!empty($filters['month'])) {
            $query->where('b.month', $filters['month']);
        }
        // Filter tahun
        if (!empty($filters['year'])) {
            $query->where('b.year', $filters['year']);
        }
        // Filter status
        if (!empty($filters['status'])) {
            $query->where('b.status', $filters['status']);
        }
        // Filter pencarian
        if (!empty($filters['search'])) {
            $keyword = $filters['search'];
            $query->where(function ($q) use ($keyword) {
                $q->where('s.name', 'LIKE', "%{$keyword}%")
                  ->orWhere('s.nisn', 'LIKE', "%{$keyword}%");
            });
        }

        return $query
            ->orderBy('b.year', 'desc')
            ->orderBy('b.month', 'desc')
            ->orderBy('s.name', 'asc')
            ->paginate($perPage)
            ->appends($filters);
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_BILLS . ' as b')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'b.student_id', '=', 's.id')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'b.academic_year_id', '=', 'ay.id')
            ->join(DatabaseEntity::TBL_SEMESTERS . ' as sm', 'b.semester_id', '=', 'sm.id')
            ->select(
                'b.*',
                's.name as student_name', 's.nisn', 's.family_card_number',
                'c.grade_level', 'm.name as major_name',
                'ay.name as academic_year_name',
                'sm.name as semester_name'
            )
            ->where('b.id', $id)
            ->first();
    }

    /**
     * Ambil tagihan bulanan seorang siswa (kalendar SPP)
     */
    public function getStudentBills($studentId)
    {
        return DB::table(DatabaseEntity::TBL_BILLS . ' as b')
            ->join(DatabaseEntity::TBL_SEMESTERS . ' as sm', 'b.semester_id', '=', 'sm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'b.academic_year_id', '=', 'ay.id')
            ->select('b.*', 'sm.name as semester_name', 'ay.name as academic_year_name')
            ->where('b.student_id', $studentId)
            ->orderBy('b.year', 'asc')
            ->orderBy('b.month', 'asc')
            ->get();
    }

    /**
     * Ambil semua siswa sekk (saudara dengan KK yang sama)
     */
    public function getStudentsInSameKK($familyCardNumber)
    {
        return DB::table(DatabaseEntity::TBL_STUDENTS . ' as s')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.name', 's.nisn', 's.status', 'c.grade_level', 'm.name as major_name')
            ->where('s.family_card_number', $familyCardNumber)
            ->get();
    }

    public function getBillItems($billId)
    {
        return DB::table(DatabaseEntity::TBL_BILL_ITEMS . ' as bi')
            ->join(DatabaseEntity::TBL_PAYMENT_TYPES . ' as pt', 'bi.payment_type_id', '=', 'pt.id')
            ->select('bi.*', 'pt.name as payment_type_name')
            ->where('bi.bill_id', $billId)
            ->get();
    }

    public function getPayments($billId)
    {
        return DB::table(DatabaseEntity::TBL_PAYMENTS . ' as p')
            ->leftJoin(DatabaseEntity::TBL_USERS . ' as u', 'p.verified_by', '=', 'u.id')
            ->select('p.*', 'u.name as verified_by_name')
            ->where('p.bill_id', $billId)
            ->orderBy('p.payment_date', 'desc')
            ->get();
    }

    /**
     * Otomatis generate tagihan saat semester dibuat.
     * Dipanggil dari SemesterUseCase::store()
     * Membuat tagihan PER SISWA PER BULAN dalam rentang semester tersebut.
     * Due date = akhir tiap bulan (otomatis).
     */
    public function autoGenerateBillsForSemester($semesterId): array
    {
        DB::beginTransaction();
        try {
            $semester = DB::table(DatabaseEntity::TBL_SEMESTERS . ' as sm')
                ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'sm.academic_year_id', '=', 'ay.id')
                ->select('sm.*', 'ay.start_date', 'ay.end_date')
                ->where('sm.id', $semesterId)
                ->first();

            if (!$semester) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Semester tidak ditemukan.'];
            }

            // Hitung range bulan dan tahun
            $monthsWithYear = $this->getMonthsWithYear($semester);

            // Ambil semua siswa aktif
            $students = DB::table(DatabaseEntity::TBL_STUDENTS . ' as s')
                ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
                ->select('s.id as student_id', 's.family_card_number', 'c.grade_level', 'c.major_id')
                ->where('s.status', 'aktif')
                ->get();

            if ($students->isEmpty()) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Belum ada siswa aktif.'];
            }

            // Ambil semua jenis pembayaran
            $paymentTypes = DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->get();
            if ($paymentTypes->isEmpty()) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Belum ada Jenis Pembayaran.'];
            }

            // Track KK yang sudah diproses agar hanya 1 tagihan per KK per bulan
            $processedKKMonths = [];
            $generatedCount = 0;

            foreach ($students as $student) {
                // Hitung tarif untuk siswa ini
                $totalAmount   = 0;
                $billItemsTemplate = [];

                foreach ($paymentTypes as $pt) {
                    $rate = $this->paymentRateUseCase->getRateForStudent(
                        $semester->academic_year_id,
                        $pt->id,
                        $student->grade_level,
                        $student->major_id
                    );

                    if ($rate) {
                        $totalAmount += $rate->amount;
                        $billItemsTemplate[] = [
                            'payment_type_id' => $pt->id,
                            'amount'          => $rate->amount,
                        ];
                    }
                }

                if (empty($billItemsTemplate)) continue;

                foreach ($monthsWithYear as $my) {
                    $m = $my['month'];
                    $y = $my['year'];
                    $kkKey = $student->family_card_number . '-' . $m . '-' . $y;

                    // Cek apakah KK ini sudah punya tagihan di bulan+tahun ini
                    // Jika ya, skip — 1 KK cukup bayar 1 kali
                    if (isset($processedKKMonths[$kkKey])) {
                        // Tapi tetap buat bill record untuk siswa ini agar terlihat di kalender
                        // dengan status mengikuti saudara (linked via KK)
                    }

                    // Cek apakah bill untuk siswa ini di bulan ini sudah ada
                    $existing = DB::table(DatabaseEntity::TBL_BILLS)
                        ->where('student_id', $student->student_id)
                        ->where('month', $m)
                        ->where('year', $y)
                        ->first();

                    if ($existing) continue;

                    // Due date = akhir bulan tersebut
                    $dueDate = Carbon::createFromDate($y, $m, 1)->endOfMonth()->toDateString();

                    $billId = DB::table(DatabaseEntity::TBL_BILLS)->insertGetId([
                        'student_id'         => $student->student_id,
                        'academic_year_id'   => $semester->academic_year_id,
                        'semester_id'        => $semesterId,
                        'month'              => $m,
                        'year'               => $y,
                        'total_amount'       => $totalAmount,
                        'status'             => 'unpaid',
                        'due_date'           => $dueDate,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);

                    // Insert bill items
                    $items = [];
                    foreach ($billItemsTemplate as $tpl) {
                        $items[] = [
                            'bill_id'         => $billId,
                            'student_id'      => $student->student_id,
                            'payment_type_id' => $tpl['payment_type_id'],
                            'amount'          => $tpl['amount'],
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                    DB::table(DatabaseEntity::TBL_BILL_ITEMS)->insert($items);

                    $processedKKMonths[$kkKey] = true;
                    $generatedCount++;
                }
            }

            DB::commit();
            return ['status' => true, 'count' => $generatedCount];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('AutoGenerateBills Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Bayar tagihan. Jika ada saudara (KK sama), otomatis tandai lunas juga.
     */
    public function recordPayment($billId, array $data): array
    {
        DB::beginTransaction();
        try {
            $bill = DB::table(DatabaseEntity::TBL_BILLS . ' as b')
                ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'b.student_id', '=', 's.id')
                ->select('b.*', 's.family_card_number')
                ->where('b.id', $billId)
                ->first();

            if (!$bill) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tagihan tidak ditemukan.'];
            }

            // Insert payment record
            $paymentId = DB::table(DatabaseEntity::TBL_PAYMENTS)->insertGetId([
                'bill_id'          => $billId,
                'amount'           => $bill->total_amount,
                'payment_method'   => $data['payment_method'],
                'payment_date'     => $data['payment_date'] ?? now()->toDateString(),
                'reference_number' => $data['reference_number'] ?? null,
                'verified_by'      => $data['verified_by'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Tandai tagihan ini sebagai lunas
            DB::table(DatabaseEntity::TBL_BILLS)->where('id', $billId)->update([
                'status'     => 'paid',
                'updated_at' => now(),
            ]);

            // Tandai saudara (KK sama, bulan+tahun sama) sebagai lunas juga
            $siblingStudentIds = DB::table(DatabaseEntity::TBL_STUDENTS)
                ->where('family_card_number', $bill->family_card_number)
                ->where('id', '!=', $bill->student_id)
                ->pluck('id');

            if ($siblingStudentIds->isNotEmpty()) {
                DB::table(DatabaseEntity::TBL_BILLS)
                    ->whereIn('student_id', $siblingStudentIds)
                    ->where('month', $bill->month)
                    ->where('year', $bill->year)
                    ->where('status', '!=', 'paid')
                    ->update([
                        'status'     => 'paid',
                        'updated_at' => now(),
                    ]);
            }

            // Auto-insert ke jurnal transaksi
            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->insert([
                'date'        => $data['payment_date'] ?? now()->toDateString(),
                'type'        => 'income',
                'category'    => 'SPP',
                'description' => 'Pemb. SPP ' . Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') . ' - KK: ' . $bill->family_card_number,
                'amount'      => $bill->total_amount,
                'payment_id'  => $paymentId,
                'recorded_by' => $data['verified_by'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('RecordPayment Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update due_date per tagihan (opsi admin set manual)
     */
    public function updateDueDate($billId, $dueDate): array
    {
        try {
            DB::table(DatabaseEntity::TBL_BILLS)->where('id', $billId)->update([
                'due_date'   => $dueDate,
                'updated_at' => now(),
            ]);
            return ['status' => true];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            $hasPaid = DB::table(DatabaseEntity::TBL_PAYMENTS)->where('bill_id', $id)->exists();
            if ($hasPaid) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tagihan tidak dapat dihapus karena sudah ada pembayaran.'];
            }
            DB::table(DatabaseEntity::TBL_BILLS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('BillDelete Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Menghitung array [{month, year}] dari semester berdasarkan start/end month + tahun ajaran.
     */
    private function getMonthsWithYear($semester): array
    {
        $startM = (int) $semester->start_month;
        $endM   = (int) $semester->end_month;
        $ayStart = Carbon::parse($semester->start_date);
        $startYear = $ayStart->year;

        $result = [];

        if ($startM <= $endM) {
            for ($m = $startM; $m <= $endM; $m++) {
                $result[] = ['month' => $m, 'year' => $startYear];
            }
        } else {
            // Wrap around (e.g. Oct-Mar)
            for ($m = $startM; $m <= 12; $m++) {
                $result[] = ['month' => $m, 'year' => $startYear];
            }
            for ($m = 1; $m <= $endM; $m++) {
                $result[] = ['month' => $m, 'year' => $startYear + 1];
            }
        }

        return $result;
    }
}
