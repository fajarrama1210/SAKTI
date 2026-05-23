@extends('_admin.layouts.app')

@push('styles')
<style>
    /* ── Page Variables ─────────────────────── */
    :root {
        --ks-green:       #1a8a5c;
        --ks-green-light: #2dce89;
        --ks-blue:        #4361ee;
        --ks-orange:      #f4a261;
        --ks-red:         #e63946;
        --ks-gray-soft:   #f8f9fc;
        --ks-card-radius: 18px;
        --ks-shadow:      0 4px 24px rgba(0,0,0,.07);
    }

    /* ── Summary Cards ──────────────────────── */
    .spp-summary-card {
        border-radius: var(--ks-card-radius);
        border: none;
        box-shadow: var(--ks-shadow);
        transition: transform .25s ease, box-shadow .25s ease;
        overflow: hidden;
        position: relative;
    }
    .spp-summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,.12);
    }
    .spp-summary-card .card-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .spp-summary-card .wave {
        position: absolute;
        bottom: 0; right: 0;
        width: 80px; height: 80px;
        border-radius: 50%;
        opacity: .06;
    }

    /* ── Main Card ──────────────────────────── */
    .spp-main-card {
        border-radius: var(--ks-card-radius);
        border: none;
        box-shadow: var(--ks-shadow);
        overflow: hidden;
    }
    .spp-main-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f2f5;
        padding: 1.4rem 1.6rem 1rem;
    }

    /* ── Filters ────────────────────────────── */
    .spp-filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        align-items: center;
    }
    .spp-filter-bar .filter-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .45rem 1rem;
        border-radius: 50px;
        font-size: .75rem;
        font-weight: 600;
        border: 1.5px solid transparent;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
        white-space: nowrap;
    }
    .filter-pill.all     { border-color: #dee2e6; color: #6c757d; background: #f8f9fc; }
    .filter-pill.all.active, .filter-pill.all:hover  { background: #343a40; color: #fff; border-color: #343a40; }
    .filter-pill.paid    { border-color: #c3e6cb; color: var(--ks-green); background: #f0faf5; }
    .filter-pill.paid.active, .filter-pill.paid:hover { background: var(--ks-green); color: #fff; border-color: var(--ks-green); }
    .filter-pill.partial { border-color: #fdeeba; color: #856404; background: #fffdf0; }
    .filter-pill.partial.active, .filter-pill.partial:hover { background: #f4a261; color: #fff; border-color: #f4a261; }
    .filter-pill.unpaid  { border-color: #f5c6cb; color: var(--ks-red); background: #fff5f5; }
    .filter-pill.unpaid.active, .filter-pill.unpaid:hover { background: var(--ks-red); color: #fff; border-color: var(--ks-red); }

    .spp-search-box {
        position: relative;
        flex: 1; min-width: 220px; max-width: 340px;
    }
    .spp-search-box .search-icon {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        color: #adb5bd; font-size: .8rem; pointer-events: none;
    }
    .spp-search-box input {
        padding-left: 34px;
        border-radius: 50px;
        border: 1.5px solid #e9ecef;
        font-size: .8rem;
        background: #f8f9fc;
        transition: border-color .2s, box-shadow .2s;
        width: 100%;
        height: 38px;
    }
    .spp-search-box input:focus {
        border-color: var(--ks-green);
        box-shadow: 0 0 0 3px rgba(26,138,92,.12);
        background: #fff;
        outline: none;
    }
    .spp-search-box .clear-btn {
        position: absolute; right: 10px; top: 50%;
        transform: translateY(-50%);
        color: #adb5bd; font-size: .75rem;
        text-decoration: none; line-height: 1;
        transition: color .2s;
    }
    .spp-search-box .clear-btn:hover { color: var(--ks-red); }

    /* ── Table ──────────────────────────────── */
    .spp-table { margin: 0; }
    .spp-table thead th {
        background: #f8f9fc;
        color: #8898aa;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: .85rem 1.2rem;
        border: none;
        white-space: nowrap;
    }
    .spp-table tbody tr {
        border-bottom: 1px solid #f4f6f9;
        transition: background .15s;
    }
    .spp-table tbody tr:hover { background: #f8fffe; }
    .spp-table tbody tr:last-child { border-bottom: none; }
    .spp-table td {
        padding: .9rem 1.2rem;
        vertical-align: middle;
        border: none;
    }

    /* ── Student Info Cell ──────────────────── */
    .student-cell { display: flex; align-items: center; gap: .7rem; }
    .student-avatar {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem; font-weight: 700;
        color: #fff; flex-shrink: 0;
    }
    .student-name  { font-weight: 600; font-size: .83rem; color: #344767; line-height: 1.2; }
    .student-class { font-size: .72rem; color: #adb5bd; margin-top: 1px; }

    /* ── Progress Bar Cell ──────────────────── */
    .amount-cell { min-width: 160px; }
    .amount-label { display: flex; justify-content: space-between; font-size: .75rem; margin-bottom: 4px; }
    .amount-label .total   { color: #adb5bd; }
    .amount-label .paid-lbl{ font-weight: 700; color: var(--ks-green); }
    .spp-progress {
        height: 6px; border-radius: 10px;
        background: #edf2f7; overflow: hidden;
    }
    .spp-progress-bar {
        height: 100%; border-radius: 10px;
        transition: width .6s ease;
    }

    /* ── Badge Status ───────────────────────── */
    .status-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .85rem; border-radius: 50px;
        font-size: .7rem; font-weight: 700; white-space: nowrap;
    }
    .badge-paid    { background: #e8f8f0; color: #1a8a5c; }
    .badge-partial { background: #fff8e6; color: #856404; }
    .badge-unpaid  { background: #fef0f0; color: #e63946; }

    /* ── Month Badge ────────────────────────── */
    .month-badge {
        display: inline-block;
        padding: .3rem .7rem;
        border-radius: 8px;
        background: #f0f2f5;
        font-size: .75rem; font-weight: 600; color: #495057;
    }

    /* ── Empty State ────────────────────────── */
    .spp-empty {
        padding: 4rem 2rem; text-align: center;
    }
    .spp-empty .empty-icon {
        width: 72px; height: 72px;
        border-radius: 20px;
        background: #f0f2f5;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.2rem;
        font-size: 1.8rem; color: #ced4da;
    }
    .spp-empty h6 { font-weight: 700; color: #344767; margin-bottom: .3rem; }
    .spp-empty p  { font-size: .82rem; color: #adb5bd; }

    /* ── Pagination footer ──────────────────── */
    .spp-footer {
        padding: .9rem 1.6rem;
        border-top: 1px solid #f0f2f5;
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .5rem;
    }

    /* ── Responsive ─────────────────────────── */
    @media (max-width: 768px) {
        .spp-filter-bar { flex-direction: column; align-items: stretch; }
        .spp-search-box { max-width: 100%; }
        .amount-cell { min-width: 120px; }
    }
</style>
@endpush

@section('content')
@php
    $totalBills   = $summaryStats->total_bills   ?? 0;
    $totalAmount  = $summaryStats->total_amount  ?? 0;
    $totalPaid    = $summaryStats->total_paid    ?? 0;
    $countPaid    = $summaryStats->count_paid    ?? 0;
    $countPartial = $summaryStats->count_partial ?? 0;
    $countUnpaid  = $summaryStats->count_unpaid  ?? 0;
    $collectRate  = $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100) : 0;

    $avatarColors = ['#1a8a5c','#4361ee','#f4a261','#e63946','#6f42c1','#0dcaf0','#fd7e14','#20c997'];
@endphp

<div class="container-fluid mt--6">

    {{-- ════════════════════════════════════════════
         SUMMARY CARDS
    ════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Total Tagihan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card spp-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#e8f8f0;">
                        <i class="fas fa-file-invoice-dollar" style="color:#1a8a5c;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Total Tagihan</p>
                        <h4 class="mb-0 font-weight-bold text-dark" style="font-size:1.1rem;">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </h4>
                        <span class="text-xs text-muted">{{ number_format($totalBills) }} tagihan</span>
                    </div>
                    <div class="wave" style="background:#1a8a5c; width:90px; height:90px;"></div>
                </div>
            </div>
        </div>

        {{-- Total Terbayar --}}
        <div class="col-xl-3 col-md-6">
            <div class="card spp-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#e8f0ff;">
                        <i class="fas fa-check-double" style="color:#4361ee;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Terbayar</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.1rem; color:#4361ee;">
                            Rp {{ number_format($totalPaid, 0, ',', '.') }}
                        </h4>
                        <span class="text-xs" style="color:#4361ee;">{{ $collectRate }}% dari tagihan</span>
                    </div>
                    <div class="wave" style="background:#4361ee;"></div>
                </div>
            </div>
        </div>

        {{-- Sisa Tagihan --}}
        <div class="col-xl-3 col-md-6">
            <div class="card spp-summary-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="card-icon" style="background:#fff0f0;">
                        <i class="fas fa-hourglass-half" style="color:#e63946;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Belum Terbayar</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.1rem; color:#e63946;">
                            Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                        </h4>
                        <span class="text-xs text-muted">{{ number_format($countUnpaid + $countPartial) }} tagihan tertunggak</span>
                    </div>
                    <div class="wave" style="background:#e63946;"></div>
                </div>
            </div>
        </div>

        {{-- Kolektibilitas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card spp-summary-card h-100">
                <div class="card-body p-4">
                    <p class="text-xs text-muted mb-2 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Tingkat Kolektibilitas</p>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h4 class="mb-0 font-weight-bold text-dark" style="font-size:1.5rem;">{{ $collectRate }}%</h4>
                        <div class="d-flex gap-3 text-xs">
                            <span><span class="badge rounded-pill px-2 py-1" style="background:#e8f8f0; color:#1a8a5c;">{{ $countPaid }} Lunas</span></span>
                            <span><span class="badge rounded-pill px-2 py-1" style="background:#fff8e6; color:#856404;">{{ $countPartial }} Cicil</span></span>
                            <span><span class="badge rounded-pill px-2 py-1" style="background:#fef0f0; color:#e63946;">{{ $countUnpaid }} Belum</span></span>
                        </div>
                    </div>
                    <div class="spp-progress">
                        <div class="spp-progress-bar"
                             style="width:{{ $collectRate }}%; background: linear-gradient(90deg, #1a8a5c, #2dce89);">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row cards --}}

    {{-- ════════════════════════════════════════════
         MAIN TABLE CARD
    ════════════════════════════════════════════ --}}
    <div class="card spp-main-card">

        {{-- Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center gap-2">
                        <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1a8a5c,#2dce89);display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fas fa-list-ul text-white" style="font-size:.75rem;"></i>
                        </span>
                        Daftar Tagihan SPP
                    </h5>
                    <p class="text-xs text-muted mb-0 mt-1">
                        Pemantauan status pembayaran tagihan bulanan seluruh siswa
                        @if($search || $status)
                            &nbsp;·&nbsp;
                            <a href="{{ route('kepala-sekolah.bills') }}" class="text-danger font-weight-bold" style="font-size:.72rem;">
                                <i class="fas fa-times me-1"></i>Reset Filter
                            </a>
                        @endif
                    </p>
                </div>

                {{-- Filter Bar --}}
                <form action="{{ route('kepala-sekolah.bills') }}" method="GET" id="billsFilterForm">
                    <div class="spp-filter-bar">

                        {{-- Quick Status Pills --}}
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('kepala-sekolah.bills', array_merge(request()->except('status','page'), [])) }}"
                               class="filter-pill all {{ !$status ? 'active' : '' }}"
                               onclick="setStatus(event, '')">
                                <i class="fas fa-border-all"></i> Semua
                            </a>
                            <a href="#" class="filter-pill paid {{ $status === 'paid' ? 'active' : '' }}"
                               onclick="setStatus(event, 'paid')">
                                <i class="fas fa-check-circle"></i> Lunas
                            </a>
                            <a href="#" class="filter-pill partial {{ $status === 'partial' ? 'active' : '' }}"
                               onclick="setStatus(event, 'partial')">
                                <i class="fas fa-history"></i> Dicicil
                            </a>
                            <a href="#" class="filter-pill unpaid {{ $status === 'unpaid' ? 'active' : '' }}"
                               onclick="setStatus(event, 'unpaid')">
                                <i class="fas fa-exclamation-circle"></i> Belum Bayar
                            </a>
                            <input type="hidden" name="status" id="statusInput" value="{{ $status }}">
                            <input type="hidden" name="search" id="searchHidden" value="{{ $search }}">
                        </div>

                        {{-- Search --}}
                        <div class="spp-search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" placeholder="Cari nama / NISN..."
                                   value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <a href="{{ route('kepala-sekolah.bills', array_filter(['status' => $status])) }}"
                                   class="clear-btn" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive" style="min-height:280px;">
            <table class="table spp-table mb-0">
                <thead>
                    <tr>
                        <th style="width:2.5rem;">#</th>
                        <th>Siswa</th>
                        <th>Bulan</th>
                        <th class="text-end">Tagihan &amp; Pembayaran</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    @php
                        $initials = collect(explode(' ', $bill->student_name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                        $avatarColor = $avatarColors[crc32($bill->student_name) % count($avatarColors)];
                        $pct = $bill->total_amount > 0 ? min(100, round(($bill->paid_amount / $bill->total_amount) * 100)) : 0;
                        $barColor = match($bill->status) {
                            'paid'    => 'linear-gradient(90deg,#1a8a5c,#2dce89)',
                            'partial' => 'linear-gradient(90deg,#f4a261,#ffd166)',
                            default   => 'linear-gradient(90deg,#e63946,#ff6b81)',
                        };
                        $monthLabel = \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('M Y');
                    @endphp
                    <tr>
                        {{-- No --}}
                        <td class="text-muted text-xs font-weight-bold">
                            {{ ($bills->currentPage() - 1) * $bills->perPage() + $loop->iteration }}
                        </td>

                        {{-- Siswa --}}
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                                <div>
                                    <div class="student-name">{{ $bill->student_name }}</div>
                                    <div class="student-class">
                                        <i class="fas fa-id-card me-1" style="font-size:.6rem;"></i>{{ $bill->nisn }}
                                        &nbsp;·&nbsp;
                                        <i class="fas fa-school me-1" style="font-size:.6rem;"></i>{{ $bill->classroom_name }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Bulan --}}
                        <td>
                            <span class="month-badge">
                                <i class="fas fa-calendar-alt me-1" style="font-size:.65rem; opacity:.6;"></i>
                                {{ $monthLabel }}
                            </span>
                        </td>

                        {{-- Tagihan & Progress --}}
                        <td class="text-end amount-cell">
                            <div class="amount-label">
                                <span class="paid-lbl">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</span>
                                <span class="total">/ Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="spp-progress">
                                <div class="spp-progress-bar" style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $pct }}% terbayar</div>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @if($bill->status === 'paid')
                                <span class="status-badge badge-paid">
                                    <i class="fas fa-check-circle"></i> Lunas
                                </span>
                            @elseif($bill->status === 'partial')
                                <span class="status-badge badge-partial">
                                    <i class="fas fa-history"></i> Dicicil
                                </span>
                            @else
                                <span class="status-badge badge-unpaid">
                                    <i class="fas fa-exclamation-circle"></i> Belum Bayar
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="spp-empty">
                                <div class="empty-icon">
                                    <i class="fas fa-search-minus"></i>
                                </div>
                                <h6>Tidak Ada Data Tagihan</h6>
                                <p>
                                    @if($search || $status)
                                        Tidak ada tagihan yang cocok dengan filter yang dipilih.
                                        <br>
                                        <a href="{{ route('kepala-sekolah.bills') }}" class="text-success font-weight-bold mt-1 d-inline-block">
                                            <i class="fas fa-redo me-1"></i>Reset Filter
                                        </a>
                                    @else
                                        Belum ada data tagihan SPP yang tersedia.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        <div class="spp-footer">
            <p class="text-xs text-muted mb-0">
                @if($bills->total() > 0)
                    Menampilkan <strong>{{ $bills->firstItem() }}</strong>–<strong>{{ $bills->lastItem() }}</strong>
                    dari <strong>{{ number_format($bills->total()) }}</strong> tagihan
                    @if($search || $status)
                        <span class="text-muted">(difilter)</span>
                    @endif
                @else
                    Tidak ada data untuk ditampilkan
                @endif
            </p>
            <div>{{ $bills->links('pagination::bootstrap-4') }}</div>
        </div>

    </div>{{-- /card --}}
</div>{{-- /container --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form         = document.getElementById('billsFilterForm');
    const statusInput  = document.getElementById('statusInput');
    const searchInput  = document.getElementById('searchInput');
    const searchHidden = document.getElementById('searchHidden');

    // ── Status pill click ──────────────────────────────
    window.setStatus = function (e, val) {
        e.preventDefault();
        statusInput.value  = val;
        searchHidden.value = searchInput.value;
        form.submit();
    };

    // ── Search: submit on Enter, debounce 500ms ────────
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchHidden.value = this.value;
            form.submit();
        }, 550);
    });

    // ── Animate progress bars on load ─────────────────
    document.querySelectorAll('.spp-progress-bar').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0%';
        requestAnimationFrame(() => {
            setTimeout(() => { bar.style.width = target; }, 80);
        });
    });
});
</script>
@endpush
