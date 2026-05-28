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
                    <i class="fas fa-school me-2"></i> Master Kelas
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola data kelas dan tingkatan.
                </p>
            </div>
            <a href="{{ route('admin.classrooms.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-plus me-1"></i> Tambah Kelas
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Kelas
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">Nama Kelas</th>
                                    <th class="text-center">Tingkat</th>
                                    <th class="text-center">Jurusan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                    <tbody>
                        @forelse($classrooms as $index => $classroom)
                            <tr>
                                <td class="text-center align-middle">{{ $classrooms->firstItem() + $index }}</td>
                                <td class="text-center align-middle">
                                    <span style="font-weight: 700; color: var(--dark-text);">
                                        <i class="fas fa-school me-1" style="color: var(--primary-green); opacity: .7;"></i> {{ $classroom->name }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">Kelas {{ $classroom->grade_level }}</td>
                                {{-- major_name didapat dari hasil JOIN di UseCase --}}
                                <td class="text-center align-middle">{{ $classroom->major_name }}</td>
                                <td class="text-center align-middle">
                                    <div class="dropdown">
                                        <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $classroom->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $classroom->id }}">
                                            <li>
                                                <a href="{{ route('admin.classrooms.edit', $classroom->id) }}" class="dropdown-item border-radius-md">
                                                    <i class="fas fa-edit text-info me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST" class="delete-form m-0">
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
                </div>
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $classrooms->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
