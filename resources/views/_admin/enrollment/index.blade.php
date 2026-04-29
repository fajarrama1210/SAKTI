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
                                @foreach ($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $selectedAY == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
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

                            {{-- ========== MODAL PINDAH KELAS ========== --}}
                            @if ($row->status == 'aktif')
                            <div class="modal fade" id="modalPindah{{ $row->id }}" tabindex="-1" aria-labelledby="modalPindahLabel{{ $row->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.enrollments.change-classroom', $row->id) }}" method="POST" class="modal-content">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalPindahLabel{{ $row->id }}">
                                                Pindah Kelas: {{ $row->student_name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Kelas Saat Ini</label>
                                                <input type="text" class="form-control" value="{{ $row->classroom_name }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Kelas Baru <span class="text-danger">*</span></label>
                                                <select name="classroom_id" class="form-control" required>
                                                    <option value="">-- Pilih Kelas Baru --</option>
                                                    @foreach ($classrooms as $room)
                                                        @if ($room->id != $row->classroom_id)
                                                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-exchange-alt"></i> Pindah Kelas
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- ========== MODAL DO ========== --}}
                            <div class="modal fade" id="modalDO{{ $row->id }}" tabindex="-1" aria-labelledby="modalDOLabel{{ $row->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.enrollments.dropout', $row->id) }}" method="POST" class="modal-content">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalDOLabel{{ $row->id }}">
                                                Konfirmasi Drop Out: {{ $row->student_name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="alert alert-warning py-2">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>Perhatian:</strong> Siswa akan dikeluarkan dan tagihan setelah bulan DO akan dibatalkan otomatis.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tanggal Keluar / DO <span class="text-danger">*</span></label>
                                                <input type="date" name="exit_date" class="form-control"
                                                    value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Alasan <span class="text-danger">*</span></label>
                                                <textarea name="exit_reason" class="form-control" rows="3" required
                                                    placeholder="Contoh: Pindah sekolah, Mengundurkan diri"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-user-times"></i> Proses DO
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data penempatan siswa untuk kriteria ini.</td>
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
