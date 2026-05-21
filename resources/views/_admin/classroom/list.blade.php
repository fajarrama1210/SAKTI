@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
    <div class="container-fluid mt--6">
        <div class="card sakti-card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
                <h3 class="mb-0 text-sakti-green font-weight-bold">Master Kelas</h3>
                <a href="{{ route('admin.classrooms.create') }}" class="btn btn-sm btn-sakti-primary">
                    <i class="fas fa-plus mr-2"></i> Tambah Kelas
                </a>
            </div>


            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center" style="width: 50px;">No</th>
                            <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Nama Kelas</th>
                            <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Tingkat</th>
                            <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Jurusan</th>
                            <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $index => $classroom)
                            <tr>
                                <td scope="col" class="text-center">{{ $classrooms->firstItem() + $index }}</td>
                                <td scope="col" class="text-center"><b>{{ $classroom->name }}</b></td>
                                <td scope="col" class="text-center">Kelas {{ $classroom->grade_level }}</td>
                                {{-- major_name didapat dari hasil JOIN di UseCase --}}
                                <td scope="col" class="text-center">{{ $classroom->major_name }}</td>
                                <td scope="col" class="text-center">
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
            <div class="card-footer py-4">
                {{ $classrooms->links() }}
            </div>
        </div>
    </div>
@endsection
