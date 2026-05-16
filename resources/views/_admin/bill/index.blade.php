@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    {{-- Sarch Box --}}
    <div class="card mb-4">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0"><i class="fas fa-money-bill-wave text-success"></i> Pembayaran SPP</h3>
                </div>
                <div class="col-auto d-flex gap-2">
                    <form action="{{ route('admin.spp.sync') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Gunakan ini jika ada siswa atau tarif baru yang tagihannya belum muncul">
                            <i class="fas fa-sync"></i> Sinkronisasi Tagihan
                        </button>
                    </form>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-outline-primary ml-2">
                        <i class="fas fa-list-alt"></i> Rekap Tagihan
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('admin.spp.index') }}">
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" value="{{ $search }}"
                           placeholder="Ketik nama siswa, NISN, atau No. KK lalu tekan Enter..."
                           autofocus>
                    @if($search)
                    <div class="input-group-append">
                        <a href="{{ route('admin.spp.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
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
                    <h4 class="text-muted">Tidak ditemukan siswa dengan kata kunci "{{ $search }}"</h4>
                    <p class="text-muted">Coba cari dengan nama lain, NISN, atau Nomor KK.</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($students as $student)
                <div class="col-xl-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="mb-0">{{ $student->name }}</h4>
                                    <p class="text-sm text-muted mb-0">
                                        NISN: <code>{{ $student->nisn }}</code> &bull;
                                        Kelas {{ $student->grade_level }} – {{ $student->major_name }}
                                    </p>
                                </div>
                                @if($student->sibling_count > 1)
                                <span class="badge badge-warning" title="Ada {{ $student->sibling_count }} siswa dengan Nomor KK yang sama">
                                    <i class="fas fa-users"></i> {{ $student->sibling_count }} SeKK
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            @if($student->bills && $student->bills->count() > 0)
                                @php
                                    $currentMonth = now()->month;
                                    $currentYear  = now()->year;
                                @endphp
                                <div class="table-responsive">
                                    <table class="table table-sm align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-xs text-muted">Bulan</th>
                                                <th class="text-xs text-muted text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->bills->take(6) as $bill)
                                            <tr class="{{ ($bill->month == $currentMonth && $bill->year == $currentYear) ? 'table-active' : '' }}">
                                                <td>
                                                    <span class="text-sm font-weight-bold">
                                                        {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                                    </span>
                                                    @if($bill->month == $currentMonth && $bill->year == $currentYear)
                                                        <span class="badge badge-primary ml-1" style="font-size:0.6rem;">BULAN INI</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($bill->status === 'paid')
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Lunas</span>
                                                    @else
                                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Belum</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($student->bills->count() > 6)
                                    <p class="text-xs text-muted mt-2 mb-0">...dan {{ $student->bills->count() - 6 }} bulan lainnya</p>
                                @endif
                            @else
                                <p class="text-sm text-muted text-center py-3 mb-0">
                                    <i class="fas fa-info-circle"></i> Belum ada tagihan. Semester mungkin belum dibuat.
                                </p>
                            @endif
                        </div>
                        <div class="card-footer bg-white pt-0">
                            <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> Lihat Detail & Bayar
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @elseif(!$search)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-4x text-primary mb-4" style="opacity:0.3"></i>
                <h4 class="text-muted">Cari Siswa untuk Memulai Pembayaran</h4>
                <p class="text-muted mb-0">Masukkan nama siswa, NISN, atau Nomor KK pada kolom pencarian di atas.</p>
            </div>
        </div>
    @endif
</div>
@endsection
