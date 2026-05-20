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

            /* LENGKUNG */
            border-radius: 25px;

            /* JARAK */
            margin: 18px;
            padding: 45px 20px 140px;

            /* BAYANGAN */
            box-shadow:
                0 20px 45px rgb(73, 106, 77),
                0 10px 25px rgba(45, 206, 137, .18),
                inset 0 1px 1px rgba(255, 255, 255, .08);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            background: rgba(244, 7, 7, 0.08);
            border-radius: 50%;
            top: -120px;
            right: -90px;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            background: rgba(255, 255, 255, .06);
            border-radius: 50%;
            bottom: -110px;
            left: -60px;
        }

        /* ======================================================
        CARD GLOBAL
    ====================================================== */

        .dashboard-card {
            border: none;
            border-radius: var(--card-radius);

            transition: .3s ease;
            overflow: hidden;

            background: #ffffff;

            /* BAYANGAN */
            box-shadow:
                0 10px 30px rgba(0, 0, 0, .06),
                0 3px 10px rgba(0, 0, 0, .04);
        }

        .dashboard-card:hover {
            transform: translateY(-6px);

            box-shadow:
                0 18px 40px rgba(0, 0, 0, .12),
                0 8px 18px rgba(0, 0, 0, .08) !important;
        }

        /* ======================================================
        STATS CARD
    ====================================================== */

        .stats-card {
            position: relative;
            background: #fff;

            border-radius: 24px;

            /* BAYANGAN */
            box-shadow:
                0 10px 25px rgba(0, 0, 0, .07),
                0 4px 10px rgba(0, 0, 0, .04);
        }

        .stats-card:hover {
            transform: translateY(-7px);

            box-shadow:
                0 22px 45px rgba(0, 0, 0, .12),
                0 10px 20px rgba(45, 206, 137, .10);
        }

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

        /* ======================================================
        ICON
    ====================================================== */

        .stats-icon {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;
            font-size: 1.3rem;

            /* BAYANGAN ICON */
            box-shadow:
                0 10px 20px rgba(0, 0, 0, .10),
                inset 0 1px 1px rgba(255, 255, 255, .12);
        }

        .stats-title {
            font-size: .78rem;
            font-weight: 700;

            color: var(--muted-text);
        }

        .stats-value {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--dark-text);
        }

        /* ======================================================
        ACTION BUTTON
    ====================================================== */

        .quick-action {
            border-radius: 22px;
            transition: .3s ease;

            background: #fff;
            border: 1px solid #edf2f7;

            /* BAYANGAN */
            box-shadow:
                0 10px 25px rgba(0, 0, 0, .05),
                0 3px 8px rgba(0, 0, 0, .03);
        }

        .quick-action:hover {
            transform: translateY(-5px) scale(1.02);

            background: linear-gradient(135deg,
                    var(--primary-green),
                    var(--secondary-green));

            color: #fff !important;

            box-shadow:
                0 20px 35px rgba(45, 206, 137, .22),
                0 8px 18px rgba(0, 0, 0, .08);
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
            font-size: .75rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--muted-text);
            font-weight: 700;
        }

        .custom-table td {
            vertical-align: middle;
            border-color: #f1f5f9;
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

            /* BAYANGAN */
            box-shadow:
                0 8px 18px rgba(45, 206, 137, .22);
        }

        /* ======================================================
        CHART
    ====================================================== */

        .chart-container {
            position: relative;
            height: 380px;
        }

        /* ======================================================
        SECTION TITLE
    ====================================================== */

        .section-title {
            font-weight: 700;
            color: var(--dark-text);
        }

        /* ======================================================
        BUTTON
    ====================================================== */

        .view-btn {
            border-radius: 50px;
            padding: 8px 18px;
            font-size: .72rem;
            letter-spacing: .5px;

            box-shadow:
                0 8px 18px rgba(45, 206, 137, .18);
        }

        .stats-nominal{
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1;
}

        /* ======================================================
        RESPONSIVE
    ====================================================== */

        @media(max-width:768px) {

            .stats-value {
                font-size: 1.3rem;
            }

            .dashboard-header {
                border-radius: 28px;

                margin: 10px;

                padding:
                    35px 15px 120px;
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
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card stats-card shadow-sm">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>
                                        <div class="stats-title mb-2">
                                            TOTAL SISWA
                                        </div>

                                        <div class="stats-value">
                                            {{ $totalStudents }}
                                        </div>
                                    </div>

                                    <div class="stats-icon bg-success text-white">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- KELAS -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card dashboard-card stats-card shadow-sm">
                            <div class="card-body">

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
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card dashboard-card stats-card">

                            <div class="card-body">

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
                    <div class="col-xl-3 col-md-6 mb-4">

                        <div class="card dashboard-card stats-card">

                            <div class="card-body">

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

                </div>

            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="container-fluid mt--6">

        <div class="row">

            <!-- CHART -->
            <div class="col-xl-8 mb-4">
                <div class="card dashboard-card shadow-sm h-100">

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
                <div class="card dashboard-card shadow-sm h-100">

                    <div class="card-header bg-white border-0 pt-4">
                        <h3 class="section-title mb-0">
                            Aksi Cepat
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-6 mb-4">
                                <a href="{{ route('admin.students.create') }}"
                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 shadow-sm text-primary text-decoration-none h-100">

                                    <i class="fas fa-user-plus fa-2x mb-3"></i>

                                    <span class="font-weight-bold text-center">
                                        Siswa Baru
                                    </span>

                                </a>
                            </div>

                            <div class="col-6 mb-4">
                                <a href="{{ route('admin.transactions.create') }}"
                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 shadow-sm text-success text-decoration-none h-100">

                                    <i class="fas fa-wallet fa-2x mb-3"></i>

                                    <span class="font-weight-bold text-center">
                                        Transaksi
                                    </span>

                                </a>
                            </div>

                            <div class="col-6 mb-4">
                                <a href="{{ route('admin.reports.payment') }}"
                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 shadow-sm text-info text-decoration-none h-100">

                                    <i class="fas fa-chart-line fa-2x mb-3"></i>

                                    <span class="font-weight-bold text-center">
                                        Laporan
                                    </span>

                                </a>
                            </div>

                            <div class="col-6 mb-4">
                                <a href="{{ route('admin.spp.index') }}"
                                    class="quick-action d-flex flex-column align-items-center justify-content-center p-4 shadow-sm text-warning text-decoration-none h-100">

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

                <div class="card dashboard-card shadow-sm">

                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

                        <h3 class="section-title mb-0">
                            Histori Pembayaran Terbaru
                        </h3>

                        <a href="{{ route('admin.spp.recap') }}" class="btn btn-success view-btn">
                            Lihat Semua
                        </a>

                    </div>

                    <div class="table-responsive">

                        <table class="table custom-table align-items-center">

                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No KK</th>
                                    <th>Metode</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($recentPayments as $pay)
                                    <tr>

                                        <td>
                                            <div class="d-flex align-items-center">

                                                <div class="payment-icon mr-3">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>

                                                <div class="font-weight-bold">
                                                    {{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}
                                                </div>

                                            </div>
                                        </td>

                                        <td class="text-muted font-weight-bold">
                                            {{ $pay->family_card_number }}
                                        </td>

                                        <td>
                                            <span class="badge badge-success px-3 py-2">
                                                {{ $pay->payment_method }}
                                            </span>
                                        </td>

                                        <td class="font-weight-bold text-success">
                                            + Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                        </td>

                                    </tr>

                                @empty
                                    <x-empty-state />
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

            const financeChart = new Chart(ctx, {

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
