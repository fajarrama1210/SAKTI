@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        /* ── Report Page Specific Styles ── */
        .report-header {
            background: linear-gradient(135deg, #064e3b 0%, #059669 60%, #34d399 100%);
            border-radius: 20px;
            padding: 28px 32px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .report-header::before {
            content: '';
            position: absolute;
            width: 260px; height: 260px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            top: -120px; right: -80px;
        }
        .report-header::after {
            content: '';
            position: absolute;
            width: 160px; height: 160px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
            bottom: -80px; left: -40px;
        }
        .report-header-content { position: relative; z-index: 1; }

        /* Filter Card */
        .filter-card {
            background: #fff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 24px rgba(5,150,105,.08), 0 1px 4px rgba(0,0,0,.04);
            overflow: hidden;
        }
        .filter-card .filter-header {
            background: #f8fafc;
            padding: 14px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-card .filter-header i {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg,#059669,#34d399);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem;
        }
        .filter-card .filter-header span {
            font-weight: 700; font-size: .9rem; color: #1e293b;
        }
        .filter-body { padding: 20px 24px 24px; }
        .filter-divider {
            border: none;
            border-top: 1px dashed #e2e8f0;
            margin: 16px 0;
        }

        /* Summary Cards */
        .summary-card {
            border-radius: 14px;
            padding: 20px 22px;
            border: none;
            position: relative;
            overflow: hidden;
        }
        .summary-card::after {
            content: '';
            position: absolute;
            width: 100px; height: 100px;
            border-radius: 50%;
            top: -40px; right: -30px;
            background: rgba(255,255,255,.12);
        }
        .summary-card.sc-green  { background: linear-gradient(135deg,#059669,#34d399); }
        .summary-card.sc-orange { background: linear-gradient(135deg,#ea580c,#fb923c); }
        .summary-card.sc-red    { background: linear-gradient(135deg,#dc2626,#f87171); }
        .summary-card.sc-blue   { background: linear-gradient(135deg,#0284c7,#38bdf8); }
        .summary-card .sc-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: rgba(255,255,255,.22);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #fff;
            margin-bottom: 14px;
        }
        .summary-card .sc-label {
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: rgba(255,255,255,.75); margin-bottom: 4px;
        }
        .summary-card .sc-value {
            font-size: 1.45rem; font-weight: 800; color: #fff; letter-spacing: -.5px; line-height: 1.2;
        }

        /* Result Card */
        .result-card {
            background: #fff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 24px rgba(5,150,105,.08), 0 1px 4px rgba(0,0,0,.04);
            overflow: hidden;
        }
        .result-card .result-header {
            padding: 18px 24px 14px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
        }
        .result-card .result-header .result-title {
            font-weight: 700; font-size: .95rem; color: #1e293b;
            display: flex; align-items: center; gap: 8px;
        }
        .result-card .result-header .result-title .badge-count {
            background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;
            font-size: .72rem; font-weight: 700; padding: 3px 10px; border-radius: 50px;
        }

        /* Table */
        .report-table thead tr {
            background: #f8fafc;
        }
        .report-table thead th {
            border-top: none !important;
            font-size: .68rem; letter-spacing: .7px;
            text-transform: uppercase; color: #64748b;
            font-weight: 700; padding: 13px 14px;
            white-space: nowrap;
        }
        .report-table tbody td {
            vertical-align: middle;
            border-color: #f1f5f9;
            padding: 12px 14px;
            font-size: .84rem;
            color: #334155;
        }
        .report-table tbody tr { transition: background .12s ease; }
        .report-table tbody tr:hover { background: #f8fafc; }

        /* Status badges */
        .badge-lunas    { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-sebagian { background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; }
        .badge-belum    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 50px;
            font-size: .72rem; font-weight: 700;
        }

        /* Export buttons */
        .btn-export-excel {
            background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;
            border-radius: 10px; padding: 7px 16px; font-size: .8rem; font-weight: 600;
            transition: all .2s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-export-excel:hover { background: #059669; color: #fff; border-color: #059669; transform: translateY(-1px); }
        .btn-export-pdf {
            background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
            border-radius: 10px; padding: 7px 16px; font-size: .8rem; font-weight: 600;
            transition: all .2s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-export-pdf:hover { background: #dc2626; color: #fff; border-color: #dc2626; transform: translateY(-1px); }

        /* No data */
        .empty-state-wrapper {
            text-align: center; padding: 60px 20px;
        }
        .empty-state-wrapper .empty-icon {
            width: 72px; height: 72px; border-radius: 20px;
            background: linear-gradient(135deg,#f1f5f9,#e2e8f0);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #94a3b8; margin: 0 auto 16px;
        }
        .empty-state-wrapper h6 { font-weight: 700; color: #475569; margin-bottom: 6px; }
        .empty-state-wrapper p  { font-size: .85rem; color: #94a3b8; margin: 0; }

        /* Custom form label */
        .filter-label {
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .7px; color: #64748b; margin-bottom: 5px; display: block;
        }

        /* Prompt state (belum filter) */
        .prompt-state {
            text-align: center; padding: 64px 20px;
        }
        .prompt-icon {
            width: 80px; height: 80px; border-radius: 24px;
            background: linear-gradient(135deg,#ecfdf5,#d1fae5);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #059669;
            margin: 0 auto 20px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt--6">

    {{-- ══ HEADER ══ --}}
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Laporan Pembayaran SPP
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Rekap rinci tagihan dan pembayaran SPP seluruh siswa.
                </p>
            </div>
            @if($filtered && $data->count() > 0)
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.payment.excel', request()->query()) }}" class="btn btn-sm btn-glass btn-glass-success">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </a>
                <a href="{{ route('admin.reports.payment.pdf', request()->query()) }}" class="btn btn-sm btn-glass btn-glass-danger">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ FILTER CARD ══ --}}
    <div class="filter-card mb-4">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            <span>Filter Laporan</span>
        </div>
        <div class="filter-body">
            <form method="GET" action="{{ route('admin.reports.payment') }}">
                {{-- Row 1: Filter Utama --}}
                <div class="row" style="gap: 0;">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="filter-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-control" id="academic_year_select" required>
                            <option value="">— Pilih Tahun Ajaran —</option>
                            @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="filter-label">Semester</label>
                        <select name="semester_id" class="form-control" id="semester_select">
                            <option value="">Semua Semester</option>
                            @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ ($filters['semester_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="filter-label">Bulan</label>
                        @php $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
                        <select name="month" class="form-control">
                            <option value="">Semua Bulan</option>
                            @foreach($bulan as $num => $nama)
                            <option value="{{ $num }}" {{ ($filters['month'] ?? '') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="filter-label">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $filters['year'] ?? now()->year }}" min="2020" max="2035">
                    </div>
                </div>

                <hr class="filter-divider">

                {{-- Row 2: Pencarian & Tampilkan --}}
                <div class="row align-items-end">
                    <div class="col-md-5 col-sm-12 mb-3 mb-md-0">
                        <label class="filter-label">Pencarian</label>
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; z-index: 10;"></i>
                            <input type="text" name="search" class="form-control" placeholder="Nama siswa, NISN, jenis pembayaran..." value="{{ $filters['search'] ?? '' }}" style="padding-left: 40px;">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <label class="filter-label"><i class="fas fa-list-ol mr-1"></i> Tampilkan per Halaman</label>
                        <select name="per_page" class="form-control">
                            <option value="50"  {{ ($filters['per_page'] ?? '50') == '50'  ? 'selected' : '' }}>50 Data</option>
                            <option value="100" {{ ($filters['per_page'] ?? '50') == '100' ? 'selected' : '' }}>100 Data</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3 mb-md-0">
                        <label class="filter-label" style="visibility:hidden;">.</label>
                        <button type="submit" class="btn btn-sakti-primary btn-block w-100 mb-0">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <label class="filter-label" style="visibility:hidden;">.</label>
                        <a href="{{ route('admin.reports.payment') }}" class="btn btn-block w-100 mb-0" style="background:#f1f5f9; color:#475569; border-radius:10px; font-weight:600;">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ HASIL LAPORAN ══ --}}
    @if($filtered)

        @php
            $totalTagihan = $data->sum('tagihan');
            $totalDibayar = $data->sum('dibayar');
            $totalSisa    = $data->sum('sisa');
            $totalData    = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : $data->count();
        @endphp

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-blue">
                    <div class="sc-icon"><i class="fas fa-database"></i></div>
                    <div class="sc-label">Total Data</div>
                    <div class="sc-value">{{ number_format($totalData) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-green">
                    <div class="sc-icon"><i class="fas fa-file-invoice"></i></div>
                    <div class="sc-label">Total Tagihan</div>
                    <div class="sc-value" style="font-size:1.2rem;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-orange">
                    <div class="sc-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="sc-label">Total Dibayar</div>
                    <div class="sc-value" style="font-size:1.2rem;">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-red">
                    <div class="sc-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="sc-label">Sisa Belum Bayar</div>
                    <div class="sc-value" style="font-size:1.2rem;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Tabel Hasil --}}
        <div class="result-card">
            <div class="result-header">
                <div class="result-title">
                    <i class="fas fa-table" style="color: var(--primary-green);"></i>
                    Detail Laporan
                    <span class="badge-count">{{ number_format($totalData) }} data ditemukan</span>
                </div>
                <div class="d-flex" style="gap:8px;">
                    @if($data->count() > 0)
                    <a href="{{ route('admin.reports.payment.excel', request()->query()) }}" class="btn-export-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.reports.payment.pdf', request()->query()) }}" class="btn-export-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table report-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:48px;">No</th>
                            <th>Siswa</th>
                            <th>Kelas / Jurusan</th>
                            <th>No. KK</th>
                            <th>Periode</th>
                            <th>Jenis Pembayaran</th>
                            <th class="text-right">Tagihan</th>
                            <th class="text-right">Dibayar</th>
                            <th class="text-right">Sisa</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $bulanFull = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        @endphp
                        @forelse($data as $index => $row)
                        <tr>
                            {{-- No --}}
                            <td style="color:#94a3b8; font-weight:600;">
                                {{ $data instanceof \Illuminate\Pagination\LengthAwarePaginator
                                    ? ($data->currentPage() - 1) * $data->perPage() + $index + 1
                                    : $index + 1 }}
                            </td>

                            {{-- Siswa --}}
                            <td>
                                <div style="font-weight: 700; color: #1e293b;">{{ $row->student_name }}</div>
                                <div style="font-size:.76rem; color:#94a3b8;">{{ $row->nisn }}</div>
                            </td>

                            {{-- Kelas / Jurusan --}}
                            <td>
                                <div style="font-weight:600;">{{ $row->grade_level ? 'Kelas '.$row->grade_level : '-' }}</div>
                                <div style="font-size:.76rem; color:#94a3b8;">{{ $row->major_name ?? '-' }}</div>
                            </td>

                            {{-- No. KK --}}
                            <td style="color:#64748b; font-size:.82rem;">{{ $row->family_card_number ?? '-' }}</td>

                            {{-- Periode --}}
                            <td>
                                <div style="font-weight:600;">{{ $bulanFull[$row->month] ?? $row->month }}</div>
                                <div style="font-size:.76rem; color:#94a3b8;">{{ $row->year }} — {{ $row->academic_year_name }}</div>
                            </td>

                            {{-- Jenis --}}
                            <td>
                                <span style="background:#f0fdf4; color:#059669; border:1px solid #d1fae5; border-radius:8px; padding:3px 10px; font-size:.76rem; font-weight:600;">
                                    {{ $row->payment_type_name }}
                                </span>
                            </td>

                            {{-- Tagihan --}}
                            <td class="text-right" style="font-weight:700; color:#1e293b;">
                                Rp {{ number_format($row->tagihan, 0, ',', '.') }}
                            </td>

                            {{-- Dibayar --}}
                            <td class="text-right" style="font-weight:700; color:#059669;">
                                Rp {{ number_format($row->dibayar, 0, ',', '.') }}
                            </td>

                            {{-- Sisa --}}
                            <td class="text-right" style="font-weight:700; color:{{ $row->sisa > 0 ? '#dc2626' : '#059669' }};">
                                Rp {{ number_format($row->sisa, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($row->status_text === 'Lunas')
                                <span class="badge-status badge-lunas">
                                    <i class="fas fa-check-circle"></i> Lunas
                                </span>
                                @elseif($row->status_text === 'Sebagian')
                                <span class="badge-status badge-sebagian">
                                    <i class="fas fa-adjust"></i> Sebagian
                                </span>
                                @else
                                <span class="badge-status badge-belum">
                                    <i class="fas fa-times-circle"></i> Belum Bayar
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state-wrapper">
                                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                                    <h6>Tidak Ada Data</h6>
                                    <p>Tidak ada data pembayaran yang sesuai dengan filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($data instanceof \Illuminate\Pagination\LengthAwarePaginator && $data->hasPages())
            <div class="px-4 py-3 border-top" style="background:#fafafa;">
                <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
                    <small style="color:#94a3b8; font-size:.78rem;">
                        Menampilkan <strong style="color:#059669;">{{ $data->firstItem() }}</strong>
                        – <strong style="color:#059669;">{{ $data->lastItem() }}</strong>
                        dari <strong>{{ $data->total() }}</strong> data
                    </small>
                    {{ $data->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>

    @else
    {{-- Prompt state --}}
    <div class="result-card">
        <div class="prompt-state">
            <div class="prompt-icon"><i class="fas fa-filter"></i></div>
            <h5 style="font-weight:700; color:#1e293b; margin-bottom:8px;">Pilih Filter Terlebih Dahulu</h5>
            <p style="color:#94a3b8; font-size:.88rem; max-width:400px; margin:0 auto;">
                Pilih <strong>Tahun Ajaran</strong> dan klik tombol <strong>Tampilkan</strong> untuk melihat laporan pembayaran SPP.
            </p>
        </div>
    </div>
    @endif

</div>

<script>
    document.getElementById('academic_year_select')?.addEventListener('change', function () {
        const ayId = this.value;
        const semesterSelect = document.getElementById('semester_select');
        semesterSelect.innerHTML = '<option value="">Semua Semester</option>';
        if (ayId) {
            fetch('/admin/semesters/api/' + ayId)
                .then(r => r.json())
                .then(data => {
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        semesterSelect.appendChild(opt);
                    });
                });
        }
    });
</script>
@endsection
