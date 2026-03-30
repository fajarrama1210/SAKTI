<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Keuangan</title>
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

        .summary {
            margin-top: 15px;
        }

        .summary td {
            border: none;
            padding: 3px 6px;
        }

        .text-green {
            color: green;
            font-weight: bold;
        }

        .text-red {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>LAPORAN TRANSAKSI KEUANGAN</h2>
    <p class="subtitle">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Uang Masuk (Rp)</th>
                <th>Uang Keluar (Rp)</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $trx)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                <td>{{ $trx->type === 'income' ? 'Masuk' : 'Keluar' }}</td>
                <td>{{ $trx->category ?? '-' }}</td>
                <td>{{ $trx->description }}</td>
                <td class="text-right">{{ $trx->type === 'income' ? number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $trx->type === 'expense' ? number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                <td>{{ $trx->recorded_by_name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f0f0f0;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right text-green">{{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-right text-red">{{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <table class="summary">
        <tr>
            <td><strong>Total Uang Masuk:</strong></td>
            <td class="text-green">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Uang Keluar:</strong></td>
            <td class="text-red">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Saldo:</strong></td>
            <td><strong>Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
</body>

</html>