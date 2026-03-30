<?php

namespace App\Imports;

use App\Entities\DatabaseEntity;
use App\UseCases\StudentUseCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToCollection, WithHeadingRow
{
    protected $studentUseCase;

    public function __construct(StudentUseCase $studentUseCase)
    {
        $this->studentUseCase = $studentUseCase;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Abaikan baris kosong
            if (empty($row['nisn']) || empty($row['nama_lengkap'])) {
                continue;
            }

            // Cari classroom_id berdasarkan Tingkat dan Jurusan
            $classroomId = $this->findClassroomId(
                $row['tingkat_pilih'] ?? null, 
                $row['jurusan_pilih'] ?? null
            );

            if (!$classroomId) {
                // Bisa tambahkan Log atau skip jika tidak ditemukan kombinasi kelasnya
                continue;
            }

            $data = [
                'family_card_number' => (string) ($row['nomor_kk_16_digit'] ?? ''),
                'nisn' => (string) $row['nisn'],
                'name' => $row['nama_lengkap'],
                'classroom_id' => $classroomId,
            ];

            $this->studentUseCase->store($data);
        }
    }

    /**
     * Mencari ID Kelas berdasarkan Grade Level dan Nama Jurusan
     */
    private function findClassroomId($grade, $majorName): ?int
    {
        if (!$grade || !$majorName) return null;

        return DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->where('c.grade_level', $grade)
            ->where('m.name', $majorName)
            ->value('c.id');
    }
}
