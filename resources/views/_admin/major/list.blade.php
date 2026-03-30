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
                            <a href="{{ route('admin.majors.edit', $major->id) }}" class="btn btn-sm btn-info">Edit</a>

                            {{-- Keamanan: Hapus HARUS pakai Form & POST/DELETE + CSRF --}}
                            <form action="{{ route('admin.majors.destroy', $major->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
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
