<?php

namespace App\Exports;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Sheet tersembunyi berisi daftar jurusan unik.
 * Digunakan sebagai sumber dropdown kolom F di sheet "Data Siswa".
 * Sheet ini disembunyikan agar user tidak bingung, tapi tetap bisa dibaca Excel untuk validasi.
 */
class StudentMajorReferenceSheet implements FromArray, WithTitle
{
    public function title(): string
    {
        return '_Jurusan'; // Underscore agar tidak mengganggu tampilan
    }

    public function array(): array
    {
        // Hanya ambil jurusan yang BENAR-BENAR punya kelas di master data
        $majors = DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->distinct()
            ->orderBy('m.name')
            ->pluck('m.name')
            ->toArray();

        return array_map(fn($name) => [$name], $majors);
    }
}
