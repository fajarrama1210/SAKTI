@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 text-dark font-weight-bold"><i class="fas fa-file-invoice-dollar text-success me-2"></i> Pemantauan Tagihan SPP</h3>
                <p class="text-xs text-muted mb-0">Laporan status tagihan bulanan seluruh siswa.</p>
            </div>
            
            <!-- FILTERS -->
            <form action="{{ route('kepala-sekolah.bills') }}" method="GET" class="mt-2 mt-md-0 d-flex gap-2 align-items-center">
                <!-- Status Filter -->
                <select name="status" class="form-select border-0 bg-light text-xs px-3 py-2" style="border-radius: 8px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Dicicil</option>
                </select>

                <!-- Search Input -->
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 text-xs" style="width: 250px;" value="{{ $search }}" placeholder="Cari nama siswa / NISN...">
                    @if($search || $status)
                        <a href="{{ route('kepala-sekolah.bills') }}" class="btn btn-light border-0 my-0 text-danger"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body px-0 pb-4">
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-xs font-weight-bold">Nama Siswa</th>
                            <th class="text-xs font-weight-bold">NISN</th>
                            <th class="text-xs font-weight-bold">Bulan Tagihan</th>
                            <th class="text-xs font-weight-bold text-end">Jumlah Tagihan</th>
                            <th class="text-xs font-weight-bold text-end">Terbayar</th>
                            <th class="text-xs font-weight-bold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td class="text-sm font-weight-bold text-dark">{{ $bill->student_name }}</td>
                                <td class="text-sm text-muted">{{ $bill->nisn }}</td>
                                <td class="text-sm text-dark font-weight-bold">
                                    {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                </td>
                                <td class="text-sm font-weight-bold text-dark text-end">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                                <td class="text-sm font-weight-bold text-success text-end">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($bill->status === 'paid')
                                        <span class="badge bg-success px-3 py-2 font-weight-bold"><i class="fas fa-check-circle me-1"></i> Lunas</span>
                                    @elseif($bill->status === 'partial')
                                        <span class="badge bg-warning px-3 py-2 font-weight-bold text-dark"><i class="fas fa-history me-1"></i> Dicicil</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 font-weight-bold"><i class="fas fa-exclamation-triangle me-1"></i> Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-excel fa-2x mb-3 text-light"></i><br>
                                    Tidak ditemukan data tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-4 mt-3 d-flex justify-content-between align-items-center">
                <div class="text-xs text-muted">
                    Menampilkan {{ $bills->firstItem() ?? 0 }} sampai {{ $bills->lastItem() ?? 0 }} dari {{ $bills->total() }} tagihan
                </div>
                <div>
                    {{ $bills->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
