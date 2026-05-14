@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Tarif Pembayaran</h3>
            <a href="{{ route('admin.payment-rates.create') }}" class="btn btn-sm btn-primary">Tambah Tarif</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Jenis</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Tarif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentRates as $index => $pr)
                    <tr>
                        <td>{{ $paymentRates->firstItem() + $index }}</td>
                        <td>{{ $pr->academic_year_name }}</td>
                        <td><b>{{ $pr->payment_type_name }}</b></td>
                        <td>Kelas {{ $pr->grade_level }}</td>
                        <td>{{ $pr->major_name ?? 'Semua Jurusan' }}</td>
                        <td><b>Rp {{ number_format($pr->amount, 0, ',', '.') }}</b></td>
                        <td>
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $pr->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $pr->id }}">
                                    <li>
                                        <a href="{{ route('admin.payment-rates.edit', $pr->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.payment-rates.destroy', $pr->id) }}" method="POST" class="delete-form m-0">
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
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $paymentRates->links() }}
        </div>
    </div>
</div>
@endsection