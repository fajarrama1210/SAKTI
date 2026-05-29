@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-envelope-open-text me-2"></i> Pengajuan Surat Izin / Sakit
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola dan ajukan surat izin atau sakit Anda di sini.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-glass btn-glass-white" data-bs-toggle="modal" data-bs-target="#addLetterModal">
                <i class="fas fa-plus me-1"></i> Ajukan Surat
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card dashboard-card">
        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
            <h3 class="section-title mb-0">
                <i class="fas fa-history me-2" style="color: var(--primary-green); opacity: .7;"></i>
                Riwayat Pengajuan
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table letters-table align-items-center mb-0">
                <thead>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-center">Kategori</th>
                        <th>Keterangan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                    <tr>
                        <td class="align-middle">
                            <span style="font-weight: 600; color: var(--dark-text); font-size: .88rem;">
                                {{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @if($letter->type == 'sick')
                                <span class="badge px-3 py-2" style="background: #fef2f2; color: #ef4444; font-weight: 600;">Sakit</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #ecfeff; color: #0891b2; font-weight: 600;">Izin</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <p class="mb-0" style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: .85rem; color: var(--muted-text);" title="{{ $letter->description }}">
                                {{ $letter->description }}
                            </p>
                        </td>
                        <td class="text-center align-middle">
                            @if($letter->status == 'pending')
                                <span class="badge px-3 py-2" style="background: #fffbeb; color: #d97706; font-weight: 600;"><i class="fas fa-clock me-1"></i>Pending</span>
                            @elseif($letter->status == 'approved')
                                <span class="badge px-3 py-2" style="background: #ecfdf5; color: #059669; font-weight: 600;"><i class="fas fa-check-circle me-1"></i>Disetujui</span>
                            @else
                                <span class="badge px-3 py-2" style="background: #fef2f2; color: #ef4444; font-weight: 600;"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank" class="btn btn-action-view mb-0" title="Lihat File">
                                <i class="fas fa-paperclip me-1"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div style="color: var(--muted-text);">
                                <i class="fas fa-envelope fa-2x mb-3 d-block" style="opacity: .3;"></i>
                                Belum ada data pengajuan surat.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                  <input type="date" class="form-control @error('submission_date') is-invalid @enderror" id="submission_date" name="submission_date" value="{{ old('submission_date') }}" required>
                  @error('submission_date')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label for="type" class="form-label">Kategori</label>
                  <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                      <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sakit</option>
                      <option value="permission" {{ old('type') == 'permission' ? 'selected' : '' }}>Izin</option>
                  </select>
                  @error('type')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label for="description" class="form-label d-flex justify-content-between align-items-center">
                      <span>Keterangan / Deskripsi <span class="text-danger">*</span></span>
                      <span id="desc-counter" class="badge" style="font-size:.75rem; background:#ecfdf5; color:#059669; font-weight:700; transition:all .2s ease;">300 / 300</span>
                  </label>
                  <textarea
                      class="form-control @error('description') is-invalid @enderror"
                      id="description"
                      name="description"
                      rows="3"
                      maxlength="300"
                      placeholder="Tuliskan alasan izin atau sakit secara singkat..."
                      required
                  >{{ old('description') }}</textarea>
                  <div id="desc-limit-warning" class="text-danger mt-1" style="font-size:.78rem; display:none;">
                      <i class="fas fa-exclamation-circle me-1"></i> Keterangan tidak boleh lebih dari 300 karakter.
                  </div>
                  @error('description')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label for="file" class="form-label">Upload Surat (PDF/JPG/PNG)</label>
                  <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".pdf,.png,.jpg,.jpeg" required>
                  <small class="text-muted">Maksimal ukuran 2MB.</small>
                  @error('file')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-sakti-primary">Kirim Pengajuan</button>
          </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ── Re-open modal on validation errors ──
        @if($errors->any())
            var addLetterModal = new bootstrap.Modal(document.getElementById('addLetterModal'));
            addLetterModal.show();
        @endif

        // ── Character counter for description ──
        const MAX_CHARS = 300;
        const textarea  = document.getElementById('description');
        const counter   = document.getElementById('desc-counter');
        const warning   = document.getElementById('desc-limit-warning');

        function updateCounter() {
            const used      = textarea.value.length;
            const remaining = MAX_CHARS - used;

            counter.textContent = remaining + ' / ' + MAX_CHARS;

            if (remaining <= 0) {
                // Over limit – red badge, show warning
                counter.style.background = '#fef2f2';
                counter.style.color      = '#ef4444';
                warning.style.display    = 'block';
                textarea.classList.add('is-invalid');
            } else if (remaining <= 50) {
                // Approaching limit – orange badge
                counter.style.background = '#fffbeb';
                counter.style.color      = '#d97706';
                warning.style.display    = 'none';
                textarea.classList.remove('is-invalid');
            } else {
                // Safe – green badge
                counter.style.background = '#ecfdf5';
                counter.style.color      = '#059669';
                warning.style.display    = 'none';
                textarea.classList.remove('is-invalid');
            }
        }

        if (textarea) {
            textarea.addEventListener('input', updateCounter);
            // Init on page load (e.g. after validation error refill)
            updateCounter();
        }

        // ── Block form submit if description exceeds limit ──
        const letterForm = document.querySelector('#addLetterModal form');
        if (letterForm) {
            letterForm.addEventListener('submit', function(e) {
                if (textarea && textarea.value.length > MAX_CHARS) {
                    e.preventDefault();
                    textarea.classList.add('is-invalid');
                    warning.style.display = 'block';
                    textarea.focus();
                    // Scroll warning into view
                    warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
</script>
@endpush
@endsection
