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
                    <i class="fas fa-tags me-2"></i> Tambah Jenis Pembayaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Tambah jenis pembayaran baru ke sistem SAKTI.</p>
            </div>
            <a href="{{ route('admin.payment-types.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                <div><h3>Formulir Tambah Jenis Pembayaran</h3><p>Isi data jenis pembayaran</p></div>
            </div>
        </div>
        <div class="form-card-body">
            @if(session('error'))
            <div class="sakti-warning-box"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        Swal.fire({
                            icon: "error",
                            title: "Validasi Gagal",
                            text: "Silakan periksa kembali isian form yang ditandai merah."
                        });
                    });
                </script>
            @endif

            <form action="{{ route('admin.payment-types.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-control-label">Nama Jenis Pembayaran</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: SPP" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_monthly" value="0">
                        <input type="checkbox" name="is_monthly" value="1" class="custom-control-input" id="is_monthly" {{ old('is_monthly', '1') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_monthly">Tagihan Bulanan</label>
                    </div>
                    <small class="text-muted">Centang jika jenis pembayaran ini ditagihkan setiap bulan (contoh: SPP).</small>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                    <a href="{{ route('admin.payment-types.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection