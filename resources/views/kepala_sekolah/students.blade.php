@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 text-dark font-weight-bold"><i class="fas fa-user-graduate text-success me-2"></i> Pemantauan Siswa</h3>
                <p class="text-xs text-muted mb-0">Daftar seluruh siswa aktif dalam sistem.</p>
            </div>
            
            <!-- SEARCH BOX -->
            <form action="{{ route('kepala-sekolah.students') }}" method="GET" class="mt-2 mt-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 text-xs" style="width: 280px;" value="{{ $search }}" placeholder="Cari nama, NISN, NIK, No. KK...">
                    @if($search)
                        <a href="{{ route('kepala-sekolah.students') }}" class="btn btn-light border-0 my-0 text-danger"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body px-0 pb-4">
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-xs font-weight-bold">NIK</th>
                            <th class="text-xs font-weight-bold">No. KK</th>
                            <th class="text-xs font-weight-bold">NISN</th>
                            <th class="text-xs font-weight-bold">Nama Lengkap</th>
                            <th class="text-xs font-weight-bold">Kelas & Jurusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="text-sm text-dark font-weight-bold">{{ $student->id_number }}</td>
                                <td class="text-sm text-muted">{{ $student->family_card_number }}</td>
                                <td class="text-sm font-weight-bold text-success">{{ $student->nisn }}</td>
                                <td class="text-sm font-weight-bold text-dark">{{ $student->name }}</td>
                                <td class="text-sm text-dark">
                                    <span class="badge bg-soft-success text-success px-3 py-2 font-weight-bold">
                                        Kelas {{ $student->grade_level }} — {{ $student->major_name }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-2x mb-3 text-light"></i><br>
                                    Tidak ditemukan data siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-4 mt-3 d-flex justify-content-between align-items-center">
                <div class="text-xs text-muted">
                    Menampilkan {{ $students->firstItem() ?? 0 }} sampai {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa
                </div>
                <div>
                    {{ $students->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
