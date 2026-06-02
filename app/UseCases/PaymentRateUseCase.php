<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentRateUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_PAYMENT_RATES . ' as pr')
            ->join(DatabaseEntity::TBL_ACADEMIC_YEARS . ' as ay', 'pr.academic_year_id', '=', 'ay.id')
            ->join(DatabaseEntity::TBL_PAYMENT_TYPES . ' as pt', 'pr.payment_type_id', '=', 'pt.id')
            ->leftJoin(DatabaseEntity::TBL_MAJORS . ' as m', 'pr.major_id', '=', 'm.id')
            ->select(
                'pr.id',
                'ay.name as academic_year_name',
                'pt.name as payment_type_name',
                'pr.grade_level',
                'm.name as major_name',
                'pr.amount'
            )
            ->orderBy('ay.name', 'desc')
            ->orderBy('pr.grade_level', 'asc')
            ->paginate($perPage);
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            // Bug #2c Fix: MySQL UNIQUE tidak menganggap NULL=NULL, cek manual duplikat
            $majorId = $data['major_id'] ?? null;
            $duplicate = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('payment_type_id', $data['payment_type_id'])
                ->where('grade_level', $data['grade_level'])
                ->when(is_null($majorId), fn($q) => $q->whereNull('major_id'), fn($q) => $q->where('major_id', $majorId))
                ->exists();

            if ($duplicate) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tarif untuk kombinasi Tahun Ajaran, Jenis Pembayaran, Kelas, dan Jurusan ini sudah ada.'];
            }

            DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->insert([
                'academic_year_id' => $data['academic_year_id'],
                'payment_type_id'  => $data['payment_type_id'],
                'grade_level'      => $data['grade_level'],
                'major_id'         => $majorId,
                'amount'           => $data['amount'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentRateStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.'];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            // Bug #2c Fix: MySQL UNIQUE tidak menganggap NULL=NULL, cek manual duplikat
            $majorId = $data['major_id'] ?? null;
            $duplicate = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('payment_type_id', $data['payment_type_id'])
                ->where('grade_level', $data['grade_level'])
                ->where('id', '!=', $id)
                ->when(is_null($majorId), fn($q) => $q->whereNull('major_id'), fn($q) => $q->where('major_id', $majorId))
                ->exists();

            if ($duplicate) {
                DB::rollBack();
                return ['status' => false, 'message' => 'Tarif untuk kombinasi Tahun Ajaran, Jenis Pembayaran, Kelas, dan Jurusan ini sudah ada.'];
            }

            DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->where('id', $id)->update([
                'academic_year_id' => $data['academic_year_id'],
                'payment_type_id' => $data['payment_type_id'],
                'grade_level' => $data['grade_level'],
                'major_id' => $majorId,
                'amount' => $data['amount'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentRateUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_PAYMENT_RATES)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentRateDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cari tarif untuk siswa tertentu berdasarkan kelas dan jurusan
     */
    public function getRateForStudent($academicYearId, $paymentTypeId, $gradeLevel, $majorId)
    {
        // Coba cari tarif spesifik per jurusan dulu
        $rate = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
            ->where('academic_year_id', $academicYearId)
            ->where('payment_type_id', $paymentTypeId)
            ->where('grade_level', $gradeLevel)
            ->where('major_id', $majorId)
            ->first();

        // Kalau tidak ada, cari tarif umum (major_id = null)
        if (!$rate) {
            $rate = DB::table(DatabaseEntity::TBL_PAYMENT_RATES)
                ->where('academic_year_id', $academicYearId)
                ->where('payment_type_id', $paymentTypeId)
                ->where('grade_level', $gradeLevel)
                ->whereNull('major_id')
                ->first();
        }

        return $rate;
    }
}
