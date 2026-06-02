<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassroomUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select('c.id', 'c.name', 'c.grade_level', 'c.major_id', 'm.name as major_name')
            ->orderBy('c.grade_level', 'asc')
            ->orderBy('c.name', 'asc')
            ->paginate($perPage);
    }

    public function getAll()
    {
        return DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select('c.id', 'c.name', 'c.grade_level', 'c.major_id', 'm.name as major_name')
            ->orderBy('c.grade_level', 'asc')
            ->orderBy('c.name', 'asc')
            ->get();
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_CLASSROOMS)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_CLASSROOMS)->insert([
                'name' => $data['name'],
                'grade_level' => $data['grade_level'],
                'major_id' => $data['major_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ClassroomStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_CLASSROOMS)->where('id', $id)->update([
                'name' => $data['name'],
                'grade_level' => $data['grade_level'],
                'major_id' => $data['major_id'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ClassroomUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_CLASSROOMS)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ClassroomDelete Error: " . $e->getMessage());
            if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === "23000") {
                return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERR_CONSTRAINT];
            }
            return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERROR_SERVER];
        }
    }
}
