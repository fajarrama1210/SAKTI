@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Edit Jurusan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.majors.update', $major->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Keamanan: HTTP Method Spoofing untuk Update --}}

                <div class="form-group">
                    <label class="form-control-label" for="name">Nama Jurusan</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $major->name) }}" required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-sakti-primary mt-3">Update Data</button>
                <a href="{{ route('admin.majors.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
