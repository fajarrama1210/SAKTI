<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use App\Entities\ResponseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SemesterUseCase
{
    protected $billUseCase;

    public function __construct(BillUseCase $billUseCase)
    {
        $this->billUseCase = $billUseCase;
    }

    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_SEMESTERS . ' as s')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('s.*', 'ay.name as academic_year_name')
            ->orderBy('ay.start_date', 'desc')
            ->orderBy('s.start_month', 'asc')
            ->paginate($perPage);
    }

    public function getAll()
    {
        return DB::table(DatabaseEntity::TBL_SEMESTERS . ' as s')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('s.*', 'ay.name as academic_year_name')
            ->orderBy('ay.start_date', 'desc')
            ->orderBy('s.start_month', 'asc')
            ->get();
    }

    public function getByAcademicYear($academicYearId)
    {
        return DB::table(DatabaseEntity::TBL_SEMESTERS)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('start_month', 'asc')
            ->get();
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_SEMESTERS . ' as s')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('s.*', 'ay.name as academic_year_name')
            ->where('s.id', $id)
            ->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            $semesterId = DB::table(DatabaseEntity::TBL_SEMESTERS)->insertGetId([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'start_month' => $data['start_month'],
                'end_month' => $data['end_month'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Auto-generate tagihan untuk semester ini
            $billResult = $this->billUseCase->autoGenerateBillsForSemester($semesterId);

            return [
                'status' => true,
                'bill_count' => $billResult['count'] ?? 0,
                'bill_message' => $billResult['status']
                    ? "Otomatis membuat {$billResult['count']} tagihan."
                    : ($billResult['message'] ?? 'Gagal membuat tagihan otomatis.')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("SemesterStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_SEMESTERS)->where('id', $id)->update([
                'academic_year_id' => $data['academic_year_id'],
                'name' => $data['name'],
                'start_month' => $data['start_month'],
                'end_month' => $data['end_month'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("SemesterUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            $semester = DB::table(DatabaseEntity::TBL_SEMESTERS)->where('id', $id)->first();
            if (!$semester) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Data tidak ditemukan.'];
            }

            // Cek apakah ada tagihan yang terhubung ke semester ini
            $hasBills = DB::table(DatabaseEntity::TBL_BILLS)
                ->where('semester_id', $id)
                ->exists();

            if ($hasBills) {
                DB::rollBack();
                return ['status' => false, 'message' => ResponseEntity::MSG_ERR_SEMESTER_HAS_BILL];
            }

            DB::table(DatabaseEntity::TBL_SEMESTERS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("SemesterDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Mendapatkan rentang bulan untuk semester tertentu
     */
    public function getMonthRange($semesterId): array
    {
        $semester = DB::table(DatabaseEntity::TBL_SEMESTERS)->where('id', $semesterId)->first();
        if (!$semester) return [];

        $months = [];
        $start = $semester->start_month;
        $end = $semester->end_month;

        if ($start <= $end) {
            for ($m = $start; $m <= $end; $m++) {
                $months[] = $m;
            }
        } else {
            for ($m = $start; $m <= 12; $m++) {
                $months[] = $m;
            }
            for ($m = 1; $m <= $end; $m++) {
                $months[] = $m;
            }
        }

        return $months;
    }
}
