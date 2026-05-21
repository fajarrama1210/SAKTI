@extends('_admin.layouts.app')

@section('content')
    <style>
        :root {
            --primary-green: #1a8a5c;
            --secondary-green: #2dce89;
            --soft-green: #e9fff5;
            --dark-text: #344767;
            --muted-text: #8392ab;
            --danger-color: #f5365c;
            --warning-color: #fb6340;
            --card-radius: 22px;
        }

        body {
            background: #f8fafc;
        }

        /* ======================================================
            HEADER
        ====================================================== */

        .dashboard-header {
            background: linear-gradient(135deg,
                    #07814e 45%,
                    #1e905f 100%);

            position: relative;
            overflow: hidden;

            border-radius: 28px;

            margin: 18px;
            padding: 45px 20px 140px;

            box-shadow:
                0 20px 45px rgba(0, 0, 0, .12),
                0 10px 25px rgba(45, 206, 137, .18);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            background: rgba(255, 255, 255, .06);
            border-radius: 50%;
            top: -120px;
            right: -90px;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            background: rgba(255, 255, 255, .05);
            border-radius: 50%;
            bottom: -110px;
            left: -60px;
        }

        /* ======================================================
            GLOBAL CARD
        ====================================================== */

        .dashboard-card {
            border: none;
            border-radius: 26px;
            overflow: hidden;
            background: #fff;
            position: relative;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .06),
                0 3px 10px rgba(0, 0, 0, .04);

            transition: .3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);

            box-shadow:
                0 20px 40px rgba(0, 0, 0, .12),
                0 8px 18px rgba(0, 0, 0, .08);
        }

        /* ======================================================
            CORAK CARD
        ====================================================== */

        .dashboard-card::before {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            background: rgba(45, 206, 137, .05);
            border-radius: 50%;
            top: -90px;
            right: -80px;
        }

        .dashboard-card::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(45, 206, 137, .04);
            border-radius: 50%;
            bottom: -60px;
            left: -40px;
        }

        /* ======================================================
        STATS CARD
    ====================================================== */

        .stats-card {
            min-height: 155px;
            height: 40%;
            display: flex;
            align-items: center;
            position: relative;
        }

        /* DEFAULT BORDER */
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(to bottom,
                    var(--primary-green),
                    var(--secondary-green));
        }

        /* TOTAL SISWA */
        .border-students::before {
            background: linear-gradient(to bottom,
                    #3b82f6,
                    #60a5fa);
        }

        /* KELAS & JURUSAN */
        .border-class::before {
            background: linear-gradient(to bottom,
                    #fb923c,
                    #facc15);
        }

        /* PEMASUKAN */
        .border-income::before {
            background: linear-gradient(to bottom,
                    #16a34a,
                    #4ade80);
        }

        /* PENGELUARAN */
        .border-expense::before {
            background: linear-gradient(to bottom,
                    #ef4444,
                    #f87171);
        }

        /* ======================================================
            ICON
        ====================================================== */

        .stats-icon {
            width: 64px;
            height: 64px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 1.4rem;

            flex-shrink: 0;

            box-shadow:
                0 10px 20px rgba(0, 0, 0, .10),
                inset 0 1px 1px rgba(255, 255, 255, .15);
        }

        /* ======================================================
            QUICK ACTION
        ====================================================== */

        .quick-action {
            border-radius: 24px;
            transition: .3s ease;

            background: #fff;
            border: 1px solid #edf2f7;

            position: relative;
            overflow: hidden;

            box-shadow:
                0 10px 25px rgba(0, 0, 0, .05),
                0 3px 8px rgba(0, 0, 0, .03);
        }

        .quick-action::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            background: rgba(45, 206, 137, .05);
            border-radius: 50%;
            top: -70px;
            right: -70px;
        }

        .quick-action:hover {
            transform: translateY(-5px);

            background: linear-gradient(135deg,
                    var(--primary-green),
                    var(--secondary-green));

            color: #fff !important;
        }

        .quick-action:hover i,
        .quick-action:hover span {
            color: #fff !important;
        }

        /* ======================================================
            TABLE
        ====================================================== */

        .custom-table thead {
            background: #f9fafb;
        }

        .custom-table th {
            border-top: none !important;
            font-size: .78rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--muted-text);
            font-weight: 700;
            padding: 18px 22px;
        }

        .custom-table td {
            vertical-align: middle;
            border-color: #edf2f7;
            padding: 20px 22px;
        }

        /* ======================================================
            PAYMENT ICON
        ====================================================== */

        .payment-icon {
            width: 42px;
            height: 42px;

            border-radius: 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: linear-gradient(135deg,
                    var(--primary-green),
                    var(--secondary-green));

            color: #fff;

            margin-right: 16px;

            flex-shrink: 0;

            box-shadow:
                0 8px 18px rgba(45, 206, 137, .22);
        }

        /* ======================================================
            SECTION
        ====================================================== */

        .section-title {
            font-weight: 800;
            color: var(--dark-text);
        }

        .view-btn {
            border-radius: 50px;
            padding: 10px 22px;
            font-size: .78rem;
            letter-spacing: .5px;

            box-shadow:
                0 8px 18px rgba(45, 206, 137, .18);
        }

        .chart-container {
            position: relative;
            height: 380px;
        }

        /* ======================================================
            RESPONSIVE
        ====================================================== */

        @media(max-width:1200px) {

            .stats-nominal {
                font-size: 1.5rem;
            }
        }

        @media(max-width:768px) {

            .dashboard-header {
                margin: 10px;
                padding: 35px 15px 120px;
                border-radius: 24px;
            }

            .stats-card {
                min-height: 140px;
            }

            .stats-value,
            .stats-nominal {
                font-size: 1.4rem;
            }

            .stats-icon {
                width: 56px;
                height: 56px;
            }
        }
    </style>

    <!-- HEADER -->
    <div class="header py-6 mb-2 dashboard-header">
        <div class="container-fluid">
            <div class="header-body">

                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <h2 class="text-white font-weight-bold mb-1">
                            Dashboard SAKTI
                        </h2>

                        <p class="text-white mb-0 opacity-8">
                            Monitoring data sekolah & keuangan realtime
                        </p>
                    </div>
                </div>

                <!-- STATS -->
                <div class="row mt-4">

                    <!-- TOTAL SISWA -->
                    <div class="col-xl-3 col-md-6 col-sm-6 mb-4 d-flex">
                        <div class="card dashboard-card stats-card border-students w-100">
                            <div class="card-body w-100">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <div class="stats-title mb-2">
                                            TOTAL SISWA
                                        </div>

                                        <div class="stats-value">
                                            {{ $totalStudents }}
                                        </div>
                                    </div>

                                    <div class="stats-icon bg-primary text-white">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- KELAS -->
                    <div class="col-xl-3 col-md-6 col-sm-6 mb-4 d-flex">
                        <div class="card dashboard-card stats-card border-class w-100">
                            <div class="card-body w-100">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <div class="stats-title mb-2">
                                            KELAS & JURUSAN
                                        </div>

                                        <div class="stats-value">
                                            {{ $totalClassrooms }}

                                            <span class="text-muted" style="font-size:1rem;">
                                                / {{ $totalMajors }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="stats-icon bg-warning text-white">
                                        <i class="fas fa-school"></i>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- PEMASUKAN -->
                    <div class="col-xl-3 col-md-6 col-sm-6 mb-4 d-flex">
                        <div class="card dashboard-card stats-card border-income w-100">

                            <div class="card-body w-100">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <div class="stats-title mb-2">
                                            PEMASUKAN
                                        </div>

                                        <div class="stats-nominal text-success">
                                            Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                                        </div>

                                    </div>

                                    <div class="stats-icon text-success" style="background: var(--soft-green);">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- PENGELUARAN -->
                    <div class="col-xl-3 col-md-6 col-sm-6 mb-4 d-flex">
                        <div class="card dashboard-card stats-card border-expense w-100">

                            <div class="card-body w-100">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <div class="stats-title mb-2">
                                            PENGELUARAN
                                        </div>

                                        <div class="stats-nominal text-danger">
                                            Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                                        </div>

                                    </div>

                                    <div class="stats-icon bg-danger text-white">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="container-fluid mt--6">

                        <div class="row">

                            <!-- CHART -->
                            <div class="col-xl-8 mb-4">
                                <div class="card dashboard-card h-100">

                                    <div class="card-header bg-white border-0 pt-4">
                                        <h3 class="section-title mb-0">
                                            Statistik Keuangan 6 Bulan Terakhir
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="financeChart"></canvas>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- QUICK ACTION -->
                            <div class="col-xl-4 mb-4">
                                <div class="card dashboard-card h-100">

                                    <div class="card-header bg-white border-0 pt-4">
                                        <h3 class="section-title mb-0">
                                            Aksi Cepat
                                        </h3>
                                    </div>

                                    <div class="card-body">

                                        <div class="row">

                                            <div class="col-6 mb-4">
                                                <a href="{{ route('admin.students.create') }}"
                                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 text-primary text-decoration-none h-100">

                                                    <i class="fas fa-user-plus fa-2x mb-3"></i>

                                                    <span class="font-weight-bold text-center">
                                                        Siswa Baru
                                                    </span>

                                                </a>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <a href="{{ route('admin.transactions.create') }}"
                                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 text-success text-decoration-none h-100">

                                                    <i class="fas fa-wallet fa-2x mb-3"></i>

                                                    <span class="font-weight-bold text-center">
                                                        Transaksi
                                                    </span>

                                                </a>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <a href="{{ route('admin.reports.payment') }}"
                                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 text-info text-decoration-none h-100">

                                                    <i class="fas fa-chart-line fa-2x mb-3"></i>

                                                    <span class="font-weight-bold text-center">
                                                        Laporan
                                                    </span>

                                                </a>
                                            </div>

                                            <div class="col-6 mb-4">
                                                <a href="{{ route('admin.spp.index') }}"
                                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 text-warning text-decoration-none h-100">

                                                    <i class="fas fa-file-invoice-dollar fa-2x mb-3"></i>

                                                    <span class="font-weight-bold text-center">
                                                        Tagihan SPP
                                                    </span>

                                                </a>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>

                        <!-- TABLE -->
                        <div class="row">
                            <div class="col-12">

                                <div class="card dashboard-card">

                                    <div
                                        class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

                                        <h3 class="section-title mb-0">
                                            Histori Pembayaran Terbaru
                                        </h3>

                                        <a href="{{ route('admin.spp.recap') }}" class="btn btn-success view-btn">
                                            Lihat Semua
                                        </a>

                                    </div>

                                    <div class="table-responsive">

                                        <table class="table custom-table align-items-center mb-0 text-center">

                                            <thead>
                                                <tr>
                                                    <th class="text-center">Tanggal</th>
                                                    <th class="text-center">No KK</th>
                                                    <th class="text-center">Metode</th>
                                                    <th class="text-center">Nominal</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                @forelse($recentPayments as $pay)
                                                    <tr>

                                                        <!-- TANGGAL -->
                                                        <td class="align-middle text-center">
                                                            <div class="d-flex align-items-center justify-content-center">

                                                                <div class="payment-icon me-3">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </div>

                                                                <div class="font-weight-bold">
                                                                    {{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}
                                                                </div>

                                                            </div>
                                                        </td>

                                                        <!-- NO KK -->
                                                        <td class="align-middle text-center text-muted font-weight-bold">
                                                            {{ $pay->family_card_number }}
                                                        </td>

                                                        <!-- METODE -->
                                                        <td class="align-middle text-center">

                                                            @php
                                                                $method = strtolower(
                                                                    trim($pay->payment_method ?? 'cash'),
                                                                );
                                                            @endphp

                                                            @if ($method == 'qris')
                                                                <span class="badge bg-info text-white px-3 py-2">
                                                                    QRIS
                                                                </span>
                                                            @elseif($method == 'cash')
                                                                <span class="badge bg-success text-white px-3 py-2">
                                                                    CASH
                                                                </span>
                                                            @elseif($method == 'transfer')
                                                                <span class="badge bg-primary text-white px-3 py-2">
                                                                    TRANSFER
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary text-white px-3 py-2">
                                                                    {{ strtoupper($pay->payment_method ?? 'CASH') }}
                                                                </span>
                                                            @endif

                                                        </td>

                                                        <!-- NOMINAL -->
                                                        <td class="align-middle text-center font-weight-bold text-success">
                                                            + Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                                        </td>

                                                    </tr>

                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-5 text-muted">
                                                            <i class="fas fa-receipt fa-2x mb-3"></i>
                                                            <br>
                                                            Belum ada pembayaran terbaru
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

                    <!-- CHART -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {

                            const ctx = document.getElementById('financeChart');

                            new Chart(ctx, {

                                type: 'bar',

                                data: {
                                    labels: @js($chartLabels),

                                    datasets: [{
                                            label: 'Pemasukan',
                                            data: @js($chartIncome),
                                            backgroundColor: 'rgba(45, 206, 137, .8)',
                                            borderRadius: 12,
                                            borderSkipped: false,
                                        },

                                        {
                                            label: 'Pengeluaran',
                                            data: @js($chartExpense),
                                            backgroundColor: 'rgba(245, 54, 92, .8)',
                                            borderRadius: 12,
                                            borderSkipped: false,
                                        }
                                    ]
                                },

                                options: {

                                    responsive: true,
                                    maintainAspectRatio: false,

                                    interaction: {
                                        mode: 'index',
                                        intersect: false
                                    },

                                    plugins: {

                                        legend: {
                                            position: 'top',
                                            labels: {
                                                usePointStyle: true,
                                                padding: 20
                                            }
                                        },

                                        tooltip: {
                                            backgroundColor: '#111827',
                                            padding: 14,
                                            cornerRadius: 12,

                                            callbacks: {
                                                label: function(context) {
                                                    return context.dataset.label + ': Rp ' +
                                                        context.parsed.y.toLocaleString('id-ID');
                                                }
                                            }
                                        }
                                    },

                                    scales: {

                                        x: {
                                            grid: {
                                                display: false
                                            }
                                        },

                                        y: {
                                            beginAtZero: true,

                                            ticks: {
                                                callback: function(value) {

                                                    if (value >= 1000000) {
                                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                                    }

                                                    if (value >= 1000) {
                                                        return 'Rp ' + (value / 1000) + ' Rb';
                                                    }

                                                    return 'Rp ' + value;
                                                }
                                            },

                                            grid: {
                                                borderDash: [5, 5]
                                            }
                                        }
                                    }
                                }
                            });

                        });
                    </script>
                @endsection
