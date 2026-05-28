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
                    <i class="fas fa-graduation-cap me-2"></i> Tambah Jurusan
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Tambah jurusan baru ke sistem SAKTI.</p>
            </div>
            <a href="{{ route('admin.majors.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                <div><h3>Formulir Tambah Jurusan</h3><p>Isi nama jurusan yang akan ditambahkan</p></div>
            </div>
        </div>
        <div class="form-card-body">
            <form action="{{ route('admin.majors.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-control-label" for="name">Nama Jurusan</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Misal: Rekayasa Perangkat Lunak" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan Data</button>
                    <a href="{{ route('admin.majors.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
