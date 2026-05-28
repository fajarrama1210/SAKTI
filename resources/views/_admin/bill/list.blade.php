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
                    <i class="fas fa-file-invoice-dollar me-2"></i> Tagihan
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola data tagihan siswa bulanan maupun sekali bayar.
                </p>
            </div>
            <a href="{{ route('admin.bills.generate-form') }}" class="btn btn-sm btn-glass btn-glass-success">
                <i class="fas fa-magic me-1"></i> Generate Tagihan per Semester
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Tagihan
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">No. KK</th>
                                    <th class="text-center">Tahun Ajaran</th>
                                    <th class="text-center">Bulan</th>
                                    <th class="text-center">Total Tagihan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Jatuh Tempo</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                    <tr>
                        <td class="text-center align-middle">{{ $bills->firstItem() + $index }}</td>
                        <td class="text-center align-middle"><b style="color: var(--dark-text);">{{ $bill->family_card_number }}</b></td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ $bill->academic_year_name }}</td>
                        <td class="text-center align-middle">
                            <span style="font-weight: 700; color: var(--dark-text);">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i> {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <b style="color: var(--primary-green);">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</b>
                        </td>
                        <td class="text-center align-middle">
                            @if($bill->status === 'paid')
                            <span class="badge badge-sm bg-gradient-success">Lunas</span>
                            @elseif($bill->status === 'partial')
                            <span class="badge badge-sm bg-gradient-warning">Sebagian</span>
                            @elseif($bill->status === 'cancelled')
                            <span class="badge badge-sm bg-gradient-secondary">Dibatalkan</span>
                            @else
                            <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                        <td class="text-center align-middle">
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $bill->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $bill->id }}">
                                    <li>
                                        <a href="{{ route('admin.bills.show', $bill->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-eye text-info me-2"></i> Detail
                                        </a>
                                    </li>
                                    @if($bill->status !== 'paid')
                                    <li>
                                        <a href="{{ route('admin.bills.pay-form', $bill->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-money-bill-wave text-success me-2"></i> Bayar
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.bills.destroy', $bill->id) }}" method="POST" class="delete-form m-0">
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
                    {{ $bills->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
