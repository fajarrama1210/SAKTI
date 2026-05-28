@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <!-- HEADER -->
    <div class="letters-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-money-bill-wave me-2"></i> Pembayaran SPP
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Cari siswa untuk kelola dan catat pembayaran SPP bulanan.
                </p>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.spp.sync') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-glass btn-glass-danger" data-bs-toggle="tooltip" title="Gunakan ini jika ada siswa atau tarif baru yang tagihannya belum muncul">
                        <i class="fas fa-sync me-1"></i> Sinkronisasi Tagihan
                    </button>
                </form>
                <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-glass btn-glass-primary">
                    <i class="fas fa-list-alt me-1"></i> Rekap Tagihan
                </a>
            </div>
        </div>
    </div>

    {{-- Search Box --}}
    <div class="card dashboard-card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.spp.index') }}">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 {{ $search ? 'border-end-0' : '' }}" value="{{ $search }}"
                           placeholder="Ketik nama siswa, NISN, atau No. KK lalu tekan Enter..."
                           autofocus autocomplete="off" style="font-weight: 500; font-size: 1rem;">
                    @if($search)
                        <a href="{{ route('admin.spp.index') }}" class="input-group-text bg-white text-danger border-start-0 text-decoration-none" title="Bersihkan Pencarian" style="cursor: pointer;">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Pencarian --}}
    @if($search && $students !== null)
        @if($students->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted font-weight-bold">Tidak ditemukan siswa dengan kata kunci "{{ $search }}"</h4>
                    <p class="text-muted">Coba cari dengan nama lain, NISN, atau Nomor KK.</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($students as $student)
                <div class="col-xl-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header pb-0 border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1 text-dark font-weight-bold">{{ $student->name }}</h5>
                                    <p class="text-sm text-muted mb-0">
                                        <span class="badge bg-secondary">NISN: {{ $student->nisn }}</span>
                                        <span class="mx-1">&bull;</span>
                                        <span class="text-dark font-weight-bold">Kelas {{ $student->grade_level }} – {{ $student->major_name }}</span>
                                    </p>
                                </div>
                                @if($student->sibling_count > 1)
                                <span class="badge bg-warning" title="Ada {{ $student->sibling_count }} siswa dengan Nomor KK yang sama">
                                    <i class="fas fa-users"></i> {{ $student->sibling_count }} Se-KK
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            @if($student->bills && $student->bills->count() > 0)
                                @php
                                    $currentMonth = now()->month;
                                    $currentYear  = now()->year;
                                @endphp
                                <div class="table-responsive">
                                    <table class="table letters-table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50%;">Bulan Tagihan</th>
                                                <th class="text-center" style="width: 50%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->bills->take(6) as $bill)
                                            <tr class="{{ ($bill->month == $currentMonth && $bill->year == $currentYear) ? 'bg-light' : '' }}">
                                                <td class="text-center align-middle">
                                                    <span class="text-sm font-weight-bold text-dark">
                                                        {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                                    </span>
                                                    @if($bill->month == $currentMonth && $bill->year == $currentYear)
                                                        <span class="badge" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4; font-weight: 600; font-size: 0.65rem; margin-left: 6px;">Bulan Ini</span>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($bill->status === 'paid')
                                                        <span class="badge" style="background: rgba(45, 206, 137, 0.1); color: #2dce89; font-weight: 600;"><i class="fas fa-check me-1"></i> Lunas</span>
                                                    @else
                                                        <span class="badge" style="background: rgba(245, 54, 92, 0.1); color: #f5365c; font-weight: 600;"><i class="fas fa-times me-1"></i> Belum Bayar</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($student->bills->count() > 6)
                                    <div class="text-center mt-3">
                                        <span class="text-xs text-muted">
                                            <i class="fas fa-ellipsis-h"></i> Dan {{ $student->bills->count() - 6 }} bulan lainnya dapat dilihat di detail
                                        </span>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light text-center border" role="alert">
                                    <i class="fas fa-info-circle text-muted mb-2 fa-lg"></i>
                                    <p class="text-sm text-muted mb-0">Belum ada tagihan untuk siswa ini.</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer pt-0 bg-white border-0">
                            <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-sakti-primary w-100">
                                <i class="fas fa-arrow-right mr-2"></i> Lihat Detail & Kelola Pembayaran
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @elseif(!$search)
        <div class="card sakti-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-4x text-sakti-green mb-4" style="opacity:0.3"></i>
                <h4 class="text-muted font-weight-bold">Cari Siswa untuk Memulai Pembayaran</h4>
                <p class="text-muted mb-0">Masukkan nama siswa, NISN, atau Nomor KK pada kolom pencarian di atas.</p>
            </div>
        </div>
    @endif
</div>
@endsection
