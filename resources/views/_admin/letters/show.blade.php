@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">

    <!-- HEADER -->
    <div class="letters-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-envelope-open-text me-2"></i> Detail Surat Pengajuan
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Lihat detail lengkap pengajuan izin/sakit siswa.
                </p>
            </div>
            <a href="{{ route('admin.letters.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); color: #fff; border-radius: 12px; font-weight: 600; backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- INFO SURAT -->
        <div class="col-lg-5 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-info-circle me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Informasi Pengajuan
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">

                    <!-- Siswa -->
                    <div class="trx-detail-row">
                        <div class="trx-detail-icon icon-user">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <div class="trx-detail-label">Nama Siswa</div>
                            <div class="trx-detail-value">{{ $letter->student->name }}</div>
                            <small style="color: var(--muted-text); font-weight: 500;">NISN: {{ $letter->student->nisn }}</small>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="trx-detail-row">
                        <div class="trx-detail-icon icon-date">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="trx-detail-label">Tanggal Pengajuan</div>
                            <div class="trx-detail-value">{{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="trx-detail-row">
                        <div class="trx-detail-icon icon-category">
                            <i class="fas {{ $letter->type == 'sick' ? 'fa-thermometer-half' : 'fa-calendar-check' }}"></i>
                        </div>
                        <div>
                            <div class="trx-detail-label">Kategori</div>
                            <div class="trx-detail-value">
                                @if($letter->type == 'sick')
                                    <span class="badge px-3 py-2" style="background: #fef2f2; color: #ef4444; font-weight: 600; border-radius: 50px; font-size: .8rem;">
                                        <i class="fas fa-thermometer-half me-1"></i> Sakit
                                    </span>
                                @else
                                    <span class="badge px-3 py-2" style="background: #ecfeff; color: #0891b2; font-weight: 600; border-radius: 50px; font-size: .8rem;">
                                        <i class="fas fa-calendar-check me-1"></i> Izin
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="trx-detail-row">
                        <div class="trx-detail-icon icon-desc">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div>
                            <div class="trx-detail-label">Status</div>
                            <div class="trx-detail-value">
                                @if($letter->status == 'pending')
                                    <span class="badge-status-pending">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @elseif($letter->status == 'approved')
                                    <span class="badge-status-approved">
                                        <i class="fas fa-check-circle me-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge-status-rejected">
                                        <i class="fas fa-times-circle me-1"></i> Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="trx-detail-row">
                        <div class="trx-detail-icon icon-source">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="trx-detail-label">Keterangan</div>
                            <div class="trx-detail-value" style="white-space: pre-wrap; word-break: break-word;">{{ $letter->description }}
                            </div>
                        </div>
                    </div>

                    <!-- Aksi -->
                    @if($letter->status == 'pending')
                    <div class="mt-4 pt-3" style="border-top: 1px solid #f1f5f9;">
                        <h6 class="font-weight-bold mb-3" style="color: var(--dark-text); font-size: .85rem;">
                            <i class="fas fa-gavel me-1" style="color: var(--primary-green);"></i> Tindakan
                        </h6>
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="form-approve">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-action-approve px-4 py-2">
                                    <i class="fas fa-check me-1"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="form-reject">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-action-reject px-4 py-2">
                                    <i class="fas fa-times me-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- PREVIEW FILE -->
        <div class="col-lg-7 mb-4">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-file-alt me-2" style="color: #2563eb; opacity: .7;"></i>
                        Lampiran Surat
                    </h3>
                    <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank" class="btn btn-sm btn-action-view">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                    </a>
                </div>
                <div class="card-body px-4 pb-4">
                    @php
                        $extension = strtolower(pathinfo($letter->file_path, PATHINFO_EXTENSION));
                        $fileUrl = asset('storage/' . $letter->file_path);
                    @endphp

                    @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                        {{-- Image Preview --}}
                        <div class="text-center" style="background: #f8fafc; border-radius: 16px; padding: 20px;">
                            <img src="{{ $fileUrl }}" alt="Surat {{ $letter->student->name }}"
                                 style="max-width: 100%; max-height: 600px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.1);">
                        </div>
                    @elseif($extension == 'pdf')
                        {{-- PDF Preview --}}
                        <div style="background: #f8fafc; border-radius: 16px; padding: 12px; overflow: hidden;">
                            <iframe src="{{ $fileUrl }}" width="100%" height="550" style="border: none; border-radius: 12px;"></iframe>
                        </div>
                    @else
                        {{-- Fallback --}}
                        <div class="text-center py-5" style="background: #f8fafc; border-radius: 16px;">
                            <i class="fas fa-file fa-3x mb-3" style="color: var(--muted-text); opacity: .4;"></i>
                            <p class="mb-3" style="color: var(--muted-text);">Preview tidak tersedia untuk format ini.</p>
                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-action-view">
                                <i class="fas fa-download me-1"></i> Download File
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const approveForms = document.querySelectorAll('form.form-approve');
        approveForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Setujui Pengajuan?',
                    text: "Siswa akan diberikan izin/sakit yang diajukan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Setujui',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const rejectForms = document.querySelectorAll('form.form-reject');
        rejectForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Tolak Pengajuan?',
                    text: "Pengajuan izin/sakit ini akan ditolak.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
