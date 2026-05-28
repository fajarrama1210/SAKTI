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
                    <i class="fas fa-list-alt me-2"></i> Rekap Tagihan SPP
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Laporan daftar tagihan siswa berdasarkan bulan dan status pembayaran.
                </p>
            </div>
            <a href="{{ route('admin.spp.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pembayaran
            </a>
        </div>
    </div>

    <div class="card dashboard-card mb-4 border-0 shadow-sm">

        {{-- Filter --}}
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.spp.recap') }}" class="row align-items-end">
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Bulan</label>
                    <select name="month" class="form-control form-control-sm">
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Tahun</label>
                    <input type="number" name="year" class="form-control form-control-sm" value="{{ $filters['year'] ?? now()->year }}" min="2024" max="2030">
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Semua</option>
                        <option value="unpaid" {{ ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Cari Nama/NISN</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Ketik pencarian...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-sakti-primary w-100 mb-0"><i class="fas fa-filter me-1"></i> Filter</button>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-light w-100 mb-0">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card dashboard-card">
        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
            <h3 class="section-title mb-0">
                <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                Data Rekap
            </h3>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
                <table class="table letters-table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center">Nama Siswa</th>
                            <th class="text-center">NISN</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Bulan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Jatuh Tempo</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                    @php
                        $isPastDue = ($bill->status !== 'paid' && $bill->status !== 'cancelled' && \Carbon\Carbon::parse($bill->due_date)->isPast());
                    @endphp
                    <tr>
                        <td class="text-center align-middle">{{ $bills->firstItem() + $index }}</td>
                        <td class="text-center align-middle"><b style="color: var(--dark-text);">{{ $bill->student_name }}</b></td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ $bill->nisn }}</td>
                        <td class="text-center align-middle">{{ $bill->grade_level }} – {{ $bill->major_name }}</td>
                        <td class="text-center align-middle">
                            <span style="font-weight: 700; color: var(--dark-text);">{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}</span>
                        </td>
                        <td class="text-center align-middle">
                            @if($bill->status === 'paid')
                                <span class="badge" style="background: rgba(45, 206, 137, 0.1); color: #2dce89; font-weight: 600;"><i class="fas fa-check me-1"></i> Lunas</span>
                            @elseif($bill->status === 'cancelled')
                                <span class="badge" style="background: rgba(136, 152, 170, 0.1); color: #8898aa; font-weight: 600;"><i class="fas fa-ban me-1"></i> Dibatalkan</span>
                            @elseif($isPastDue)
                                <span class="badge" style="background: rgba(245, 54, 92, 0.1); color: #f5365c; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i> Terlambat</span>
                            @else
                                <span class="badge" style="background: rgba(251, 99, 64, 0.1); color: #fb6340; font-weight: 600;"><i class="fas fa-clock me-1"></i> Belum Bayar</span>
                            @endif
                        </td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">
                            {{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}
                        </td>
                        <td class="text-center align-middle">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow">
                                    <a class="dropdown-item" href="{{ route('admin.spp.student', $bill->id) }}">
                                        <i class="fas fa-eye text-info"></i> Lihat Detail Siswa
                                    </a>
                                    @if($bill->status !== 'paid')
                                    <form action="{{ route('admin.spp.destroy', $bill->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Hapus Tagihan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
