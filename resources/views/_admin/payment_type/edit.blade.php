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
                    <i class="fas fa-tags me-2"></i> Edit Jenis Pembayaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Perbarui jenis pembayaran: <strong class="text-white">{{ $paymentType->name }}</strong></p>
            </div>
            <a href="{{ route('admin.payment-types.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-edit"></i></div>
                <div><h3>Formulir Edit Jenis Pembayaran</h3><p>Ubah data jenis pembayaran lalu simpan</p></div>
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

            <form action="{{ route('admin.payment-types.update', $paymentType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-control-label">Nama Jenis Pembayaran</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $paymentType->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_monthly" value="0">
                        <input type="checkbox" name="is_monthly" value="1" class="custom-control-input" id="is_monthly" {{ old('is_monthly', $paymentType->is_monthly) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_monthly">Tagihan Bulanan</label>
                    </div>
                </div>
                <div class="form-group" id="semester-select-container" style="display: none;">
                    <label class="form-control-label">Semester (Opsional)</label>
                    <select name="semester_id" class="form-control @error('semester_id') is-invalid @enderror">
                        <option value="">Semua Semester (Global)</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ old('semester_id', $paymentType->semester_id) == $sem->id ? 'selected' : '' }}>
                                {{ $sem->semester_name }} ({{ $sem->academic_year_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Pilih semester jika tagihan bulanan ini hanya ditagihkan di semester tertentu. Kosongkan untuk menagih di semua semester.</small>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Update</button>
                    <a href="{{ route('admin.payment-types.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isMonthlyCheckbox = document.getElementById('is_monthly');
        const semesterContainer = document.getElementById('semester-select-container');

        function toggleSemesterSelect() {
            if (isMonthlyCheckbox.checked) {
                semesterContainer.style.display = 'block';
            } else {
                semesterContainer.style.display = 'none';
                semesterContainer.querySelector('select').value = '';
            }
        }

        isMonthlyCheckbox.addEventListener('change', toggleSemesterSelect);
        toggleSemesterSelect();
    });
</script>
@endpush