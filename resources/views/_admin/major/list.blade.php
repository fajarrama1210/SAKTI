@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Master Jurusan</h3>
            <a href="{{ route('admin.majors.create') }}" class="btn btn-sm btn-primary">Tambah Data</a>
        </div>

        {{-- Alert Notifikasi --}}
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="text-center">No</th>
                        <th scope="col" class="text-center">Nama Jurusan</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($majors as $index => $major)
                    <tr>
                        <td scope="col" class="text-center">{{ $majors->firstItem() + $index }}</td>
                        <td scope="col" class="text-center">{{ $major->name }}</td>
                        <td scope="col" class="text-center">
                            <div class="dropdown">
                                <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $major->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $major->id }}">
                                    <li>
                                        <a href="{{ route('admin.majors.edit', $major->id) }}" class="dropdown-item border-radius-md">
                                            <i class="fas fa-edit text-info me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.majors.destroy', $major->id) }}" method="POST" class="delete-form m-0">
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
                        <td colspan="3" class="text-center">Belum ada data jurusan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination yang Ringan --}}
        <div class="card-footer py-4">
            {{ $majors->links() }}
        </div>
    </div>
</div>
@endsection
