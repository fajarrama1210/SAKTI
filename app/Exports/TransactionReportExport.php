<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
            'Tanggal',
            'Tipe',
            'Kategori',
            'Keterangan',
            'Uang Masuk (Rp)',
            'Uang Keluar (Rp)',
            'Dicatat Oleh',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($row->date)->format('d/m/Y'),
            $row->type === 'income' ? 'Masuk' : 'Keluar',
            $row->category ?? '-',
            $row->description,
            $row->type === 'income' ? $row->amount : '',
            $row->type === 'expense' ? $row->amount : '',
            $row->recorded_by_name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
