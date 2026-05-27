@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="trx-detail-card">

                {{-- HEADER --}}
                <div class="trx-detail-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <span class="trx-code">
                                <i class="fas fa-hashtag me-1"></i> TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 class="trx-title mt-1">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Detail Transaksi
                            </h3>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            @if($transaction->type === 'income')
                                <span class="trx-badge trx-badge-income">
                                    <i class="fas fa-arrow-down"></i> Uang Masuk
                                </span>
                            @else
                                <span class="trx-badge trx-badge-expense">
                                    <i class="fas fa-arrow-up"></i> Uang Keluar
                                </span>
                            @endif
                            <a href="{{ route('admin.transactions.index') }}" class="btn-back">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="card-body p-4">

                    {{-- AMOUNT BOX --}}
                    <div class="trx-amount-box {{ $transaction->type === 'expense' ? 'expense' : '' }} mb-4">
                        <div class="trx-amount-label">Jumlah Transaksi</div>
                        <div class="trx-amount-value {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </div>
                    </div>

                    {{-- DETAIL ROWS --}}
                    <div class="px-2">

                        {{-- Tanggal --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-date">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Tanggal Transaksi</div>
                                <div class="trx-detail-value">
                                    {{ \Carbon\Carbon::parse($transaction->date)->locale('id')->isoFormat('D MMMM YYYY') }}
                                </div>
                            </div>
                        </div>

                        {{-- Kategori --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-category">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Kategori</div>
                                <div class="trx-detail-value">
                                    {{ $transaction->category ?? '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Asal Catatan --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-source">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Asal Catatan</div>
                                <div class="trx-detail-value">
                                    @if($transaction->payment_id)
                                        <span class="trx-source-badge trx-source-auto">
                                            <i class="fas fa-robot"></i> Otomatis SPP (Tercatat oleh Sistem)
                                        </span>
                                    @else
                                        <span class="trx-source-badge trx-source-manual">
                                            <i class="fas fa-user-edit"></i> Pencatatan Manual Admin
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Dicatat Oleh --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-user">
                                <i class="far fa-user"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Dicatat Oleh</div>
                                <div class="trx-detail-value">
                                    {{ $transaction->recorded_by_name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Waktu Pencatatan --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-time">
                                <i class="far fa-clock"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Waktu Pencatatan</div>
                                <div class="trx-detail-value">
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                                </div>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="trx-detail-row">
                            <div class="trx-detail-icon icon-desc">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div>
                                <div class="trx-detail-label">Keterangan / Deskripsi</div>
                                <div class="trx-detail-value" style="white-space: normal; line-height: 1.6;">
                                    {{ $transaction->description }}
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
