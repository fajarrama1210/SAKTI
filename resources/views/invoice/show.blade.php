<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 30px 15px;
        }

        .invoice-wrapper {
            width: 100%;
            max-width: 720px;
        }

        /* --- ACTION BAR (tidak dicetak) --- */
        .action-bar {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all .2s;
        }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: #fff; color: #374151; border: 1.5px solid #e5e7eb; }
        .btn-secondary:hover { background: #f9fafb; }

        /* --- INVOICE CARD --- */
        .invoice-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08), 0 2px 10px rgba(0,0,0,.04);
            overflow: hidden;
        }

        /* --- HEADER --- */
        .invoice-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            padding: 40px 44px 36px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .invoice-header::before {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            top: -80px; right: -80px;
        }
        .invoice-header::after {
            content: '';
            position: absolute;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            bottom: -60px; left: -40px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative; z-index: 2;
        }
        .school-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.3px;
        }
        .school-sub { font-size: 13px; opacity: .8; margin-top: 4px; }
        .invoice-title-block { text-align: right; }
        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            opacity: .95;
        }
        .invoice-number {
            font-size: 13px;
            opacity: .75;
            margin-top: 4px;
            font-weight: 500;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-top: 20px;
            position: relative; z-index: 2;
        }
        .badge-paid { background: rgba(52,211,153,.25); color: #6ee7b7; border: 1px solid rgba(52,211,153,.4); }
        .badge-partial { background: rgba(251,191,36,.25); color: #fde68a; border: 1px solid rgba(251,191,36,.4); }
        .badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

        /* --- BODY --- */
        .invoice-body { padding: 40px 44px; }

        /* GRID INFO */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-bottom: 36px;
        }
        .info-block label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #9ca3af;
            display: block;
            margin-bottom: 6px;
        }
        .info-block .value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            line-height: 1.5;
        }
        .info-block .value .sub {
            font-size: 13px;
            font-weight: 400;
            color: #6b7280;
        }

        /* METHOD BADGE */
        .method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
        }
        .method-qris { background: #ede9fe; color: #7c3aed; }
        .method-cash { background: #d1fae5; color: #065f46; }
        .method-transfer { background: #dbeafe; color: #1d4ed8; }
        .method-other { background: #f3f4f6; color: #374151; }

        /* DIVIDER */
        .divider { height: 1px; background: #f1f5f9; margin: 0 0 28px; }

        /* TABLE */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .items-table thead tr {
            background: #f8fafc;
        }
        .items-table thead th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #9ca3af;
            border-bottom: 2px solid #f1f5f9;
        }
        .items-table thead th:last-child { text-align: right; }
        .items-table tbody td {
            padding: 14px 16px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f9fafb;
        }
        .items-table tbody td:last-child { text-align: right; font-weight: 600; }
        .items-table tbody tr:last-child td { border-bottom: none; }

        /* SUMMARY */
        .summary-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }
        .summary-row:not(:last-child) { border-bottom: 1px solid #edf2f7; }
        .summary-row .label { font-size: 14px; color: #6b7280; }
        .summary-row .amount { font-size: 14px; font-weight: 600; color: #111827; }
        .summary-row.total { padding-top: 14px; margin-top: 4px; }
        .summary-row.total .label {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }
        .summary-row.total .amount {
            font-size: 22px;
            font-weight: 800;
            color: #2563eb;
        }

        /* FOOTER */
        .invoice-footer {
            padding: 24px 44px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-note { font-size: 12px; color: #9ca3af; }
        .footer-note strong { color: #6b7280; }
        .stamp-area {
            text-align: center;
            min-width: 140px;
        }
        .stamp-box {
            width: 120px;
            height: 70px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
        }
        .stamp-box span { font-size: 11px; color: #d1d5db; }
        .stamp-label { font-size: 11px; color: #9ca3af; font-weight: 600; }

        /* REFERENCE BOX */
        .ref-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 28px;
        }
        .ref-box label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #93c5fd; display: block; margin-bottom: 4px; }
        .ref-box .ref-value { font-size: 13px; font-weight: 700; color: #1d4ed8; font-family: monospace; letter-spacing: .5px; }

        /* =========== PRINT =========== */
        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .invoice-card { box-shadow: none; border-radius: 0; }
            .invoice-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .summary-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        @media (max-width: 600px) {
            .invoice-header, .invoice-body, .invoice-footer { padding-left: 24px; padding-right: 24px; }
            .info-grid { grid-template-columns: 1fr; gap: 18px; }
            .header-top { flex-direction: column; gap: 12px; }
            .invoice-title-block { text-align: left; }
        }
    </style>
</head>
<body>
<div class="invoice-wrapper">

    {{-- Action Bar --}}
    <div class="action-bar">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            ← Kembali
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="invoice-card">

        {{-- ===== HEADER ===== --}}
        <div class="invoice-header">
            <div class="header-top">
                <div>
                    <div class="school-name">SAKTI</div>
                    <div class="school-sub">Sistem Administrasi Keuangan Terpadu & Informasi</div>
                </div>
                <div class="invoice-title-block">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">{{ $invoiceNumber }}</div>
                </div>
            </div>

            @php
                $remaining = $payment->bill_total - $totalPaidForBill;
                $isFullyPaid = $payment->bill_status === 'paid';
            @endphp

            <div class="status-badge {{ $isFullyPaid ? 'badge-paid' : 'badge-partial' }}">
                <span class="badge-dot"></span>
                {{ $isFullyPaid ? 'Lunas' : 'Sebagian (Cicilan)' }}
            </div>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="invoice-body">

            {{-- Info Grid --}}
            <div class="info-grid">
                <div>
                    <div class="info-block">
                        <label>Nama Siswa</label>
                        <div class="value">{{ $payment->student_name }}</div>
                    </div>
                    <div class="info-block" style="margin-top:18px;">
                        <label>NISN</label>
                        <div class="value">{{ $payment->nisn }}</div>
                    </div>
                    <div class="info-block" style="margin-top:18px;">
                        <label>Kelas / Jurusan</label>
                        <div class="value">
                            Kelas {{ $payment->grade_level }} - {{ $payment->classroom_name }}
                            <span class="sub">{{ $payment->major_name }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="info-block">
                        <label>Periode Tagihan</label>
                        <div class="value">
                            {{ \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F') }} {{ $payment->year }}
                            <span class="sub">{{ $payment->academic_year_name }}</span>
                        </div>
                    </div>
                    <div class="info-block" style="margin-top:18px;">
                        <label>Tanggal Pembayaran</label>
                        <div class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d F Y') }}</div>
                    </div>
                    <div class="info-block" style="margin-top:18px;">
                        <label>Metode Pembayaran</label>
                        <div class="value">
                            @php $m = strtolower($payment->payment_method ?? 'cash'); @endphp
                            @if($m === 'qris')
                                <span class="method-badge method-qris">⬛ QRIS</span>
                            @elseif($m === 'cash')
                                <span class="method-badge method-cash">💵 Tunai</span>
                            @elseif($m === 'transfer')
                                <span class="method-badge method-transfer">🏦 Transfer</span>
                            @else
                                <span class="method-badge method-other">{{ strtoupper($m) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Referensi jika ada (QRIS/Transfer) --}}
            @if($payment->reference_number)
            <div class="ref-box">
                <label>Nomor Referensi Transaksi</label>
                <div class="ref-value">{{ $payment->reference_number }}</div>
            </div>
            @endif

            {{-- Rincian Item --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Rincian Pembayaran</th>
                        <th>Nominal Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billItems as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->type_name }}</td>
                        <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#9ca3af; padding: 20px;">
                            Rincian item tidak tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Summary --}}
            <div class="summary-box">
                <div class="summary-row">
                    <span class="label">Total Tagihan Bulan Ini</span>
                    <span class="amount">Rp {{ number_format($payment->bill_total, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="label">Total Terbayar (semua transaksi)</span>
                    <span class="amount" style="color:#059669;">Rp {{ number_format($totalPaidForBill, 0, ',', '.') }}</span>
                </div>
                @if(!$isFullyPaid)
                <div class="summary-row">
                    <span class="label">Sisa Tagihan</span>
                    <span class="amount" style="color:#dc2626;">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span class="label">Dibayar Kali Ini</span>
                    <span class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($payment->notes)
            <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px 18px; margin-bottom:28px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#92400e;margin-bottom:4px;">Catatan</div>
                <div style="font-size:13px;color:#78350f;">{{ $payment->notes }}</div>
            </div>
            @endif

        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="invoice-footer">
            <div class="footer-note">
                Dicetak pada: <strong>{{ now()->translatedFormat('d F Y, H:i') }}</strong><br>
                Dokumen ini sah sebagai bukti pembayaran resmi SAKTI.
            </div>
            <div class="stamp-area">
                <div class="stamp-box"><span>Tanda Tangan</span></div>
                <div class="stamp-label">Bendahara Sekolah</div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
