@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Catat Transaksi Manual</h3>
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

            <form action="{{ route('admin.transactions.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tipe Transaksi</label>
                            <select name="type" class="form-control" required>
                                <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Uang Masuk</option>
                                <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Uang Keluar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Kategori</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="Contoh: ATK, Gaji, Donasi" list="category-list">
                            <datalist id="category-list">
                                <option value="SPP">
                                <option value="Uang Gedung">
                                <option value="Donasi">
                                <option value="Pendaftaran">
                                <option value="Gaji Guru">
                                <option value="ATK">
                                <option value="Listrik">
                                <option value="Air">
                                <option value="Internet">
                                <option value="Pemeliharaan">
                                <option value="Lain-lain">
                            </datalist>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Jumlah (Rp)</label>
                            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" min="1" placeholder="50000" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-control-label">Keterangan / Deskripsi</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Contoh: Pembelian kertas HVS 5 rim" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Simpan Transaksi</button>
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection