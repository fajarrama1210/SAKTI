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
                    <i class="fas fa-chalkboard me-2"></i> Tambah Kelas
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Tambah kelas baru ke sistem SAKTI.</p>
            </div>
            <a href="{{ route('admin.classrooms.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-plus"></i></div>
                <div>
                    <h3>Formulir Tambah Kelas</h3>
                    <p>Isi data kelas yang akan ditambahkan</p>
                </div>
            </div>
        </div>
        <div class="form-card-body">
            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label" for="name">Nama Kelas</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: X RPL 1" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label" for="grade_level">Tingkat Kelas</label>
                            <select name="grade_level" id="grade_level"
                                class="form-control @error('grade_level') is-invalid @enderror" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="10" {{ old('grade_level') == '10' ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ old('grade_level') == '11' ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ old('grade_level') == '12' ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                            @error('grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-control-label" for="major_id">Jurusan</label>
                            <select name="major_id" id="major_id" class="form-control @error('major_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('major_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Simpan Data</button>
                    <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
