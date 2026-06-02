@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        /* Modern Segmented Control Pills */
        .fin-type-toggle {
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0;
            padding: 3px !important;
            border-radius: 50px;
            display: inline-flex;
            gap: 2px;
        }
        .btn-toggle-pill {
            background: transparent;
            border: none;
            outline: none;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            border-radius: 50px;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .btn-toggle-pill:hover {
            color: #334155;
        }
        .btn-toggle-pill.active {
            background-color: #fff;
            color: #059669;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        /* Custom Input Groups & Fields */
        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-group-custom i {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 0.8rem;
            pointer-events: none;
            z-index: 10;
        }
        .st-select-premium {
            padding-left: 36px !important;
            padding-right: 16px !important;
            height: 38px;
            border-radius: 50px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s ease;
            cursor: pointer;
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        /* Custom arrow for select fields */
        select.st-select-premium {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 10px 10px;
            padding-right: 32px !important;
        }
        .st-select-premium:hover {
            border-color: #cbd5e1;
            background: #fff;
        }
        .st-select-premium:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
            background: #fff;
        }

        /* Premium Buttons */
        .btn-filter-premium {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff !important;
            border: none;
            font-weight: 700;
            font-size: 0.78rem;
            border-radius: 50px;
            height: 38px;
            padding: 0 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-filter-premium:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.25);
        }
        .btn-filter-premium:active {
            transform: translateY(0);
        }
        .btn-reset-premium {
            background: #f1f5f9;
            color: #475569 !important;
            border: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 0.78rem;
            border-radius: 50px;
            height: 38px;
            padding: 0 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-reset-premium:hover {
            background: #e2e8f0;
            color: #1e293b !important;
            text-decoration: none;
        }
    </style>
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
                <div class="mt-2 pt-2 border-top border-light d-flex justify-content-between align-items-center" style="font-size: .72rem; font-weight: 500; color: rgba(255,255,255,.9);">
                    <span><i class="fas fa-circle me-1" style="font-size: .5rem; opacity: .8;"></i> Aktif: <strong>{{ $activeStudentsCount }}</strong></span>
                    <span><i class="fas fa-graduation-cap me-1" style="font-size: .65rem; opacity: .8;"></i> Lulus: <strong>{{ $graduatedStudentsCount }}</strong></span>
                    <span><i class="fas fa-sign-out-alt me-1" style="font-size: .65rem; opacity: .8;"></i> Keluar: <strong>{{ $leftStudentsCount }}</strong></span>
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

    <!-- DYNAMIC FINANCIAL SUMMARY SECTION -->
    <div class="card dashboard-card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="section-title mb-0">
                        <i class="fas fa-wallet me-2" style="color: var(--primary-green); opacity: .8;"></i>
                        Ringkasan Finansial Sekolah
                    </h5>
                    <p class="text-sm text-muted mb-0">Laporan pemasukan, pengeluaran, dan sisa kas berdasarkan filter waktu.</p>
                </div>
                
                <!-- Filter Form -->
                <form action="{{ route('kepala-sekolah.dashboard') }}" method="GET" id="finFilterForm" class="d-flex align-items-center gap-3 flex-wrap mt-3 mt-lg-0">
                    <!-- Segmented Toggle Control -->
                    <div class="fin-type-toggle">
                        <button type="button" class="btn-toggle-pill {{ $filterType === 'month' ? 'active' : '' }}" data-val="month">Bulan</button>
                        <button type="button" class="btn-toggle-pill {{ $filterType === 'semester' ? 'active' : '' }}" data-val="semester">Semester</button>
                        <button type="button" class="btn-toggle-pill {{ $filterType === 'year' ? 'active' : '' }}" data-val="year">Tahun</button>
                    </div>
                    <input type="hidden" name="fin_filter_type" id="finFilterType" value="{{ $filterType }}">

                    <!-- Month Input Group -->
                    <div id="finMonthWrapper" class="filter-input-wrapper input-group-custom">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="month" name="fin_month" class="st-select-premium" style="width: 170px;" value="{{ $selectedMonth }}">
                    </div>

                    <!-- Semester Select Group -->
                    <div id="finSemesterWrapper" class="filter-input-wrapper input-group-custom" style="display: none;">
                        <i class="fas fa-graduation-cap"></i>
                        <select name="fin_semester_id" class="st-select-premium" style="width: 250px;">
                            @foreach($semestersList as $sem)
                                <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->academic_year_name }} - {{ $sem->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Select Group -->
                    <div id="finYearWrapper" class="filter-input-wrapper input-group-custom" style="display: none;">
                        <i class="fas fa-clock"></i>
                        <select name="fin_year" class="st-select-premium" style="width: 160px;">
                            @foreach($yearsList as $yr)
                                <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                    Tahun {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn-filter-premium">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        
                        <a href="{{ route('kepala-sekolah.dashboard') }}" class="btn-reset-premium">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Financial Metrics Results -->
            <div class="row">
                <!-- TOTAL PEMASUKAN -->
                <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
                    <div class="summary-card sc-green h-100" style="min-height: 120px;">
                        <div class="sc-icon"><i class="fas fa-arrow-down"></i></div>
                        <div class="sc-label">Jumlah Pemasukan</div>
                        <div class="sc-value">Rp {{ number_format($filteredIncome, 0, ',', '.') }}</div>
                        <div class="mt-2" style="font-size: .75rem; font-weight: 500; color: rgba(255,255,255,.8);">
                            @if($filterType === 'month')
                                Bulan {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                            @elseif($filterType === 'semester')
                                Semester: {{ $semestersList->firstWhere('id', $selectedSemesterId)->academic_year_name ?? '' }} - {{ $semestersList->firstWhere('id', $selectedSemesterId)->name ?? '' }}
                            @elseif($filterType === 'year')
                                Tahun {{ $selectedYear }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TOTAL PENGELUARAN -->
                <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
                    <div class="summary-card sc-red h-100" style="min-height: 120px;">
                        <div class="sc-icon"><i class="fas fa-arrow-up"></i></div>
                        <div class="sc-label">Jumlah Pengeluaran</div>
                        <div class="sc-value">Rp {{ number_format($filteredExpense, 0, ',', '.') }}</div>
                        <div class="mt-2" style="font-size: .75rem; font-weight: 500; color: rgba(255,255,255,.8);">
                            @if($filterType === 'month')
                                Bulan {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}
                            @elseif($filterType === 'semester')
                                Semester: {{ $semestersList->firstWhere('id', $selectedSemesterId)->academic_year_name ?? '' }} - {{ $semestersList->firstWhere('id', $selectedSemesterId)->name ?? '' }}
                            @elseif($filterType === 'year')
                                Tahun {{ $selectedYear }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SELISIH (SISA) -->
                <div class="col-xl-4 col-md-12">
                    <div class="summary-card {{ $filteredDifference >= 0 ? 'sc-purple' : 'sc-orange' }} h-100" style="min-height: 120px;">
                        <div class="sc-icon">
                            <i class="fas {{ $filteredDifference >= 0 ? 'fa-balance-scale-right' : 'fa-balance-scale-left' }}"></i>
                        </div>
                        <div class="sc-label">Selisih (Sisa)</div>
                        <div class="sc-value">
                            {{ $filteredDifference < 0 ? '-' : '' }} Rp {{ number_format(abs($filteredDifference), 0, ',', '.') }}
                        </div>
                        <div class="mt-2" style="font-size: .75rem; font-weight: 500; color: rgba(255,255,255,.8);">
                            Status: <strong>{{ $filteredDifference >= 0 ? 'Surplus' : 'Defisit' }}</strong>
                        </div>
                    </div>
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
        // --- Financial filter toggle & button interaction ---
        const finFilterType = document.getElementById('finFilterType');
        const finMonthWrapper = document.getElementById('finMonthWrapper');
        const finSemesterWrapper = document.getElementById('finSemesterWrapper');
        const finYearWrapper = document.getElementById('finYearWrapper');
        const toggleButtons = document.querySelectorAll('.btn-toggle-pill');

        function toggleFinFilters() {
            if (!finFilterType) return;
            const val = finFilterType.value;
            if (finMonthWrapper) finMonthWrapper.style.display = 'none';
            if (finSemesterWrapper) finSemesterWrapper.style.display = 'none';
            if (finYearWrapper) finYearWrapper.style.display = 'none';

            if (val === 'month') {
                if (finMonthWrapper) finMonthWrapper.style.display = 'flex';
            } else if (val === 'semester') {
                if (finSemesterWrapper) finSemesterWrapper.style.display = 'flex';
            } else if (val === 'year') {
                if (finYearWrapper) finYearWrapper.style.display = 'flex';
            }
        }

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                toggleButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (finFilterType) {
                    finFilterType.value = this.getAttribute('data-val');
                    toggleFinFilters();
                }
            });
        });

        // Initialize state
        toggleFinFilters();
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
