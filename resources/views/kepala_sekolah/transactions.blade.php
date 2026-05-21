@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 text-dark font-weight-bold"><i class="fas fa-wallet text-success me-2"></i> Pemantauan Transaksi</h3>
                <p class="text-xs text-muted mb-0">Riwayat jurnal penerimaan dan pengeluaran kas sekolah.</p>
            </div>
            
            <!-- SEARCH BOX -->
            <form action="{{ route('kepala-sekolah.transactions') }}" method="GET" class="mt-2 mt-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 text-xs" style="width: 280px;" value="{{ $search }}" placeholder="Cari deskripsi atau no referensi...">
                    @if($search)
                        <a href="{{ route('kepala-sekolah.transactions') }}" class="btn btn-light border-0 my-0 text-danger"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body px-0 pb-4">
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-xs font-weight-bold">Tanggal</th>
                            <th class="text-xs font-weight-bold">Nomor Referensi</th>
                            <th class="text-xs font-weight-bold">Deskripsi</th>
                            <th class="text-xs font-weight-bold text-center">Tipe</th>
                            <th class="text-xs font-weight-bold text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="text-sm font-weight-bold text-dark">
                                    {{ \Carbon\Carbon::parse($tx->date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="text-sm text-muted font-weight-bold">{{ $tx->reference_number ?? '-' }}</td>
                                <td class="text-sm text-dark">{{ $tx->description }}</td>
                                <td class="text-center">
                                    @if($tx->type === 'income')
                                        <span class="badge bg-soft-success text-success px-3 py-2 font-weight-bold text-xs"><i class="fas fa-arrow-down me-1"></i> Pemasukan</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger px-3 py-2 font-weight-bold text-xs"><i class="fas fa-arrow-up me-1"></i> Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="text-sm font-weight-bold text-end @if($tx->type === 'income') text-success @else text-danger @endif">
                                    @if($tx->type === 'income') + @else - @endif Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-2x mb-3 text-light"></i><br>
                                    Tidak ditemukan data transaksi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-4 mt-3 d-flex justify-content-between align-items-center">
                <div class="text-xs text-muted">
                    Menampilkan {{ $transactions->firstItem() ?? 0 }} sampai {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
                </div>
                <div>
                    {{ $transactions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
