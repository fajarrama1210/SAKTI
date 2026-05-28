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
                    <i class="fas fa-edit me-2"></i> Edit Transaksi Manual
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Perbarui catatan transaksi keuangan.</p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-edit"></i></div>
                <div><h3>Formulir Edit Transaksi</h3><p>Ubah data transaksi lalu simpan</p></div>
            </div>
        </div>
        <div class="form-card-body">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tanggal</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', $transaction->date) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tipe Transaksi</label>
                            <select name="type" class="form-control" required>
                                <option value="income" {{ old('type', $transaction->type) === 'income' ? 'selected' : '' }}>Uang Masuk</option>
                                <option value="expense" {{ old('type', $transaction->type) === 'expense' ? 'selected' : '' }}>Uang Keluar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Kategori</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category', $transaction->category) }}" placeholder="Contoh: ATK, Gaji, Donasi" list="category-list">
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
                            <input type="text" name="amount" id="input-rupiah" class="form-control" value="{{ old('amount', $transaction->amount) }}" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="form-control-label">Keterangan / Deskripsi</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description', $transaction->description) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Update Transaksi</button>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var inputTarif = document.getElementById('input-rupiah');
        if (inputTarif) {
            inputTarif.value = formatRupiah(inputTarif.value);
            inputTarif.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
            });
        }

        function formatRupiah(angka, prefix) {
            if (!angka) return '';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    });
</script>
@endpush

@endsection
