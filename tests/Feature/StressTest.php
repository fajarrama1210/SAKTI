<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * ============================================================
 * SAKTI STRESS TEST SUITE
 * ============================================================
 *
 * Suite test ini menguji ketahanan sistem SAKTI di bawah beban besar.
 *
 * Jalankan: php artisan test --filter=StressTest
 *
 * Kategori test:
 *  1. Seeding & Volume    → Pastikan data massal bisa dibuat
 *  2. Query Performance   → Dashboard, laporan, rekap dengan data besar
 *  3. Business Logic      → Pembayaran, enrollment, saudara seKK
 *  4. Concurrency-like    → Operasi beruntun cepat
 *  5. Edge Cases          → Duplikat, constraint, data invalid
 *
 * CATATAN: family_card_number di tabel students memiliki UNIQUE constraint.
 *   Artinya logika saudara seKK (BillUseCase::recordPayment) TIDAK BISA berfungsi
 *   dengan skema database saat ini. Ini adalah TEMUAN BUG potensial.
 * ============================================================
 */
class StressTest extends TestCase
{
    use RefreshDatabase;

    // ===== DATA VOLUME CONFIG =====
    const STUDENT_COUNT   = 200;
    const MONTHS          = [7, 8, 9, 10, 11, 12];

    private $adminUser;
    private $majorIds     = [];
    private $classroomIds = [];
    private $classroomMap = [];
    private $studentIds   = [];
    private $academicYearId;
    private $semesterId;
    private $paymentTypeIds = [];

    /**
     * Setup: Buat data dasar yang digunakan oleh semua test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBaseData();
    }

    // =================================================================
    // 1. VOLUME & SEEDING TESTS
    // =================================================================

    /** @test */
    public function it_can_create_hundreds_of_students_quickly()
    {
        $startTime = microtime(true);

        $this->assertCount(self::STUDENT_COUNT, $this->studentIds);
        $this->assertEquals(self::STUDENT_COUNT, DB::table('students')->count());

        $elapsed = microtime(true) - $startTime;
        $this->assertLessThan(5, $elapsed, "Verifikasi siswa terlalu lambat: {$elapsed}s");
    }

    /** @test */
    public function it_creates_enrollment_for_all_students()
    {
        $enrollmentCount = DB::table('student_enrollments')
            ->where('academic_year_id', $this->academicYearId)
            ->count();

        $this->assertEquals(self::STUDENT_COUNT, $enrollmentCount);
    }

    // =================================================================
    // 2. MASS BILL GENERATION TEST
    // =================================================================

