@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <!-- HEADER -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-money-check-alt me-2"></i> Tarif Pembayaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Atur besaran tarif untuk setiap jenis pembayaran.
                </p>
            </div>
            <a href="{{ route('admin.payment-rates.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-plus me-1"></i> Tambah Tarif
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Tarif Pembayaran
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">Tahun Ajaran</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Jurusan</th>
                                    <th class="text-center">Tarif</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                <tbody>
                    @forelse($paymentRates as $index => $pr)
                    <tr>
                    <tr>
                        <td class="text-center align-middle">{{ $paymentRates->firstItem() + $index }}</td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ $pr->academic_year_name }}</td>
                        <td class="text-center align-middle">
                            <span style="font-weight: 700; color: var(--dark-text);">{{ $pr->payment_type_name }}</span>
                        </td>
                        <td class="text-center align-middle">Kelas {{ $pr->grade_level }}</td>
                        <td class="text-center align-middle">{{ $pr->major_name ?? 'Semua Jurusan' }}</td>
                        <td class="text-center align-middle" style="color: var(--dark-text);"><b>Rp {{ number_format($pr->amount, 0, ',', '.') }}</b></td>
                        <td class="text-center align-middle">
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
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $paymentRates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection