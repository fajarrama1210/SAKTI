@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        /* ── Report Page Specific Styles (shared with payment report) ── */
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
        .filter-label {
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .7px; color: #64748b; margin-bottom: 5px; display: block;
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
        .summary-card.sc-red    { background: linear-gradient(135deg,#dc2626,#f87171); }
        .summary-card.sc-blue   { background: linear-gradient(135deg,#0284c7,#38bdf8); }
        .summary-card.sc-purple { background: linear-gradient(135deg,#7c3aed,#a78bfa); }
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
            font-size: 1.35rem; font-weight: 800; color: #fff; letter-spacing: -.5px; line-height: 1.2;
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
        .report-table thead tr { background: #f8fafc; }
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

        /* Empty / Prompt state */
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

        .prompt-state { text-align: center; padding: 64px 20px; }
        .prompt-icon {
            width: 80px; height: 80px; border-radius: 24px;
            background: linear-gradient(135deg,#ecfdf5,#d1fae5);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #059669; margin: 0 auto 20px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid mt--6">

    {{-- ══ HEADER ══ --}}
    <div class="report-header">
        <div class="report-header-content d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <div>
                <p class="text-white mb-1" style="opacity:.7; font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:1px;">SAKTI — Laporan</p>
                <h2 class="text-white font-weight-bold mb-1" style="font-size:1.5rem; letter-spacing:-.02em;">
                    <i class="fas fa-exchange-alt mr-2"></i> Laporan Transaksi Keuangan
                </h2>
                <p class="text-white mb-0" style="opacity:.65; font-size:.86rem;">Rekap rinci transaksi uang masuk dan uang keluar.</p>
            </div>
            @if($filtered && $data->count() > 0)
            <div class="d-flex" style="gap:8px;">
                <a href="{{ route('admin.reports.transaction.excel', request()->query()) }}" class="btn-export-excel">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
                <a href="{{ route('admin.reports.transaction.pdf', request()->query()) }}" class="btn-export-pdf">
                    <i class="fas fa-file-pdf"></i> PDF
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
            <form method="GET" action="{{ route('admin.reports.transaction') }}">
                {{-- Row 1: Filter Utama --}}
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="filter-label">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-control" id="academic_year_select">
                            <option value="">— Opsional —</option>
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
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label class="filter-label">Bulan</label>
                        @php $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']; @endphp
                        <select name="month" class="form-control">
                            <option value="">Semua Bulan</option>
                            @foreach($bulan as $num => $nama)
                            <option value="{{ $num }}" {{ ($filters['month'] ?? '') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label class="filter-label">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $filters['year'] ?? now()->year }}" min="2020" max="2035">
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label class="filter-label">Tipe Transaksi</label>
                        <select name="type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="income"  {{ ($filters['type'] ?? '') === 'income'  ? 'selected' : '' }}>Uang Masuk</option>
                            <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Uang Keluar</option>
                        </select>
                    </div>
                </div>

                <hr class="filter-divider">

                {{-- Row 2: Pencarian & Tampilkan --}}
                <div class="row align-items-end">
                    <div class="col-md-5 col-sm-12 mb-3 mb-md-0">
                        <label class="filter-label">Pencarian</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8fafc; border-color:#e2e8f0; border-right:0;">
                                    <i class="fas fa-search" style="color:#94a3b8; font-size:.8rem;"></i>
                                </span>
                            </div>
                            <input type="text" name="search" class="form-control" placeholder="Keterangan, kategori, atau nama pencatat..." value="{{ $filters['search'] ?? '' }}" style="border-left:0;">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <label class="filter-label">Tampilkan per Halaman</label>
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
                        <a href="{{ route('admin.reports.transaction') }}" class="btn btn-block w-100 mb-0" style="background:#f1f5f9; color:#475569; border-radius:10px; font-weight:600;">
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
            $totalData = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : $data->count();
            $saldo     = $totalIncome - $totalExpense;
        @endphp

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-blue">
                    <div class="sc-icon"><i class="fas fa-database"></i></div>
                    <div class="sc-label">Total Transaksi</div>
                    <div class="sc-value">{{ number_format($totalData) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-green">
                    <div class="sc-icon"><i class="fas fa-arrow-circle-down"></i></div>
                    <div class="sc-label">Total Uang Masuk</div>
                    <div class="sc-value" style="font-size:1.15rem;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card sc-red">
                    <div class="sc-icon"><i class="fas fa-arrow-circle-up"></i></div>
                    <div class="sc-label">Total Uang Keluar</div>
                    <div class="sc-value" style="font-size:1.15rem;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="summary-card {{ $saldo >= 0 ? 'sc-purple' : 'sc-red' }}">
                    <div class="sc-icon"><i class="fas fa-balance-scale"></i></div>
                    <div class="sc-label">Saldo / Selisih</div>
                    <div class="sc-value" style="font-size:1.15rem;">
                        {{ $saldo >= 0 ? '' : '-' }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Detail --}}
        <div class="result-card">
            <div class="result-header">
                <div class="result-title">
                    <i class="fas fa-table" style="color: var(--primary-green);"></i>
                    Detail Transaksi
                    <span class="badge-count">{{ number_format($totalData) }} data ditemukan</span>
                </div>
                <div class="d-flex" style="gap:8px;">
                    @if($data->count() > 0)
                    <a href="{{ route('admin.reports.transaction.excel', request()->query()) }}" class="btn-export-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.reports.transaction.pdf', request()->query()) }}" class="btn-export-pdf">
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
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th class="text-right">Uang Masuk</th>
                            <th class="text-right">Uang Keluar</th>
                            <th>Dicatat Oleh</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $trx)
                        <tr>
                            {{-- No --}}
                            <td style="color:#94a3b8; font-weight:600;">
                                {{ $data instanceof \Illuminate\Pagination\LengthAwarePaginator
                                    ? ($data->currentPage() - 1) * $data->perPage() + $index + 1
                                    : $index + 1 }}
                            </td>

                            {{-- Tanggal --}}
                            <td>
                                <div style="font-weight:700; color:#1e293b;">
                                    {{ \Carbon\Carbon::parse($trx->date)->format('d') }}
                                    {{ ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'][\Carbon\Carbon::parse($trx->date)->month - 1] }}
                                    {{ \Carbon\Carbon::parse($trx->date)->format('Y') }}
                                </div>
                                <div style="font-size:.74rem; color:#94a3b8;">
                                    {{ \Carbon\Carbon::parse($trx->date)->translatedFormat('l') ?? \Carbon\Carbon::parse($trx->date)->format('D') }}
                                </div>
                            </td>

                            {{-- Tipe --}}
                            <td>
                                @if($trx->type === 'income')
                                <span style="background:rgba(16,185,129,.1); color:#059669; border:1px solid #a7f3d0; border-radius:8px; padding:4px 12px; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; gap:5px;">
                                    <i class="fas fa-arrow-down"></i> Masuk
                                </span>
                                @else
                                <span style="background:rgba(220,38,38,.08); color:#dc2626; border:1px solid #fecaca; border-radius:8px; padding:4px 12px; font-size:.75rem; font-weight:700; display:inline-flex; align-items:center; gap:5px;">
                                    <i class="fas fa-arrow-up"></i> Keluar
                                </span>
                                @endif
                            </td>

                            {{-- Kategori --}}
                            <td>
                                @if($trx->category)
                                <span style="background:#f5f3ff; color:#7c3aed; border:1px solid #e9d5ff; border-radius:8px; padding:3px 10px; font-size:.76rem; font-weight:600;">
                                    {{ $trx->category }}
                                </span>
                                @else
                                <span style="color:#94a3b8;">—</span>
                                @endif
                            </td>

                            {{-- Uang Masuk --}}
                            <td class="text-right">
                                @if($trx->type === 'income')
                                <span style="font-weight:800; color:#059669;">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </span>
                                @else
                                <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Uang Keluar --}}
                            <td class="text-right">
                                @if($trx->type === 'expense')
                                <span style="font-weight:800; color:#dc2626;">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </span>
                                @else
                                <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Dicatat Oleh --}}
                            <td>
                                <div style="font-weight:600; color:#475569;">{{ $trx->recorded_by_name ?? '—' }}</div>
                                @if($trx->payment_id)
                                <div style="font-size:.72rem; color:#7c3aed; font-weight:600;">
                                    <i class="fas fa-robot mr-1"></i>Otomatis
                                </div>
                                @else
                                <div style="font-size:.72rem; color:#94a3b8;">Manual</div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-detail-trx mb-0" 
                                    style="background:#f1f5f9; color:#475569; border-radius:8px; font-weight:600; padding: 5px 12px;"
                                    data-id="{{ $trx->id }}"
                                    data-date="{{ \Carbon\Carbon::parse($trx->date)->translatedFormat('d F Y') }}"
                                    data-type="{{ $trx->type }}"
                                    data-category="{{ $trx->category }}"
                                    data-method="{{ $trx->payment_method }}"
                                    data-reference="{{ $trx->reference_number }}"
                                    data-description="{{ $trx->description }}"
                                    data-amount="Rp {{ number_format($trx->amount, 0, ',', '.') }}"
                                    data-recorded="{{ $trx->recorded_by_name }}"
                                    data-automated="{{ $trx->payment_id ? '1' : '0' }}"
                                    data-payment-id="{{ $trx->payment_id }}">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state-wrapper">
                                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                                    <h6>Tidak Ada Data</h6>
                                    <p>Tidak ada transaksi yang sesuai dengan filter yang dipilih.</p>
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
                Pilih <strong>Tahun</strong>, <strong>Bulan</strong>, atau <strong>Semester</strong> lalu klik <strong>Tampilkan</strong> untuk melihat laporan transaksi.
            </p>
        </div>
    </div>
    @endif

</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="detailTransactionModal" tabindex="-1" role="dialog" aria-labelledby="detailTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; background: #f8fafc; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h5 class="modal-title" id="detailTransactionModalLabel" style="font-weight: 700; color: #1e293b; margin: 0;">
                    <i class="fas fa-receipt text-success mr-2"></i> Detail Transaksi
                </h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; color: #94a3b8; outline: none; border: none; background: transparent; padding: 0; margin: 0; line-height: 1; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding: 24px;">
                <div class="text-center mb-4">
                    <div id="modal-type-badge" class="mb-2"></div>
                    <h3 id="modal-amount" style="font-weight: 800; color: #1e293b; font-size: 1.8rem; margin: 0;"></h3>
                    <p id="modal-category" style="margin: 4px 0 0; font-size: 0.85rem; font-weight: 600; color: #7c3aed;"></p>
                </div>
                
                <hr style="border: none; border-top: 1px dashed #e2e8f0; margin: 16px 0;">
                
                <div class="row style-details" style="font-size: 0.88rem; color: #334155;">
                    <div class="col-5 text-muted mb-3 font-weight-600">Tanggal</div>
                    <div class="col-7 text-right mb-3 font-weight-bold" id="modal-date"></div>
                    
                    <div class="col-5 text-muted mb-3 font-weight-600">Metode Pembayaran</div>
                    <div class="col-7 text-right mb-3" id="modal-method"></div>
                    
                    <div class="col-5 text-muted mb-3 font-weight-600">Nomor Referensi</div>
                    <div class="col-7 text-right mb-3 font-weight-bold" id="modal-reference" style="font-family: monospace;"></div>
                    
                    <div class="col-5 text-muted mb-3 font-weight-600">Dicatat Oleh</div>
                    <div class="col-7 text-right mb-3" id="modal-recorded"></div>
                    
                    <div class="col-5 text-muted mb-3 font-weight-600">Sifat Pencatatan</div>
                    <div class="col-7 text-right mb-3" id="modal-automation"></div>
                    
                    <div class="col-12 mt-2">
                        <label class="text-muted font-weight-600 mb-1" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Keterangan</label>
                        <div class="p-3 bg-light rounded" id="modal-description" style="word-break: break-word; font-weight: 500; font-size: 0.85rem; line-height: 1.5; color: #475569; border-left: 4px solid #059669;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f1f5f9; background: #f8fafc; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; padding: 12px 24px; display: flex; justify-content: space-between;">
                <div id="modal-invoice-container">
                    <!-- Dynamic Invoice Button -->
                </div>
                <button type="button" class="btn btn-secondary mb-0" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; font-size: 0.8rem; padding: 8px 20px;">Tutup</button>
            </div>
        </div>
    </div>
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

    document.addEventListener("DOMContentLoaded", function() {
        const detailButtons = document.querySelectorAll('.btn-detail-trx');
        const modalElement = document.getElementById('detailTransactionModal');
        // Initialize the Bootstrap 5 Modal
        const myModal = new bootstrap.Modal(modalElement);
        
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const date = this.getAttribute('data-date');
                const type = this.getAttribute('data-type');
                const category = this.getAttribute('data-category') || 'Lainnya';
                const method = this.getAttribute('data-method');
                const reference = this.getAttribute('data-reference') || '—';
                const description = this.getAttribute('data-description');
                const amount = this.getAttribute('data-amount');
                const recorded = this.getAttribute('data-recorded') || '—';
                const paymentId = this.getAttribute('data-payment-id');
                const isAutomated = this.getAttribute('data-automated') === '1';

                // Populate text content
                document.getElementById('modal-date').textContent = date;
                document.getElementById('modal-amount').textContent = amount;
                document.getElementById('modal-category').textContent = category;
                document.getElementById('modal-reference').textContent = reference;
                
                let cleanDescription = description;
                if (cleanDescription && cleanDescription.includes('Pemb.')) {
                    cleanDescription = cleanDescription.replace(/Pemb\./g, 'Pembayaran');
                }
                document.getElementById('modal-description').textContent = cleanDescription;
                document.getElementById('modal-recorded').textContent = recorded;

                // Type Badge
                let badgeHtml = '';
                if (type === 'income') {
                    badgeHtml = `<span class="badge" style="font-size: 0.78rem; font-weight:700; padding: 6px 16px; border-radius: 50px; background-color: #059669; color: #ffffff;"><i class="fas fa-arrow-down mr-1"></i> Pemasukan (Masuk)</span>`;
                    document.getElementById('modal-amount').style.color = '#059669';
                } else {
                    badgeHtml = `<span class="badge" style="font-size: 0.78rem; font-weight:700; padding: 6px 16px; border-radius: 50px; background-color: #dc2626; color: #ffffff;"><i class="fas fa-arrow-up mr-1"></i> Pengeluaran (Keluar)</span>`;
                    document.getElementById('modal-amount').style.color = '#dc2626';
                }
                document.getElementById('modal-type-badge').innerHTML = badgeHtml;

                // Method Badge
                let methodHtml = '';
                if (method === 'cash') {
                    methodHtml = `<span class="badge text-white" style="font-weight: 600; background-color: #0284c7; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-money-bill-wave mr-1"></i> Tunai</span>`;
                } else if (method === 'qris') {
                    methodHtml = `<span class="badge text-white" style="font-weight: 600; background-color: #be185d; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-qrcode mr-1"></i> QRIS</span>`;
                } else if (method === 'transfer') {
                    methodHtml = `<span class="badge text-white" style="font-weight: 600; background-color: #059669; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-university mr-1"></i> Transfer</span>`;
                } else if (method === 'other') {
                    methodHtml = `<span class="badge text-white" style="font-weight: 600; background-color: #64748b; padding: 4px 10px; border-radius: 6px;">Lainnya</span>`;
                } else {
                    methodHtml = type === 'expense' 
                        ? `<span class="badge text-white" style="font-weight: 600; background-color: #0284c7; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-money-bill-wave mr-1"></i> Tunai</span>` 
                        : '<span style="color:#94a3b8;">—</span>';
                }
                document.getElementById('modal-method').innerHTML = methodHtml;

                // Automation Badge
                let autoHtml = '';
                if (isAutomated) {
                    autoHtml = `<span class="badge text-white" style="font-weight:600; background-color: #7c3aed; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-robot mr-1"></i> Otomatis (Sistem)</span>`;
                } else {
                    autoHtml = `<span class="badge text-white" style="font-weight:600; background-color: #64748b; padding: 4px 10px; border-radius: 6px;"><i class="fas fa-user-edit mr-1"></i> Manual (Admin)</span>`;
                }
                document.getElementById('modal-automation').innerHTML = autoHtml;

                // Invoice Button
                const invoiceContainer = document.getElementById('modal-invoice-container');
                if (paymentId) {
                    const invoiceUrl = `/admin/spp/invoice/${paymentId}`;
                    invoiceContainer.innerHTML = `
                        <a href="${invoiceUrl}" target="_blank" class="btn btn-success mb-0" style="border-radius: 10px; font-weight: 600; font-size: 0.8rem; padding: 8px 16px; background-color: #059669; border-color: #059669;">
                            <i class="fas fa-print mr-1"></i> Cetak Invoice
                        </a>
                    `;
                } else {
                    invoiceContainer.innerHTML = '';
                }

                // Show the modal via Bootstrap 5 API
                myModal.show();
            });
        });
    });
</script>
@endsection