    /** @test */
    public function it_can_auto_generate_bills_for_hundreds_of_students()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);

        $startTime = microtime(true);
        $result = $billUseCase->autoGenerateBillsForSemester($this->semesterId);
        $elapsed = microtime(true) - $startTime;

        $this->assertTrue($result['status'], 'Auto generate bills gagal: ' . ($result['message'] ?? ''));
        $this->assertGreaterThan(0, $result['count'], 'Tidak ada tagihan yang dibuat');

        // Seharusnya = STUDENT_COUNT * jumlah bulan
        $expectedBills = self::STUDENT_COUNT * count(self::MONTHS);
        $this->assertEquals($expectedBills, $result['count'],
            "Ekspektasi {$expectedBills} tagihan, dibuat {$result['count']}");

        // Performa: harus di bawah 60 detik untuk 200 siswa * 6 bulan = 1200 tagihan
        $this->assertLessThan(60, $elapsed,
            "Auto-generate tagihan terlalu lambat: {$elapsed}s untuk {$result['count']} tagihan");

        // Verifikasi bill items juga dibuat
        $billItemCount = DB::table('bill_items')->count();
        $this->assertGreaterThan(0, $billItemCount);
    }

    // =================================================================
    // 3. DATABASE BUG DETECTION: family_card_number UNIQUE constraint
    // =================================================================

    /**
     * @test
     *
     * TEMUAN BUG: family_card_number di tabel students dibuat UNIQUE,
     * padahal logika bisnis di BillUseCase::recordPayment mengasumsikan
     * bahwa beberapa siswa BISA memiliki family_card_number yang SAMA
     * (saudara seKK).
     *
     * Ini berarti fitur "bayar 1 saudara, semua saudara lunas" TIDAK AKAN
     * pernah bekerja karena database mencegah duplikasi family_card_number.
     *
     * REKOMENDASI: Ubah migration untuk menghapus UNIQUE constraint
     * dari family_card_number, dan ganti dengan INDEX biasa.
     */
    public function it_detects_sibling_kk_constraint_bug()
    {
        // Coba insert 2 siswa dengan family_card_number yang sama
        $familyCard = '3273123456789012';
        $classroomId = $this->classroomIds[0];

        // Siswa pertama
        DB::table('students')->insert([
            'nisn'               => '9900000001',
            'id_number'          => 'BUG001',
            'name'               => 'Kakak Test',
            'classroom_id'       => $classroomId,
            'family_card_number' => $familyCard,
            'qr_code'            => 'QR-BUG1',
            'status'             => 'aktif',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Siswa kedua dengan KK yang sama → HARUS BERHASIL karena UNIQUE constraint sudah diubah ke INDEX
        DB::table('students')->insert([
            'nisn'               => '9900000002',
            'id_number'          => 'BUG002',
            'name'               => 'Adik Test',
            'classroom_id'       => $classroomId,
            'family_card_number' => $familyCard, // SAME KK
            'qr_code'            => 'QR-BUG2',
            'status'             => 'aktif',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->assertEquals(2, DB::table('students')->where('family_card_number', $familyCard)->count());
    }

    // =================================================================
    // 4. RAPID SEQUENTIAL PAYMENTS (Concurrency-like)
    // =================================================================

    /** @test */
    public function it_handles_rapid_sequential_payments()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Ambil 50 tagihan unpaid secara acak
        $bills = DB::table('bills')
            ->where('status', 'unpaid')
            ->inRandomOrder()
            ->limit(50)
            ->get();

        $startTime = microtime(true);
        $successCount = 0;
        $failCount = 0;

        foreach ($bills as $bill) {
            $result = $billUseCase->recordPayment($bill->id, [
                'payment_method' => 'cash',
                'payment_date'   => now()->toDateString(),
                'verified_by'    => $this->adminUser->id,
            ]);

            if ($result['status']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $elapsed = microtime(true) - $startTime;

        $this->assertGreaterThan(0, $successCount, 'Tidak ada pembayaran yang berhasil');
        $this->assertLessThan(60, $elapsed,
            "50 pembayaran terlalu lambat: {$elapsed}s ({$successCount} sukses, {$failCount} gagal)");
    }

    // =================================================================
    // 5. MASS GRADUATION WITH DEBT CHECK
    // =================================================================

    /** @test */
    public function it_handles_mass_graduation_with_debt_checking()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $enrollmentUseCase = app(\App\UseCases\EnrollmentUseCase::class);

        // Generate tagihan
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Luluskan 100 siswa pertama
        $graduateIds = array_slice($this->studentIds, 0, 100);

        $startTime = microtime(true);
        $result = $enrollmentUseCase->processGraduation(
            $this->academicYearId,
            $graduateIds,
            now()->toDateString()
        );
        $elapsed = microtime(true) - $startTime;

        $this->assertTrue($result['status'], 'Proses kelulusan gagal');
        $this->assertEquals(100, $result['graduated_count']);
        $this->assertIsArray($result['students_with_debt']);

        // Semua 100 siswa harus punya tunggakan (karena belum ada yang bayar)
        $this->assertCount(100, $result['students_with_debt'],
            'Semua siswa seharusnya punya tunggakan');

        // Performa
        $this->assertLessThan(30, $elapsed,
            "Kelulusan 100 siswa terlalu lambat: {$elapsed}s");

        // Verifikasi status enrollment
        $graduatedEnrollments = DB::table('student_enrollments')
            ->whereIn('student_id', $graduateIds)
            ->where('academic_year_id', $this->academicYearId)
            ->where('status', 'lulus')
            ->count();

        $this->assertEquals(100, $graduatedEnrollments);

        // Verifikasi status student
        $graduatedStudents = DB::table('students')
            ->whereIn('id', $graduateIds)
            ->where('status', 'lulus')
            ->count();

        $this->assertEquals(100, $graduatedStudents);
    }

    // =================================================================
    // 6. DROPOUT PROCESSING WITH BILL CANCELLATION
    // =================================================================

    /**
     * @test
     *
     * TEMUAN BUG #2: bills.status ENUM hanya ['unpaid', 'partial', 'paid']
     * tapi EnrollmentUseCase::processDropout() mencoba set status = 'cancelled'.
     *
     * Ini menyebabkan CHECK constraint violation di SQLite, dan kemungkinan
     * juga gagal di MySQL ketat (strict mode).
     *
     * REKOMENDASI: Tambahkan 'cancelled' ke ENUM status di tabel bills.
     */
    public function it_detects_cancelled_status_missing_from_bills_enum()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $enrollmentUseCase = app(\App\UseCases\EnrollmentUseCase::class);

        // Generate tagihan
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Ambil enrollment aktif
        $enrollment = DB::table('student_enrollments')
            ->where('academic_year_id', $this->academicYearId)
            ->where('status', 'aktif')
            ->first();

        $this->assertNotNull($enrollment, 'Tidak ada enrollment aktif');

        // DO siswa di bulan September
        $result = $enrollmentUseCase->processDropout($enrollment->id, [
            'exit_date'   => '2025-09-15',
            'exit_reason' => 'Stress test DO',
        ]);

        // BUG TERSELESAIKAN: processDropout BERHASIL karena status 'cancelled'
        // kini sudah ada di ENUM bills.status
        $this->assertTrue($result['status'],
            'processDropout seharusnya berhasil setelah cancelled ditambahkan ke ENUM');
        $this->assertGreaterThan(0, $result['cancelled_bills']);
    }

    // =================================================================
    // 7. HEAVY DASHBOARD QUERY TEST
    // =================================================================

    /** @test */
    public function dashboard_loads_under_heavy_data()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Buat beberapa payment agar ada data di dashboard
        $bills = DB::table('bills')->where('status', 'unpaid')->limit(20)->get();
        foreach ($bills as $bill) {
            $billUseCase->recordPayment($bill->id, [
                'payment_method' => 'cash',
                'payment_date'   => now()->toDateString(),
                'verified_by'    => $this->adminUser->id,
            ]);
        }

        // Hit dashboard endpoint
        $startTime = microtime(true);
        $response = $this->actingAs($this->adminUser)->get('/dashboard');
        $elapsed = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10, $elapsed,
            "Dashboard terlalu lambat dengan data besar: {$elapsed}s");
    }

    // =================================================================
    // 8. SPP SEARCH WITH LARGE DATASET
    // =================================================================

    /** @test */
    public function spp_search_performs_well_with_many_students()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Search by name partial match
        $startTime = microtime(true);
        $response = $this->actingAs($this->adminUser)->get('/admin/spp?search=Siswa+Test+1');
        $elapsed = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10, $elapsed,
            "Pencarian SPP terlalu lambat: {$elapsed}s");
    }

    // =================================================================
    // 9. BILL RECAP PAGINATION UNDER LOAD
    // =================================================================

    /** @test */
    public function bill_recap_paginates_correctly_with_many_bills()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        $totalBills = DB::table('bills')->count();
        $this->assertGreaterThanOrEqual(self::STUDENT_COUNT * count(self::MONTHS), $totalBills,
            "Jumlah tagihan kurang dari ekspektasi: {$totalBills}");

        // Hit recap page
        $startTime = microtime(true);
        $response = $this->actingAs($this->adminUser)->get('/admin/spp/rekap?month=7&year=2025');
        $elapsed = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10, $elapsed,
            "Rekap SPP terlalu lambat: {$elapsed}s");
    }

    // =================================================================
    // 10. PREVENT DUPLICATE ENROLLMENT
    // =================================================================

    /** @test */
    public function it_prevents_duplicate_enrollment()
    {
        $enrollmentUseCase = app(\App\UseCases\EnrollmentUseCase::class);

        $studentId = $this->studentIds[0];
        $classroomId = $this->classroomIds[0];

        $result = $enrollmentUseCase->enroll([
            'student_id'       => $studentId,
            'classroom_id'     => $classroomId,
            'academic_year_id' => $this->academicYearId,
        ]);

        $this->assertFalse($result['status']);
        $this->assertStringContainsString('sudah terdaftar', $result['message']);
    }

    // =================================================================
    // 11. PREVENT DELETING BILL WITH PAYMENT
    // =================================================================

    /** @test */
    public function it_prevents_deleting_paid_bills()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Bayar satu tagihan
        $bill = DB::table('bills')->where('status', 'unpaid')->first();
        $billUseCase->recordPayment($bill->id, [
            'payment_method' => 'cash',
            'payment_date'   => now()->toDateString(),
            'verified_by'    => $this->adminUser->id,
        ]);

        // Coba hapus
        $result = $billUseCase->delete($bill->id);
        $this->assertFalse($result['status']);
        $this->assertStringContainsString('pembayaran', $result['message']);
    }

    // =================================================================
    // 12. TRANSACTION REPORT UNDER HEAVY LOAD
    // =================================================================

    /** @test */
    public function transaction_report_handles_large_datasets()
    {
        // Buat banyak transaksi
        $batch = [];
        for ($i = 0; $i < 500; $i++) {
            $batch[] = [
                'date'        => Carbon::createFromDate(2025, rand(7, 12), rand(1, 28))->toDateString(),
                'type'        => rand(0, 1) ? 'income' : 'expense',
                'category'    => 'Stress Test',
                'description' => 'Transaksi stress test #' . $i,
                'amount'      => rand(1, 100) * 50000,
                'payment_id'  => null,
                'recorded_by' => $this->adminUser->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        DB::table('transactions')->insert($batch);

        // Hit report
        $startTime = microtime(true);
        $response = $this->actingAs($this->adminUser)->get('/admin/reports/transaction?year=2025&month=10');
        $elapsed = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10, $elapsed,
            "Laporan transaksi terlalu lambat: {$elapsed}s");
    }

    // =================================================================
    // 13. PREVENT DELETING ACADEMIC YEAR WITH DEPENDENCIES
    // =================================================================

    /** @test */
    public function it_prevents_deleting_academic_year_with_semesters()
    {
        $academicYearUseCase = app(\App\UseCases\AcademicYearUseCase::class);

        $result = $academicYearUseCase->delete($this->academicYearId);
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['message']);
    }

    // =================================================================
    // 14. PREVENT DELETING SEMESTER WITH BILLS
    // =================================================================

    /** @test */
    public function it_prevents_deleting_semester_with_bills()
    {
        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $semesterUseCase = app(\App\UseCases\SemesterUseCase::class);

        // Generate bills
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Try delete semester
        $result = $semesterUseCase->delete($this->semesterId);
        $this->assertFalse($result['status']);
    }

    // =================================================================
    // 15. MEMORY USAGE TEST
    // =================================================================

    /** @test */
    public function it_doesnt_consume_excessive_memory()
    {
        $memBefore = memory_get_usage(true);

        $billUseCase = app(\App\UseCases\BillUseCase::class);
        $billUseCase->autoGenerateBillsForSemester($this->semesterId);

        // Lakukan operasi-operasi berat
        $billUseCase->getPaginated(20, ['month' => 7, 'year' => 2025]);
        $billUseCase->getPaginated(20, ['search' => 'Siswa']);

        $memAfter = memory_get_usage(true);
        $memUsedMB = ($memAfter - $memBefore) / 1024 / 1024;

        // Harus di bawah 128MB
        $this->assertLessThan(128, $memUsedMB,
            "Memory usage terlalu tinggi: {$memUsedMB}MB");
    }

    // =================================================================
    // 16. CHANGE CLASSROOM (PINDAH KELAS)
    // =================================================================

    /** @test */
    public function it_handles_classroom_change_correctly()
    {
        $enrollmentUseCase = app(\App\UseCases\EnrollmentUseCase::class);

        $enrollment = DB::table('student_enrollments')
            ->where('academic_year_id', $this->academicYearId)
            ->where('status', 'aktif')
            ->first();

        $this->assertNotNull($enrollment);

        // Pindah ke kelas lain
        $newClassroomId = $this->classroomIds[0];
        if ($newClassroomId == $enrollment->classroom_id) {
            $newClassroomId = $this->classroomIds[1];
        }

        $result = $enrollmentUseCase->changeClassroom($enrollment->id, $newClassroomId);

        $this->assertTrue($result['status']);

        // Verifikasi
        $updated = DB::table('student_enrollments')->where('id', $enrollment->id)->first();
        $this->assertEquals($newClassroomId, $updated->classroom_id);

        // Student table juga harus update
        $student = DB::table('students')->where('id', $enrollment->student_id)->first();
        $this->assertEquals($newClassroomId, $student->classroom_id);
    }

    // =================================================================
    // HELPER: Seed base data untuk semua test
    // =================================================================

    private function seedBaseData(): void
    {
        // Admin user
        $this->adminUser = \App\Models\User::factory()->create([
            'name'  => 'Admin Tester',
            'email' => 'admin@test.com',
            'role'  => 'admin',
        ]);

        // Jurusan
        $majorNames = ['TKJ', 'RPL', 'MM', 'AK', 'AP'];
        foreach ($majorNames as $name) {
            $this->majorIds[] = DB::table('majors')->insertGetId([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kelas (15 kelas: 5 jurusan × 3 tingkat)
        foreach ($this->majorIds as $majorId) {
            foreach ([10, 11, 12] as $grade) {
                $id = DB::table('classrooms')->insertGetId([
                    'major_id'    => $majorId,
                    'grade_level' => $grade,
                    'name'        => "Kelas {$grade}",
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $this->classroomIds[] = $id;
                $this->classroomMap[$id] = ['grade_level' => $grade, 'major_id' => $majorId];
            }
        }

        // Siswa: setiap siswa HARUS punya family_card_number UNIK (karena UNIQUE constraint di DB)
        $students = [];
        for ($i = 0; $i < self::STUDENT_COUNT; $i++) {
            $students[] = [
                'nisn'               => '00' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                'id_number'          => 'NIS' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'name'               => 'Siswa Test ' . ($i + 1),
                'classroom_id'       => $this->classroomIds[$i % count($this->classroomIds)],
                'family_card_number' => '327' . str_pad($i + 1, 13, '0', STR_PAD_LEFT), // UNIK per siswa
                'qr_code'            => 'QR-' . Str::random(10),
                'status'             => 'aktif',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }
        DB::table('students')->insert($students);
        $this->studentIds = DB::table('students')->pluck('id')->toArray();

        // Tahun Ajaran
        $this->academicYearId = DB::table('academic_years')->insertGetId([
            'name'       => 'TA 2025/2026',
            'start_date' => '2025-07-01',
            'end_date'   => '2026-06-30',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Semester
        $this->semesterId = DB::table('semesters')->insertGetId([
            'academic_year_id' => $this->academicYearId,
            'name'             => 'Ganjil 2025/2026',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Jenis Pembayaran
        foreach (['SPP', 'Uang Gedung', 'Kegiatan'] as $pt) {
            $this->paymentTypeIds[] = DB::table('payment_types')->insertGetId([
                'name'       => $pt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tarif Pembayaran (per grade level, per payment type)
        $amounts = [250000, 500000, 100000];
        foreach ([10, 11, 12] as $grade) {
            foreach ($this->paymentTypeIds as $idx => $ptId) {
                DB::table('payment_rates')->insert([
                    'academic_year_id' => $this->academicYearId,
                    'payment_type_id'  => $ptId,
                    'grade_level'      => $grade,
                    'major_id'         => null,
                    'amount'           => $amounts[$idx] + ($grade * 10000),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        // Enrollment semua siswa
        $enrollments = [];
        foreach ($this->studentIds as $studentId) {
            $student = DB::table('students')->where('id', $studentId)->first();
            $enrollments[] = [
                'student_id'       => $studentId,
                'classroom_id'     => $student->classroom_id,
                'academic_year_id' => $this->academicYearId,
                'status'           => 'aktif',
                'enrolled_at'      => '2025-07-15',
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }
        DB::table('student_enrollments')->insert($enrollments);
    }
}
