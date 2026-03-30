@extends('_admin.layouts.app')

@section('content')
    <div class="container-fluid mt--6">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="mb-0">Tambah Siswa & Buat Akun</h3>
            </div>

            <div class="card-body">
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
                <div class="alert alert-info">
                    <strong>Info:</strong> Akun login (User) akan digenerate otomatis.<br>
                    Email: <b>[NISN]@smkakbar.sch.id</b> | Password Default: <b>password123</b>
                </div>

                <form action="{{ route('admin.students.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Nomor KK (16 Digit)</label>
                        <input type="text" name="family_card_number"
                            class="form-control @error('family_card_number') is-invalid @enderror"
                            value="{{ old('family_card_number') }}" required maxlength="16">
                        @error('family_card_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>NISN</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror"
                            value="{{ old('nisn') }}" required>
                        @error('nisn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="classroom_id" class="form-control @error('classroom_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $room)
                                <option value="{{ $room->id }}"
                                    {{ old('classroom_id') == $room->id ? 'selected' : '' }}>
                                    Kelas {{ $room->grade_level }} - {{ $room->major_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
