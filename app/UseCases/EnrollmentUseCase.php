<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use App\Entities\ResponseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentUseCase
{
    /**
     * Ambil semua enrollment per tahun ajaran (dengan info siswa + kelas + jurusan)
     */
    public function getByAcademicYear($academicYearId, $perPage = 15, $filters = [])
    {
        $query = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'e.student_id', '=', 's.id')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'e.academic_year_id', '=', 'ay.id')
            ->select(
                'e.id',
                'e.student_id',
                'e.classroom_id',
                'e.academic_year_id',
                'e.status',
                'e.enrolled_at',
                'e.exited_at',
                'e.exit_reason',
                's.nisn',
                's.name as student_name',
                's.family_card_number',
                'c.name as classroom_name',
                'c.grade_level',
                'm.name as major_name',
                'ay.name as academic_year_name'
            )
            ->where('e.academic_year_id', $academicYearId);

        // Filter status
        if (!empty($filters['status'])) {
            $query->where('e.status', $filters['status']);
        }

        // Filter kelas
        if (!empty($filters['classroom_id'])) {
            $query->where('e.classroom_id', $filters['classroom_id']);
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
            ->orderBy('c.grade_level', 'asc')
            ->orderBy('c.name', 'asc')
            ->orderBy('s.name', 'asc')
            ->paginate($perPage)
            ->appends($filters + ['academic_year_id' => $academicYearId]);
    }

    /**
     * Ambil enrollment aktif untuk siswa tertentu di tahun ajaran tertentu
     */
    public function getActiveEnrollment($studentId, $academicYearId)
    {
        return DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select('e.*', 'c.name as classroom_name', 'c.grade_level', 'm.name as major_name')
            ->where('e.student_id', $studentId)
            ->where('e.academic_year_id', $academicYearId)
            ->first();
    }

    /**
     * Ambil semua enrollment siswa (histori lintas tahun ajaran)
     */
    public function getStudentHistory($studentId)
    {
        return DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'e.academic_year_id', '=', 'ay.id')
            ->select('e.*', 'c.name as classroom_name', 'c.grade_level', 'm.name as major_name', 'ay.name as academic_year_name')
            ->where('e.student_id', $studentId)
            ->orderBy('ay.start_date', 'desc')
            ->get();
    }

    /**
     * Ambil semua siswa aktif di tahun ajaran tertentu (untuk generate tagihan)
     */
    public function getActiveStudentsForBilling($academicYearId)
    {
        return DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
            ->join(DatabaseEntity::TBL_STUDENTS . ' as s', 'e.student_id', '=', 's.id')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 'e.classroom_id', '=', 'c.id')
            ->select(
                's.id as student_id',
                's.family_card_number',
                'c.grade_level',
                'c.major_id',
                'e.id as enrollment_id'
            )
            ->where('e.academic_year_id', $academicYearId)
            ->where('e.status', 'aktif')
            ->get();
    }

    /**
     * Daftarkan siswa ke kelas + tahun ajaran
     */
    public function enroll(array $data): array
    {
        DB::beginTransaction();
        try {
            // Cek apakah siswa sudah punya enrollment di tahun ajaran ini
            $existing = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)
                ->where('student_id', $data['student_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->first();

            if ($existing) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Siswa sudah terdaftar di tahun ajaran ini.'];
            }

            DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->insert([
                'student_id' => $data['student_id'],
                'classroom_id' => $data['classroom_id'],
                'academic_year_id' => $data['academic_year_id'],
                'status' => 'aktif',
                'enrolled_at' => $data['enrolled_at'] ?? now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update classroom_id di students juga (untuk backward compat)
            DB::table(DatabaseEntity::TBL_STUDENTS)
                ->where('id', $data['student_id'])
                ->update([
                    'classroom_id' => $data['classroom_id'],
                    'status' => 'aktif',
                    'updated_at' => now(),
                ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Enrollment Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Pindah kelas (dalam tahun ajaran yang sama)
     */
    public function changeClassroom($enrollmentId, $newClassroomId): array
    {
        DB::beginTransaction();
        try {
            $enrollment = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->where('id', $enrollmentId)->first();
            if (!$enrollment) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Enrollment tidak ditemukan.'];
            }

            DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->where('id', $enrollmentId)->update([
                'classroom_id' => $newClassroomId,
                'updated_at' => now(),
            ]);

            // Update classroom_id di students juga
            DB::table(DatabaseEntity::TBL_STUDENTS)
                ->where('id', $enrollment->student_id)
                ->update(['classroom_id' => $newClassroomId, 'updated_at' => now()]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ChangeClassroom Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Proses DO / Keluar Siswa
     * - Update enrollment status → 'do'
     * - Cancel tagihan UNPAID setelah bulan efektif DO
     * - Update status siswa → 'keluar'
     */
    public function processDropout($enrollmentId, array $data): array
    {
        DB::beginTransaction();
        try {
            $enrollment = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS . ' as e')
                ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'e.academic_year_id', '=', 'ay.id')
                ->select('e.*', 'ay.start_date', 'ay.end_date')
                ->where('e.id', $enrollmentId)
                ->first();

            if (!$enrollment) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Enrollment tidak ditemukan.'];
            }

            $exitDate = $data['exit_date'];
            $exitMonth = (int) date('n', strtotime($exitDate));
            $exitYear = (int) date('Y', strtotime($exitDate));

            // 1. Update enrollment
            DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->where('id', $enrollmentId)->update([
                'status' => 'do',
                'exited_at' => $exitDate,
                'exit_reason' => $data['exit_reason'] ?? 'Dikeluarkan',
                'updated_at' => now(),
            ]);

            // 2. Update student status
            DB::table(DatabaseEntity::TBL_STUDENTS)
                ->where('id', $enrollment->student_id)
                ->update(['status' => 'keluar', 'updated_at' => now()]);

            // 3. Cancel tagihan UNPAID SETELAH bulan DO
            //    Tagihan di bulan DO dan sebelumnya tetap ada (tanggungan)
            $billsToCancel = DB::table(DatabaseEntity::TBL_BILLS)
                ->where('student_id', $enrollment->student_id)
                ->where('academic_year_id', $enrollment->academic_year_id)
                ->where('status', 'unpaid')
                ->where(function ($q) use ($exitMonth, $exitYear) {
                    $q->where('year', '>', $exitYear)
                      ->orWhere(function ($q2) use ($exitMonth, $exitYear) {
                          $q2->where('year', $exitYear)->where('month', '>', $exitMonth);
                      });
                })
                ->pluck('id');

            if ($billsToCancel->isNotEmpty()) {
                DB::table(DatabaseEntity::TBL_BILLS)
                    ->whereIn('id', $billsToCancel)
                    ->update(['status' => 'cancelled', 'updated_at' => now()]);
            }

            DB::commit();
            return [
                'status' => true,
                'cancelled_bills' => $billsToCancel->count(),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ProcessDropout Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Proses Kelulusan Massal
     * - Update enrollment status → 'lulus' untuk siswa terpilih
     * - Update status siswa → 'lulus'
     * - Return daftar siswa yang punya tunggakan (untuk warning)
     */
    public function processGraduation($academicYearId, array $studentIds, $graduationDate = null): array
    {
        DB::beginTransaction();
        try {
            $gradDate = $graduationDate ?? now()->toDateString();

            // Cek tunggakan per siswa
            $studentsWithDebt = [];
            $graduatedCount = 0;

            foreach ($studentIds as $studentId) {
                $unpaidCount = DB::table(DatabaseEntity::TBL_BILLS)
                    ->where('student_id', $studentId)
                    ->where('academic_year_id', $academicYearId)
                    ->where('status', 'unpaid')
                    ->count();

                if ($unpaidCount > 0) {
                    $student = DB::table(DatabaseEntity::TBL_STUDENTS)->where('id', $studentId)->first();
                    $studentsWithDebt[] = [
                        'id' => $studentId,
                        'name' => $student->name ?? 'Unknown',
                        'unpaid_count' => $unpaidCount,
                    ];
                }

                // Tetap luluskan, tapi catat peringatan
                DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)
                    ->where('student_id', $studentId)
                    ->where('academic_year_id', $academicYearId)
                    ->update([
                        'status' => 'lulus',
                        'exited_at' => $gradDate,
                        'exit_reason' => $unpaidCount > 0 ? 'Lulus dengan tunggakan' : 'Lulus',
                        'updated_at' => now(),
                    ]);

                DB::table(DatabaseEntity::TBL_STUDENTS)
                    ->where('id', $studentId)
                    ->update(['status' => 'lulus', 'updated_at' => now()]);

                $graduatedCount++;
            }

            DB::commit();
            return [
                'status' => true,
                'graduated_count' => $graduatedCount,
                'students_with_debt' => $studentsWithDebt,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ProcessGraduation Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Promosi massal: naik kelas semua siswa aktif dari TA lama ke TA baru
     */
    public function promoteStudents($fromAcademicYearId, $toAcademicYearId, array $promotionMap): array
    {
        DB::beginTransaction();
        try {
            $promotedCount = 0;

            // promotionMap = [['student_id' => 1, 'new_classroom_id' => 5], ...]
            foreach ($promotionMap as $item) {
                // Tandai enrollment lama sebagai 'naik_kelas'
                DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)
                    ->where('student_id', $item['student_id'])
                    ->where('academic_year_id', $fromAcademicYearId)
                    ->where('status', 'aktif')
                    ->update([
                        'status' => 'naik_kelas',
                        'exited_at' => now()->toDateString(),
                        'exit_reason' => 'Naik kelas ke TA baru',
                        'updated_at' => now(),
                    ]);

                // Buat enrollment baru di TA baru
                DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->insert([
                    'student_id' => $item['student_id'],
                    'classroom_id' => $item['new_classroom_id'],
                    'academic_year_id' => $toAcademicYearId,
                    'status' => 'aktif',
                    'enrolled_at' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update students.classroom_id
                DB::table(DatabaseEntity::TBL_STUDENTS)
                    ->where('id', $item['student_id'])
                    ->update(['classroom_id' => $item['new_classroom_id'], 'updated_at' => now()]);

                $promotedCount++;
            }

            DB::commit();
            return ['status' => true, 'promoted_count' => $promotedCount];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PromoteStudents Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            $enrollment = DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->where('id', $id)->first();
            if (!$enrollment) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Data tidak ditemukan.'];
            }

            // Cek apakah ada tagihan terkait
            $hasBills = DB::table(DatabaseEntity::TBL_BILLS)
                ->where('student_id', $enrollment->student_id)
                ->where('academic_year_id', $enrollment->academic_year_id)
                ->exists();

            if ($hasBills) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tidak bisa menghapus penempatan yang sudah memiliki tagihan.'];
            }

            DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("EnrollmentDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
