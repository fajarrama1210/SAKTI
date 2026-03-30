<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use App\Entities\ResponseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);
    }

    public function getAll()
    {
        return DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getActive()
    {
        return DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)
            ->where('is_active', true)
            ->first();
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            // Jika di-set aktif, nonaktifkan yang lain
            if (!empty($data['is_active'])) {
                DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->update(['is_active' => false]);
            }

            DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->insert([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => !empty($data['is_active']) ? true : false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("AcademicYearStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            if (!empty($data['is_active'])) {
                DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->update(['is_active' => false]);
            }

            DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('id', $id)->update([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_active' => !empty($data['is_active']) ? true : false,
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("AcademicYearUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            // Check if used in semesters
            $hasSemesters = DB::table(DatabaseEntity::TBL_SEMESTERS)->where('academic_year_id', $id)->exists();
            if ($hasSemesters) {
                DB::rollBack();
                return ['status' => false, 'message' => ResponseEntity::MSG_ERR_ACADEMIC_YEAR_HAS_SEMESTER];
            }

            // Check if used in payment rates (tarif)
            $hasRates = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->where('academic_year_id', $id)->exists();
            if ($hasRates) {
                DB::rollBack();
                return ['status' => false, 'message' => ResponseEntity::MSG_ERR_ACADEMIC_YEAR_HAS_RATE];
            }

            // Check if used in bills (tagihan)
            $hasBills = DB::table(DatabaseEntity::TBL_BILLS)->where('academic_year_id', $id)->exists();
            if ($hasBills) {
                DB::rollBack();
                return ['status' => false, 'message' => ResponseEntity::MSG_ERR_ACADEMIC_YEAR_HAS_BILL];
            }

            DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("AcademicYearDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
