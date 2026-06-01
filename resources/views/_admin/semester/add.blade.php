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
                    <i class="fas fa-book me-2"></i> Tambah Semester
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Tambah data semester baru ke sistem SAKTI.</p>
            </div>
            <a href="{{ route('admin.semesters.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                <div><h3>Formulir Tambah Semester</h3><p>Isi data semester yang akan ditambahkan</p></div>
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

            @php
            $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            @endphp

            <form action="{{ route('admin.semesters.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $ay->is_active ? $ay->id : null) == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('academic_year_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Nama Semester</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Semester Ganjil" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Bulan Mulai</label>
                            <select name="start_month" class="form-control @error('start_month') is-invalid @enderror" required>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ old('start_month') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                            @error('start_month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Bulan Akhir</label>
                            <select name="end_month" class="form-control @error('end_month') is-invalid @enderror" required>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ old('end_month') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                            @error('end_month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                    <a href="{{ route('admin.semesters.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection