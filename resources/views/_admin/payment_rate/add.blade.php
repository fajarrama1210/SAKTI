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
                    <i class="fas fa-money-check-alt me-2"></i> Tambah Tarif Pembayaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Atur tarif pembayaran berdasarkan tahun ajaran, jenis, dan kelas.</p>
            </div>
            <a href="{{ route('admin.payment-rates.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                <div><h3>Formulir Tambah Tarif Pembayaran</h3><p>Isi semua field yang diperlukan</p></div>
            </div>
        </div>
        <div class="form-card-body">
            @if(session('error'))
            <div class="sakti-warning-box"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="sakti-warning-box">
                <i class="fas fa-exclamation-circle"></i>
                <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('admin.payment-rates.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_year_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Jenis Pembayaran</label>
                            <select name="payment_type_id" class="form-control @error('payment_type_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($paymentTypes as $pt)
                                <option value="{{ $pt->id }}" {{ old('payment_type_id') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                            @error('payment_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Kelas</label>
                            <select name="grade_level" class="form-control @error('grade_level') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach([10, 11, 12] as $grade)
                                <option value="{{ $grade }}" {{ old('grade_level') == $grade ? 'selected' : '' }}>Kelas {{ $grade }}</option>
                                @endforeach
                            </select>
                            @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Jurusan</label>
                            <select name="major_id" class="form-control @error('major_id') is-invalid @enderror">
                                <option value="">Semua Jurusan</option>
                                @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                            @error('major_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tarif (Rp)</label>
                            <input type="text" name="amount" id="input-rupiah" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="20.000" required>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                    <a href="{{ route('admin.payment-rates.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var inputTarif = document.getElementById('input-rupiah');
        if (inputTarif) {
            inputTarif.value = formatRupiah(inputTarif.value);
            inputTarif.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
            });
        }

        function formatRupiah(angka, prefix) {
            if (!angka) return '';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    });
</script>
@endpush

@endsection