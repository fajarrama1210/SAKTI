@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0"><i class="fas fa-list-alt"></i> Rekap Tagihan SPP</h3>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.spp.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Pembayaran
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card-body pt-0 pb-2">
            <form method="GET" action="{{ route('admin.spp.recap') }}" class="row align-items-end">
                <div class="col-md-2">
                    <label class="form-control-label text-xs">Bulan</label>
                    <select name="month" class="form-control form-control-sm">
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-control-label text-xs">Tahun</label>
                    <input type="number" name="year" class="form-control form-control-sm" value="{{ $filters['year'] ?? now()->year }}" min="2024" max="2030">
                </div>
                <div class="col-md-2">
                    <label class="form-control-label text-xs">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Semua</option>
                        <option value="unpaid" {{ ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-control-label text-xs">Cari Nama/NISN</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ $filters['search'] ?? '' }}" placeholder="Cari...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
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
                        <td>{{ $bills->firstItem() + $index }}</td>
                        <td><b>{{ $bill->student_name }}</b></td>
                        <td><code>{{ $bill->nisn }}</code></td>
                        <td>{{ $bill->grade_level }} – {{ $bill->major_name }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                        </td>
                        <td class="text-center">
                            @if($bill->status === 'paid')
                                <span class="badge badge-sm bg-gradient-success">Lunas</span>
                            @elseif($bill->status === 'cancelled')
                                <span class="badge badge-sm bg-gradient-secondary">Dibatalkan</span>
                            @elseif($isPastDue)
                                <span class="badge badge-sm bg-gradient-danger">Terlambat</span>
                            @else
                                <span class="badge badge-sm bg-gradient-warning">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <small>{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-center">
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
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $bills->links() }}
        </div>
    </div>
</div>
@endsection
