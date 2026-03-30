<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran SPP</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-size: 10px;
        }

        td {
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .badge-lunas {
            color: green;
            font-weight: bold;
        }

        .badge-sebagian {
            color: orange;
            font-weight: bold;
        }

        .badge-belum {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>LAPORAN PEMBAYARAN SPP</h2>
    <p class="subtitle">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun Ajaran</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>No. KK</th>
                <th>Bulan</th>
                <th>Jenis</th>
                <th>Tagihan</th>
                <th>Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
            $bulanFull = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            $totalTagihan = 0; $totalDibayar = 0; $totalSisa = 0;
            @endphp
            @foreach($data as $index => $row)
            @php $totalTagihan += $row->tagihan; $totalDibayar += $row->dibayar; $totalSisa += $row->sisa; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->academic_year_name }}</td>
                <td>{{ $row->nisn }}</td>
                <td>{{ $row->student_name }}</td>
                <td>{{ $row->grade_level ? 'Kelas ' . $row->grade_level : '-' }}</td>
                <td>{{ $row->major_name ?? '-' }}</td>
                <td>{{ $row->family_card_number }}</td>
                <td>{{ ($bulanFull[$row->month] ?? $row->month) . ' ' . $row->year }}</td>
                <td>{{ $row->payment_type_name }}</td>
                <td class="text-right">{{ number_format($row->tagihan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->dibayar, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->sisa, 0, ',', '.') }}</td>
                <td>
                    <span class="{{ $row->status_text === 'Lunas' ? 'badge-lunas' : ($row->status_text === 'Sebagian' ? 'badge-sebagian' : 'badge-belum') }}">
                        {{ $row->status_text }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f0f0f0;">
                <td colspan="9" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($totalTagihan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalDibayar, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>