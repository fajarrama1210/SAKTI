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
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $pt->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $pt->id }}">
                                    <li>
                                        <a href="{{ route('admin.payment-types.edit', $pt->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.payment-types.destroy', $pt->id) }}" method="POST" class="delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item border-radius-md text-danger">
                                                <i class="fas fa-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
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