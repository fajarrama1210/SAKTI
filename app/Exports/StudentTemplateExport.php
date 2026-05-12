<?php

namespace App\Exports;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Data Siswa'      => new StudentDataSheet(),
            'Referensi Kelas' => new StudentReferenceSheet(),
            '_Jurusan'        => new StudentMajorReferenceSheet(),
        ];
    }
}
