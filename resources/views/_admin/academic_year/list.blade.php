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
                            <a href="{{ route('admin.academic-years.edit', $ay->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.academic-years.destroy', $ay->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data tahun ajaran.</td>
                    </tr>
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