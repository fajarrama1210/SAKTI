@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">Manajemen Pengajuan Surat Izin / Sakit</h4>
                        <p class="text-sm text-muted mb-0">Kelola dan setujui pengajuan izin atau sakit dari siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Daftar Pengajuan</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Siswa</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kategori</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Keterangan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi / File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($letters as $letter)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $letter->student->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">NISN: {{ $letter->student->nisn }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ \Carbon\Carbon::parse($letter->submission_date)->translatedFormat('d F Y') }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $letter->type == 'sick' ? 'Sakit' : 'Izin' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs text-secondary mb-0" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $letter->description }}">{{ $letter->description }}</p>
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
                                    <td class="align-middle text-center">
                                        <a href="{{ asset('storage/' . $letter->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-0 me-2" title="Lihat Surat">
                                            <i class="fas fa-file-alt"></i>
                                        </a>

                                        @if($letter->status == 'pending')
                                        <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="d-inline form-approve">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success mb-0 me-2" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.letters.update-status', $letter->id) }}" method="POST" class="d-inline form-reject">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-danger mb-0" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-xs text-secondary mb-0">Belum ada pengajuan surat.</p>
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
                    confirmButtonColor: '#2dce89',
                    cancelButtonColor: '#8898aa',
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
                    confirmButtonColor: '#f5365c',
                    cancelButtonColor: '#8898aa',
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
