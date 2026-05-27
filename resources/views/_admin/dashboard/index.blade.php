@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')

    <!-- HEADER -->
    <div class="dashboard-header mb-4">
        <div class="container-fluid">
            <div class="header-body position-relative" style="z-index: 1;">
                <h2 class="text-white font-weight-bold mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                    Dashboard SAKTI
                </h2>
                <p class="text-white mb-0" style="opacity: .75; font-size: 0.9rem;">
                    Monitoring data sekolah & keuangan realtime
                </p>
            </div>
        </div>
    </div>

    <!-- STATS CARDS — Terpisah dari header -->
    <div class="row mb-4">

        <!-- TOTAL SISWA -->
        <div class="col-xl-3 col-md-6 col-sm-6 mb-4 mb-xl-0">
            <div class="card dashboard-card stats-card border-students w-100">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-title mb-1">TOTAL SISWA</div>
                            <div class="stats-value">{{ $totalStudents }}</div>
                        </div>
                        <div class="stats-icon icon-students">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KELAS & JURUSAN -->
        <div class="col-xl-3 col-md-6 col-sm-6 mb-4 mb-xl-0">
            <div class="card dashboard-card stats-card border-class w-100">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-title mb-1">KELAS & JURUSAN</div>
                            <div class="stats-value">
                                {{ $totalClassrooms }}
                                <span style="font-size: .9rem; font-weight: 500; color: var(--muted-text);">/ {{ $totalMajors }}</span>
                            </div>
                        </div>
                        <div class="stats-icon icon-class">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PEMASUKAN -->
        <div class="col-xl-3 col-md-6 col-sm-6 mb-4 mb-xl-0">
            <div class="card dashboard-card stats-card border-income w-100">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-title mb-1">PEMASUKAN</div>
                            <div class="stats-nominal" style="color: #059669;">
                                Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="stats-icon icon-income">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PENGELUARAN -->
        <div class="col-xl-3 col-md-6 col-sm-6 mb-4 mb-xl-0">
            <div class="card dashboard-card stats-card border-expense w-100">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-title mb-1">PENGELUARAN</div>
                            <div class="stats-nominal" style="color: #ef4444;">
                                Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="stats-icon icon-expense">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- CHART & QUICK ACTION -->
    <div class="row mb-4">

        <!-- CHART -->
        <div class="col-xl-8 mb-4 mb-xl-0">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-chart-bar me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Statistik Keuangan 6 Bulan Terakhir
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTION -->
        <div class="col-xl-4">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-bolt me-2" style="color: #f59e0b; opacity: .8;"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">

                        <div class="col-6">
                            <a href="{{ route('admin.students.create') }}"
                                class="quick-action d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none h-100"
                                style="color: #6366f1;">
                                <i class="fas fa-user-plus fa-lg mb-2"></i>
                                <span class="font-weight-bold text-center" style="font-size: .82rem;">Siswa Baru</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('admin.transactions.create') }}"
                                class="quick-action d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none h-100"
                                style="color: #059669;">
                                <i class="fas fa-wallet fa-lg mb-2"></i>
                                <span class="font-weight-bold text-center" style="font-size: .82rem;">Transaksi</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('admin.reports.payment') }}"
                                class="quick-action d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none h-100"
                                style="color: #0ea5e9;">
                                <i class="fas fa-chart-line fa-lg mb-2"></i>
                                <span class="font-weight-bold text-center" style="font-size: .82rem;">Laporan</span>
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('admin.spp.index') }}"
                                class="quick-action d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none h-100"
                                style="color: #f59e0b;">
                                <i class="fas fa-file-invoice-dollar fa-lg mb-2"></i>
                                <span class="font-weight-bold text-center" style="font-size: .82rem;">Tagihan SPP</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- HISTORI PEMBAYARAN -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-receipt me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Histori Pembayaran Terbaru
                    </h3>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-success view-btn">
                        Lihat Semua <i class="fas fa-arrow-right ms-1" style="font-size: .7rem;"></i>
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
                                    <td class="align-middle text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="payment-icon me-2">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div class="font-weight-bold" style="font-size: .88rem;">
                                                {{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center font-weight-bold" style="color: var(--muted-text);">
                                        {{ $pay->family_card_number }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @php
                                            $method = strtolower(trim($pay->payment_method ?? 'cash'));
                                        @endphp
                                        @if ($method == 'qris')
                                            <span class="badge px-3 py-2" style="background: #ecfeff; color: #0891b2; font-weight: 600;">QRIS</span>
                                        @elseif($method == 'cash')
                                            <span class="badge px-3 py-2" style="background: #ecfdf5; color: #059669; font-weight: 600;">CASH</span>
                                        @elseif($method == 'transfer')
                                            <span class="badge px-3 py-2" style="background: #eff6ff; color: #2563eb; font-weight: 600;">TRANSFER</span>
                                        @else
                                            <span class="badge px-3 py-2" style="background: #f8fafc; color: #64748b; font-weight: 600;">
                                                {{ strtoupper($pay->payment_method ?? 'CASH') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center font-weight-bold" style="color: #059669;">
                                        + Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5" style="color: var(--muted-text);">
                                        <i class="fas fa-receipt fa-2x mb-3 d-block" style="opacity: .4;"></i>
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

    <!-- PENGAJUAN IZIN/SAKIT PENDING -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-envelope-open-text me-2" style="color: #f59e0b; opacity: .8;"></i>
                        Pengajuan Izin/Sakit Pending
                    </h3>
                    <a href="{{ route('admin.letters.index') }}" class="btn btn-sm btn-warning view-btn" style="color: #fff;">
                        Kelola Pengajuan ({{ $pendingLettersCount }})
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table align-items-center mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLetters as $letter)
                                <tr>
                                    <td class="align-middle text-center font-weight-bold" style="font-size: .88rem;">
                                        {{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="align-middle text-center font-weight-bold" style="color: var(--muted-text);">
                                        {{ $letter->student_name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($letter->type == 'sick')
                                            <span class="badge px-3 py-2" style="background: #fef2f2; color: #ef4444; font-weight: 600;">Sakit</span>
                                        @else
                                            <span class="badge px-3 py-2" style="background: #ecfeff; color: #0891b2; font-weight: 600;">Izin</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.letters.index') }}" class="btn btn-sm mb-0" style="background: var(--soft-green); color: var(--primary-green); font-weight: 600; border-radius: 10px;">
                                            <i class="fas fa-eye me-1"></i> Tinjau
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4" style="color: var(--muted-text);">
                                        <i class="fas fa-check-circle mb-2 d-block" style="font-size: 1.5rem; opacity: .3;"></i>
                                        Belum ada pengajuan izin pending.
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

                    <!-- PENGATURAN SURAT IZIN WIDGET -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card dashboard-card">
                                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                    <h3 class="section-title mb-0">
                                        Pengajuan Izin/Sakit Pending
                                    </h3>
                                    <a href="{{ route('admin.letters.index') }}" class="btn btn-warning view-btn">
                                        Kelola Pengajuan ({{ $pendingLettersCount }})
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table custom-table align-items-center mb-0 text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Siswa</th>
                                                <th class="text-center">Kategori</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pendingLetters as $letter)
                                                <tr>
                                                    <td class="align-middle text-center font-weight-bold">
                                                        {{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d M Y') }}
                                                    </td>
                                                    <td class="align-middle text-center text-muted font-weight-bold">
                                                        {{ $letter->student_name }}
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="badge bg-info text-white px-3 py-2">
                                                            {{ $letter->type == 'sick' ? 'Sakit' : 'Izin' }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('admin.letters.index') }}" class="btn btn-sm btn-outline-primary mb-0">
                                                            Tinjau
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">
                                                        Belum ada pengajuan izin pending.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
                            backgroundColor: 'rgba(5, 150, 105, .75)',
                            borderRadius: 10,
                            borderSkipped: false,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Pengeluaran',
                            data: @js($chartExpense),
                            backgroundColor: 'rgba(239, 68, 68, .75)',
                            borderRadius: 10,
                            borderSkipped: false,
                            barPercentage: 0.6,
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
                                pointStyle: 'rectRounded',
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    weight: 600,
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { family: "'Inter', sans-serif", weight: 600 },
                            bodyFont: { family: "'Inter', sans-serif" },
                            padding: 14,
                            cornerRadius: 10,
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
                            grid: { display: false },
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11,
                                    weight: 500
                                },
                                color: '#94a3b8'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11,
                                    weight: 500
                                },
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                    return 'Rp ' + value;
                                }
                            },
                            grid: {
                                borderDash: [3, 3],
                                color: '#f1f5f9'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
