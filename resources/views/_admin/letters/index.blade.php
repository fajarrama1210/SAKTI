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
                    <i class="fas fa-envelope-open-text me-2"></i> Manajemen Pengajuan Surat Izin / Sakit
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola dan setujui pengajuan izin atau sakit dari siswa.
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Pengajuan
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi / File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($letters as $letter)
                                <tr>
                                    <!-- SISWA -->
                                    <td>
                                        <div class="d-flex align-items-center px-2">
                                            <div class="trx-detail-icon icon-user me-3" style="width: 38px; height: 38px; font-size: .82rem;">
                                                <i class="fas fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0" style="font-size: .88rem; font-weight: 700; color: var(--dark-text);">
                                                    {{ $letter->student->name }}
                                                </h6>
                                                <span style="font-size: .75rem; color: var(--muted-text); font-weight: 500;">
                                                    NISN: {{ $letter->student->nisn }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- TANGGAL -->
                                    <td>
                                        <span style="font-weight: 600; color: var(--dark-text); font-size: .88rem;">
                                            {{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}
                                        </span>
                                    </td>

                                    <!-- KATEGORI -->
                                    <td>
                                        @if($letter->type == 'sick')
                                            <span class="badge px-3 py-2" style="background: #fef2f2; color: #ef4444; font-weight: 600; border-radius: 50px; font-size: .76rem;">
                                                <i class="fas fa-thermometer-half me-1"></i> Sakit
                                            </span>
                                        @else
                                            <span class="badge px-3 py-2" style="background: #ecfeff; color: #0891b2; font-weight: 600; border-radius: 50px; font-size: .76rem;">
                                                <i class="fas fa-calendar-check me-1"></i> Izin
                                            </span>
                                        @endif
                                    </td>

                                    <!-- KETERANGAN -->
                                    <td>
                                        <p class="mb-0" style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: .85rem; color: var(--muted-text);" title="{{ $letter->description }}">
                                            {{ $letter->description }}
                                        </p>
                                    </td>

                                    <!-- STATUS -->
                                    <td>
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
                                    </td>

                                    <!-- AKSI -->
                                    <td class="align-middle text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank" class="btn btn-action-view mb-0" title="Lihat Surat">
                                                <i class="fas fa-file-alt me-1"></i> Lihat
                                            </a>

                                            @if($letter->status == 'pending')
                                            <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="d-inline form-approve">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-action-approve mb-0" title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="d-inline form-reject">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-action-reject mb-0" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-envelope-open fa-2x d-block mb-3" style="color: var(--muted-text); opacity: .3;"></i>
                                        <span style="color: var(--muted-text); font-size: .88rem;">Belum ada pengajuan surat.</span>
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
