@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Transaksi</h3>
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-sm btn-primary">Catat Transaksi</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $trx)
                    <tr>
                        <td>{{ $transactions->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                        <td>
                            @if($trx->type === 'income')
                            <span class="badge badge-success">Masuk</span>
                            @else
                            <span class="badge badge-danger">Keluar</span>
                            @endif
                        </td>
                        <td>{{ $trx->category ?? '-' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($trx->description, 50) }}</td>
                        <td><b>Rp {{ number_format($trx->amount, 0, ',', '.') }}</b></td>
                        <td>{{ $trx->recorded_by_name ?? '-' }}</td>
                        <td>
                            @if(!$trx->payment_id)
                            <form action="{{ route('admin.transactions.destroy', $trx->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            @else
                            <span class="badge badge-info">Otomatis SPP</span>
                            @endif
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