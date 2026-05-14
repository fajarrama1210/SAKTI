@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Tahun Ajaran</h3>
            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-sm btn-primary">Tambah Tahun Ajaran</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">No</th>
                        <th scope="col" class="text-center">Nama Tahun Ajaran</th>
                        <th scope="col" class="text-center">Tanggal Mulai</th>
                        <th scope="col" class="text-center">Tanggal Akhir</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $index => $ay)
                    <tr>
                        <td class="text-center">{{ $academicYears->firstItem() + $index }}</td>
                        <td class="text-center"><b>{{ $ay->name }}</b></td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($ay->start_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($ay->end_date)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            @if($ay->is_active)
                            <b class="text-success" style="font-weight: 800;">Aktif</b>
                            @else
                            <b class="text-danger" style="font-weight: 800;">Nonaktif</b>
                            @endif
                        </td>
                        <td class="text-center">
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
        <div class="card-footer py-4">
            {{ $academicYears->links() }}
        </div>
    </div>
</div>
@endsection