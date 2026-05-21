@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Daftar Tarif Pembayaran</h3>
            <a href="{{ route('admin.payment-rates.create') }}" class="btn btn-sm btn-sakti-primary">
                <i class="fas fa-plus mr-2"></i> Tambah Tarif
            </a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Tahun Ajaran</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Jenis</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Kelas</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Jurusan</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Tarif</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentRates as $index => $pr)
                    <tr>
                        <td class="text-center">{{ $paymentRates->firstItem() + $index }}</td>
                        <td class="text-center">{{ $pr->academic_year_name }}</td>
                        <td class="text-center"><b>{{ $pr->payment_type_name }}</b></td>
                        <td class="text-center">Kelas {{ $pr->grade_level }}</td>
                        <td class="text-center">{{ $pr->major_name ?? 'Semua Jurusan' }}</td>
                        <td class="text-center"><b>Rp {{ number_format($pr->amount, 0, ',', '.') }}</b></td>
                        <td class="text-center">
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