<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_STUDENTS.' as s')
            ->join(DatabaseEntity::TBL_CLASSROOMS.' as c', 's.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS.' as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.nisn', 's.id_number', 's.name', 's.family_card_number', 's.status', 'c.grade_level', 'm.name as major_name')
            ->orderBy('s.id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Cari siswa berdasarkan nama atau NISN (untuk fitur pencarian pembayaran SPP)
     */
    public function search($keyword)
    {
        return DB::table(DatabaseEntity::TBL_STUDENTS.' as s')
            ->join(DatabaseEntity::TBL_CLASSROOMS.' as c', 's.classroom_id', '=', 'c.id')
            ->join(DatabaseEntity::TBL_MAJORS.' as m', 'c.major_id', '=', 'm.id')
            ->select('s.id', 's.nisn', 's.name', 's.family_card_number', 's.status', 'c.grade_level', 'm.name as major_name')
            ->where(function ($q) use ($keyword) {
                $q->where('s.name', 'LIKE', "%{$keyword}%")
                    ->orWhere('s.nisn', 'LIKE', "%{$keyword}%")
                    ->orWhere('s.id_number', 'LIKE', "%{$keyword}%")
                    ->orWhere('s.family_card_number', 'LIKE', "%{$keyword}%");
            })
            ->where('s.status', 'aktif')
            ->limit(20)
            ->get();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            $qrCodeText = $data['nisn'].'-'.Str::random(8);

            DB::table(DatabaseEntity::TBL_STUDENTS)->insert([
                'id_number' => $data['id_number'],
                'family_card_number' => $data['family_card_number'],
                'nisn' => $data['nisn'],
                'name' => $data['name'],
                'classroom_id' => $data['classroom_id'],
                'qr_code' => $qrCodeText,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Ambil ID siswa yang baru saja dibuat
            $studentId = DB::getPdo()->lastInsertId();

            // OTOMATISASI PENEMPATAN: Masukkan ke Tahun Ajaran Aktif
            $activeAY = DB::table(DatabaseEntity::TBL_ACADEMIC_YEARS)->where('is_active', true)->first();
            if ($activeAY) {
                DB::table(DatabaseEntity::TBL_STUDENT_ENROLLMENTS)->insert([
                    'student_id' => $studentId,
                    'classroom_id' => $data['classroom_id'],
                    'academic_year_id' => $activeAY->id,
                    'status' => 'aktif',
                    'enrolled_at' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('StudentStore Error: '.$e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_STUDENTS)->where('id', $id)->first();
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_STUDENTS)->where('id', $id)->update([
                'id_number' => $data['id_number'],
                'family_card_number' => $data['family_card_number'],
                'nisn' => $data['nisn'],
                'name' => $data['name'],
                'classroom_id' => $data['classroom_id'],
                'updated_at' => now(),
            ]);

            DB::commit();

            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('StudentUpdate Error: '.$e->getMessage());

            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_STUDENTS)->where('id', $id)->delete();

            DB::commit();

            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('StudentDelete Error: '.$e->getMessage());
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERR_CONSTRAINT];
            }

            return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERROR_SERVER];
        }
    }
}
