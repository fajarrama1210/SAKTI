@extends('student.layouts.app-mobile')

@section('content')
    <style>
        :root {
            --primary: #2dce89;
            --success: #2dce89;
            --danger: #f5365c;
            --info: #11cdef;
            --warning: #fb6340;
            --dark: #344767;
            --muted: #8392ab;
            --soft-bg: #f8f9fe;
        }

        body {
            background: var(--soft-bg);
        }

        /* =========================================
                    BACKGROUND HIJAU HEADER
            ========================================= */

        /* background wrapper removed */

        /* =========================================
                                    WELCOME CARD
                                ========================================= */

        .welcome-card {
            border: none;
            border-radius: 28px;
            overflow: hidden;
            position: relative;

            background: linear-gradient(135deg,
                    #07814e 0%,
                    #2dce89 100%);

            box-shadow:
                0 20px 40px rgba(0, 0, 0, .12),
                0 10px 20px rgba(45, 206, 137, .18);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;

            background: rgba(255, 255, 255, .08);

            top: -130px;
            right: -100px;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;

            background: rgba(255, 255, 255, .05);

            bottom: -100px;
            left: -60px;
        }

        /* =========================================
                                    STATS SECTION
                                ========================================= */

        .stats-wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        .stats-col {
            display: flex;
            margin-bottom: 24px;
        }

        .stats-card {
            width: 100%;
            height: 100%;

            border: none;
            border-radius: 24px;

            transition: .3s ease;

            box-shadow:
                0 10px 25px rgba(0, 0, 0, .06),
                0 3px 10px rgba(0, 0, 0, .04);
        }

        .stats-card:hover {
            transform: translateY(-6px);

            box-shadow:
                0 18px 35px rgba(0, 0, 0, .12),
                0 8px 16px rgba(0, 0, 0, .08);
        }

        .stats-title {
            min-height: 42px;

            display: flex;
            align-items: flex-start;

            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .7px;

            text-transform: uppercase;

            color: var(--muted);
        }

        .stats-value {
            min-height: 52px;

            display: flex;
            align-items: center;

            font-size: 1.7rem;
            font-weight: 800;

            color: var(--dark);

            line-height: 1.2;
        }

        .stats-sub {
            min-height: 42px;

            display: flex;
            align-items: flex-start;

            font-size: .78rem;
            font-weight: 600;
        }

        .stats-icon {
            width: 60px;
            height: 60px;

            border-radius: 18px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #fff;
            font-size: 1.2rem;

            flex-shrink: 0;

            box-shadow:
                0 10px 20px rgba(0, 0, 0, .12);
        }

        /* =========================================
                                    MAIN CARD
                                ========================================= */

        .main-card {
            border: none;
            border-radius: 26px;

            overflow: hidden;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .06),
                0 3px 10px rgba(0, 0, 0, .04);
        }

        /* =========================================
                                    QR CARD
                                ========================================= */

        .qr-wrapper {
            background: #f8f9fa;
            padding: 18px;

            border-radius: 22px;

            box-shadow:
                inset 0 2px 6px rgba(0, 0, 0, .05);
        }

        .qr-box {
            width: 170px;
            height: 170px;

            border-radius: 18px;

            border: 2px dashed #d1d5db;

            background: #fff;

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        #qrcode img, #qrcode canvas {
            max-width: 100%;
            max-height: 100%;
            height: auto;
        }

        /* =========================================
                                    TABLE
                                ========================================= */

        .custom-table thead {
            background: #f9fafb;
        }

        .custom-table th {
            border-top: none !important;

            font-size: .72rem;
            font-weight: 700;

            letter-spacing: .5px;
            text-transform: uppercase;

            color: var(--muted);
        }

        .custom-table td {
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        /* =========================================
                                    BADGE
                                ========================================= */

        .student-badge {
            background: #fff;
            color: var(--success);

            padding: 12px 18px;

            border-radius: 50px;

            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
            font-size: 0.88rem;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .08);
        }

        /* =========================================
                                PATTERN / CORAK CARD
                            ========================================= */

        .stats-card,
        .main-card,
        .welcome-card {
            position: relative;
            overflow: hidden;
        }

        /* BULAT BESAR */
        .stats-card::before,
        .main-card::before {
            content: '';

            position: absolute;

            width: 140px;
            height: 140px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .05);

            top: -60px;
            right: -60px;

            z-index: 0;
        }

        /* BULAT KECIL */
        .stats-card::after,
        .main-card::after {
            content: '';

            position: absolute;

            width: 90px;
            height: 90px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .04);

            bottom: -35px;
            left: -35px;

            z-index: 0;
        }

        /* ISI CARD DI ATAS PATTERN */
        .stats-card .card-body,
        .main-card .card-body,
        .main-card .card-header,
        .welcome-card .card-body {
            position: relative;
            z-index: 2;
        }

        /* =========================================
                                CORAK TITIK-TITIK
                            ========================================= */

        .pattern-dots {
            position: absolute;

            width: 180px;
            height: 180px;

            top: -30px;
            right: -30px;

            opacity: .08;

            background-image:
                radial-gradient(#ffffff 2px, transparent 2px);

            background-size: 18px 18px;

            z-index: 1;
        }

        /* =========================================
                                GLOW EFFECT
                            ========================================= */

        .glow-success {
            position: absolute;

            width: 180px;
            height: 180px;

            background: rgba(45, 206, 137, .18);

            filter: blur(70px);

            top: -60px;
            right: -50px;

            z-index: 0;
        }

        .glow-danger {
            position: absolute;

            width: 160px;
            height: 160px;

            background: rgba(245, 54, 92, .15);

            filter: blur(65px);

            bottom: -70px;
            left: -40px;

            z-index: 0;
        }

        /* =========================================
                                HOVER LEBIH HIDUP
                            ========================================= */

        .stats-card:hover {
            transform: translateY(-8px) scale(1.01);

            box-shadow:
                0 25px 45px rgba(0, 0, 0, .14),
                0 10px 20px rgba(0, 0, 0, .08);
        }

        .main-card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 22px 40px rgba(0, 0, 0, .12),
                0 10px 20px rgba(0, 0, 0, .06);
        }

        .footer .copyright,
        .footer .copyright a,
        .footer .fa-heart,
        .footer .text-muted {
            color: #000000c1 !important;
        }

        .footer {
            position: relative;
            z-index: 10;
            margin-top: 30px;
        }

        .footer {
            margin-top: 0 !important;
            position: relative;
            z-index: 10;
        }
    </style>


        <div class="container-fluid py-4">

            <!-- =========================================
                                    HEADER
                                ========================================= -->

            <div class="row mb-4">

                <div class="col-lg-12">

                    <div class="card welcome-card text-white">

                        <div class="card-body p-4 position-relative">

                            <div class="d-md-flex justify-content-between align-items-center">

                                <div>

                                    <h2 class="font-weight-bold text-white mb-2">
                                        Halo, {{ $student->name }} 👋
                                    </h2>

                                    <p class="mb-0 text-white" style="opacity:.9;">
                                        Selamat datang kembali di portal siswa SAKTI.
                                        Di sini Anda dapat memantau pembayaran SPP dan jadwal pelajaran Anda.
                                    </p>

                                </div>

                                <div class="mt-3 mt-md-0">

                                    <span class="student-badge">
                                        KELAS {{ $student->grade_level }} - {{ $student->classroom_name }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- =========================================
                                    REMINDER CICILAN
                                ========================================= --}}

            @if($reminderBills->isNotEmpty())
            @php
                $reminderData = $reminderBills->map(function($rb) {
                    $rb->paid_amount = $rb->paid_amount ?? 0;
                    $rb->remaining = $rb->total_amount - $rb->paid_amount;
                    $rb->pct = $rb->total_amount > 0 ? min(100, round($rb->paid_amount / $rb->total_amount * 100)) : 0;
                    $rb->period_label = \Carbon\Carbon::createFromDate($rb->year, $rb->month, 1)->translatedFormat('F Y');
                    $rb->due_label = \Carbon\Carbon::parse($rb->due_date)->translatedFormat('d M Y');
                    $rb->is_overdue = \Carbon\Carbon::parse($rb->due_date)->isPast();
                    return $rb;
                });
            @endphp

            {{-- MOBILE: Compact reminder with horizontal scroll --}}
            <div class="d-lg-none mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#ff6b35,#f7931e);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-bell text-white" style="font-size:.8rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="font-size:.85rem;color:#1e293b;">Pengingat Tagihan</h6>
                            <small style="color:#94a3b8;font-size:.7rem;">{{ $reminderBills->count() }} belum lunas</small>
                        </div>
                    </div>
                    <a href="{{ route('student.bills') }}" style="font-size:.75rem;color:#059669;font-weight:600;">Semua →</a>
                </div>
                <div class="mobile-reminder-scroll">
                    @foreach($reminderData as $rb)
                    <div class="mobile-reminder-item" style="background:{{ $rb->status === 'partial' ? '#fffbeb' : '#fff5f5' }};border-color:{{ $rb->status === 'partial' ? '#fcd34d' : '#fca5a5' }};">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="font-weight-bold" style="font-size:.85rem;color:#1e293b;">{{ $rb->period_label }}</div>
                            </div>
                            @if($rb->status === 'partial')
                                <span class="badge" style="background:linear-gradient(135deg,#f7931e,#f5a623);color:#fff;border-radius:8px;font-size:.65rem;">Dicicil</span>
                            @else
                                <span class="badge" style="background:linear-gradient(135deg,#f5365c,#d63031);color:#fff;border-radius:8px;font-size:.65rem;">Belum Bayar</span>
                            @endif
                        </div>
                        <div style="height:5px;border-radius:50px;background:#e9ecef;overflow:hidden;" class="mb-2">
                            <div style="height:100%;width:{{ $rb->pct }}%;border-radius:50px;background:linear-gradient(90deg,#f7931e,#2dce89);"></div>
                        </div>
                        <div class="d-flex justify-content-between" style="font-size:.72rem;">
                            <span style="color:#64748b;">Sisa: <b style="color:#ef4444;">Rp {{ number_format($rb->remaining,0,',','.') }}</b></span>
                        </div>
                        <div class="mt-1" style="font-size:.68rem;color:{{ $rb->is_overdue ? '#ef4444' : '#94a3b8' }};">
                            <i class="fas fa-calendar-alt me-1"></i> {{ $rb->due_label }}
                            @if($rb->is_overdue) <b>(Terlambat!)</b> @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- DESKTOP: Original reminder layout --}}
            <div class="row mb-4 d-none d-lg-flex">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius:20px;overflow:hidden;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center px-4 py-3" style="background:linear-gradient(135deg,#ff6b35,#f7931e);">
                                <div class="me-3" style="width:42px;height:42px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-bell text-white fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-white font-weight-bold">⚠️ Pengingat Pembayaran SPP</h6>
                                    <small class="text-white" style="opacity:.85;">Anda memiliki <b>{{ $reminderBills->count() }}</b> tagihan yang belum lunas.</small>
                                </div>
                                <a href="{{ route('student.bills') }}" class="btn btn-sm text-white font-weight-bold" style="background:rgba(255,255,255,.2);border-radius:10px;border:1px solid rgba(255,255,255,.4);white-space:nowrap;">Lihat Semua →</a>
                            </div>
                            <div class="px-4 py-3">
                                <div class="row g-3">
                                    @foreach($reminderData->take(3) as $rb)
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 h-100" style="background:{{ $rb->status === 'partial' ? '#fffbeb' : '#fff5f5' }};border:1px solid {{ $rb->status === 'partial' ? '#fcd34d' : '#fca5a5' }};">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="text-xs font-weight-bold text-muted">Periode</span>
                                                    <div class="font-weight-bold text-dark">{{ $rb->period_label }}</div>
                                                </div>
                                                @if($rb->status === 'partial')
                                                    <span class="badge" style="background:linear-gradient(135deg,#f7931e,#f5a623);color:#fff;border-radius:8px;">Dicicil</span>
                                                @else
                                                    <span class="badge" style="background:linear-gradient(135deg,#f5365c,#d63031);color:#fff;border-radius:8px;">Belum Bayar</span>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-between text-xs mb-1">
                                                <span class="text-muted">Terbayar: <b class="text-success">Rp {{ number_format($rb->paid_amount,0,',','.') }}</b></span>
                                                <span class="text-muted">Sisa: <b class="text-danger">Rp {{ number_format($rb->remaining,0,',','.') }}</b></span>
                                            </div>
                                            <div style="height:6px;border-radius:50px;background:#e9ecef;overflow:hidden;" class="mb-2">
                                                <div style="height:100%;width:{{ $rb->pct }}%;border-radius:50px;background:linear-gradient(90deg,#f7931e,#2dce89);transition:width .6s;"></div>
                                            </div>
                                            <small class="text-{{ $rb->is_overdue ? 'danger' : 'muted' }}">
                                                <i class="fas fa-calendar-alt me-1"></i> Jatuh Tempo: {{ $rb->due_label }}
                                                @if($rb->is_overdue) <b class="text-danger">(Terlambat!)</b> @endif
                                            </small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($reminderBills->count() > 3)
                                <div class="text-center mt-3">
                                    <a href="{{ route('student.bills') }}" class="btn btn-sm btn-warning font-weight-bold">
                                        <i class="fas fa-list me-1"></i>Lihat {{ $reminderBills->count() - 3 }} tagihan lainnya
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- =========================================
                                    STATS CARD
                                ========================================= -->

            <div class="row stats-wrapper">

                <!-- TOTAL TAGIHAN -->
                <!-- xs:full(col-12), sm:half(col-sm-6), md:half(col-md-6), xl:quarter(col-xl-3) -->
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 stats-col">

                    <div class="card stats-card">

                        <div class="pattern-dots"></div>
                        <div class="glow-success"></div>

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="stats-title">
                                        Total Bulan Tagihan
                                    </div>

                                    <div class="stats-value">
                                        {{ $totalBillsCount }} Bulan
                                    </div>

                                    <div class="stats-sub text-muted">
                                        Seluruh tagihan semester ini
                                    </div>

                                </div>

                                <div class="stats-icon bg-info">
                                    <i class="fas fa-file-invoice"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- TUNGGAKAN -->
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 stats-col">

                    <div class="card stats-card">
                        <div class="glow-danger"></div>
                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="stats-title">
                                        Total Tunggakan
                                    </div>

                                    <div class="stats-value text-danger">
                                        Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                                    </div>

                                    <div class="stats-sub text-danger">
                                        Harus segera dilunasi
                                    </div>

                                </div>

                                <div class="stats-icon bg-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- TERBAYAR -->
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 stats-col">

                    <div class="card stats-card">
                        <div class="pattern-dots"></div>
                        <div class="glow-success"></div>
                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="stats-title">
                                        Total Terbayar
                                    </div>

                                    <div class="stats-value text-success">
                                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                    </div>

                                    <div class="stats-sub text-success">
                                        Pembayaran yang sah
                                    </div>

                                </div>

                                <div class="stats-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- JURUSAN -->
                <div class="col-12 col-sm-6 col-md-6 col-xl-3 stats-col">

                    <div class="card stats-card">
                        <div class="pattern-dots"></div>
                        <div class="glow-success"></div>
                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="stats-title">
                                        Jurusan Anda
                                    </div>

                                    <div class="stats-value" style="font-size:1.25rem;">
                                        {{ $student->major_name }}
                                    </div>

                                    <div class="stats-sub text-muted">
                                        Program keahlian terdaftar
                                    </div>

                                </div>

                                <div class="stats-icon bg-warning">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- =========================================
                                    CONTENT
                                ========================================= -->

            <div class="row mt-2">

                <!-- PROFILE CARD: hidden on mobile (QR lives in Profile page), visible md+ -->
                <div class="col-12 col-md-5 col-lg-4 mb-4 d-none d-md-block">

                    <div class="card main-card h-100">

                        <div class="card-header bg-transparent border-0 pt-4">

                            <h5 class="font-weight-bold text-dark mb-0">
                                Kartu Pelajar Digital
                            </h5>

                        </div>

                        <div class="card-body text-center">

                            <div class="qr-wrapper mb-4">

                                <div class="qr-box mx-auto">

                                    <div id="qrcode" style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;"></div>

                                </div>

                            </div>

                            <h4 class="font-weight-bold text-dark mb-1">
                                {{ $student->name }}
                            </h4>

                            <p class="text-muted mb-4">
                                NISN : {{ $student->nisn }}
                            </p>

                            <hr>

                            <div class="text-left">

                                <div class="d-flex justify-content-between mb-3">

                                    <span class="text-muted font-weight-bold text-sm">
                                        Nomor KK
                                    </span>

                                    <span class="font-weight-bold text-dark text-sm">
                                        {{ $student->family_card_number }}
                                    </span>

                                </div>

                                <div class="d-flex justify-content-between mb-3">

                                    <span class="text-muted font-weight-bold text-sm">
                                        No. Identitas
                                    </span>

                                    <span class="font-weight-bold text-dark text-sm">
                                        {{ $student->id_number ?? '-' }}
                                    </span>

                                </div>

                                <div class="d-flex justify-content-between">

                                    <span class="text-muted font-weight-bold text-sm">
                                        Status
                                    </span>

                                    <span class="badge badge-success px-3 py-2">
                                        {{ $student->status }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- TABLE: xs/sm full-width, md/lg 7-cols -->
                <div class="col-12 col-md-7 col-lg-8 mb-4">

                    <div class="card main-card h-100">

                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4">

                            <h5 class="font-weight-bold text-dark mb-0">
                                Histori Pembayaran Terkini
                            </h5>

                            <a href="{{ route('student.bills') }}" class="text-success font-weight-bold text-sm">
                                Semua Tagihan →
                            </a>

                        </div>

                        <div class="card-body">

                            {{-- MOBILE: Card list --}}
                            <div class="d-lg-none">
                                @forelse($recentPayments as $payment)
                                    @php $method = strtolower(trim($payment->payment_method ?? 'cash')); @endphp
                                    <div class="mobile-payment-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="font-weight-bold" style="color:#1e293b;font-size:.88rem;">
                                                    {{ \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F') }} {{ $payment->year }}
                                                </div>
                                                <div style="font-size:.72rem;color:#94a3b8;">{{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y H:i') }}</div>
                                            </div>
                                            <span class="font-weight-bold" style="color:#059669;font-size:.9rem;">Rp {{ number_format($payment->amount,0,',','.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if($method == 'qris')
                                                <span class="badge bg-info text-white" style="font-size:.68rem;"><i class="fas fa-qrcode me-1"></i>QRIS</span>
                                            @elseif($method == 'cash')
                                                <span class="badge bg-success text-white" style="font-size:.68rem;"><i class="fas fa-money-bill me-1"></i>CASH</span>
                                            @elseif($method == 'transfer')
                                                <span class="badge bg-primary text-white" style="font-size:.68rem;"><i class="fas fa-exchange-alt me-1"></i>TRANSFER</span>
                                            @else
                                                <span class="badge bg-secondary text-white" style="font-size:.68rem;">{{ strtoupper($payment->payment_method ?? '-') }}</span>
                                            @endif
                                            <a href="{{ route('student.invoice.show', $payment->payment_id) }}" target="_blank" style="font-size:.75rem;color:#059669;font-weight:600;"><i class="fas fa-file-invoice me-1"></i>Invoice</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-receipt fa-2x mb-2" style="color:#cbd5e1;"></i>
                                        <p class="mb-0" style="color:#94a3b8;font-size:.85rem;">Belum ada riwayat pembayaran.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- DESKTOP: Original table --}}
                            <div class="table-responsive d-none d-lg-block">

                                <table class="table custom-table align-items-center mb-0">
                                    <thead>
                                        <thead>
                                            <tr>
                                                <th class="text-center">Periode Tagihan</th>
                                                <th class="text-center">Tanggal Bayar</th>
                                                <th class="text-center">Metode</th>
                                                <th class="text-center">Jumlah</th>
                                                <th class="text-center">Referensi</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                    </thead>
                                    <tbody>
                                        @forelse($recentPayments as $payment)
                                            <tr>
                                                <td class="text-center"><span class="font-weight-bold text-dark text-sm">{{ \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F') }} {{ $payment->year }}</span></td>
                                                <td class="text-center"><span class="text-muted text-sm">{{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y H:i') }}</span></td>
                                                <td class="align-middle text-center">
                                                    @php $method = strtolower(trim($payment->payment_method ?? 'cash')); @endphp
                                                    @if($method == 'qris') <span class="badge bg-info text-white px-3 py-2"><i class="fas fa-qrcode me-1"></i>QRIS</span>
                                                    @elseif($method == 'cash') <span class="badge bg-success text-white px-3 py-2"><i class="fas fa-money-bill me-1"></i>CASH</span>
                                                    @elseif($method == 'transfer') <span class="badge bg-primary text-white px-3 py-2"><i class="fas fa-exchange-alt me-1"></i>TRANSFER</span>
                                                    @else <span class="badge bg-secondary text-white px-3 py-2">{{ strtoupper($payment->payment_method ?? '-') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center"><span class="font-weight-bold text-success text-sm">Rp {{ number_format($payment->amount,0,',','.') }}</span></td>
                                                <td class="text-center"><span class="text-muted font-weight-bold text-sm">{{ $payment->reference_number ?? '-' }}</span></td>
                                                <td class="text-center"><a href="{{ route('student.invoice.show', $payment->payment_id) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;font-size:11px;padding:4px 10px;" target="_blank"><i class="fas fa-file-invoice"></i> Invoice</a></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center py-5"><i class="fas fa-receipt fa-3x text-muted mb-3"></i><p class="text-muted mb-0">Belum ada riwayat pembayaran.</p></td></tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $student->nisn }}",
            width: 150,
            height: 150,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });
</script>
@endpush
