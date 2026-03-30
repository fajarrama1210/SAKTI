<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MajorUseCase
{
    public function getAll()
    {
        // Mengambil semua data untuk keperluan Dropdown (<select>)
        return DB::table(DatabaseEntity::TBL_MAJORS)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getPaginated($perPage = 10)
    {
        // Optimasi: Gunakan select spesifik, jangan SELECT *
        // Gunakan pagination agar database tidak ngos-ngosan menarik ribuan data
        return DB::table(DatabaseEntity::TBL_MAJORS)
            ->select('id', 'name')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_MAJORS)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_MAJORS)->insert([
                'name' => $data['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("MajorStore Error: " . $e->getMessage()); // Keamanan: Log error, jangan tampilkan ke user
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_MAJORS)->where('id', $id)->update([
                'name' => $data['name'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("MajorUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_MAJORS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("MajorDelete Error: " . $e->getMessage());
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === "23000") {
                return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERR_CONSTRAINT];
            }
            return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERROR_SERVER];
        }
    }
}
