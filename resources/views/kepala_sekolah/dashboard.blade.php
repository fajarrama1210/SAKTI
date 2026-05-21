@extends('_admin.layouts.app')

@section('content')
<style>
    :root {
        --primary-green: #1a8a5c;
        --secondary-green: #2dce89;
        --soft-green: #e9fff5;
        --dark-text: #344767;
        --muted-text: #8392ab;
        --card-radius: 20px;
    }

    body {
        background: #f8fafc;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 40%, #134e4a 100%);
        position: relative;
        overflow: hidden;
        border-radius: 25px;
        margin: 18px;
        padding: 40px 24px 120px;
        box-shadow: 0 20px 45px rgba(15, 118, 110, 0.25);
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        top: -100px;
        right: -80px;
    }

    .dashboard-card {
        border: none;
        border-radius: var(--card-radius);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .08) !important;
    }

    .stats-card {
        position: relative;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        border-left: 5px solid var(--primary-green);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 1.25rem;
    }

    .stats-title {
        font-size: .75rem;
        font-weight: 700;
        color: var(--muted-text);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--dark-text);
    }

    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
    }

    .section-title {
        font-weight: 700;
        color: var(--dark-text);
    }
</style>

<!-- HEADER -->
<div class="header py-6 mb-2 dashboard-header">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col">
                    <h2 class="text-white font-weight-bold mb-1">
                        Monitoring Kepala Sekolah
                    </h2>
                    <p class="text-white mb-0 opacity-8">
                        Laporan pemantauan data akademik, tagihan SPP, dan perkembangan finansial sekolah secara realtime.
                    </p>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="row mt-4">
                <!-- TOTAL SISWA -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card dashboard-card stats-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stats-title mb-2">Total Siswa</div>
                                    <div class="stats-value">{{ $totalStudents }} Siswa</div>
                                    <small class="text-muted">{{ $totalClassrooms }} Kelas / {{ $totalMajors }} Jurusan</small>
                                </div>
                                <div class="stats-icon bg-gradient-info text-white">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOTAL PENERIMAAN -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card dashboard-card stats-card shadow-sm" style="border-left-color: #2dce89;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stats-title mb-2">Penerimaan SPP (Terbayar)</div>
                                    <div class="stats-value text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                                    <small class="text-muted">Dari total tagihan Rp {{ number_format($totalBilled, 0, ',', '.') }}</small>
                                </div>
                                <div class="stats-icon bg-gradient-success text-white">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SISA TAGIHAN -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card dashboard-card stats-card shadow-sm" style="border-left-color: #f5365c;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stats-title mb-2">Tunggakan SPP (Belum Bayar)</div>
                                    <div class="stats-value text-danger">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                                    <small class="text-muted">Harus ditagihkan ke siswa</small>
                                </div>
                                <div class="stats-icon bg-gradient-danger text-white">
                                    <i class="fas fa-exclamation-circle"></i>
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
        <!-- CHART 1: FINANCIAL COMPARISON -->
        <div class="col-xl-8 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="section-title mb-0">Statistik Finansial (6 Bulan Terakhir)</h5>
                    <p class="text-sm text-muted mb-0">Perbandingan pemasukan vs pengeluaran sekolah.</p>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART 2: SPP BILL STATUS -->
        <div class="col-xl-4 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="section-title mb-0">Status Tagihan SPP</h5>
                    <p class="text-sm text-muted mb-0">Rasio lunas vs belum lunas (berdasarkan jumlah invoice).</p>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="chart-container" style="height: 240px;">
                        <canvas id="sppStatusChart"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <span class="badge bg-success me-2"><i class="fas fa-circle text-xxs me-1"></i> Lunas: {{ $paidBillsCount }}</span>
                        <span class="badge bg-danger"><i class="fas fa-circle text-xxs me-1"></i> Belum Lunas: {{ $unpaidBillsCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CHART 3: CLASSROOM STUDENTS DISTRIBUTION -->
        <div class="col-xl-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="section-title mb-0">Distribusi Siswa per Kelas</h5>
                    <p class="text-sm text-muted mb-0">Visualisasi jumlah siswa aktif di masing-masing kelas.</p>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="studentDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART 4: PAYMENT METHODS -->
        <div class="col-xl-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="section-title mb-0">Metode Pembayaran SPP</h5>
                    <p class="text-sm text-muted mb-0">Frekuensi penggunaan metode pembayaran oleh siswa.</p>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="chart-container" style="height: 260px;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE: RECENT PAYMENTS -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card dashboard-card shadow-sm">
                <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="section-title mb-0">Histori Pembayaran SPP Terbaru</h5>
                        <p class="text-sm text-muted mb-0">Daftar transaksi pembayaran SPP terakhir masuk.</p>
                    </div>
                    <a href="{{ route('kepala-sekolah.bills') }}" class="btn btn-sm btn-outline-success border-0 px-3">
                        Lihat Semua Tagihan <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-xs font-weight-bold">Tanggal</th>
                                <th class="text-xs font-weight-bold">Nama Siswa</th>
                                <th class="text-xs font-weight-bold">No. Kartu Keluarga</th>
                                <th class="text-xs font-weight-bold">Metode</th>
                                <th class="text-xs font-weight-bold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $pay)
                                <tr>
                                    <td class="text-sm font-weight-bold">
                                        {{ \Carbon\Carbon::parse($pay->payment_date)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="text-sm font-weight-bold text-dark">{{ $pay->student_name }}</td>
                                    <td class="text-sm text-muted">{{ $pay->family_card_number }}</td>
                                    <td>
                                        <span class="badge bg-light text-success px-3 py-2 font-weight-bold text-xs">{{ $pay->payment_method }}</span>
                                    </td>
                                    <td class="text-sm font-weight-bold text-success">+ Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi pembayaran masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- 1. Financial Chart (Income vs Expense) ---
        const financialCtx = document.getElementById('financialChart').getContext('2d');
        new Chart(financialCtx, {
            type: 'bar',
            data: {
                labels: @js($chartLabels),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @js($chartIncome),
                        backgroundColor: 'rgba(45, 206, 137, 0.85)',
                        borderColor: '#2dce89',
                        borderWidth: 1,
                        borderRadius: 8,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @js($chartExpense),
                        backgroundColor: 'rgba(245, 54, 92, 0.85)',
                        borderColor: '#f5365c',
                        borderWidth: 1,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });

        // --- 2. SPP Status Chart (Doughnut) ---
        const sppCtx = document.getElementById('sppStatusChart').getContext('2d');
        new Chart(sppCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Belum Lunas'],
                datasets: [{
                    data: [{{ $paidBillsCount }}, {{ $unpaidBillsCount }}],
                    backgroundColor: ['#2dce89', '#f5365c'],
                    hoverOffset: 4,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '75%'
            }
        });

        // --- 3. Student Distribution Chart (Horizontal Bar) ---
        const distCtx = document.getElementById('studentDistributionChart').getContext('2d');
        new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: @js($classroomNames),
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: @js($classroomStudentCounts),
                    backgroundColor: 'rgba(26, 138, 92, 0.8)',
                    borderColor: '#1a8a5c',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 5 } }
                }
            }
        });

        // --- 4. Payment Methods Chart (Pie) ---
        const methodCtx = document.getElementById('paymentMethodsChart').getContext('2d');
        new Chart(methodCtx, {
            type: 'pie',
            data: {
                labels: @js($paymentMethods),
                datasets: [{
                    data: @js($paymentMethodCounts),
                    backgroundColor: [
                        '#1a8a5c',
                        '#2dce89',
                        '#11cdef',
                        '#fb6340',
                        '#f5365c',
                        '#8965e0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12 } }
                }
            }
        });
    });
</script>
@endpush
@endsection
