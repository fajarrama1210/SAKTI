<?php

namespace App\UseCases;

use App\Entities\DatabaseEntity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleUseCase
{
    public function getPaginated($perPage = 10)
    {
        return DB::table(DatabaseEntity::TBL_SCHEDULES . ' as s')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->select(
                's.id',
                's.classroom_id',
                's.day',
                's.start_time',
                's.duration',
                's.end_time',
                's.subject',
                's.room',
                's.teacher_name',
                'c.name as classroom_name'
            )
            ->orderByRaw("FIELD(s.day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') ASC")
            ->orderBy('s.start_time', 'asc')
            ->paginate($perPage);
    }

    public function getAll()
    {
        return DB::table(DatabaseEntity::TBL_SCHEDULES . ' as s')
            ->join(DatabaseEntity::TBL_CLASSROOMS . ' as c', 's.classroom_id', '=', 'c.id')
            ->select(
                's.id',
                's.classroom_id',
                's.day',
                's.start_time',
                's.duration',
                's.end_time',
                's.subject',
                's.room',
                's.teacher_name',
                'c.name as classroom_name'
            )
            ->orderByRaw("FIELD(s.day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') ASC")
            ->orderBy('s.start_time', 'asc')
            ->get();
    }

    public function getById($id)
    {
        return DB::table(DatabaseEntity::TBL_SCHEDULES)->where('id', $id)->first();
    }

    public function store(array $data): array
    {
        DB::beginTransaction();
        try {
            foreach ($data['schedules'] as $item) {
                $startTime = Carbon::parse($item['start_time']);
                $endTime = $startTime->copy()->addHours((int) $item['duration'])->format('H:i:s');

                DB::table(DatabaseEntity::TBL_SCHEDULES)->insert([
                    'classroom_id' => $data['classroom_id'],
                    'day' => $item['day'],
                    'start_time' => $startTime->format('H:i:s'),
                    'duration' => $item['duration'],
                    'end_time' => $endTime,
                    'subject' => $item['subject'],
                    'room' => $item['room'],
                    'teacher_name' => $item['teacher_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ScheduleStore Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data): array
    {
        DB::beginTransaction();
        try {
            $startTime = Carbon::parse($data['start_time']);
            $endTime = $startTime->copy()->addHours((int) $data['duration'])->format('H:i:s');

            DB::table(DatabaseEntity::TBL_SCHEDULES)->where('id', $id)->update([
                'classroom_id' => $data['classroom_id'],
                'day' => $data['day'],
                'start_time' => $startTime->format('H:i:s'),
                'duration' => $data['duration'],
                'end_time' => $endTime,
                'subject' => $data['subject'],
                'room' => $data['room'],
                'teacher_name' => $data['teacher_name'],
                'updated_at' => now(),
            ]);

            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ScheduleUpdate Error: " . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete($id): array
    {
        DB::beginTransaction();
        try {
            DB::table(DatabaseEntity::TBL_SCHEDULES)->where('id', $id)->delete();
            DB::commit();
            return ['status' => true];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("ScheduleDelete Error: " . $e->getMessage());
            return ['status' => false, 'message' => \App\Entities\ResponseEntity::MSG_ERROR_SERVER];
        }
    }
}
