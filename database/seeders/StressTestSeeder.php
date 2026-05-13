<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * StressTestSeeder
 *
 * Mengisi database dengan volume data besar untuk stress test.
 * Jalankan: php artisan db:seed --class=StressTestSeeder
 *
 * ⚠️ PERINGATAN: Seeder ini menambah data BANYAK.
 *    Pastikan Anda menjalankan di environment testing/development.
 */
class StressTestSeeder extends Seeder
{
    // ===== KONFIGURASI VOLUME =====
    const TOTAL_MAJORS      = 5;
    const CLASSES_PER_MAJOR = 6;   // 5 * 6 = 30 kelas
    const TOTAL_STUDENTS    = 1000;
    const FAMILY_GROUPS     = 400; // 400 KK, rata-rata 2.5 anak per KK
    const PAYMENT_TYPES     = 3;
    const MONTHS_PER_SEM    = 6;

    private array $majorIds       = [];
    private array $classroomIds   = [];
    private array $classroomMap   = []; // id => [grade_level, major_id]
    private array $studentIds     = [];
    private array $familyCards    = [];
    private int   $academicYearId;
    private int   $semesterId;
    private array $paymentTypeIds = [];
    private array $rateMap        = [];

    public function run(): void
    {
        $this->command->info('🔥 SAKTI Stress Test Seeder dimulai...');
        $startTime = microtime(true);

        $this->seedAdmin();
        $this->seedMajors();
        $this->seedClassrooms();
        $this->seedStudents();
        $this->seedAcademicYear();
        $this->seedSemesters();
        $this->seedPaymentTypes();
        $this->seedPaymentRates();
        $this->seedEnrollments();
        $this->seedBillsAndPayments();
        $this->seedTransactions();

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->command->info("✅ Selesai dalam {$elapsed} detik!");
        $this->printSummary();
    }

