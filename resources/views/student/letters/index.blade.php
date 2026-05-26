@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">Pengajuan Surat Izin / Sakit</h4>
                        <p class="text-sm text-muted mb-0">Kelola dan ajukan surat izin atau sakit Anda di sini.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLetterModal">
                            <i class="fas fa-plus me-2"></i> Ajukan Surat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Riwayat Pengajuan</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal Pengajuan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lampiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($letters as $letter)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $letter->type == 'sick' ? 'Sakit' : 'Izin' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $letter->description }}">{{ $letter->description }}</p>
                                    </td>
                                    <td>
                                        @if($letter->status == 'pending')
                                            <span class="badge badge-sm bg-gradient-warning">Pending</span>
                                        @elseif($letter->status == 'approved')
                                            <span class="badge badge-sm bg-gradient-success">Disetujui</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank" class="btn btn-link text-primary text-sm mb-0 px-0">
                                            <i class="fas fa-paperclip me-1"></i> Lihat File
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-xs text-secondary mb-0">Belum ada data pengajuan surat.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajukan Surat -->
<div class="modal fade" id="addLetterModal" tabindex="-1" aria-labelledby="addLetterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('student.letters.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="addLetterModalLabel">Ajukan Surat Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label for="submission_date" class="form-label">Tanggal Mulai (Izin/Sakit)</label>
                  <input type="date" class="form-control" id="submission_date" name="submission_date" required>
              </div>
              <div class="mb-3">
                  <label for="type" class="form-label">Kategori</label>
                  <select class="form-select" id="type" name="type" required>
                      <option value="sick">Sakit</option>
                      <option value="permission">Izin</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label for="description" class="form-label">Keterangan / Deskripsi</label>
                  <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                  <label for="file" class="form-label">Upload Surat (PDF/JPG/PNG)</label>
                  <input class="form-control" type="file" id="file" name="file" accept=".pdf,.png,.jpg,.jpeg" required>
                  <small class="text-muted">Maksimal ukuran 2MB.</small>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection
