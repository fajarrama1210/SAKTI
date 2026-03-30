@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-8 order-xl-1">
            <div class="card">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0"><i class="fas fa-magic"></i> Generate Tagihan Per Semester</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('admin.bills.index') }}" class="btn btn-sm btn-primary">Kembali</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Info:</strong> Sistem akan otomatis men-generate tagihan untuk <b>seluruh Kartu Keluarga</b> yang memiliki siswa aktif berdasarkan Semester yang dipilih.
                        Kebijakan sekolah: <b>1 KK = 1 tagihan</b> per semester, berapapun jumlah siswa dalam KK tersebut.
                        Tagihan yang sudah ada tidak akan di-generate ulang.
                    </div>

                    <form action="{{ route('admin.bills.generate') }}" method="POST">
                        @csrf
                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Semester</label>
                                        <select name="semester_id" class="form-control @error('semester_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Semester --</option>
                                            @foreach($semesters as $sem)
                                            <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                                                {{ $sem->academic_year_name }} — {{ $sem->name }}
                                                (Bulan {{ $sem->start_month }}–{{ $sem->end_month }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        @if($semesters->isEmpty())
                                            <small class="text-danger mt-1 d-block">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Belum ada semester. Silakan tambahkan semester terlebih dahulu di menu
                                                <a href="{{ route('admin.semesters.index') }}">Manajemen Semester</a>.
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Tanggal Jatuh Tempo</label>
                                        <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success" id="btn-generate">
                                    <i class="fas fa-magic"></i> Generate Tagihan Sekarang
                                </button>
                                <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4 order-xl-2">
            <div class="card bg-gradient-warning shadow">
                <div class="card-body text-white">
                    <h5 class="text-white"><i class="fas fa-lightbulb"></i> Catatan Penting</h5>
                    <ul class="pl-3 mb-0 mt-2" style="font-size: 0.85rem;">
                        <li class="mb-2">Pastikan data <strong>Tarif Pembayaran</strong> sudah diatur untuk tahun ajaran semester yang dipilih.</li>
                        <li class="mb-2">Pastikan semua siswa aktif sudah terdaftar dengan <strong>Nomor KK yang benar</strong>.</li>
                        <li class="mb-2">Siswa dengan status <strong>Lulus / Keluar</strong> tidak akan diikutkan dalam generate tagihan.</li>
                        <li>Jika ada KK yang sudah memiliki tagihan di semester ini, tagihan tersebut <strong>tidak akan dibuat ulang</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Konfirmasi Generate Tagihan',
            html: 'Sistem akan membuat tagihan untuk <b>seluruh KK siswa aktif</b> pada semester yang dipilih.<br><br>Apakah Anda yakin?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection