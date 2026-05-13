@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        <div class="col-xl-8 order-xl-1">
            <div class="card">
                <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Import Data Siswa Ekstraksi Excel</h3>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-primary">Kembali</a>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4 mt-2">
                        <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                        <h4 class="text-uppercase text-muted">Unggah File Excel</h4>
                        <p class="text-sm text-muted mb-0">Silakan unggah file Excel Anda. Pastikan format kolom telah sesuai dengan template yang diunduh agar sistem dapat mengekstrak dan mendaftarkan akun siswa secara otomatis.</p>
                    </div>

                    @if (session('import_errors'))
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading text-white"><i class="fas fa-exclamation-triangle mr-2"></i> Ada Kesalahan pada File Excel!</h4>
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
                            <button type="submit" class="btn btn-warning btn-lg w-100 mb-3"><i class="fas fa-upload mr-2"></i> Import Data Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 order-xl-2">
            <div class="card card-profile">
                <div class="card-body pt-0 pt-md-4">
                    <div class="text-center mt-md-5">
                        <h5 class="h3">
                            <i class="fas fa-info-circle text-primary"></i> Belum punya Templatenya?
                        </h5>
                        <div class="h5 font-weight-300 mt-3">
                            Unduh template Excel resmi untuk mempermudah pendaftaran data siswa Anda.
                        </div>
                        <p class="mt-4 mb-4 text-sm text-muted">
                            Template ini dilengkapi dengan <strong class="text-dark">dropdown data kelas otomatis</strong> (tidak perlu tebak ID lagi!) dan akan secara otomatis membuatkan akun login bagi siswa terkait.
                        </p>
                        <a href="{{ route('admin.students.template') }}" class="btn btn-success">
                            <i class="fas fa-download mr-1"></i> Download Template
                        </a>
                    </div>
                </div>
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
