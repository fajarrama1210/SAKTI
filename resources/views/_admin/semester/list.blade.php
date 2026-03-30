@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Semester</h3>
            <a href="{{ route('admin.semesters.create') }}" class="btn btn-sm btn-primary">Tambah Semester</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="text-center">No</th>
                        <th scope="col" class="text-center">Tahun Ajaran</th>
                        <th scope="col" class="text-center">Nama Semester</th>
                        <th scope="col" class="text-center">Bulan Mulai</th>
                        <th scope="col" class="text-center">Bulan Akhir</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @forelse($semesters as $index => $s)
                    <tr>
                        <td scope="col" class="text-center">{{ $semesters->firstItem() + $index }}</td>
                        <td scope="col" class="text-center">{{ $s->academic_year_name }}</td>
                        <td scope="col" class="text-center"><b>{{ $s->name }}</b></td>
                        <td scope="col" class="text-center">{{ $bulan[$s->start_month] ?? $s->start_month }}</td>
                        <td scope="col" class="text-center">{{ $bulan[$s->end_month] ?? $s->end_month }}</td>
                        <td scope="col" class="text-center">
                            <a href="{{ route('admin.semesters.edit', $s->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.semesters.destroy', $s->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data semester.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $semesters->links() }}
        </div>
    </div>
</div>
@endsection