<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentTypeUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_PAYMENT_TYPES . ' as pt')
            ->leftJoin('semesters as s', 'pt.semester_id', '=', 's.id')
            ->leftJoin('academic_years as ay', 's.academic_year_id', '=', 'ay.id')
            ->select('pt.*', 's.name as semester_name', 'ay.name as academic_year_name')
            ->orderBy('pt.id', 'desc')
            ->paginate($perPage);
    }

    public function getAll()
    {
        return DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            $semesterId = !empty($data['is_monthly']) ? ($data['semester_id'] ?? null) : null;
            DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->insert([
                'name' => $data['name'],
                'is_monthly' => !empty($data['is_monthly']) ? true : false,
                'semester_id' => $semesterId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentTypeStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            $semesterId = !empty($data['is_monthly']) ? ($data['semester_id'] ?? null) : null;
            DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->where('id', $id)->update([
                'name' => $data['name'],
                'is_monthly' => !empty($data['is_monthly']) ? true : false,
                'semester_id' => $semesterId,
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentTypeUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_PAYMENT_TYPES)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentTypeDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
