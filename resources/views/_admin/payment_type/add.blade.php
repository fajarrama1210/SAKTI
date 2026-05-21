@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Tambah Jenis Pembayaran</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('admin.payment-types.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-control-label">Nama Jenis Pembayaran</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: SPP" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_monthly" value="0">
                        <input type="checkbox" name="is_monthly" value="1" class="custom-control-input" id="is_monthly" {{ old('is_monthly', '1') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_monthly">Tagihan Bulanan</label>
                    </div>
                    <small class="text-muted">Centang jika jenis pembayaran ini ditagihkan setiap bulan (contoh: SPP).</small>
                </div>

                <button type="submit" class="btn btn-sakti-primary mt-3">Simpan</button>
                <a href="{{ route('admin.payment-types.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection