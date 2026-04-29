<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom 'name' ke classrooms agar punya identitas jelas
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('name', 60)->nullable()->after('id');
        });

        // Auto-fill nama kelas berdasarkan data yang sudah ada
        $classrooms = DB::table('classrooms as c')
            ->join('majors as m', 'c.major_id', '=', 'm.id')
            ->select('c.id', 'c.grade_level', 'm.name as major_name')
            ->get();

        foreach ($classrooms as $classroom) {
            DB::table('classrooms')->where('id', $classroom->id)->update([
                'name' => $classroom->grade_level . ' ' . $classroom->major_name,
            ]);
        }

        // Set NOT NULL setelah di-fill
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('name', 60)->nullable(false)->change();
        });

        // 2. Buat tabel student_enrollments
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['aktif', 'naik_kelas', 'lulus', 'do', 'pindah'])->default('aktif');
            $table->date('enrolled_at'); // tanggal masuk ke kelas ini
            $table->date('exited_at')->nullable(); // tanggal keluar (naik kelas/lulus/DO)
            $table->string('exit_reason', 255)->nullable(); // alasan keluar
            $table->timestamps();

            // 1 siswa hanya punya 1 enrollment per tahun ajaran
            $table->unique(['student_id', 'academic_year_id'], 'enrollments_student_ay_unique');
        });

        // 3. Migrasi data: buat enrollment dari students.classroom_id yang ada
        //    Menggunakan tahun ajaran aktif (jika ada)
        $activeAY = DB::table('academic_years')->where('is_active', true)->first();

        if ($activeAY) {
            $students = DB::table('students')
                ->select('id', 'classroom_id', 'status', 'created_at')
                ->get();

            $enrollments = [];
            foreach ($students as $student) {
                $enrollmentStatus = 'aktif';
                $exitedAt = null;
                if ($student->status === 'lulus') {
                    $enrollmentStatus = 'lulus';
                    $exitedAt = now()->toDateString();
                } elseif ($student->status === 'keluar') {
                    $enrollmentStatus = 'do';
                    $exitedAt = now()->toDateString();
                }

                $enrollments[] = [
                    'student_id' => $student->id,
                    'classroom_id' => $student->classroom_id,
                    'academic_year_id' => $activeAY->id,
                    'status' => $enrollmentStatus,
                    'enrolled_at' => $student->created_at ? date('Y-m-d', strtotime($student->created_at)) : $activeAY->start_date,
                    'exited_at' => $exitedAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($enrollments)) {
                // Insert dalam batch
                foreach (array_chunk($enrollments, 100) as $chunk) {
                    DB::table('student_enrollments')->insert($chunk);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
