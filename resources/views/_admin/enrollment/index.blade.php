@extends('_admin.layouts.app')

@section('content')
    <div class="container-fluid mt--6">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Penempatan Siswa (Per Tahun Ajaran)</h3>
                <a href="{{ route('admin.enrollments.graduation') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-graduation-cap"></i> Kelulusan Massal
                </a>
            </div>

            <div class="card-body border-bottom">
                <form action="{{ route('admin.enrollments.index') }}" method="GET" class="row">

                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Pilih Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control" onchange="this.form.submit()">

                                @if($academicYears->isEmpty())
                                    <option value="">-- Belum Ada Tahun Ajaran --</option>
                                @else
                                    @foreach ($academicYears as $ay)
                                        <option value="{{ $ay->id }}" {{ $selectedAY == $ay->id ? 'selected' : '' }}>
                                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Filter Kelas</label>
                            <select name="classroom_id" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Kelas</option>
                                @foreach ($classrooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ ($filters['classroom_id'] ?? '') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>Filter Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ ($filters['status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="lulus" {{ ($filters['status'] ?? '') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="do" {{ ($filters['status'] ?? '') == 'do' ? 'selected' : '' }}>DO / Keluar</option>
                                <option value="naik_kelas" {{ ($filters['status'] ?? '') == 'naik_kelas' ? 'selected' : '' }}>Naik Kelas</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Cari Nama/NISN</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari Siswa..."
                                    value="{{ $filters['search'] ?? '' }}">
                                <button class="btn btn-primary mb-0" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $index => $row)
                            <tr>
                                <td class="text-center">{{ $enrollments->firstItem() + $index }}</td>
                                <td>{{ $row->nisn }}</td>
                                <td><b>{{ $row->student_name }}</b></td>
                                <td class="text-center">{{ $row->classroom_name }}</td>
                                <td class="text-center">
                                    @if ($row->status == 'aktif')
                                        <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                    @elseif($row->status == 'lulus')
                                        <span class="badge badge-sm bg-gradient-primary">Lulus</span>
                                    @elseif($row->status == 'do')
                                        <span class="badge badge-sm bg-gradient-danger">DO / Keluar</span>
                                    @elseif($row->status == 'naik_kelas')
                                        <span class="badge badge-sm bg-gradient-info">Naik Kelas</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">{{ $row->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($row->status == 'aktif')
                                        {{-- Tombol Pindah Kelas --}}
                                        <button type="button" class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalPindah{{ $row->id }}"
                                            title="Pindah Kelas">
                                            <i class="fas fa-exchange-alt"></i> Pindah
                                        </button>

                                        {{-- Tombol Set DO --}}
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDO{{ $row->id }}">
                                            <i class="fas fa-user-times"></i> Set DO
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.enrollments.destroy', $row->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Hapus Penempatan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                                
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    @if($academicYears->isEmpty())
                                        <div class="mb-3">
                                            <i class="fas fa-calendar-times fa-3x text-warning"></i>
                                        </div>
                                        <h4 class="text-warning">Tahun Ajaran Belum Tersedia</h4>
                                        <p>Silakan buat data Tahun Ajaran terlebih dahulu untuk mengelola penempatan siswa.</p>
                                        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Kelola Tahun Ajaran
                                        </a>
                                    @elseif(!$selectedAY)
                                        <div class="mb-3">
                                            <i class="fas fa-search fa-3x text-muted"></i>
                                        </div>
                                        <h4>Pilih Tahun Ajaran</h4>
                                        <p>Silakan pilih tahun ajaran dari menu dropdown di atas.</p>
                                    @else
                                        <div class="mb-3">
                                            <i class="fas fa-users-slash fa-3x text-muted"></i>
                                        </div>
                                        <p>Tidak ada data penempatan siswa untuk kriteria ini.</p>
                                    @endif
                                </td>

                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-4">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>
@endsection
