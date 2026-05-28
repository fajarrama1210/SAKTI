@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">

    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-file-import me-2"></i> Import Data Siswa
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Unggah file Excel untuk mendaftarkan siswa secara massal.</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 order-xl-1">
            <div class="sakti-form-card">
                <div class="form-card-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon"><i class="fas fa-file-excel"></i></div>
                        <div><h3>Unggah File Excel</h3><p>Format .xlsx atau .xls, maks 2MB</p></div>
                    </div>
                </div>
                <div class="form-card-body">
                    <div class="text-center mb-4 mt-2">
                        <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                        <h4 class="text-uppercase text-muted font-weight-bold">Unggah File Excel</h4>
                        <p class="text-sm text-muted mb-0">Silakan unggah file Excel Anda. Pastikan format kolom telah sesuai dengan template yang diunduh agar sistem dapat mengekstrak dan mendaftarkan akun siswa secara otomatis.</p>
                    </div>

                    @if (session('import_errors'))
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading text-white font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Ada Kesalahan pada File Excel!</h4>
                            <p class="text-white text-sm mb-2">Beberapa data di baris berikut tidak dapat diimport. Silakan perbaiki file Excel Anda dan coba lagi.</p>
                            <hr class="my-2" style="border-color: rgba(255,255,255,.2)">
                            <ul class="mb-0 text-white text-sm pl-4" style="max-height: 200px; overflow-y: auto;">
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="pl-lg-4 pr-lg-4">
                            <div class="form-group mb-4">
                                <div class="custom-file">
                                    <input type="file" name="file_excel" class="custom-file-input" id="customFileLang" lang="id" required accept=".xlsx, .xls">
                                    <label class="custom-file-label text-left" for="customFileLang">Pilih file .xlsx / .xls</label>
                                </div>
                                <small class="form-text text-muted mt-2">Batas ukuran file maksimal 2MB. Format yang didukung: .xlsx, .xls</small>
                            </div>
                        </div>

                        <div class="text-center mt-4 mb-2">
                            <button type="submit" class="btn btn-sakti-primary btn-lg w-100 mb-3"><i class="fas fa-upload mr-2"></i> Import Data Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4 order-xl-2">
            <div class="sakti-info-box" style="border-radius: 20px; padding: 28px;">
                <h5 style="color: #065f46; font-weight: 700;"><i class="fas fa-info-circle"></i> Belum punya Template?</h5>
                <p class="mt-2 mb-3" style="font-size: .88rem;">
                    Unduh template Excel resmi. Template dilengkapi dengan <strong>dropdown data kelas otomatis</strong> dan akan membuatkan akun login bagi siswa secara otomatis.
                </p>
                <a href="{{ route('admin.students.template') }}" class="btn btn-sakti-primary btn-sm">
                    <i class="fas fa-download me-1"></i> Download Template
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var fileName = e.target.files[0].name;
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        });
    });
</script>
@endpush
@endsection
