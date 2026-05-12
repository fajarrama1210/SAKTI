<?php

namespace App\Exports;

use App\Entities\DatabaseEntity;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentDataSheet implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    public function title(): string
    {
        return 'Data Siswa';
    }

    public function headings(): array
    {
        return [
            'NOMOR KK (16 Digit)',
            'NIK (16 Digit)',
            'NISN',
            'NAMA LENGKAP',
            'KELAS',
            'JURUSAN',
        ];
    }

    public function array(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, // No KK
            'B' => 25, // NIK
            'C' => 18, // NISN
            'D' => 35, // Nama Lengkap
            'E' => 15, // Kelas
            'F' => 35, // Jurusan
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5e72e4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Dropdown KELAS (Ambil dari master grade_level)
                $grades = DB::table(DatabaseEntity::TBL_CLASSROOMS)->distinct()->orderBy('grade_level')->pluck('grade_level')->toArray();
                $gradeFormula = '"' . implode(',', $grades) . '"';

                // 2. Dropdown JURUSAN (Hanya jurusan yang punya kelas di master data)
                $majorCount = DB::table(DatabaseEntity::TBL_CLASSROOMS . ' as c')
                    ->join(DatabaseEntity::TBL_MAJORS . ' as m', 'c.major_id', '=', 'm.id')
                    ->distinct()
                    ->count('m.name');
                $majorFormula = "'_Jurusan'!\$A\$1:\$A\$" . $majorCount;

                // Terapkan ke 100 baris
                for ($row = 2; $row <= 101; $row++) {
                    // Validasi Kolom E (KELAS)
                    $valE = $sheet->getCell('E' . $row)->getDataValidation();
                    $valE->setType(DataValidation::TYPE_LIST);
                    $valE->setAllowBlank(true);
                    $valE->setShowDropDown(true);
                    $valE->setFormula1($gradeFormula);

                    // Validasi Kolom F (JURUSAN)
                    $valF = $sheet->getCell('F' . $row)->getDataValidation();
                    $valF->setType(DataValidation::TYPE_LIST);
                    $valF->setAllowBlank(true);
                    $valF->setShowDropDown(true);
                    $valF->setFormula1($majorFormula);
                }

                $sheet->getRowDimension(1)->setRowHeight(30);
                
                $sheet->getStyle('A2:A101')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->getStyle('B2:B101')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->getStyle('C2:C101')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                $sheet->freezePane('A2');
            },
        ];
    }
}
