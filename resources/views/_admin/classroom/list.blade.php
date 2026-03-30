@extends('_admin.layouts.app')
@section('content')
    <div class="container-fluid mt--6">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Master Kelas</h3>
                <a href="{{ route('admin.classrooms.create') }}" class="btn btn-sm btn-primary">Tambah Kelas</a>
            </div>


            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Tingkat Kelas</th>
                            <th scope="col" class="text-center">Jurusan</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $index => $classroom)
                            <tr>
                                <td scope="col" class="text-center">{{ $classrooms->firstItem() + $index }}</td>
                                <td scope="col" class="text-center">Kelas {{ $classroom->grade_level }}</td>
                                {{-- major_name didapat dari hasil JOIN di UseCase --}}
                                <td scope="col" class="text-center">{{ $classroom->major_name }}</td>
                                <td scope="col" class="text-center">
                                    <a href="{{ route('admin.classrooms.edit', $classroom->id) }}"
                                        class="btn btn-sm btn-info">Edit</a>

                                    <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data kelas.</td>
                            </tr>
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
