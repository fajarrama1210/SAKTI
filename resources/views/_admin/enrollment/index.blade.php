@extends('_admin.layouts.app')

@section('content')
    <div class="container-fluid mt--6">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="mb-0">Penempatan Siswa (Per Tahun Ajaran)</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.enrollments.promotion') }}" class="btn btn-sm btn-info me-2">
                        <i class="fas fa-angle-double-up text-xs me-1"></i> Kenaikan Kelas Massal
                    </a>
                    <a href="{{ route('admin.enrollments.graduation') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-graduation-cap text-xs me-1"></i> Kelulusan Massal
                    </a>
                </div>
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
                                    <div class="dropdown">
                                        <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $row->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $row->id }}">
                                            @if ($row->status == 'aktif')
                                            <li>
                                                <a href="#" class="dropdown-item border-radius-md" data-bs-toggle="modal" data-bs-target="#modalPindah{{ $row->id }}">
                                                    <i class="fas fa-exchange-alt text-info me-2"></i> Pindah Kelas
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="dropdown-item border-radius-md text-danger" data-bs-toggle="modal" data-bs-target="#modalDO{{ $row->id }}">
                                                    <i class="fas fa-user-times me-2"></i> Set DO
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            @endif
                                            <li>
                                                <form action="{{ route('admin.enrollments.destroy', $row->id) }}" method="POST" class="delete-form m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item border-radius-md text-danger">
                                                        <i class="fas fa-trash me-2"></i> Hapus Penempatan
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    @if ($row->status == 'aktif')
                                    <!-- Modal Pindah Kelas -->
                                    <div class="modal fade" id="modalPindah{{ $row->id }}" tabindex="-1" aria-labelledby="modalPindahLabel{{ $row->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalPindahLabel{{ $row->id }}">Pindah Kelas</h5>
                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.enrollments.change-classroom', $row->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-start" style="white-space: normal;">
                                                        <p class="text-sm mb-3">Pindahkan siswa <strong>{{ $row->student_name }}</strong> ke kelas lain pada tahun ajaran yang sama.</p>
                                                        <div class="form-group text-start mb-0">
                                                            <label class="form-control-label">Pilih Kelas Baru</label>
                                                            <select name="classroom_id" class="form-control" required>
                                                                <option value="">-- Pilih Kelas --</option>
                                                                @foreach ($classrooms as $room)
                                                                    <option value="{{ $room->id }}" {{ $row->classroom_id == $room->id ? 'selected' : '' }}>
                                                                        {{ $room->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary mb-0" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success mb-0">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Set DO -->
                                    <div class="modal fade" id="modalDO{{ $row->id }}" tabindex="-1" aria-labelledby="modalDOLabel{{ $row->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalDOLabel{{ $row->id }}">Set Keluar / DO</h5>
                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.enrollments.dropout', $row->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body text-start" style="white-space: normal;">
                                                        <p class="text-sm mb-3">Atur status siswa <strong>{{ $row->student_name }}</strong> menjadi Keluar / DO. Tagihan yang belum dibayar di bulan-bulan berikutnya akan dibatalkan otomatis.</p>
                                                        <div class="form-group text-start mb-3">
                                                            <label class="form-control-label">Tanggal Keluar</label>
                                                            <input type="date" name="exit_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="form-group text-start mb-0">
                                                            <label class="form-control-label">Alasan Keluar</label>
                                                            <textarea name="exit_reason" class="form-control" rows="3" placeholder="Contoh: Pindah sekolah, Lulus dini, dll." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary mb-0" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger mb-0">Set DO / Keluar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                                
                        @empty
                            <x-empty-state />
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
