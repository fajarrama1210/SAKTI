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
            'NISN',
            'NAMA LENGKAP',
            'TINGKAT (Pilih)',
            'JURUSAN (Pilih)',
        ];
    }

    public function array(): array
    {
        // Return empty array agar template bersih tanpa contoh data Budi/Siti
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 18,
            'C' => 35,
            'D' => 20,
            'E' => 35,
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

                // 1. Dropdown Tingkat (10, 11, 12)
                $grades = DB::table(DatabaseEntity::TBL_CLASSROOMS)->distinct()->orderBy('grade_level')->pluck('grade_level')->toArray();
                $gradeFormula = '"' . implode(',', $grades) . '"';

                // 2. Dropdown Jurusan
                $majors = DB::table(DatabaseEntity::TBL_MAJORS)->orderBy('name')->pluck('name')->toArray();
                $majorFormula = '"' . implode(',', $majors) . '"';

                // Terapkan ke 100 baris
                for ($row = 2; $row <= 101; $row++) {
                    // Validation Kolom D (Tingkat)
                    $valD = $sheet->getCell('D' . $row)->getDataValidation();
                    $valD->setType(DataValidation::TYPE_LIST);
                    $valD->setAllowBlank(true);
                    $valD->setShowDropDown(true);
                    $valD->setFormula1($gradeFormula);

                    // Validation Kolom E (Jurusan)
                    $valE = $sheet->getCell('E' . $row)->getDataValidation();
                    $valE->setType(DataValidation::TYPE_LIST);
                    $valE->setAllowBlank(true);
                    $valE->setShowDropDown(true);
                    $valE->setFormula1($majorFormula);
                }

                // Baris header lebih tinggi
                $sheet->getRowDimension(1)->setRowHeight(30);
                
                // Set format kolom A sebagai Text agar No KK tidak jadi Scientific (1.23E+15)
                $sheet->getStyle('A2:A101')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->getStyle('B2:B101')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                $sheet->freezePane('A2');
            },
        ];
    }
}
