@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        /* Filter bar and pill styles */
        .tx-filter-bar { display: flex; flex-wrap: wrap; gap: .55rem; align-items: center; }
        .tx-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .42rem .95rem;
            border-radius: 50px;
            font-size: .73rem; font-weight: 700;
            border: 1.5px solid transparent;
            cursor: pointer; transition: all .2s;
            text-decoration: none; white-space: nowrap;
        }
        .tx-pill.all     { border-color:#dee2e6; color:#6c757d; background:#f8f9fc; }
        .tx-pill.all.active, .tx-pill.all:hover { background:#343a40; color:#fff; border-color:#343a40; }
        .tx-pill.income  { border-color:#c3e6cb; color:var(--primary-green); background:#f0faf5; }
        .tx-pill.income.active, .tx-pill.income:hover { background:var(--primary-green); color:#fff; border-color:var(--primary-green); }
        .tx-pill.expense { border-color:#f5c6cb; color:var(--danger-color); background:#fff5f5; }
        .tx-pill.expense.active, .tx-pill.expense:hover { background:var(--danger-color); color:#fff; border-color:var(--danger-color); }

        /* Search input & month picker */
        .tx-search-box { position:relative; min-width:210px; flex:1; max-width:320px; }
        .tx-search-box .s-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.78rem; pointer-events:none; }
        .tx-search-box input {
            padding-left:34px; border-radius:50px;
            border:1.5px solid #e9ecef; font-size:.78rem;
            background:#f8f9fc; height:38px; width:100%;
            transition: border-color .2s, box-shadow .2s;
        }
        .tx-search-box input:focus { border-color:var(--primary-green); box-shadow:0 0 0 3px rgba(5,150,105,.12); background:#fff; outline:none; }
        .tx-search-box .clr { position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.73rem; text-decoration:none; transition:color .2s; }
        .tx-search-box .clr:hover { color:var(--danger-color); }

        .tx-month-input {
            height:38px; border-radius:50px;
            border:1.5px solid #e9ecef; font-size:.78rem;
            background:#f8f9fc; padding:.3rem .9rem;
            transition: border-color .2s;
            color:#495057;
        }
        .tx-month-input:focus { border-color:var(--primary-green); box-shadow:0 0 0 3px rgba(5,150,105,.12); outline:none; background:#fff; }

        /* Type badge inside table */
        .type-badge {
            display: inline-flex; align-items: center; gap: .32rem;
            padding: .32rem .78rem; border-radius: 50px;
            font-size: .68rem; font-weight: 700; white-space: nowrap;
        }
        .type-income  { background:#e8f8f0; color:var(--primary-green); }
        .type-expense { background:#fef0f0; color:var(--danger-color); }

        /* Category chip */
        .cat-chip {
            display:inline-block;
            padding:.2rem .55rem; border-radius:6px;
            font-size:.65rem; font-weight:600;
            background:#f0f2f5; color:#6c757d;
            max-width:130px; overflow:hidden;
            text-overflow:ellipsis; white-space:nowrap;
            vertical-align:middle;
        }

        /* Amount styling */
        .amount-income  { color:var(--primary-green); font-weight:700; }
        .amount-expense { color:var(--danger-color); font-weight:700; }

        /* Date cell */
        .date-main { font-weight:700; font-size:.82rem; color:#344767; }
        .date-sub  { font-size:.68rem; color:#adb5bd; }

        /* Description cell */
        .desc-main { font-size:.82rem; color:#344767; font-weight:500; }
        .recorder  { font-size:.68rem; color:#adb5bd; margin-top:2px; }

        .net-positive { color:var(--primary-green); }
        .net-negative { color:var(--danger-color); }

        .tx-footer {
            padding:.9rem 1.6rem;
            border-top:1px solid #f0f2f5;
            display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:.5rem;
        }

        @media (max-width:768px) {
            .tx-filter-bar { flex-direction:column; align-items:stretch; }
            .tx-search-box { max-width:100%; }
        }
    </style>
@endpush

@section('content')
@php
    $totalIncome  = $summaryStats->total_income  ?? 0;
    $totalExpense = $summaryStats->total_expense ?? 0;
    $totalCount   = $summaryStats->total_count   ?? 0;
    $isPositive   = $netCash >= 0;
@endphp

<div class="container-fluid">

    {{-- ══════════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Total Pemasukan --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card sc-green h-100">
                <div class="sc-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="sc-label">Total Pemasukan</div>
                <div class="sc-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card sc-red h-100">
                <div class="sc-icon"><i class="fas fa-arrow-up"></i></div>
                <div class="sc-label">Total Pengeluaran</div>
                <div class="sc-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Kas Bersih --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card {{ $isPositive ? 'sc-blue' : 'sc-orange' }} h-100">
                <div class="sc-icon"><i class="fas fa-balance-scale"></i></div>
                <div class="sc-label">Kas Bersih</div>
                <div class="sc-value">{{ $isPositive ? '+' : '-' }} Rp {{ number_format(abs($netCash), 0, ',', '.') }}</div>
                <div class="mt-2" style="font-size: .78rem; font-weight: 500; color: rgba(255,255,255,.8);">
                    {{ $isPositive ? 'Surplus' : 'Defisit' }}
                </div>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="col-xl-3 col-md-6">
            <div class="summary-card sc-purple h-100">
                <div class="sc-icon"><i class="fas fa-receipt"></i></div>
                <div class="sc-label">Total Transaksi</div>
                <div class="sc-value">{{ number_format($totalCount) }}</div>
                <div class="mt-2" style="font-size: .78rem; font-weight: 500; color: rgba(255,255,255,.8);">
                    entri jurnal
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ══════════════════════════════════════════
         MAIN TABLE CARD
    ══════════════════════════════════════════ --}}
    <div class="card dashboard-card">

        {{-- Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

                {{-- Title --}}
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center gap-2">
                        <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg, var(--primary-green), var(--secondary-green));display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fas fa-exchange-alt text-white" style="font-size:.72rem;"></i>
                        </span>
                        Riwayat Transaksi Kas
                    </h5>
                    <p class="text-xs text-muted mb-0 mt-1">
                        Jurnal penerimaan &amp; pengeluaran kas sekolah
                        @if($search || $type || $month)
                            &nbsp;·&nbsp;
                            <a href="{{ route('kepala-sekolah.transactions') }}" class="text-danger font-weight-bold" style="font-size:.72rem;">
                                <i class="fas fa-times me-1"></i>Reset Filter
                            </a>
                        @endif
                    </p>
                </div>

                {{-- Filter Bar --}}
                <form action="{{ route('kepala-sekolah.transactions') }}" method="GET" id="txFilterForm">
                    <div class="tx-filter-bar">

                        {{-- Type Pills --}}
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="#" class="tx-pill all {{ !$type ? 'active' : '' }}"
                               onclick="setType(event,'')">
                                <i class="fas fa-border-all"></i> Semua
                            </a>
                            <a href="#" class="tx-pill income {{ $type==='income' ? 'active' : '' }}"
                               onclick="setType(event,'income')">
                                <i class="fas fa-arrow-down"></i> Pemasukan
                            </a>
                            <a href="#" class="tx-pill expense {{ $type==='expense' ? 'active' : '' }}"
                               onclick="setType(event,'expense')">
                                <i class="fas fa-arrow-up"></i> Pengeluaran
                            </a>
                            <input type="hidden" name="type"   id="typeInput"   value="{{ $type }}">
                            <input type="hidden" name="search" id="searchHidden" value="{{ $search }}">
                            <input type="hidden" name="month"  id="monthHidden"  value="{{ $month }}">
                        </div>

                        {{-- Month Picker --}}
                        <input type="month" class="tx-month-input" id="monthInput"
                               value="{{ $month }}" title="Filter bulan">

                        {{-- Search --}}
                        <div class="tx-search-box">
                            <i class="fas fa-search s-icon"></i>
                            <input type="text" id="searchInput"
                                   placeholder="Cari deskripsi / kategori..."
                                   value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <a href="{{ route('kepala-sekolah.transactions', array_filter(['type'=>$type,'month'=>$month])) }}"
                                   class="clr" title="Hapus pencarian"><i class="fas fa-times"></i></a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive" style="min-height:280px;">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th style="width:2.5rem;">#</th>
                        <th>Tanggal</th>
                        <th>Deskripsi &amp; Pencatat</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    @php
                        $d = \Carbon\Carbon::parse($tx->date);
                    @endphp
                    <tr>
                        {{-- No --}}
                        <td class="text-muted text-xs font-weight-bold">
                            {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                        </td>

                        {{-- Tanggal --}}
                        <td>
                            <div class="date-main">{{ $d->translatedFormat('d M Y') }}</div>
                            <div class="date-sub">{{ $d->translatedFormat('l') }}</div>
                        </td>

                        {{-- Deskripsi & Pencatat --}}
                        <td style="max-width:280px;">
                            <div class="desc-main" style="line-height:1.4;">{{ $tx->description }}</div>
                            @if(!empty($tx->recorder_name))
                                <div class="recorder">
                                    <i class="fas fa-user-edit me-1" style="font-size:.6rem;"></i>{{ $tx->recorder_name }}
                                </div>
                            @endif
                        </td>

                        {{-- Tipe --}}
                        <td class="text-center">
                            @if($tx->type === 'income')
                                <span class="type-badge type-income">
                                    <i class="fas fa-arrow-down"></i> Pemasukan
                                </span>
                            @else
                                <span class="type-badge type-expense">
                                    <i class="fas fa-arrow-up"></i> Pengeluaran
                                </span>
                            @endif
                        </td>

                        {{-- Kategori --}}
                        <td class="text-center">
                            @if(!empty($tx->category))
                                <span class="cat-chip" title="{{ $tx->category }}">
                                    {{ $tx->category }}
                                </span>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>

                        {{-- Nominal --}}
                        <td class="text-end {{ $tx->type === 'income' ? 'amount-income' : 'amount-expense' }}" style="font-size:.85rem;">
                            {{ $tx->type === 'income' ? '+' : '−' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="tx-footer">
            <p class="text-xs text-muted mb-0">
                @if($transactions->total() > 0)
                    Menampilkan <strong>{{ $transactions->firstItem() }}</strong>–<strong>{{ $transactions->lastItem() }}</strong>
                    dari <strong>{{ number_format($transactions->total()) }}</strong> transaksi
                    @if($search || $type || $month)
                        <span class="text-muted">(difilter)</span>
                    @endif
                @else
                    Tidak ada data untuk ditampilkan
                @endif
            </p>
            <div>{{ $transactions->links('pagination::bootstrap-4') }}</div>
        </div>

    </div>{{-- /card --}}
</div>{{-- /container --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form         = document.getElementById('txFilterForm');
    const typeInput    = document.getElementById('typeInput');
    const searchInput  = document.getElementById('searchInput');
    const searchHidden = document.getElementById('searchHidden');
    const monthInput   = document.getElementById('monthInput');
    const monthHidden  = document.getElementById('monthHidden');

    // ── Type pill ─────────────────────────────────
    window.setType = function (e, val) {
        e.preventDefault();
        typeInput.value    = val;
        searchHidden.value = searchInput.value;
        monthHidden.value  = monthInput.value;
        form.submit();
    };

    // ── Search debounce 500ms ──────────────────────
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchHidden.value = this.value;
            monthHidden.value  = monthInput.value;
            form.submit();
        }, 520);
    });

    // ── Month picker ───────────────────────────────
    monthInput.addEventListener('change', function () {
        monthHidden.value  = this.value;
        searchHidden.value = searchInput.value;
        form.submit();
    });
});
</script>
@endpush