    private function seedAdmin(): void
    {
        $this->command->info('  → Membuat admin user...');
        if (!DB::table('users')->where('email', 'admin@sakti.test')->exists()) {
            DB::table('users')->insert([
                'name'     => 'Admin Stress Test',
                'email'    => 'admin@sakti.test',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedMajors(): void
    {
        $this->command->info('  → Membuat ' . self::TOTAL_MAJORS . ' jurusan...');
        $names = ['Teknik Komputer & Jaringan', 'Rekayasa Perangkat Lunak', 'Multimedia', 'Akuntansi', 'Administrasi Perkantoran'];

        foreach ($names as $name) {
            $exists = DB::table('majors')->where('name', $name)->first();
            if ($exists) {
                $this->majorIds[] = $exists->id;
            } else {
                $this->majorIds[] = DB::table('majors')->insertGetId([
                    'name'       => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedClassrooms(): void
    {
        $totalClasses = self::TOTAL_MAJORS * self::CLASSES_PER_MAJOR;
        $this->command->info("  → Membuat {$totalClasses} kelas...");

        $gradeLevels = [10, 10, 11, 11, 12, 12]; // 2 kelas per tingkat per jurusan

        foreach ($this->majorIds as $majorId) {
            foreach ($gradeLevels as $idx => $grade) {
                $suffix = chr(65 + $idx); // A, B, C, D, E, F
                $name = "Kelas {$grade}-{$suffix}";

                $exists = DB::table('classrooms')
                    ->where('major_id', $majorId)
                    ->where('name', $name)
                    ->first();

                if ($exists) {
                    $this->classroomIds[] = $exists->id;
                    $this->classroomMap[$exists->id] = ['grade_level' => $grade, 'major_id' => $majorId];
                } else {
                    $id = DB::table('classrooms')->insertGetId([
                        'major_id'    => $majorId,
                        'grade_level' => $grade,
                        'name'        => $name,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                    $this->classroomIds[] = $id;
                    $this->classroomMap[$id] = ['grade_level' => $grade, 'major_id' => $majorId];
                }
            }
        }
    }

    private function seedStudents(): void
    {
        $this->command->info('  → Membuat ' . self::TOTAL_STUDENTS . ' siswa...');

        // Buat family card numbers
        for ($i = 0; $i < self::FAMILY_GROUPS; $i++) {
            $this->familyCards[] = '327' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        }

        $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fajar', 'Gita', 'Hani', 'Irfan', 'Joko',
            'Kartika', 'Lina', 'Muhamad', 'Nadia', 'Omar', 'Putri', 'Qori', 'Rina', 'Sari', 'Tono',
            'Umar', 'Vina', 'Wati', 'Xena', 'Yusuf', 'Zahra', 'Andi', 'Bayu', 'Cahya', 'Dian',
            'Elsa', 'Firman', 'Galuh', 'Hendra', 'Intan', 'Joni', 'Kiki', 'Laras', 'Mega', 'Nisa'];
        $lastNames = ['Pratama', 'Santoso', 'Wijaya', 'Kusuma', 'Hidayat', 'Nugraha', 'Saputra',
            'Ramadhan', 'Permana', 'Setiawan', 'Suryadi', 'Wibowo', 'Hartono', 'Suharto', 'Sudrajat'];

        $batch = [];
        $batchSize = 200;

        for ($i = 0; $i < self::TOTAL_STUDENTS; $i++) {
            $name = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
            $nisn = '00' . str_pad($i + 1, 8, '0', STR_PAD_LEFT);
            $classroomId = $this->classroomIds[array_rand($this->classroomIds)];
            $familyCard = $this->familyCards[array_rand($this->familyCards)];

            $batch[] = [
                'nisn'               => $nisn,
                'id_number'          => 'NIS' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'name'               => $name,
                'classroom_id'       => $classroomId,
                'family_card_number' => $familyCard,
                'qr_code'            => $nisn . '-' . Str::random(8),
                'status'             => 'aktif',
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('students')->insert($batch);
                $batch = [];
                $this->command->info("    ... {$i} siswa dibuat");
            }
        }

        if (!empty($batch)) {
            DB::table('students')->insert($batch);
        }

        // Ambil semua ID siswa
        $this->studentIds = DB::table('students')->pluck('id')->toArray();
        $this->command->info("    Total siswa di DB: " . count($this->studentIds));
    }

    private function seedAcademicYear(): void
    {
        $this->command->info('  → Membuat tahun ajaran...');

        $existing = DB::table('academic_years')->where('name', 'TA 2025/2026 (Stress)')->first();
        if ($existing) {
            $this->academicYearId = $existing->id;
            return;
        }

        $this->academicYearId = DB::table('academic_years')->insertGetId([
            'name'       => 'TA 2025/2026 (Stress)',
            'start_date' => '2025-07-01',
            'end_date'   => '2026-06-30',
            'is_active'  => false,  // Jangan override active AY yang sudah ada
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedSemesters(): void
    {
        $this->command->info('  → Membuat semester...');

        $existing = DB::table('semesters')
            ->where('academic_year_id', $this->academicYearId)
            ->where('name', 'Ganjil (Stress)')
            ->first();

        if ($existing) {
            $this->semesterId = $existing->id;
            return;
        }

        $this->semesterId = DB::table('semesters')->insertGetId([
            'academic_year_id' => $this->academicYearId,
            'name'             => 'Ganjil (Stress)',
            'start_month'      => 7,
            'end_month'        => 12,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    private function seedPaymentTypes(): void
    {
        $this->command->info('  → Membuat jenis pembayaran...');
        $types = ['SPP Bulanan', 'Uang Gedung', 'Kegiatan Siswa'];

        foreach ($types as $type) {
            $existing = DB::table('payment_types')->where('name', $type)->first();
            if ($existing) {
                $this->paymentTypeIds[] = $existing->id;
            } else {
                $this->paymentTypeIds[] = DB::table('payment_types')->insertGetId([
                    'name'       => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedPaymentRates(): void
    {
        $this->command->info('  → Membuat tarif pembayaran...');

        $amounts = [250000, 500000, 100000]; // per payment type

        foreach ([10, 11, 12] as $gradeLevel) {
            foreach ($this->paymentTypeIds as $idx => $ptId) {
                $exists = DB::table('payment_rates')
                    ->where('academic_year_id', $this->academicYearId)
                    ->where('payment_type_id', $ptId)
                    ->where('grade_level', $gradeLevel)
                    ->whereNull('major_id')
                    ->first();

                if ($exists) {
                    $this->rateMap["{$gradeLevel}_{$ptId}"] = $exists->amount;
                    continue;
                }

                // Tarif bervariasi sedikit per kelas
                $amount = $amounts[$idx] + ($gradeLevel * 10000);

                DB::table('payment_rates')->insert([
                    'academic_year_id' => $this->academicYearId,
                    'payment_type_id'  => $ptId,
                    'grade_level'      => $gradeLevel,
                    'major_id'         => null,
                    'amount'           => $amount,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                $this->rateMap["{$gradeLevel}_{$ptId}"] = $amount;
            }
        }
    }

    private function seedEnrollments(): void
    {
        $this->command->info('  → Membuat enrollment untuk ' . count($this->studentIds) . ' siswa...');

        $batch = [];
        $batchSize = 200;

        foreach ($this->studentIds as $idx => $studentId) {
            $student = DB::table('students')->where('id', $studentId)->first();
            if (!$student) continue;

            // Cek apakah sudah ada enrollment
            $exists = DB::table('student_enrollments')
                ->where('student_id', $studentId)
                ->where('academic_year_id', $this->academicYearId)
                ->exists();

            if ($exists) continue;

            $batch[] = [
                'student_id'       => $studentId,
                'classroom_id'     => $student->classroom_id,
                'academic_year_id' => $this->academicYearId,
                'status'           => 'aktif',
                'enrolled_at'      => '2025-07-15',
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('student_enrollments')->insert($batch);
                $batch = [];
                $this->command->info("    ... {$idx} enrollment dibuat");
            }
        }

        if (!empty($batch)) {
            DB::table('student_enrollments')->insert($batch);
        }
    }

    private function seedBillsAndPayments(): void
    {
        $this->command->info('  → Membuat tagihan & pembayaran massal...');

        $months = [7, 8, 9, 10, 11, 12]; // semester ganjil
        $billBatch = [];
        $batchSize = 500;
        $totalBills = 0;
        $totalPayments = 0;

        $adminId = DB::table('users')->where('email', 'admin@sakti.test')->value('id');

        foreach ($this->studentIds as $idx => $studentId) {
            $student = DB::table('students')->where('id', $studentId)->first();
            if (!$student) continue;

            $classroom = $this->classroomMap[$student->classroom_id] ?? null;
            if (!$classroom) continue;

            $gradeLevel = $classroom['grade_level'];

            // Hitung total amount per bulan
            $totalAmount = 0;
            $billItems = [];
            foreach ($this->paymentTypeIds as $ptId) {
                $key = "{$gradeLevel}_{$ptId}";
                if (isset($this->rateMap[$key])) {
                    $totalAmount += $this->rateMap[$key];
                    $billItems[] = ['payment_type_id' => $ptId, 'amount' => $this->rateMap[$key]];
                }
            }

            if ($totalAmount <= 0) continue;

            foreach ($months as $m) {
                $year = $m >= 7 ? 2025 : 2026;

                // Cek duplikat
                $exists = DB::table('bills')
                    ->where('student_id', $studentId)
                    ->where('month', $m)
                    ->where('year', $year)
                    ->exists();

                if ($exists) continue;

                // Random status: 60% unpaid, 35% paid, 5% cancelled
                $rand = rand(1, 100);
                $status = $rand <= 60 ? 'unpaid' : ($rand <= 95 ? 'paid' : 'cancelled');

                $dueDate = Carbon::createFromDate($year, $m, 1)->endOfMonth()->toDateString();

                $billId = DB::table('bills')->insertGetId([
                    'student_id'       => $studentId,
                    'academic_year_id' => $this->academicYearId,
                    'semester_id'      => $this->semesterId,
                    'month'            => $m,
                    'year'             => $year,
                    'total_amount'     => $totalAmount,
                    'status'           => $status,
                    'due_date'         => $dueDate,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Bill items
                $items = [];
                foreach ($billItems as $bi) {
                    $items[] = [
                        'bill_id'         => $billId,
                        'student_id'      => $studentId,
                        'payment_type_id' => $bi['payment_type_id'],
                        'amount'          => $bi['amount'],
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
                DB::table('bill_items')->insert($items);

                $totalBills++;

                // Jika paid, buat payment record
                if ($status === 'paid') {
                    $paymentDate = Carbon::createFromDate($year, $m, rand(1, 28))->toDateString();
                    $methods = ['cash', 'transfer', 'other'];

                    $paymentId = DB::table('payments')->insertGetId([
                        'bill_id'          => $billId,
                        'amount'           => $totalAmount,
                        'payment_method'   => $methods[array_rand($methods)],
                        'payment_date'     => $paymentDate,
                        'reference_number' => 'REF-' . strtoupper(Str::random(8)),
                        'verified_by'      => $adminId,
                        'notes'            => null,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    // Auto transaction
                    DB::table('transactions')->insert([
                        'date'        => $paymentDate,
                        'type'        => 'income',
                        'category'    => 'SPP',
                        'description' => "Pemb. SPP " . Carbon::createFromDate($year, $m, 1)->translatedFormat('F Y'),
                        'amount'      => $totalAmount,
                        'payment_id'  => $paymentId,
                        'recorded_by' => $adminId,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);

                    $totalPayments++;
                }
            }

            if ($idx % 200 === 0 && $idx > 0) {
                $this->command->info("    ... Proses siswa #{$idx}: {$totalBills} tagihan, {$totalPayments} pembayaran");
            }
        }

        $this->command->info("    Total tagihan dibuat: {$totalBills}");
        $this->command->info("    Total pembayaran dibuat: {$totalPayments}");
    }

    private function seedTransactions(): void
    {
        $this->command->info('  → Membuat transaksi pengeluaran acak...');

        $adminId = DB::table('users')->where('email', 'admin@sakti.test')->value('id');
        $categories = ['Gaji Guru', 'Listrik & Air', 'ATK', 'Perbaikan', 'Kegiatan Sekolah', 'Konsumsi', 'Transportasi'];
        $batch = [];

        for ($i = 0; $i < 500; $i++) {
            $month = rand(7, 12);
            $batch[] = [
                'date'        => Carbon::createFromDate(2025, $month, rand(1, 28))->toDateString(),
                'type'        => 'expense',
                'category'    => $categories[array_rand($categories)],
                'description' => 'Pengeluaran ' . $categories[array_rand($categories)] . ' bulan ' . $month,
                'amount'      => rand(1, 50) * 100000,
                'payment_id'  => null,
                'recorded_by' => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('transactions')->insert($batch);
        $this->command->info("    Total transaksi expense: " . count($batch));
    }

    private function printSummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 RINGKASAN DATA:');
        $this->command->table(
            ['Tabel', 'Jumlah Record'],
            [
                ['majors', DB::table('majors')->count()],
                ['classrooms', DB::table('classrooms')->count()],
                ['students', DB::table('students')->count()],
                ['student_enrollments', DB::table('student_enrollments')->count()],
                ['academic_years', DB::table('academic_years')->count()],
                ['semesters', DB::table('semesters')->count()],
                ['payment_types', DB::table('payment_types')->count()],
                ['payment_rates', DB::table('payment_rates')->count()],
                ['bills', DB::table('bills')->count()],
                ['bill_items', DB::table('bill_items')->count()],
                ['payments', DB::table('payments')->count()],
                ['transactions', DB::table('transactions')->count()],
            ]
        );
    }
}
