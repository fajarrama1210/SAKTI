@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h3 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Detail Transaksi
                    </h3>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body bg-light pt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0 mb-4 mt-3">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                                        <div>
                                            <span class="text-xs text-muted text-uppercase font-weight-bold">Kode Transaksi</span>
                                            <h4 class="mb-0 text-dark font-weight-bold">TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</h4>
                                        </div>
                                        <div>
                                            @if($transaction->type === 'income')
                                                <span class="badge badge-lg bg-gradient-success text-uppercase font-weight-bold px-3 py-2" style="font-size: 0.8rem;">
                                                    <i class="fas fa-arrow-down me-1"></i> Uang Masuk
                                                </span>
                                            @else
                                                <span class="badge badge-lg bg-gradient-danger text-uppercase font-weight-bold px-3 py-2" style="font-size: 0.8rem;">
                                                    <i class="fas fa-arrow-up me-1"></i> Uang Keluar
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Amount Section -->
                                        <div class="col-12 mb-4">
                                            <div class="bg-gradient-secondary p-4 rounded-3 text-center border">
                                                <span class="text-xs text-muted text-uppercase font-weight-bold d-block mb-1">Jumlah Transaksi</span>
                                                <h1 class="mb-0 font-weight-bolder {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                                </h1>
                                            </div>
                                        </div>

                                        <!-- Detail Table -->
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-borderless align-items-center mb-0">
                                                    <tbody>
                                                        <tr class="border-bottom">
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted" style="width: 180px;">Tanggal Transaksi</td>
                                                            <td class="px-0 py-3 text-sm text-dark font-weight-bold">
                                                                <i class="far fa-calendar-alt text-primary me-2"></i> 
                                                                {{ \Carbon\Carbon::parse($transaction->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                                            </td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted">Kategori</td>
                                                            <td class="px-0 py-3 text-sm text-dark font-weight-bold">
                                                                <span class="badge bg-secondary text-dark text-xs px-2.5 py-1.5 border">
                                                                    {{ $transaction->category ?? '-' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted">Asal Catatan</td>
                                                            <td class="px-0 py-3 text-sm text-dark font-weight-bold">
                                                                @if($transaction->payment_id)
                                                                    <span class="badge bg-gradient-info text-white text-xs px-2.5 py-1.5 shadow-sm">
                                                                        <i class="fas fa-robot me-1"></i> Otomatis SPP (Tercatat oleh Sistem)
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-gradient-secondary text-muted text-xs px-2.5 py-1.5 border">
                                                                        <i class="fas fa-user-edit me-1"></i> Pencatatan Manual Admin
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted">Dicatat Oleh</td>
                                                            <td class="px-0 py-3 text-sm text-dark font-weight-bold">
                                                                <i class="far fa-user text-muted me-2"></i> {{ $transaction->recorded_by_name ?? '-' }}
                                                            </td>
                                                        </tr>
                                                        <tr class="border-bottom">
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted">Waktu Pencatatan</td>
                                                            <td class="px-0 py-3 text-sm text-dark">
                                                                <i class="far fa-clock text-muted me-2"></i> 
                                                                {{ \Carbon\Carbon::parse($transaction->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-0 py-3 text-sm font-weight-bold text-muted align-top">Keterangan / Deskripsi</td>
                                                            <td class="px-0 py-3 text-sm text-dark text-wrap leading-relaxed" style="white-space: normal; line-height: 1.6;">
                                                                {{ $transaction->description }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
