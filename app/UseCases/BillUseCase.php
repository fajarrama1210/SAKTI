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
                'b.student_id',
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
        $bills = DB::table(DatabaseEntity::TBL_BILLS . ' as b')
            ->join(DatabaseEntity::TBL_SEMESTERS . ' as sm', 'b.semester_id', '=', 'sm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'b.academic_year_id', '=', 'ay.id')
            ->select('b.*', 'sm.name as semester_name', 'ay.name as academic_year_name')
            ->where('b.student_id', $studentId)
            ->orderBy('b.year', 'asc')
            ->orderBy('b.month', 'asc')
            ->get();

        // Tambahkan paid_amount untuk setiap tagihan (untuk progress cicilan)
        foreach ($bills as $bill) {
            $bill->paid_amount = DB::table(DatabaseEntity::TBL_PAYMENTS)
                ->where('bill_id', $bill->id)
                ->sum('amount');
        }

        return $bills;
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
     * Sekarang menggunakan ENROLLMENT untuk menentukan kelas siswa, bukan students.classroom_id langsung.
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

            // Ambil siswa dari ENROLLMENT aktif di tahun ajaran ini
            $students = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
                ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'e.student_id', '=', 's.id')
                ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
                ->select('s.id as student_id', 's.family_card_number', 'c.grade_level', 'c.major_id')
                ->where('e.academic_year_id', $semester->academic_year_id)
                ->where('e.status', 'aktif')
                ->get();

            if ($students->isEmpty()) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Belum ada siswa yang terdaftar (enrollment) di tahun ajaran ini.'];
            }

            // Ambil semua jenis pembayaran
            $paymentTypes = DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->get();
            if ($paymentTypes->isEmpty()) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Belum ada Jenis Pembayaran.'];
            }

            // Pre-load semua tarif
            $allRates = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
                ->where('academic_year_id', $semester->academic_year_id)
                ->get();

            $generatedCount = 0;

            // Pre-load semua tagihan yang sudah ada untuk tahun ajaran ini (Mencegah N+1 Query)
            $existingBills = DB::table(DatabaseEntity::TBL_BILLS)
                ->where('academic_year_id', $semester->academic_year_id)
                ->select('student_id', 'month', 'year')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->student_id . '-' . $item->month . '-' . $item->year => true];
                })->toArray();

            foreach ($students as $student) {
                $generatedCount += $this->generateMissingBills($student, $semester, $monthsWithYear, $paymentTypes, $allRates, $existingBills);
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
     * Sinkronisasi tagihan untuk SATU siswa (digunakan saat siswa baru mendaftar)
     */
    public function syncBillsForStudent($studentId, $academicYearId): array
    {
        DB::beginTransaction();
        try {
            $student = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
                ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'e.student_id', '=', 's.id')
                ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
                ->select('s.id as student_id', 's.family_card_number', 'c.grade_level', 'c.major_id')
                ->where('e.student_id', $studentId)
                ->where('e.academic_year_id', $academicYearId)
                ->first();

            if (!$student) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Data penempatan siswa tidak ditemukan.'];
            }

            $semesters = DB::table(DatabaseEntity::TBL_SEMESTERS . ' as sm')
                ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'sm.academic_year_id', '=', 'ay.id')
                ->select('sm.*', 'ay.start_date', 'ay.end_date')
                ->where('sm.academic_year_id', $academicYearId)
                ->get();
            $paymentTypes = DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->get();
            $allRates = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->where('academic_year_id', $academicYearId)->get();
            
            // Ambil semua tagihan yang sudah ada untuk siswa ini
            $existingBills = DB::table(DatabaseEntity::TBL_BILLS)
                ->where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->select('student_id', 'month', 'year')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->student_id . '-' . $item->month . '-' . $item->year => true];
                })->toArray();

            $generatedCount = 0;
            foreach ($semesters as $semester) {
                $monthsWithYear = $this->getMonthsWithYear($semester);
                $generatedCount += $this->generateMissingBills($student, $semester, $monthsWithYear, $paymentTypes, $allRates, $existingBills);
            }

            DB::commit();
            return ['status' => true, 'count' => $generatedCount];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SyncBillsForStudent Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper privat untuk membuat tagihan yang belum ada (Dry Logic)
     * Bug #1 Fix: is_monthly=false hanya di-generate pada bulan PERTAMA semester.
     */
    private function generateMissingBills($student, $semester, $monthsWithYear, $paymentTypes, $allRates, $existingBills): int
    {
        $count = 0;

        // Pisahkan payment type bulanan dan sekali bayar
        $monthlyTypes = $paymentTypes->filter(fn($pt) => $pt->is_monthly)->values();
        $oneTimeTypes = $paymentTypes->filter(fn($pt) => !$pt->is_monthly)->values();

        // Cek payment type sekali bayar yang sudah pernah di-bill siswa ini (di semua tahun ajaran)
        $existingOneTimePtIds = [];
        if ($oneTimeTypes->isNotEmpty()) {
            $existingOneTimePtIds = DB::table('bill_items as bi')
                ->join('bills as b', 'bi.bill_id', '=', 'b.id')
                ->where('b.student_id', $student->student_id)
                ->whereIn('bi.payment_type_id', $oneTimeTypes->pluck('id'))
                ->pluck('bi.payment_type_id')
                ->unique()
                ->toArray();
        }

        foreach ($monthsWithYear as $idx => $my) {
            $m = $my['month'];
            $y = $my['year'];
            $billKey = $student->student_id . '-' . $m . '-' . $y;

            if (isset($existingBills[$billKey])) continue;

            $totalAmount       = 0;
            $billItemsTemplate = [];

            // Tarif BULANAN — muncul di setiap bulan
            foreach ($monthlyTypes as $pt) {
                // Filter berdasarkan semester_id jika di-set pada jenis pembayaran
                if ($pt->semester_id !== null && $pt->semester_id != $semester->id) {
                    continue;
                }
                $rate = $this->findRateForStudent($allRates, $pt->id, $student->grade_level, $student->major_id);
                if ($rate) {
                    $totalAmount         += $rate->amount;
                    $billItemsTemplate[]  = ['payment_type_id' => $pt->id, 'amount' => $rate->amount];
                }
            }

            // Tarif SEKALI BAYAR — hanya di bulan PERTAMA semester ini
            if ($idx === 0) {
                foreach ($oneTimeTypes as $pt) {
                    if (in_array($pt->id, $existingOneTimePtIds)) continue;
                    $rate = $this->findRateForStudent($allRates, $pt->id, $student->grade_level, $student->major_id);
                    if ($rate) {
                        $totalAmount         += $rate->amount;
                        $billItemsTemplate[]  = ['payment_type_id' => $pt->id, 'amount' => $rate->amount];
                        $existingOneTimePtIds[] = $pt->id; // Cegah duplikat dalam loop
                    }
                }
            }

            if (empty($billItemsTemplate)) continue;

            $dueDate = Carbon::createFromDate($y, $m, 1)->endOfMonth()->toDateString();
            $billId  = DB::table(DatabaseEntity::TBL_BILLS)->insertGetId([
                'student_id'         => $student->student_id,
                'family_card_number' => $student->family_card_number,
                'academic_year_id'   => $semester->academic_year_id,
                'semester_id'        => $semester->id,
                'month'              => $m,
                'year'               => $y,
                'total_amount'       => $totalAmount,
                'status'             => 'unpaid',
                'due_date'           => $dueDate,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

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
            $count++;
        }

        return $count;
    }

    /**
     * Bug #2b Fix: Cari tarif — spesifik jurusan dulu, fallback ke null (semua jurusan).
     */
    private function findRateForStudent($allRates, $paymentTypeId, $gradeLevel, $majorId)
    {
        $rate = $allRates->first(fn($r) =>
            $r->payment_type_id == $paymentTypeId && $r->grade_level == $gradeLevel && $r->major_id == $majorId
        );
        if (!$rate) {
            $rate = $allRates->first(fn($r) =>
                $r->payment_type_id == $paymentTypeId && $r->grade_level == $gradeLevel && $r->major_id === null
            );
        }
        return $rate;
    }

    /**
     * Bug #4 Fix: Update nominal bill_items dan total_amount untuk bill UNPAID
     * saat tarif berubah. Dipanggil saat sinkronisasi.
     */
    public function syncUnpaidBillAmounts($semesterId): array
    {
        DB::beginTransaction();
        try {
            $semester = DB::table(DatabaseEntity::TBL_SEMESTERS . ' as sm')
                ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'sm.academic_year_id', '=', 'ay.id')
                ->select('sm.*', 'ay.start_date', 'ay.end_date')
                ->where('sm.id', $semesterId)
                ->first();
            if (!$semester) return ['status' => false, 'count' => 0];

            $monthsWithYear = $this->getMonthsWithYear($semester);
            $firstMonth = $monthsWithYear[0]['month'] ?? null;
            $firstYear  = $monthsWithYear[0]['year'] ?? null;

            $paymentTypes = DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->get();
            $monthlyTypes = $paymentTypes->filter(fn($pt) => $pt->is_monthly)->values();
            $oneTimeTypes = $paymentTypes->filter(fn($pt) => !$pt->is_monthly)->values();

            $allRates = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
                ->where('academic_year_id', $semester->academic_year_id)
                ->get();

            $unpaidBills = DB::table(DatabaseEntity::TBL_BILLS . ' as b')
                ->join(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e', function ($j) use ($semester) {
                    $j->on('e.student_id', '=', 'b.student_id')
                      ->where('e.academic_year_id', '=', $semester->academic_year_id);
                })
                ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
                ->select('b.id as bill_id', 'b.student_id', 'b.month', 'b.year', 'c.grade_level', 'c.major_id')
                ->where('b.semester_id', $semesterId)
                ->where('b.status', '!=', 'cancelled')
                ->get();

            $updatedCount = 0;
            foreach ($unpaidBills as $bill) {
                $billItems = DB::table(DatabaseEntity::TBL_BILL_ITEMS)->where('bill_id', $bill->bill_id)->get();
                $existingItemPtIds = $billItems->pluck('payment_type_id')->toArray();

                $applicableItems = [];

                // 1. Bulanan — berlaku di setiap bulan
                foreach ($monthlyTypes as $pt) {
                    // Filter berdasarkan semester_id jika di-set pada jenis pembayaran
                    if ($pt->semester_id !== null && $pt->semester_id != $semesterId) {
                        continue;
                    }
                    $rate = $this->findRateForStudent($allRates, $pt->id, $bill->grade_level, $bill->major_id);
                    if ($rate) {
                        $applicableItems[$pt->id] = $rate->amount;
                    }
                }

                // 2. Sekali Bayar — hanya berlaku di bulan pertama semester
                if ($bill->month == $firstMonth && $bill->year == $firstYear) {
                    foreach ($oneTimeTypes as $pt) {
                        // check if already has bill item in any bill for this student
                        $alreadyBilled = DB::table(DatabaseEntity::TBL_BILL_ITEMS)
                            ->where('student_id', $bill->student_id)
                            ->where('payment_type_id', $pt->id)
                            ->exists();
                        if (!$alreadyBilled || in_array($pt->id, $existingItemPtIds)) {
                            $rate = $this->findRateForStudent($allRates, $pt->id, $bill->grade_level, $bill->major_id);
                            if ($rate) {
                                $applicableItems[$pt->id] = $rate->amount;
                            }
                        }
                    }
                }

                $isModified = false;

                // Sync: Update atau tambah bill items
                foreach ($applicableItems as $ptId => $amount) {
                    if (in_array($ptId, $existingItemPtIds)) {
                        $currentItem = $billItems->first(fn($item) => $item->payment_type_id == $ptId);
                        if ($currentItem && $currentItem->amount != $amount) {
                            DB::table(DatabaseEntity::TBL_BILL_ITEMS)
                                ->where('id', $currentItem->id)
                                ->update(['amount' => $amount, 'updated_at' => now()]);
                            $isModified = true;
                        }
                    } else {
                        // Tambah baru jika belum ada di bill_items
                        DB::table(DatabaseEntity::TBL_BILL_ITEMS)->insert([
                            'bill_id'         => $bill->bill_id,
                            'student_id'      => $bill->student_id,
                            'payment_type_id' => $ptId,
                            'amount'          => $amount,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                        $isModified = true;
                    }
                }

                // Jika ada perubahan, update total_amount dan status bill-nya
                if ($isModified) {
                    $newTotal = DB::table(DatabaseEntity::TBL_BILL_ITEMS)
                        ->where('bill_id', $bill->bill_id)
                        ->sum('amount');

                    // Hitung jumlah yang sudah dibayar
                    $totalPaid = DB::table(DatabaseEntity::TBL_PAYMENTS)
                        ->where('bill_id', $bill->bill_id)
                        ->sum('amount');

                    if ($totalPaid >= $newTotal) {
                        $newStatus = 'paid';
                    } elseif ($totalPaid > 0) {
                        $newStatus = 'partial';
                    } else {
                        $newStatus = 'unpaid';
                    }

                    DB::table(DatabaseEntity::TBL_BILLS)
                        ->where('id', $bill->bill_id)
                        ->update([
                            'total_amount' => $newTotal,
                            'status'       => $newStatus,
                            'updated_at'   => now()
                        ]);

                    $updatedCount++;
                }
            }

            DB::commit();
            return ['status' => true, 'count' => $updatedCount];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SyncUnpaidBillAmounts Error: ' . $e->getMessage());
            return ['status' => false, 'count' => 0];
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
                ->leftJoin(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
                ->select('b.*', 's.name as student_name', 's.family_card_number', 'c.name as classroom_name')
                ->where('b.id', $billId)
                ->first();

            if (!$bill) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tagihan tidak ditemukan.'];
            }

            // 1. Hitung nominal yang sudah dibayar dan sisa tagihan (atau sisa per item jika ada bill_item_id)
            $billItemId = $data['bill_item_id'] ?? null;
            if ($billItemId) {
                $item = DB::table(DatabaseEntity::TBL_BILL_ITEMS)->where('id', $billItemId)->first();
                if (!$item) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Rincian tagihan tidak ditemukan.'];
                }
                $itemPaid = DB::table('payment_allocations')
                    ->where('bill_item_id', $billItemId)
                    ->sum('amount');
                $remainingAmount = $item->amount - $itemPaid;
            } else {
                $currentPaidAmount = DB::table(DatabaseEntity::TBL_PAYMENTS)
                    ->where('bill_id', $billId)
                    ->sum('amount');
                $remainingAmount = $bill->total_amount - $currentPaidAmount;
            }

            // Jika $data['amount'] tidak di-set, asumsikan lunas (bayar sisa)
            $payAmount = isset($data['amount']) ? (int) $data['amount'] : $remainingAmount;

            // 2. Validasi input
            if ($payAmount <= 0) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tagihan sudah lunas atau nominal tidak valid.'];
            }

            if ($payAmount > $remainingAmount) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Nominal bayar melebihi sisa tagihan. Sisa tagihan: ' . number_format($remainingAmount, 0, ',', '.')];
            }

            $refNumber = $data['reference_number'] ?? null;

            // 3. Insert payment record
            $paymentId = DB::table(DatabaseEntity::TBL_PAYMENTS)->insertGetId([
                'bill_id'          => $billId,
                'amount'           => $payAmount,
                'payment_method'   => $data['payment_method'],
                'payment_date'     => $data['payment_date'] ?? now()->toDateString(),
                'reference_number' => $refNumber,
                'verified_by'      => $data['verified_by'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Jika nomor referensi dikosongkan, generate otomatis oleh sistem
            if (empty($refNumber)) {
                $payDate = $data['payment_date'] ?? now()->toDateString();
                $datePrefix = date('Ymd', strtotime($payDate));
                $paddedId = str_pad($paymentId, 5, '0', STR_PAD_LEFT);

                if ($data['payment_method'] === 'cash') {
                    $refNumber = 'KW-' . $datePrefix . '-' . $paddedId;
                } elseif ($data['payment_method'] === 'transfer') {
                    $refNumber = 'TRF-' . $datePrefix . '-' . $paddedId;
                } else {
                    $refNumber = 'PAY-' . $datePrefix . '-' . $paddedId;
                }

                DB::table(DatabaseEntity::TBL_PAYMENTS)->where('id', $paymentId)->update([
                    'reference_number' => $refNumber
                ]);
            }

            // 4. Alokasi pembayaran ke rincian tagihan (bill_items)
            if ($billItemId) {
                DB::table('payment_allocations')->insert([
                    'payment_id' => $paymentId,
                    'bill_item_id' => $billItemId,
                    'amount' => $payAmount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $billItems = DB::table(DatabaseEntity::TBL_BILL_ITEMS)->where('bill_id', $billId)->get();
                $amountToAllocate = $payAmount;

                foreach ($billItems as $item) {
                    if ($amountToAllocate <= 0) break;

                    $itemPaid = DB::table('payment_allocations')->where('bill_item_id', $item->id)->sum('amount');
                    $itemRemaining = $item->amount - $itemPaid;

                    if ($itemRemaining > 0) {
                        $allocate = min($amountToAllocate, $itemRemaining);
                        
                        DB::table('payment_allocations')->insert([
                            'payment_id' => $paymentId,
                            'bill_item_id' => $item->id,
                            'amount' => $allocate,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $amountToAllocate -= $allocate;
                    }
                }
            }

            // 5. Update status tagihan (partial atau paid)
            $newTotalPaid = DB::table(DatabaseEntity::TBL_PAYMENTS)
                ->where('bill_id', $billId)
                ->sum('amount');
            $newStatus = ($newTotalPaid >= $bill->total_amount) ? 'paid' : 'partial';

            DB::table(DatabaseEntity::TBL_BILLS)->where('id', $billId)->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

            // 6. Auto-insert ke jurnal transaksi (Buku Kas)
            $studentInfo = $bill->student_name . ' (' . ($bill->classroom_name ?? 'Tanpa Kelas') . ')';
            $monthName = Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y');
            
            $paymentTypeDesc = '';
            if ($billItemId) {
                $ptName = DB::table('bill_items as bi')
                    ->join('payment_types as pt', 'bi.payment_type_id', '=', 'pt.id')
                    ->where('bi.id', $billItemId)
                    ->value('pt.name');
                if ($ptName) {
                    $paymentTypeDesc = ' (' . $ptName . ')';
                }
            }
            
            DB::table(DatabaseEntity::TBL_TRANSACTIONS)->insert([
                'date'        => $data['payment_date'] ?? now()->toDateString(),
                'type'        => 'income',
                'category'    => 'Pembayaran',
                'description' => 'Pembayaran ' . $monthName . $paymentTypeDesc . ' - ' . $studentInfo,
                'amount'      => $payAmount,
                'payment_id'  => $paymentId,
                'recorded_by' => $data['verified_by'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // 7. Proses diskon saudara (hanya aktif jika tagihan UTAMA sudah lunas)
            $isSiblingDiscountEnabled = env('FEATURE_SIBLING_DISCOUNT', false);
            if ($newStatus === 'paid' && $isSiblingDiscountEnabled) {
                $this->processSiblingDiscounts($bill, $paymentId, $data);
            }

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('RecordPayment Error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Memproses tagihan saudara se-KK.
     * Dibuatkan record payment khusus (Reference ke bayaran utama) agar uang tidak masuk jurnal 2x,
     * tetapi status tagihan saudara tetap sah lunas dengan audit trail yang jelas.
     */
    private function processSiblingDiscounts($mainBill, $mainPaymentId, $data)
    {
        $siblingStudentIds = DB::table(DatabaseEntity::TBL_STUDENTS)
            ->where('family_card_number', $mainBill->family_card_number)
            ->where('id', '!=', $mainBill->student_id)
            ->pluck('id');

        if ($siblingStudentIds->isEmpty()) {
            return;
        }

        $siblingBills = DB::table(DatabaseEntity::TBL_BILLS)
            ->whereIn('student_id', $siblingStudentIds)
            ->where('month', $mainBill->month)
            ->where('year', $mainBill->year)
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($siblingBills as $sibBill) {
            $sibPaid = DB::table(DatabaseEntity::TBL_PAYMENTS)
                ->where('bill_id', $sibBill->id)
                ->sum('amount');
            $sibRemaining = $sibBill->total_amount - $sibPaid;

            if ($sibRemaining > 0) {
                // Insert payment record for sibling (tanpa memasukkan ke transactions)
                $sibPaymentId = DB::table(DatabaseEntity::TBL_PAYMENTS)->insertGetId([
                    'bill_id'          => $sibBill->id,
                    'amount'           => $sibRemaining,
                    'payment_method'   => 'other',
                    'payment_date'     => $data['payment_date'] ?? now()->toDateString(),
                    'reference_number' => 'REF-' . $mainPaymentId,
                    'verified_by'      => $data['verified_by'] ?? null,
                    'notes'            => 'Digratiskan (Diskon Saudara se-KK dari Bill ID: ' . $mainBill->id . ')',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Allocate for sibling
                $sibItems = DB::table(DatabaseEntity::TBL_BILL_ITEMS)->where('bill_id', $sibBill->id)->get();
                $sibAmountToAllocate = $sibRemaining;

                foreach ($sibItems as $item) {
                    if ($sibAmountToAllocate <= 0) break;
                    $itemPaid = DB::table('payment_allocations')->where('bill_item_id', $item->id)->sum('amount');
                    $itemRemaining = $item->amount - $itemPaid;
                    if ($itemRemaining > 0) {
                        $allocate = min($sibAmountToAllocate, $itemRemaining);
                        DB::table('payment_allocations')->insert([
                            'payment_id' => $sibPaymentId,
                            'bill_item_id' => $item->id,
                            'amount' => $allocate,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $sibAmountToAllocate -= $allocate;
                    }
                }

                // Update sibling bill status
                DB::table(DatabaseEntity::TBL_BILLS)->where('id', $sibBill->id)->update([
                    'status'     => 'paid',
                    'updated_at' => now(),
                ]);
            }
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
        
        $startDate = $semester->start_date ?? null;
        if (!$startDate) {
            $startDate = DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)
                ->where('id', $semester->academic_year_id)
                ->value('start_date');
        }
        
        $ayStart = Carbon::parse($startDate);
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
    /**
     * Data untuk Matrix Pembayaran (Grid Siswa vs Bulan)
     */
    public function getPaymentMatrix($filters)
    {
        $classroomId = $filters['classroom_id'] ?? null;
        $semesterId = $filters['semester_id'] ?? null;

        if (!$classroomId || !$semesterId) return null;

        $semester = DB::table(DatabaseEntity::TBL_SEMESTERS . ' as sm')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'sm.academic_year_id', '=', 'ay.id')
            ->select('sm.*', 'ay.start_date')
            ->where('sm.id', $semesterId)
            ->first();
            
        if (!$semester) return null;

        $months = $this->getMonthsWithYear($semester);

        $students = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'e.student_id', '=', 's.id')
            ->select('s.id', 's.name', 's.nisn')
            ->where('e.classroom_id', $classroomId)
            ->where('e.academic_year_id', $semester->academic_year_id)
            ->where('e.status', 'aktif')
            ->orderBy('s.name', 'asc')
            ->get();

        $bills = DB::table(DatabaseEntity::TBL_BILLS)
            ->where('semester_id', $semesterId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        return [
            'months' => $months,
            'students' => $students,
            'bills' => $bills,
            'semester' => $semester
        ];
    }
}
