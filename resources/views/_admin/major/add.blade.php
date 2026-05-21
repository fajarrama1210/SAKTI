@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Tambah Jurusan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.majors.store') }}" method="POST">
                @csrf {{-- Keamanan: Wajib ada untuk mencegah CSRF Attack --}}

                <div class="form-group">
                    <label class="form-control-label" for="name">Nama Jurusan</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Misal: Rekayasa Perangkat Lunak" required>

                    {{-- Pesan Error Validasi --}}
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-sakti-primary mt-3">Simpan Data</button>
                <a href="{{ route('admin.majors.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
