@extends('_admin.layouts.app')

@push('styles')
<style>
    /* ── Variables ──────────────────────────── */
    :root {
        --st-green:  #1a8a5c;
        --st-blue:   #4361ee;
        --st-purple: #6f42c1;
        --st-orange: #f4a261;
        --st-red:    #e63946;
        --st-radius: 18px;
        --st-shadow: 0 4px 24px rgba(0,0,0,.07);
    }

    /* ── Summary Cards ──────────────────────── */
    .st-card {
        border-radius: var(--st-radius);
        border: none;
        box-shadow: var(--st-shadow);
        transition: transform .25s ease, box-shadow .25s ease;
        overflow: hidden;
        position: relative;
    }
    .st-card:hover { transform: translateY(-4px); box-shadow: 0 14px 34px rgba(0,0,0,.12); }
    .st-card .c-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .st-card .blob {
        position: absolute; bottom: -20px; right: -20px;
        width: 90px; height: 90px; border-radius: 50%; opacity: .07;
    }

    /* ── Grade Distribution Bar ─────────────── */
    .grade-dist { display: flex; gap: 6px; margin-top: 10px; }
    .grade-seg {
        height: 8px; border-radius: 10px; flex-shrink: 0;
        transition: width .6s ease;
    }
    .grade-legend {
        display: flex; flex-wrap: wrap; gap: .4rem .8rem; margin-top: 8px;
    }
    .grade-legend-item { display: flex; align-items: center; gap: 4px; font-size: .68rem; color: #6c757d; }
    .grade-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ── Main Card ──────────────────────────── */
    .st-main-card {
        border-radius: var(--st-radius); border: none;
        box-shadow: var(--st-shadow); overflow: hidden;
    }
    .st-main-card .card-header {
        background: #fff; border-bottom: 1px solid #f0f2f5;
        padding: 1.4rem 1.6rem 1rem;
    }

    /* ── Filter Bar ─────────────────────────── */
    .st-filter-bar { display: flex; flex-wrap: wrap; gap: .55rem; align-items: center; }

    .st-pill {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .95rem; border-radius: 50px; font-size: .72rem;
        font-weight: 700; border: 1.5px solid transparent;
        cursor: pointer; transition: all .2s; text-decoration: none; white-space: nowrap;
    }
    .st-pill.all     { border-color:#dee2e6; color:#6c757d; background:#f8f9fc; }
    .st-pill.all.active, .st-pill.all:hover { background:#343a40; color:#fff; border-color:#343a40; }
    .st-pill.aktif   { border-color:#c3e6cb; color:var(--st-green); background:#f0faf5; }
    .st-pill.aktif.active, .st-pill.aktif:hover { background:var(--st-green); color:#fff; border-color:var(--st-green); }
    .st-pill.lulus   { border-color:#d4edda; color:#155724; background:#f4fdf6; }
    .st-pill.lulus.active, .st-pill.lulus:hover { background:#155724; color:#fff; border-color:#155724; }
    .st-pill.keluar  { border-color:#f5c6cb; color:var(--st-red); background:#fff5f5; }
    .st-pill.keluar.active, .st-pill.keluar:hover { background:var(--st-red); color:#fff; border-color:var(--st-red); }

    /* ── Search & Select ─────────────────────── */
    .st-search-box { position:relative; min-width:210px; flex:1; max-width:300px; }
    .st-search-box .s-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.78rem; pointer-events:none; }
    .st-search-box input {
        padding-left:34px; border-radius:50px;
        border:1.5px solid #e9ecef; font-size:.78rem;
        background:#f8f9fc; height:38px; width:100%;
        transition: border-color .2s, box-shadow .2s;
    }
    .st-search-box input:focus { border-color:var(--st-green); box-shadow:0 0 0 3px rgba(26,138,92,.12); background:#fff; outline:none; }
    .st-search-box .clr { position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#adb5bd; font-size:.73rem; text-decoration:none; transition:color .2s; }
    .st-search-box .clr:hover { color:var(--st-red); }

    .st-select {
        height:38px; border-radius:50px;
        border:1.5px solid #e9ecef; font-size:.78rem;
        background:#f8f9fc; padding:.3rem 1rem;
        color:#495057; cursor:pointer;
        transition: border-color .2s;
        min-width:160px;
    }
    .st-select:focus { border-color:var(--st-green); box-shadow:0 0 0 3px rgba(26,138,92,.12); outline:none; background:#fff; }

    /* ── Table ──────────────────────────────── */
    .st-table { margin:0; }
    .st-table thead th {
        background:#f8f9fc; color:#8898aa; font-size:.68rem;
        font-weight:700; text-transform:uppercase; letter-spacing:.06em;
        padding:.8rem 1.2rem; border:none; white-space:nowrap;
    }
    .st-table tbody tr { border-bottom:1px solid #f4f6f9; transition:background .15s; }
    .st-table tbody tr:hover { background:#f8fffe; }
    .st-table tbody tr:last-child { border-bottom:none; }
    .st-table td { padding:.85rem 1.2rem; vertical-align:middle; border:none; }

    /* ── Student Cell ────────────────────────── */
    .stu-cell { display:flex; align-items:center; gap:.7rem; }
    .stu-avatar {
        width:40px; height:40px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-size:.88rem; font-weight:800; color:#fff; flex-shrink:0;
        letter-spacing:-.5px;
    }
    .stu-name  { font-weight:700; font-size:.83rem; color:#344767; line-height:1.2; }
    .stu-nisn  { font-size:.68rem; color:#adb5bd; margin-top:2px; }

    /* ── Identity Cell ───────────────────────── */
    .id-label  { font-size:.65rem; color:#adb5bd; margin-bottom:1px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }
    .id-value  { font-size:.78rem; color:#495057; font-weight:600; font-family:monospace; letter-spacing:.03em; }

    /* ── Classroom Badge ─────────────────────── */
    .class-badge {
        display:inline-flex; align-items:center; gap:.32rem;
        padding:.32rem .75rem; border-radius:50px;
        font-size:.7rem; font-weight:700;
        background:#e8f8f0; color:#1a8a5c;
        white-space:nowrap;
    }
    .grade-bubble {
        display:inline-flex; align-items:center; justify-content:center;
        width:20px; height:20px; border-radius:50%;
        background:#1a8a5c; color:#fff;
        font-size:.62rem; font-weight:800; flex-shrink:0;
    }

    /* ── Status Badge ────────────────────────── */
    .stu-status {
        display:inline-flex; align-items:center; gap:.3rem;
        padding:.28rem .7rem; border-radius:50px;
        font-size:.67rem; font-weight:700;
    }
    .s-aktif  { background:#e8f8f0; color:#1a8a5c; }
    .s-lulus  { background:#e3f5e9; color:#155724; }
    .s-keluar { background:#fef0f0; color:#e63946; }
    .s-other  { background:#f0f2f5; color:#6c757d; }

    /* ── Empty State ─────────────────────────── */
    .st-empty { padding:4rem 2rem; text-align:center; }
    .st-empty .e-icon { width:72px; height:72px; border-radius:20px; background:#f0f2f5; display:flex; align-items:center; justify-content:center; margin:0 auto 1.2rem; font-size:1.8rem; color:#ced4da; }
    .st-empty h6 { font-weight:700; color:#344767; margin-bottom:.3rem; }
    .st-empty p  { font-size:.82rem; color:#adb5bd; }

    /* ── Footer ──────────────────────────────── */
    .st-footer {
        padding:.9rem 1.6rem; border-top:1px solid #f0f2f5;
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:.5rem;
    }

    @media (max-width:768px) {
        .st-filter-bar { flex-direction:column; align-items:stretch; }
        .st-search-box { max-width:100%; }
        .st-select { min-width:100%; }
    }
</style>
@endpush

@section('content')
@php
    $gradeColors = ['#1a8a5c','#4361ee','#6f42c1','#f4a261','#e63946','#0dcaf0'];
    $avatarColors = ['#1a8a5c','#4361ee','#f4a261','#e63946','#6f42c1','#0dcaf0','#fd7e14','#20c997'];

    $countAktif  = $statusStats->get('aktif')->total  ?? 0;
    $countLulus  = $statusStats->get('lulus')->total  ?? 0;
    $countKeluar = $statusStats->get('keluar')->total ?? 0;

    $activeFilters = collect([$search, $classroomId, $status])->filter()->count();
@endphp

<div class="container-fluid mt--6">

    {{-- ══════════════════════════════════════
         SUMMARY CARDS
    ══════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Total Siswa --}}
        <div class="col-xl-3 col-md-6">
            <div class="card st-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="c-icon" style="background:#e8f0ff;">
                        <i class="fas fa-users" style="color:#4361ee;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Total Siswa</p>
                        <h4 class="mb-0 font-weight-bold text-dark" style="font-size:1.5rem;">{{ number_format($totalStudents) }}</h4>
                        <span class="text-xs text-muted">terdaftar dalam sistem</span>
                    </div>
                    <div class="blob" style="background:#4361ee;"></div>
                </div>
            </div>
        </div>

        {{-- Siswa Aktif --}}
        <div class="col-xl-3 col-md-6">
            <div class="card st-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="c-icon" style="background:#e8f8f0;">
                        <i class="fas fa-user-check" style="color:#1a8a5c;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Aktif</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.5rem; color:#1a8a5c;">{{ number_format($countAktif) }}</h4>
                        <span class="text-xs text-muted">siswa aktif belajar</span>
                    </div>
                    <div class="blob" style="background:#1a8a5c;"></div>
                </div>
            </div>
        </div>

        {{-- Lulus --}}
        <div class="col-xl-3 col-md-6">
            <div class="card st-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="c-icon" style="background:#f3efff;">
                        <i class="fas fa-user-graduate" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Lulus</p>
                        <h4 class="mb-0 font-weight-bold" style="font-size:1.5rem; color:#6f42c1;">{{ number_format($countLulus) }}</h4>
                        <span class="text-xs text-muted">siswa telah lulus</span>
                    </div>
                    <div class="blob" style="background:#6f42c1;"></div>
                </div>
            </div>
        </div>

        {{-- Distribusi per Tingkat --}}
        <div class="col-xl-3 col-md-6">
            <div class="card st-card h-100">
                <div class="card-body p-4">
                    <p class="text-xs text-muted mb-1 font-weight-bold text-uppercase" style="letter-spacing:.05em;">Distribusi per Tingkat</p>

                    @if($gradeStats->count() > 0)
                        {{-- Progress bar distribusi --}}
                        <div class="grade-dist">
                            @foreach($gradeStats as $i => $gs)
                                @php $pct = $totalStudents > 0 ? round(($gs->total / $totalStudents) * 100) : 0; @endphp
                                <div class="grade-seg" style="width:{{ $pct }}%; background:{{ $gradeColors[$i % count($gradeColors)] }};"></div>
                            @endforeach
                        </div>
                        {{-- Legend --}}
                        <div class="grade-legend mt-2">
                            @foreach($gradeStats as $i => $gs)
                                <div class="grade-legend-item">
                                    <div class="grade-dot" style="background:{{ $gradeColors[$i % count($gradeColors)] }};"></div>
                                    <span>Kls {{ $gs->grade_level }} <strong>({{ $gs->total }})</strong></span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-muted mt-2">Belum ada data.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /row cards --}}

    {{-- ══════════════════════════════════════
         MAIN TABLE CARD
    ══════════════════════════════════════ --}}
    <div class="card st-main-card">

        {{-- Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

                {{-- Title --}}
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark d-flex align-items-center gap-2">
                        <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1a8a5c,#2dce89);display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fas fa-user-graduate text-white" style="font-size:.72rem;"></i>
                        </span>
                        Daftar Siswa
                    </h5>
                    <p class="text-xs text-muted mb-0 mt-1">
                        Data identitas seluruh siswa terdaftar dalam sistem
                        @if($activeFilters > 0)
                            &nbsp;·&nbsp;
                            <a href="{{ route('kepala-sekolah.students') }}" class="text-danger font-weight-bold" style="font-size:.72rem;">
                                <i class="fas fa-times me-1"></i>Reset Filter
                            </a>
                        @endif
                    </p>
                </div>

                {{-- Filter Bar --}}
                <form action="{{ route('kepala-sekolah.students') }}" method="GET" id="stuFilterForm">
                    <div class="st-filter-bar">

                        {{-- Status Pills --}}
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="#" class="st-pill all {{ !$status ? 'active' : '' }}" onclick="setStatus(event,'')">
                                <i class="fas fa-border-all"></i> Semua
                            </a>
                            <a href="#" class="st-pill aktif {{ $status==='aktif' ? 'active' : '' }}" onclick="setStatus(event,'aktif')">
                                <i class="fas fa-circle" style="font-size:.5rem;"></i> Aktif
                            </a>
                            <a href="#" class="st-pill lulus {{ $status==='lulus' ? 'active' : '' }}" onclick="setStatus(event,'lulus')">
                                <i class="fas fa-graduation-cap"></i> Lulus
                            </a>
                            <a href="#" class="st-pill keluar {{ $status==='keluar' ? 'active' : '' }}" onclick="setStatus(event,'keluar')">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </div>

                        {{-- Classroom Select --}}
                        <select class="st-select" id="classroomSelect" name="classroom_id_hidden"
                                title="Filter kelas">
                            <option value="">Semua Kelas</option>
                            @foreach($classroomOptions as $cls)
                                <option value="{{ $cls->id }}" {{ $classroomId == $cls->id ? 'selected' : '' }}>
                                    Kls {{ $cls->grade_level }} — {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Hidden inputs --}}
                        <input type="hidden" name="status"       id="statusInput"      value="{{ $status }}">
                        <input type="hidden" name="search"       id="searchHidden"     value="{{ $search }}">
                        <input type="hidden" name="classroom_id" id="classroomHidden"  value="{{ $classroomId }}">

                        {{-- Search --}}
                        <div class="st-search-box">
                            <i class="fas fa-search s-icon"></i>
                            <input type="text" id="searchInput"
                                   placeholder="Cari nama / NISN / NIK..."
                                   value="{{ $search }}" autocomplete="off">
                            @if($search)
                                <a href="{{ route('kepala-sekolah.students', array_filter(['status'=>$status,'classroom_id'=>$classroomId])) }}"
                                   class="clr"><i class="fas fa-times"></i></a>
                            @endif
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive" style="min-height:280px;">
            <table class="table st-table mb-0">
                <thead>
                    <tr>
                        <th style="width:2.5rem;">#</th>
                        <th>Siswa</th>
                        <th>NIK</th>
                        <th>No. KK</th>
                        <th>Kelas</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $initials = collect(explode(' ', $student->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                        $aColor   = $avatarColors[crc32($student->name) % count($avatarColors)];
                        $statusKey = strtolower($student->status ?? 'other');
                    @endphp
                    <tr>
                        {{-- No --}}
                        <td class="text-muted text-xs font-weight-bold">
                            {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                        </td>

                        {{-- Siswa --}}
                        <td>
                            <div class="stu-cell">
                                <div class="stu-avatar" style="background:{{ $aColor }};">{{ $initials }}</div>
                                <div>
                                    <div class="stu-name">{{ $student->name }}</div>
                                    <div class="stu-nisn">
                                        <i class="fas fa-id-badge me-1" style="font-size:.6rem;"></i>NISN: {{ $student->nisn }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- NIK --}}
                        <td>
                            @if(!empty($student->id_number))
                                <div class="id-label">NIK</div>
                                <div class="id-value">{{ $student->id_number }}</div>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>

                        {{-- No. KK --}}
                        <td>
                            @if(!empty($student->family_card_number))
                                <div class="id-label">No. KK</div>
                                <div class="id-value">{{ $student->family_card_number }}</div>
                            @else
                                <span class="text-xs text-muted">—</span>
                            @endif
                        </td>

                        {{-- Kelas --}}
                        <td>
                            <span class="class-badge">
                                <span class="grade-bubble">{{ $student->grade_level }}</span>
                                {{ $student->classroom_name }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'aktif'  => ['cls' => 's-aktif',  'icon' => 'fa-check-circle',  'label' => 'Aktif'],
                                    'lulus'  => ['cls' => 's-lulus',  'icon' => 'fa-graduation-cap','label' => 'Lulus'],
                                    'keluar' => ['cls' => 's-keluar', 'icon' => 'fa-sign-out-alt',  'label' => 'Keluar'],
                                ];
                                $s = $statusMap[$statusKey] ?? ['cls' => 's-other', 'icon' => 'fa-circle', 'label' => ucfirst($statusKey)];
                            @endphp
                            <span class="stu-status {{ $s['cls'] }}">
                                <i class="fas {{ $s['icon'] }}" style="font-size:.6rem;"></i>
                                {{ $s['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="st-footer">
            <p class="text-xs text-muted mb-0">
                @if($students->total() > 0)
                    Menampilkan <strong>{{ $students->firstItem() }}</strong>–<strong>{{ $students->lastItem() }}</strong>
                    dari <strong>{{ number_format($students->total()) }}</strong> siswa
                    @if($activeFilters > 0)
                        <span class="text-muted">(difilter)</span>
                    @endif
                @else
                    Tidak ada data untuk ditampilkan
                @endif
            </p>
            <div>{{ $students->links('pagination::bootstrap-4') }}</div>
        </div>

    </div>{{-- /card --}}
</div>{{-- /container --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form            = document.getElementById('stuFilterForm');
    const statusInput     = document.getElementById('statusInput');
    const searchInput     = document.getElementById('searchInput');
    const searchHidden    = document.getElementById('searchHidden');
    const classroomSelect = document.getElementById('classroomSelect');
    const classroomHidden = document.getElementById('classroomHidden');

    // ── Status pill ────────────────────────────
    window.setStatus = function (e, val) {
        e.preventDefault();
        statusInput.value     = val;
        searchHidden.value    = searchInput.value;
        classroomHidden.value = classroomSelect.value;
        form.submit();
    };

    // ── Classroom select change ────────────────
    classroomSelect.addEventListener('change', function () {
        classroomHidden.value = this.value;
        searchHidden.value    = searchInput.value;
        form.submit();
    });

    // ── Search debounce ────────────────────────
    let searchTimer;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            searchHidden.value    = this.value;
            classroomHidden.value = classroomSelect.value;
            form.submit();
        }, 520);
    });

    // ── Animate grade distribution bars ────────
    document.querySelectorAll('.grade-seg').forEach(seg => {
        const target = seg.style.width;
        seg.style.width = '0%';
        requestAnimationFrame(() => setTimeout(() => { seg.style.width = target; }, 100));
    });
});
</script>
@endpush
