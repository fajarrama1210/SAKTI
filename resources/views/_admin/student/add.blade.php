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
                    <i class="fas fa-user-graduate me-2"></i> Tambah Siswa & Buat Akun
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Daftarkan siswa baru dan buat akun portal untuk mereka.</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-user-plus"></i></div>
                <div><h3>Formulir Tambah Siswa</h3><p>Password default akun siswa adalah NISN mereka</p></div>
            </div>
        </div>
        <div class="form-card-body">
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

            <form action="{{ route('admin.students.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">NIK (16 Digit)</label>
                            <input type="text" name="id_number"
                                class="form-control @error('id_number') is-invalid @enderror"
                                value="{{ old('id_number') }}" required maxlength="16"
                                inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('id_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Nomor KK (16 Digit)</label>
                            <input type="text" name="family_card_number"
                                class="form-control @error('family_card_number') is-invalid @enderror"
                                value="{{ old('family_card_number') }}" required maxlength="16"
                                inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('family_card_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn') }}" required maxlength="10"
                                inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Kelas</label>
                            <select name="classroom_id" class="form-control @error('classroom_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($classrooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} - {{ $room->major_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classroom_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="sakti-info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Info:</strong> Akun portal siswa akan dibuat otomatis. Username: <strong>email siswa (NISN@sakti.sch.id)</strong>, Password default: <strong>NISN siswa</strong>.
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan Data</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
