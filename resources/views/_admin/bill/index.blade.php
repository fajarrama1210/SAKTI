@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    {{-- Search Box --}}
    <div class="card sakti-card mb-4">
        <div class="card-header border-0 bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0 text-sakti-green font-weight-bold">
                        <i class="fas fa-money-bill-wave mr-2"></i> Pembayaran SPP
                    </h3>
                </div>
                <div class="col-auto d-flex gap-2">
                    <form action="{{ route('admin.spp.sync') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success" style="border-color: #2dce89; color: #2dce89;" data-bs-toggle="tooltip" title="Gunakan ini jika ada siswa atau tarif baru yang tagihannya belum muncul">
                            <i class="fas fa-sync mr-1"></i> Sinkronisasi Tagihan
                        </button>
                    </form>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-outline-primary ml-2" style="border-color: #5e72e4; color: #5e72e4;">
                        <i class="fas fa-list-alt mr-1"></i> Rekap Tagihan
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <form method="GET" action="{{ route('admin.spp.index') }}">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 {{ $search ? 'border-end-0' : '' }}" value="{{ $search }}"
                           placeholder="Ketik nama siswa, NISN, atau No. KK lalu tekan Enter..."
                           autofocus autocomplete="off">
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
                                    <table class="table align-items-center mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase ps-4">Bulan Tagihan</th>
                                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($student->bills->take(6) as $bill)
                                            <tr class="{{ ($bill->month == $currentMonth && $bill->year == $currentYear) ? 'bg-light' : '' }}">
                                                <td class="ps-4 align-middle">
                                                    <span class="text-sm font-weight-bold text-dark">
                                                        {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                                    </span>
                                                    @if($bill->month == $currentMonth && $bill->year == $currentYear)
                                                        <span class="badge bg-primary ms-2">Bulan Ini</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @if($bill->status === 'paid')
                                                        <span class="badge bg-success"><i class="fas fa-check"></i> Lunas</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Belum Bayar</span>
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
