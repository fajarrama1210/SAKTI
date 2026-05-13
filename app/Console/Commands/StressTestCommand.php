<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * ============================================================
 * SAKTI STRESS TEST ARTISAN COMMAND
 * ============================================================
 *
 * Menjalankan serangkaian stress test langsung pada database
 * dan mengukur response time serta memory usage.
 *
 * Jalankan: php artisan stress:test
 * Jalankan: php artisan stress:test --seed  (seed data dulu)
 * ============================================================
 */
class StressTestCommand extends Command
{
    protected $signature = 'stress:test
        {--seed : Jalankan StressTestSeeder sebelum test}
        {--iterations=3 : Jumlah iterasi per test}';

    protected $description = '🔥 Jalankan stress test pada komponen utama SAKTI';

    private array $results = [];

    public function handle(): int
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   🔥  SAKTI STRESS TEST RUNNER  🔥      ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->newLine();

        // Opsi seed
        if ($this->option('seed')) {
            $this->info('📦 Menjalankan StressTestSeeder...');
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\StressTestSeeder']);
            $this->newLine();
        }

        // Cek data ada
        $studentCount = DB::table('students')->count();
        $billCount = DB::table('bills')->count();
        $this->info("📊 Data saat ini: {$studentCount} siswa, {$billCount} tagihan");

        if ($studentCount < 10) {
            $this->error('❌ Data terlalu sedikit! Jalankan dengan --seed terlebih dahulu.');
            return 1;
        }

        $iterations = (int) $this->option('iterations');
        $this->info("🔄 Iterasi per test: {$iterations}");
        $this->newLine();

        // ====== RUN ALL TESTS ======
        $this->runTest('Dashboard Query', $iterations, function () {
            $this->testDashboardQuery();
        });

        $this->runTest('Student Paginated List', $iterations, function () {
            $this->testStudentList();
        });

        $this->runTest('Student Search', $iterations, function () {
            $this->testStudentSearch();
        });

        $this->runTest('Bill Recap (Paginated)', $iterations, function () {
            $this->testBillRecap();
        });

        $this->runTest('Student Bill Detail', $iterations, function () {
            $this->testStudentBillDetail();
        });

        $this->runTest('Sibling Lookup (KK)', $iterations, function () {
            $this->testSiblingLookup();
        });

        $this->runTest('Payment Recording', $iterations, function () {
            $this->testPaymentRecording();
        });

        $this->runTest('Transaction Report', $iterations, function () {
            $this->testTransactionReport();
        });

        $this->runTest('Enrollment List', $iterations, function () {
            $this->testEnrollmentList();
        });

        $this->runTest('Payment Report (UseCase)', $iterations, function () {
            $this->testPaymentReport();
        });

        // ====== LAPORAN HASIL ======
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════╗');
        $this->info('║                     📋 LAPORAN HASIL                            ║');
        $this->info('╚══════════════════════════════════════════════════════════════════╝');

        $tableData = [];
        $allPassed = true;

        foreach ($this->results as $name => $data) {
            $avgMs = round($data['avg_ms'], 1);
            $minMs = round($data['min_ms'], 1);
            $maxMs = round($data['max_ms'], 1);
            $memMB = round($data['mem_mb'], 2);
            $status = $data['status'];

            // Threshold: warning > 1s, fail > 3s
            $statusIcon = '✅';
            if ($status === 'ERROR') {
                $statusIcon = '❌';
                $allPassed = false;
            } elseif ($avgMs > 3000) {
                $statusIcon = '❌ SLOW';
                $allPassed = false;
            } elseif ($avgMs > 1000) {
                $statusIcon = '⚠️ WARN';
            }

            $tableData[] = [
                $name,
                "{$avgMs}ms",
                "{$minMs}ms",
                "{$maxMs}ms",
                "{$memMB}MB",
                $statusIcon,
            ];
        }

        $this->table(
            ['Test', 'Avg Time', 'Min', 'Max', 'Memory', 'Status'],
            $tableData
        );

        $this->newLine();

        if ($allPassed) {
            $this->info('🎉 Semua test PASSED! Sistem berjalan dengan baik di bawah beban.');
        } else {
            $this->warn('⚠️ Beberapa test menunjukkan masalah performa. Lihat detail di atas.');
        }

        // Memory total
        $peakMB = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $this->info("💾 Peak Memory Usage: {$peakMB}MB");
        $this->newLine();

