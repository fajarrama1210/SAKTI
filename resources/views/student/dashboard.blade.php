@extends('_admin.layouts.app')

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
            flex-direction: column;
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

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .08);
        }

        /* =========================================
                        RESPONSIVE
                    ========================================= */

        @media(max-width:768px) {

            .stats-title,
            .stats-value,
            .stats-sub {
                min-height: auto;
            }

            .stats-value {
                font-size: 1.3rem;
            }

            .welcome-card {
                border-radius: 22px;
            }

            .qr-box {
                width: 140px;
                height: 140px;
            }
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

        <!-- =========================================
                        STATS CARD
                    ========================================= -->

        <div class="row stats-wrapper">

            <!-- TOTAL TAGIHAN -->
            <div class="col-xl-3 col-md-6 stats-col">

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
            <div class="col-xl-3 col-md-6 stats-col">

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
            <div class="col-xl-3 col-md-6 stats-col">

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
            <div class="col-xl-3 col-md-6 stats-col">

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

            <!-- PROFILE -->
            <div class="col-lg-4 mb-4">

                <div class="card main-card h-100">

                    <div class="card-header bg-transparent border-0 pt-4">

                        <h5 class="font-weight-bold text-dark mb-0">
                            Kartu Pelajar Digital
                        </h5>

                    </div>

                    <div class="card-body text-center">

                        <div class="qr-wrapper mb-4">

                            <div class="qr-box mx-auto">

                                <i class="fas fa-qrcode fa-5x text-dark"></i>

                                <span class="text-xs font-weight-bold text-muted mt-3">
                                    {{ $student->qr_code }}
                                </span>

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

            <!-- TABLE -->
            <div class="col-lg-8 mb-4">

                <div class="card main-card h-100">

                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4">

                        <h5 class="font-weight-bold text-dark mb-0">
                            Histori Pembayaran Terkini
                        </h5>

                        <a href="{{ route('student.bills') }}" class="text-success font-weight-bold text-sm">
                            Semua Tagihan →
                        </a>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table custom-table align-items-center mb-0">

                                <thead>

                                    <tr>
                                        <th>Periode Tagihan</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Metode</th>
                                        <th>Jumlah</th>
                                        <th>Referensi</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($recentPayments as $payment)
                                        <tr>

                                            <td>
                                                <span class="font-weight-bold text-dark text-sm">
                                                    {{ \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F') }}
                                                    {{ $payment->year }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="text-muted text-sm">
                                                    {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y H:i') }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge badge-light px-3 py-2">
                                                    {{ $payment->payment_method }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="font-weight-bold text-success text-sm">
                                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="text-muted font-weight-bold text-sm">
                                                    {{ $payment->reference_number ?? '-' }}
                                                </span>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="5" class="text-center py-5">

                                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>

                                                <p class="text-muted mb-0">
                                                    Belum ada riwayat pembayaran yang tercatat.
                                                </p>

                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
