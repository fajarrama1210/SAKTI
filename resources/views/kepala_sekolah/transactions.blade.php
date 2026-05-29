@extends('_admin.layouts.app')

@push('styles')
<style>
    /* ── Variables ────────────────────────────── */
    :root {
        --tx-green:  #1a8a5c;
        --tx-blue:   #4361ee;
        --tx-red:    #e63946;
        --tx-orange: #f4a261;
        --tx-radius: 18px;
        --tx-shadow: 0 4px 24px rgba(0,0,0,.07);
    }

    /* ── Summary Cards ────────────────────────── */
    .tx-summary-card {
        border-radius: var(--tx-radius);
        border: none;
        box-shadow: var(--tx-shadow);
        transition: transform .25s ease, box-shadow .25s ease;
        overflow: hidden;
        position: relative;
    }
    .tx-summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 34px rgba(0,0,0,.12);
    }
    .tx-summary-card .card-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .tx-summary-card .blob {
        position: absolute;
        bottom: -20px; right: -20px;
        width: 90px; height: 90px;
        border-radius: 50%;
        opacity: .07;
    }

    /* ── Main Card ────────────────────────────── */
    .tx-main-card {
        border-radius: var(--tx-radius);
        border: none;
        box-shadow: var(--tx-shadow);
        overflow: hidden;
    }
    .tx-main-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f2f5;
        padding: 1.4rem 1.6rem 1rem;
    }

    /* ── Filter Bar ───────────────────────────── */
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
    .tx-pill.income  { border-color:#c3e6cb; color:var(--tx-green); background:#f0faf5; }
    .tx-pill.income.active, .tx-pill.income:hover { background:var(--tx-green); color:#fff; border-color:var(--tx-green); }
    .tx-pill.expense { border-color:#f5c6cb; color:var(--tx-red); background:#fff5f5; }
    .tx-pill.expense.active, .tx-pill.expense:hover { background:var(--tx-red); color:#fff; border-color:var(--tx-red); }

    .tx-search-box { position:relative; min-width:210px; flex:1; max-width:320px; }
    .tx-search-box .s-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.78rem; pointer-events:none; }
    .tx-search-box input {
        padding-left:34px; border-radius:50px;
        border:1.5px solid #e9ecef; font-size:.78rem;
        background:#f8f9fc; height:38px; width:100%;
        transition: border-color .2s, box-shadow .2s;
    }
    .tx-search-box input:focus { border-color:var(--tx-green); box-shadow:0 0 0 3px rgba(26,138,92,.12); background:#fff; outline:none; }
    .tx-search-box .clr { position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.73rem; text-decoration:none; transition:color .2s; }
    .tx-search-box .clr:hover { color:var(--tx-red); }

    .tx-month-input {
        height:38px; border-radius:50px;
        border:1.5px solid #e9ecef; font-size:.78rem;
        background:#f8f9fc; padding:.3rem .9rem;
        transition: border-color .2s;
        color:#495057;
    }
    .tx-month-input:focus { border-color:var(--tx-green); box-shadow:0 0 0 3px rgba(26,138,92,.12); outline:none; background:#fff; }

    /* ── Table ────────────────────────────────── */
    .tx-table { margin:0; }
    .tx-table thead th {
        background:#f8f9fc; color:#8898aa;
        font-size:.68rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.06em;
        padding:.8rem 1.2rem; border:none; white-space:nowrap;
    }
    .tx-table tbody tr { border-bottom:1px solid #f4f6f9; transition:background .15s; }
    .tx-table tbody tr:hover { background:#f8fffe; }
    .tx-table tbody tr:last-child { border-bottom:none; }
    .tx-table td { padding:.85rem 1.2rem; vertical-align:middle; border:none; }

    /* ── Type Badge ───────────────────────────── */
    .type-badge {
        display:inline-flex; align-items:center; gap:.32rem;
        padding:.32rem .78rem; border-radius:50px;
        font-size:.68rem; font-weight:700; white-space:nowrap;
    }
    .type-income  { background:#e8f8f0; color:#1a8a5c; }
    .type-expense { background:#fef0f0; color:#e63946; }

    /* ── Category Chip ────────────────────────── */
    .cat-chip {
        display:inline-block;
        padding:.2rem .55rem; border-radius:6px;
        font-size:.65rem; font-weight:600;
        background:#f0f2f5; color:#6c757d;
        max-width:130px; overflow:hidden;
        text-overflow:ellipsis; white-space:nowrap;
        vertical-align:middle;
    }

    /* ── Amount ───────────────────────────────── */
    .amount-income  { color:#1a8a5c; font-weight:700; }
    .amount-expense { color:#e63946; font-weight:700; }

    /* ── Date Cell ────────────────────────────── */
    .date-main { font-weight:700; font-size:.82rem; color:#344767; }
    .date-sub  { font-size:.68rem; color:#adb5bd; }

    /* ── Description Cell ─────────────────────── */
    .desc-main { font-size:.82rem; color:#344767; font-weight:500; }
    .recorder  { font-size:.68rem; color:#adb5bd; margin-top:2px; }

    /* ── Net indicator ────────────────────────── */
    .net-positive { color:#1a8a5c; }
    .net-negative { color:#e63946; }

    /* ── Empty State ──────────────────────────── */
    .tx-empty { padding:4rem 2rem; text-align:center; }
    .tx-empty .empty-icon { width:72px; height:72px; border-radius:20px; background:#f0f2f5; display:flex; align-items:center; justify-content:center; margin:0 auto 1.2rem; font-size:1.8rem; color:#ced4da; }
    .tx-empty h6 { font-weight:700; color:#344767; margin-bottom:.3rem; }
    .tx-empty p  { font-size:.82rem; color:#adb5bd; }

    /* ── Footer ───────────────────────────────── */
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

<div class="container-fluid mt--6">

    {{-- ══════════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Total Pemasukan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card tx-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#e8f8f0;">
                        <i class="fas fa-arrow-down" style="color:#1a8a5c;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Total Pemasukan</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.05rem; color:#1a8a5c;">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="blob" style="background:#1a8a5c;"></div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-xl-3 col-md-6">
            <div class="card tx-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#fef0f0;">
                        <i class="fas fa-arrow-up" style="color:#e63946;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Total Pengeluaran</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.05rem; color:#e63946;">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="blob" style="background:#e63946;"></div>
                </div>
            </div>
        </div>

        {{-- Kas Bersih --}}
        <div class="col-xl-3 col-md-6">
            <div class="card tx-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:{{ $isPositive ? '#e8f0ff' : '#fff8e6' }};">
                        <i class="fas fa-balance-scale" style="color:{{ $isPositive ? '#4361ee' : '#856404' }};"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Kas Bersih</p>
                        <h4 class="mb-0 font-weight-bold {{ $isPositive ? 'net-positive' : 'net-negative' }}" style="font-size:1.05rem;">
                            {{ $isPositive ? '+' : '-' }} Rp {{ number_format(abs($netCash), 0, ',', '.') }}
                        </h4>
                        <span class="text-xs text-muted">{{ $isPositive ? 'Surplus' : 'Defisit' }}</span>
                    </div>
                    <div class="blob" style="background:{{ $isPositive ? '#4361ee' : '#f4a261' }};"></div>
                </div>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="col-xl-3 col-md-6">
            <div class="card tx-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#f3efff;">
                        <i class="fas fa-receipt" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Total Transaksi</p>
                        <h4 class="mb-0 font-weight-bold text-dark" style="font-size:1.05rem;">
                            {{ number_format($totalCount) }}
                        </h4>
                        <span class="text-xs text-muted">entri jurnal</span>
                    </div>
                    <div class="blob" style="background:#6f42c1;"></div>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ══════════════════════════════════════════
         MAIN TABLE CARD
    ══════════════════════════════════════════ --}}
    <div class="card tx-main-card">

        {{-- Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

                {{-- Title --}}
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center gap-2">
                        <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1a8a5c,#2dce89);display:inline-flex;align-items:center;justify-content:center;">
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
            <table class="table tx-table mb-0">
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
