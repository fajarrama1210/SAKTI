@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Edit Data Siswa</h3>
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

            <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">NIK (16 Digit)</label>
                            <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number', $student->id_number) }}" required maxlength="16">
                            @error('id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Nomor Kartu Keluarga (16 Digit)</label>
                            <input type="text" name="family_card_number" class="form-control @error('family_card_number') is-invalid @enderror" value="{{ old('family_card_number', $student->family_card_number) }}" required maxlength="16">
                            @error('family_card_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">NISN</label>
                            <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $student->nisn) }}" required maxlength="10">
                            @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-control-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-control-label">Pilih Kelas</label>
                    <select name="classroom_id" class="form-control @error('classroom_id') is-invalid @enderror" required>
                        @foreach($classrooms as $room)
                            <option value="{{ $room->id }}" {{ old('classroom_id', $student->classroom_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - {{ $room->major_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classroom_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Data Siswa</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
