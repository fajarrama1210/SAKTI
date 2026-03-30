<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $rowNumber = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun Ajaran',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'No. KK',
            'Bulan',
            'Jenis Pembayaran',
            'Tagihan (Rp)',
            'Dibayar (Rp)',
            'Sisa (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        return [
            $this->rowNumber,
            $row->academic_year_name,
            $row->nisn,
            $row->student_name,
            $row->grade_level ? 'Kelas ' . $row->grade_level : '-',
            $row->major_name ?? '-',
            $row->family_card_number,
            ($monthNames[$row->month] ?? $row->month) . ' ' . $row->year,
            $row->payment_type_name,
            $row->tagihan,
            $row->dibayar,
            $row->sisa,
            $row->status_text,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
