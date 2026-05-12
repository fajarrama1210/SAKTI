<?php

namespace App\Exports;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentReferenceSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Referensi Kelas';
    }

    public function headings(): array
    {
        return [
            'ID Kelas',
            'Tingkat',
            'Jurusan',
            'Keterangan',
        ];
    }

    public function array(): array
    {
        $classrooms = DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
            ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
            ->select('c.id', 'c.grade_level', 'm.name as major_name')
            ->orderBy('c.grade_level', 'asc')
            ->orderBy('m.name', 'asc')
            ->get();

        $rows = [];
        foreach ($classrooms as $c) {
            $rows[] = [
                $c->id,
                'Kelas ' . $c->grade_level,
                $c->major_name,
                'Tingkat ' . $c->grade_level . ' - ' . $c->major_name,
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 15,
            'C' => 35,
            'D' => 40,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2dce89'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
