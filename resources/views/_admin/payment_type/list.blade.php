@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Jenis Pembayaran</h3>
            <a href="{{ route('admin.payment-types.create') }}" class="btn btn-sm btn-primary">Tambah Jenis</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTypes as $index => $pt)
                    <tr>
                        <td>{{ $paymentTypes->firstItem() + $index }}</td>
                        <td><b>{{ $pt->name }}</b></td>
                        <td>
                            @if($pt->is_monthly)
                            <span class="badge badge-info">Bulanan</span>
                            @else
                            <span class="badge badge-warning">Sekali Bayar</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.payment-types.edit', $pt->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.payment-types.destroy', $pt->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data jenis pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $paymentTypes->links() }}
        </div>
    </div>
</div>
@endsection