@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Daftar Transaksi</h3>
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-sm btn-sakti-primary">
                <i class="fas fa-plus mr-2"></i> Catat Transaksi
            </a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Tanggal</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Tipe</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Kategori</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Keterangan</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Jumlah</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Dicatat Oleh</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr>
                        <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($trx->type === 'income')
                            <span class="badge badge-sm bg-gradient-success">Masuk</span>
                            @else
                            <span class="badge badge-sm bg-gradient-danger">Keluar</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $trx->category ?? '-' }}</td>
                        <td class="text-center">{{ \Illuminate\Support\Str::limit($trx->description, 50) }}</td>
                        <td class="text-center"><b>Rp {{ number_format($trx->amount, 0, ',', '.') }}</b></td>
                        <td class="text-center">{{ $trx->recorded_by_name ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.transactions.show', $trx->id) }}" class="btn btn-sm btn-outline-info mb-0" style="padding: 4px 10px; font-size: 0.75rem; border-radius: 4px; border-color: #11cdef; color: #11cdef;">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection