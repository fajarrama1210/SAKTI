@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')

<!-- HEADER -->
<div class="sakti-page-header mb-4">
    <div class="container-fluid">
        <div class="header-body position-relative" style="z-index: 1;">
            <h2 class="text-white font-weight-bold mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Monitoring Kepala Sekolah
            </h2>
            <p class="text-white mb-0" style="opacity: .75; font-size: 0.9rem;">
                Laporan pemantauan data akademik, tagihan SPP, dan perkembangan finansial sekolah secara realtime.
            </p>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="container-fluid">
    <!-- STATS CARDS -->
    <div class="row mb-4">
        <!-- TOTAL SISWA -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="summary-card sc-blue h-100">
                <div class="sc-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="sc-label">Total Siswa</div>
                <div class="sc-value">{{ $totalStudents }} Siswa</div>
                <div class="mt-2" style="font-size: .78rem; font-weight: 500; color: rgba(255,255,255,.8);">
                    {{ $totalClassrooms }} Kelas / {{ $totalMajors }} Jurusan
                </div>
            </div>
        </div>

        <!-- TOTAL PENERIMAAN -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="summary-card sc-green h-100">
                <div class="sc-icon"><i class="fas fa-check-circle"></i></div>
                <div class="sc-label">Penerimaan SPP (Terbayar)</div>
                <div class="sc-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="mt-2" style="font-size: .78rem; font-weight: 500; color: rgba(255,255,255,.8);">
                    Dari total tagihan Rp {{ number_format($totalBilled, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- SISA TAGIHAN -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="summary-card sc-red h-100">
                <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="sc-label">Tunggakan SPP (Belum Bayar)</div>
                <div class="sc-value">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                <div class="mt-2" style="font-size: .78rem; font-weight: 500; color: rgba(255,255,255,.8);">
                    Harus ditagihkan ke siswa
                </div>
            </div>
        </div>
    </div>
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
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="section-title mb-0">
                            <i class="fas fa-receipt me-2" style="color: var(--primary-green); opacity: .7;"></i>
                            Histori Pembayaran SPP Terbaru
                        </h5>
                        <p class="text-sm text-muted mb-0">Daftar transaksi pembayaran SPP terakhir masuk.</p>
                    </div>
                    <a href="{{ route('kepala-sekolah.bills') }}" class="btn btn-sm btn-success view-btn">
                        Lihat Semua Tagihan <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>No. Kartu Keluarga</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $pay)
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($pay->payment_date)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="font-weight-bold text-dark">{{ $pay->student_name }}</td>
                                    <td class="text-muted">{{ $pay->family_card_number }}</td>
                                    <td>
                                        <span class="badge bg-light text-success px-3 py-2 font-weight-bold text-xs">{{ $pay->payment_method }}</span>
                                    </td>
                                    <td class="font-weight-bold text-success">+ Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
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

    <!-- PENGATURAN SURAT IZIN WIDGET -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="section-title mb-0">
                            <i class="fas fa-envelope-open-text me-2" style="color: var(--primary-green); opacity: .7;"></i>
                            Pengajuan Izin/Sakit Siswa
                        </h5>
                        <p class="text-sm text-muted mb-0">Daftar pengajuan izin atau sakit yang belum disetujui.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLetters as $letter)
                                <tr>
                                    <td class="font-weight-bold">
                                        {{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}
                                    </td>
                                    <td class="font-weight-bold text-dark">{{ $letter->student_name }}</td>
                                    <td>
                                        <span class="badge bg-light text-info px-3 py-2 font-weight-bold text-xs">{{ $letter->type == 'sick' ? 'Sakit' : 'Izin' }}</span>
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
