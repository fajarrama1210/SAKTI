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
                        <th class="text-center">No</th>
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
                            @if(!$trx->payment_id)
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $trx->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $trx->id }}">
                                    <li>
                                        <a href="{{ route('admin.transactions.edit', $trx->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.transactions.destroy', $trx->id) }}" method="POST" class="delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item border-radius-md text-danger">
                                                <i class="fas fa-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @else
                            <span class="badge badge-sm bg-gradient-info">Otomatis SPP</span>
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