        return $allPassed ? 0 : 1;
    }

    // ====== TEST METHODS ======

    private function testDashboardQuery(): void
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        DB::table('students')->count();
        DB::table('classrooms')->count();
        DB::table('majors')->count();
        DB::table('academic_years')->where('is_active', true)->first();

        DB::table('transactions')
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        DB::table('transactions')
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // Chart data: 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            DB::table('transactions')
                ->where('type', 'income')
                ->whereMonth('date', $d->month)
                ->whereYear('date', $d->year)
                ->sum('amount');
            DB::table('transactions')
                ->where('type', 'expense')
                ->whereMonth('date', $d->month)
                ->whereYear('date', $d->year)
                ->sum('amount');
        }

        // Recent payments
        DB::table('payments as p')
            ->join('bills as b', 'p.bill_id', '=', 'b.id')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->select('p.*', 's.family_card_number', 's.name as student_name')
            ->orderBy('p.payment_date', 'desc')
            ->limit(5)
            ->get();
    }

    private function testStudentList(): void
    {
        DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.nisn', 's.id_number', 's.name', 's.family_card_number', 's.status', 'c.grade_level', 'm.name as major_name')
            ->orderBy('s.id', 'desc')
            ->paginate(10);
    }

    private function testStudentSearch(): void
    {
        $keyword = 'Ahmad';
        DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.nisn', 's.name', 's.family_card_number', 's.status', 'c.grade_level', 'm.name as major_name')
            ->where(function ($q) use ($keyword) {
                $q->where('s.name', 'LIKE', "%{$keyword}%")
                  ->orWhere('s.nisn', 'LIKE', "%{$keyword}%");
            })
            ->where('s.status', 'aktif')
            ->limit(20)
            ->get();
    }

    private function testBillRecap(): void
    {
        DB::table('bills as b')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->join('semesters as sm', 'b.semester_id', '=', 'sm.id')
            ->join('academic_years as ay', 'b.academic_year_id', '=', 'ay.id')
            ->select('b.id', 's.name', 's.nisn', 'c.grade_level', 'm.name as major_name',
                     'ay.name as ay_name', 'sm.name as sem_name', 'b.month', 'b.year',
                     'b.total_amount', 'b.status', 'b.due_date')
            ->where('b.month', 10)
            ->where('b.year', 2025)
            ->orderBy('b.year', 'desc')
            ->orderBy('b.month', 'desc')
            ->orderBy('s.name', 'asc')
            ->paginate(20);
    }

    private function testStudentBillDetail(): void
    {
        $studentId = DB::table('students')->inRandomOrder()->value('id');
        if (!$studentId) return;

        DB::table('bills as b')
            ->join('semesters as sm', 'b.semester_id', '=', 'sm.id')
            ->join('academic_years as ay', 'b.academic_year_id', '=', 'ay.id')
            ->select('b.*', 'sm.name as semester_name', 'ay.name as academic_year_name')
            ->where('b.student_id', $studentId)
            ->orderBy('b.year', 'asc')
            ->orderBy('b.month', 'asc')
            ->get();
    }

    private function testSiblingLookup(): void
    {
        $familyCard = DB::table('students')
            ->select('family_card_number', DB::raw('COUNT(*) as cnt'))
            ->groupBy('family_card_number')
            ->having('cnt', '>', 1)
            ->first();

        if (!$familyCard) return;

        DB::table('students as s')
            ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.name', 's.nisn', 's.status', 'c.grade_level', 'm.name as major_name')
            ->where('s.family_card_number', $familyCard->family_card_number)
            ->get();
    }

    private function testPaymentRecording(): void
    {
        $bill = DB::table('bills')->where('status', 'unpaid')->first();
        if (!$bill) return;

        // Simulasi query path tanpa mutate database
        DB::table('bills as b')
            ->join('students as s', 'b.student_id', '=', 's.id')
            ->select('b.*', 's.family_card_number')
            ->where('b.id', $bill->id)
            ->first();

        DB::table('students')
            ->where('family_card_number', $bill->student_id)
            ->pluck('id');
    }

    private function testTransactionReport(): void
    {
        DB::table('transactions as t')
            ->leftJoin('users as u', 't.recorded_by', '=', 'u.id')
            ->select('t.*', 'u.name as recorded_by_name')
            ->whereMonth('t.date', 10)
            ->whereYear('t.date', 2025)
            ->orderBy('t.date', 'asc')
            ->get();
    }

    private function testEnrollmentList(): void
    {
        $ayId = DB::table('academic_years')->value('id');
        if (!$ayId) return;

        DB::table('student_enrollments as e')
            ->join('students as s', 'e.student_id', '=', 's.id')
            ->join('classrooms as c', 'e.classroom_id', '=', 'c.id')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->join('academic_years as ay', 'e.academic_year_id', '=', 'ay.id')
            ->select('e.*', 's.nisn', 's.name', 'c.name as classroom_name', 'c.grade_level',
                     'm.name as major_name', 'ay.name as ay_name')
            ->where('e.academic_year_id', $ayId)
            ->orderBy('c.grade_level', 'asc')
            ->orderBy('s.name', 'asc')
            ->paginate(20);
    }

    private function testPaymentReport(): void
    {
        $ayId = DB::table('academic_years')->value('id');
        if (!$ayId) return;

        $reportUseCase = app(\App\UseCases\ReportUseCase::class);
        $reportUseCase->getPaymentReport([
            'academic_year_id' => $ayId,
            'month' => 10,
            'year' => 2025,
        ]);
    }

    // ====== RUNNER HELPER ======

    private function runTest(string $name, int $iterations, callable $fn): void
    {
        $this->info("⏱  Testing: {$name} ({$iterations}x)...");

        $times = [];
        $memBefore = memory_get_usage(true);
        $status = 'OK';

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            try {
                $fn();
            } catch (\Throwable $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $status = 'ERROR';
                break;
            }
            $elapsed = (microtime(true) - $start) * 1000; // ms
            $times[] = $elapsed;
        }

        $memAfter = memory_get_usage(true);

        if (empty($times)) {
            $this->results[$name] = [
                'avg_ms' => 0, 'min_ms' => 0, 'max_ms' => 0,
                'mem_mb' => 0, 'status' => 'ERROR',
            ];
            return;
        }

        $avg = array_sum($times) / count($times);
        $min = min($times);
        $max = max($times);
        $memMB = ($memAfter - $memBefore) / 1024 / 1024;

        $this->results[$name] = [
            'avg_ms' => $avg,
            'min_ms' => $min,
            'max_ms' => $max,
            'mem_mb' => $memMB,
            'status' => $status,
        ];

        $avgMs = round($avg, 1);
        $this->info("   → Avg: {$avgMs}ms");
    }
}
