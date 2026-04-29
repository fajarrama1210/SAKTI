@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Edit Kelas</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.classrooms.update', $classroom->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-control-label" for="name">Nama Kelas</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $classroom->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-control-label" for="grade_level">Tingkat Kelas</label>
                    <select name="grade_level" id="grade_level" class="form-control @error('grade_level') is-invalid @enderror" required>
                        <option value="10" {{ old('grade_level', $classroom->grade_level) == '10' ? 'selected' : '' }}>Kelas 10</option>
                        <option value="11" {{ old('grade_level', $classroom->grade_level) == '11' ? 'selected' : '' }}>Kelas 11</option>
                        <option value="12" {{ old('grade_level', $classroom->grade_level) == '12' ? 'selected' : '' }}>Kelas 12</option>
                        <option value="13" {{ old('grade_level', $classroom->grade_level) == '13' ? 'selected' : '' }}>Kelas 13</option>
                    </select>
                    @error('grade_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-control-label" for="major_id">Jurusan</label>
                    <select name="major_id" id="major_id" class="form-control @error('major_id') is-invalid @enderror" required>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id', $classroom->major_id) == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('major_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Data</button>
                <a href="{{ route('admin.classrooms.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
