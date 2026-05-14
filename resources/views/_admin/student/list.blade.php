@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Siswa</h3>
            <div class="text-right">
                <a href="{{ route('admin.students.import.view') }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-file-import"></i> Import Excel
                </a>
                <a href="{{ route('admin.students.template') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> Template
                </a>
                <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus"></i> Tambah Manual
                </a>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">No</th>
                        <th scope="col" class="text-center">NISN</th>
                        <th scope="col" class="text-center">Nama Lengkap</th>
                        <th scope="col" class="text-center">Kelas</th>
                        <th scope="col" class="text-center">NIK</th>
                        <th scope="col" class="text-center">No. KK</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                    <tr>
                        <td scope="col" class="text-center">{{ $students->firstItem() + $index }}</td>
                        <td scope="col" class="text-center"><b>{{ $student->nisn }}</b></td>
                        <td scope="col" class="text-center"><b>{{ $student->name }}</b></td>
                        <td scope="col" class="text-center">{{ $student->classroom_name }}</td>
                        <td scope="col" class="text-center"><code>{{ $student->id_number }}</code></td>
                        <td scope="col" class="text-center">{{ $student->family_card_number }}</td>
                        <td scope="col" class="text-center">
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
                        <td scope="col" class="text-center">
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
        <div class="card-footer py-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection