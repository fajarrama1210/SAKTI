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
                    <i class="fas fa-users me-2"></i> Daftar Siswa
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola data siswa, import data, dan status siswa.
                </p>
            </div>
            <div class="d-flex gap-2">
                <!-- Original btn-warning (yellow) -->
                <a href="{{ route('admin.students.import.view') }}" class="btn btn-sm btn-glass btn-glass-warning">
                    <i class="fas fa-file-import me-1"></i> Import Excel
                </a>
                <!-- Original btn-success (green) - we will use white/light green for contrast on green bg -->
                <a href="{{ route('admin.students.template') }}" class="btn btn-sm btn-glass btn-glass-white">
                    <i class="fas fa-download me-1"></i> Template
                </a>
                <!-- Original btn-sakti-primary (primary/green) -->
                <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                    <i class="fas fa-user-plus me-1"></i> Tambah Manual
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Data Seluruh Siswa
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">NISN</th>
                                    <th class="text-center">Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">NIK</th>
                                    <th class="text-center">No. KK</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td class="text-center align-middle">{{ $students->firstItem() + $index }}</td>
                        <td class="text-center align-middle"><b style="color: var(--dark-text);">{{ $student->nisn }}</b></td>
                        <td class="text-start align-middle">
                            <div class="d-flex align-items-center px-2">
                                <div class="trx-detail-icon icon-user me-3" style="width: 32px; height: 32px; font-size: .75rem;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <h6 class="mb-0" style="font-size: .88rem; font-weight: 700; color: var(--dark-text);">
                                    {{ $student->name }}
                                </h6>
                            </div>
                        </td>
                        <td class="text-center align-middle">{{ $student->classroom_name }}</td>
                        <td class="text-center align-middle"><code style="color: #64748b; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">{{ $student->id_number }}</code></td>
                        <td class="text-center align-middle" style="color: var(--muted-text); font-weight: 500;">{{ $student->family_card_number }}</td>
                        <td class="text-center align-middle">
                            @if ($student->status == 'aktif')
                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                            @elseif($student->status == 'lulus')
                                <span class="badge badge-sm bg-gradient-primary">Lulus</span>
                            @elseif($student->status == 'keluar' || $student->status == 'do')
                                <span class="badge badge-sm bg-gradient-danger">Keluar</span>
                            @else
                                <span class="badge badge-sm bg-gradient-secondary">{{ $student->status }}</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $student->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $student->id }}">
                                    <li>
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="delete-form m-0">
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
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection