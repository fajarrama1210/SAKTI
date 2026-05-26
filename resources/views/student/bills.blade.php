@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">Tagihan & Riwayat Pembayaran SPP</h4>
                        <p class="text-sm text-muted mb-0">Daftar lengkap tagihan bulanan dan riwayat pembayaran Anda untuk tahun ajaran aktif.</p>
                    </div>
                    <div>
                        <span class="badge bg-success-soft text-success p-2 font-weight-bold">
                            {{ $student->name }} (NISN: {{ $student->nisn }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reminder Cicilan -->
    @php $partialBills = $bills->filter(fn($b) => in_array($b->status, ['partial','unpaid'])); @endphp
    @if($partialBills->isNotEmpty())
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                <div class="d-flex align-items-center px-4 py-3" style="background:linear-gradient(135deg,#ff6b35,#f7931e);">
                    <div class="me-3" style="width:40px;height:40px;background:rgba(255,255,255,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-bell text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 text-white font-weight-bold">⚠️ Pengingat: Ada {{ $partialBills->count() }} Tagihan yang Belum Lunas</h6>
                        <small class="text-white" style="opacity:.85;">Klik tagihan di bawah untuk melihat detail dan riwayat cicilan Anda.</small>
                    </div>
                    <div class="text-end">
                        <div class="text-white font-weight-bold" style="font-size:1.3rem;">
                            Rp {{ number_format($partialBills->sum(fn($b) => $b->total_amount - $b->paid_amount), 0, ',', '.') }}
                        </div>
                        <small class="text-white" style="opacity:.85;">Total sisa tunggakan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Bills List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Daftar Tagihan Bulanan</h6>
                </div>
                <div class="card-body p-3">
                    <div class="accordion" id="accordionBills">
                        @forelse($bills as $index => $bill)
                            @php
                                $statusBadge = [
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning',
                                    'unpaid' => 'bg-danger'
                                ][$bill->status];
                                
                                $statusLabel = [
                                    'paid' => 'Lunas',
                                    'partial' => 'Sebagian',
                                    'unpaid' => 'Belum Bayar'
                                ][$bill->status];

                                $remaining = $bill->total_amount - $bill->paid_amount;
                            @endphp
                            
                            <div class="accordion-item border-0 border-radius-lg mb-3 shadow-sm" style="overflow: hidden;">
                                <h2 class="accordion-header" id="heading-{{ $bill->id }}">
                                    <button class="accordion-button collapsed px-4 py-3 bg-light d-md-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $bill->id }}" aria-expanded="false" aria-controls="collapse-{{ $bill->id }}">
                                        <div class="d-md-flex align-items-center flex-grow-1">
                                            <!-- Periode -->
                                            <div class="me-4 mb-2 mb-md-0" style="min-width: 150px;">
                                                <h6 class="mb-0 text-dark font-weight-bold">
                                                    {{ \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F') }} {{ $bill->year }}
                                                </h6>
                                                <span class="text-xs text-muted">{{ $bill->academic_year_name }}</span>
                                            </div>
                                            <!-- Status -->
                                            <div class="me-4 mb-2 mb-md-0">
                                                <span class="badge {{ $statusBadge }} text-xxs font-weight-bold">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>
                                            <!-- Jatuh Tempo -->
                                            <div class="me-4 mb-2 mb-md-0" style="min-width: 180px;">
                                                <span class="text-xs text-muted">Jatuh Tempo: </span>
                                                <span class="text-xs font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') }}
                                                </span>
                                            </div>
                                            <!-- Total & Bayar -->
                                            <div class="d-flex align-items-center ms-auto pe-4">
                                                <div class="text-end me-4">
                                                    <span class="text-xs text-muted d-block">Tagihan</span>
                                                    <span class="text-sm font-weight-bold text-dark">
                                                        Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <span class="text-xs text-muted d-block">Sisa Bayar</span>
                                                    <span class="text-sm font-weight-bold {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $bill->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $bill->id }}" data-bs-parent="#accordionBills">
                                    <div class="accordion-body bg-white border-top p-4">
                                        <div class="row">
                                            <!-- Rincian Item Tagihan -->
                                            <div class="col-lg-6 mb-4 mb-lg-0">
                                                <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-3">Rincian Item Tagihan</h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach($bill->items as $item)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0 border-bottom">
                                                            <div class="text-xs text-dark font-weight-bold">
                                                                {{ $item->payment_type_name }}
                                                            </div>
                                                            <div class="text-xs text-dark font-weight-bold">
                                                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0 font-weight-bold text-dark">
                                                        <span class="text-xs">Total Tagihan</span>
                                                        <span class="text-xs">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Riwayat Pembayaran untuk Tagihan Ini -->
                                            <div class="col-lg-6">
                                                <h6 class="text-xs font-weight-bold text-uppercase text-muted mb-3">Riwayat Pembayaran</h6>
                                                @if(count($bill->payments) > 0)
                                                    <div class="timeline timeline-one-side">
                                                        @foreach($bill->payments as $payment)
                                                            <div class="timeline-block mb-3">
                                                                <span class="timeline-step">
                                                                    <i class="fas fa-check-circle text-success text-gradient"></i>
                                                                </span>
                                                                <div class="timeline-content">
                                                                    <h6 class="text-dark text-xs font-weight-bold mb-0">
                                                                        Rp {{ number_format($payment->amount, 0, ',', '.') }} - Terbayar
                                                                    </h6>
                                                                    <p class="text-secondary text-xxs font-weight-bold mt-1 mb-0">
                                                                        {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y H:i') }} | Ref: {{ $payment->reference_number ?? '-' }}
                                                                    </p>
                                                                    @if($payment->notes)
                                                                        <p class="text-xs text-muted italic mt-1 mb-0">"{{ $payment->notes }}"</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center py-4 bg-light border-radius-lg">
                                                        <i class="fas fa-receipt text-muted mb-2"></i>
                                                        <p class="text-xs text-muted mb-0">Belum ada pembayaran untuk bulan ini.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Tidak Ada Tagihan</h6>
                                <p class="text-xs text-muted mb-0">Belum ada tagihan yang dibuat untuk akun Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
