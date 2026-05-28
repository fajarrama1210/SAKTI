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
                    <i class="fas fa-calendar-check me-2"></i> Tahun Ajaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola periode tahun ajaran aktif dan non-aktif.
                </p>
            </div>
            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-plus me-1"></i> Tambah Tahun Ajaran
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Tahun Ajaran
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">Nama Tahun Ajaran</th>
                                    <th class="text-center">Tanggal Mulai</th>
                                    <th class="text-center">Tanggal Akhir</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                <tbody>
                    @forelse($academicYears as $index => $ay)
                    <tr>
                        <td class="text-center align-middle">{{ $academicYears->firstItem() + $index }}</td>
                        <td class="text-center align-middle">
                            <span style="font-weight: 700; color: var(--dark-text);">
                                <i class="fas fa-calendar-alt me-1 text-muted"></i> {{ $ay->name }}
                            </span>
                        </td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ \Carbon\Carbon::parse($ay->start_date)->format('d/m/Y') }}</td>
                        <td class="text-center align-middle" style="color: var(--muted-text);">{{ \Carbon\Carbon::parse($ay->end_date)->format('d/m/Y') }}</td>
                        <td class="text-center align-middle">
                            @if($ay->is_active)
                            <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 600;"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                            @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600;"><i class="fas fa-times-circle me-1"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $ay->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $ay->id }}">
                                    <li>
                                        <a href="{{ route('admin.academic-years.edit', $ay->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.academic-years.destroy', $ay->id) }}" method="POST" class="delete-form m-0">
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
                    {{ $academicYears->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection