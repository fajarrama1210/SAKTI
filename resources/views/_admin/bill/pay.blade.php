@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0"><i class="fas fa-money-bill-wave"></i> Catat Pembayaran</h3>
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

            <div class="alert alert-primary">
                <div class="row">
                    <div class="col-md-8">
                        <strong>KK:</strong> <code>{{ $bill->family_card_number }}</code><br>
                        <strong>Semester:</strong> {{ $bill->semester_name }} ({{ $bill->academic_year_name }})<br>
                        <strong>Periode:</strong> <b>{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}</b><br>
                        <strong>Total Tagihan:</strong> Rp {{ number_format($bill->total_amount, 0, ',', '.') }}<br>
                        <strong>Sisa yang harus dibayar:</strong> <b class="text-white" style="font-size:1.1em">Rp {{ number_format($remaining, 0, ',', '.') }}</b>
                    </div>
                    @if($siblings->count() > 1)
                    <div class="col-md-4">
                        <div class="alert alert-warning mb-0 py-2">
                            <i class="fas fa-users"></i> <b>{{ $siblings->count() }} siswa</b> terdaftar dalam KK ini:<br>
                            <ul class="pl-3 mb-0 mt-1" style="font-size:0.85rem;">
                                @foreach($siblings as $sib)
                                <li>{{ $sib->name }} <span class="badge badge-{{ $sib->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($sib->status) }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.bills.pay', $bill->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Jumlah Bayar (Rp)</label>
                            <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $remaining) }}" min="1" required>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Metode Pembayaran</label>
                            <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                                <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                            </select>
                            @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Tanggal Bayar</label>
                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', now()->toDateString()) }}" required>
                            @error('payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">No. Referensi / Kwitansi <small class="text-muted">(Opsional)</small></label>
                            <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}" placeholder="Contoh: KW-2026-001">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Catatan <small class="text-muted">(Opsional)</small></label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-check"></i> Simpan Pembayaran
                </button>
                <a href="{{ route('admin.bills.show', $bill->id) }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection