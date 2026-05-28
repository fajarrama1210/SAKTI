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
                    <i class="fas fa-exchange-alt me-2"></i> Transaksi
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola data transaksi pemasukan dan pengeluaran.
                </p>
            </div>
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-plus me-1"></i> Catat Transaksi
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Transaksi
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Dicatat Oleh</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr>
                        <td class="text-center align-middle">{{ $transactions->firstItem() + $index }}</td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">
                            {{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}
                        </td>
                        <td class="text-center align-middle">
                            @if($trx->type === 'income')
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 600;"><i class="fas fa-arrow-down me-1"></i> Masuk</span>
                            @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600;"><i class="fas fa-arrow-up me-1"></i> Keluar</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <span style="font-weight: 600; color: var(--dark-text);">{{ $trx->category ?? '-' }}</span>
                        </td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ \Illuminate\Support\Str::limit($trx->description, 50) }}</td>
                        <td class="text-center align-middle">
                            <b style="color: {{ $trx->type === 'income' ? 'var(--success)' : 'var(--danger)' }};">Rp {{ number_format($trx->amount, 0, ',', '.') }}</b>
                        </td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ $trx->recorded_by_name ?? '-' }}</td>
                        <td class="text-center align-middle">
                            <a href="{{ route('admin.transactions.show', $trx->id) }}" class="btn btn-action-view mb-0" title="Detail Transaksi">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
                </div>
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
