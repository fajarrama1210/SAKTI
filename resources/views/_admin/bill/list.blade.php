@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="mb-0">Daftar Tagihan</h3>
            <a href="{{ route('admin.bills.generate-form') }}" class="btn btn-sm btn-success">
                <i class="fas fa-magic"></i> Generate Tagihan per Semester
            </a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>No. KK</th>
                        <th>Tahun Ajaran</th>
                        <th>Bulan</th>
                        <th>Total Tagihan</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $index => $bill)
                    <tr>
                        <td>{{ $bills->firstItem() + $index }}</td>
                        <td><b>{{ $bill->family_card_number }}</b></td>
                        <td>{{ $bill->academic_year_name }}</td>
                        <td>{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}</td>
                        <td><b>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</b></td>
                        <td>
                            @if($bill->status === 'paid')
                            <span class="badge badge-sm bg-gradient-success">Lunas</span>
                            @elseif($bill->status === 'partial')
                            <span class="badge badge-sm bg-gradient-warning">Sebagian</span>
                            @else
                            <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                        <td>
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
        <div class="card-footer py-4">
            {{ $bills->links() }}
        </div>
    </div>
</div>
@endsection