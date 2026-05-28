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
                    <i class="fas fa-file-invoice-dollar me-2"></i> Detail Tagihan
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Informasi lengkap tagihan dan riwayat pembayaran.</p>
            </div>
            <a href="{{ route('admin.bills.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Header Info --}}
    <div class="sakti-form-card mb-4">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-info-circle"></i></div>
                <div><h3>Informasi Tagihan</h3><p>Data pokok tagihan ini</p></div>
            </div>
        </div>
        <div class="form-card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="180">No. Kartu Keluarga</td>
                            <td><b>{{ $bill->family_card_number }}</b></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td>{{ $bill->semester_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode Bulan</td>
                            <td><b>{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}</b></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted" width="180">Total Tagihan</td>
                            <td><b class="text-sakti-green" style="font-size: 1.2em;">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Dibayar</td>
                            <td><b class="text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sisa</td>
                            <td><b class="text-danger">Rp {{ number_format($bill->total_amount - $totalPaid, 0, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($bill->status === 'paid')
                                <span class="badge badge-sm bg-gradient-success">Lunas</span>
                                @elseif($bill->status === 'partial')
                                <span class="badge badge-sm bg-gradient-warning">Sebagian</span>
                                @elseif($bill->status === 'cancelled')
                                <span class="badge badge-sm bg-gradient-secondary">Dibatalkan</span>
                                @else
                                <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @if($bill->status !== 'paid' && $bill->status !== 'cancelled')
            <a href="{{ route('admin.bills.pay-form', $bill->id) }}" class="btn btn-sakti-primary">
                <i class="fas fa-money-bill-wave"></i> Catat Pembayaran
            </a>
            @endif
            <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    {{-- Siswa dalam KK Ini --}}
    @if($siblings->isNotEmpty())
    <div class="sakti-form-card mb-4">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-users"></i></div>
                <div><h3>Siswa dalam KK {{ $bill->family_card_number }}</h3><p>{{ $siblings->count() }} siswa terdaftar</p></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Nama Siswa</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">NISN</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Kelas</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siblings as $i => $sib)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><b>{{ $sib->name }}</b></td>
                        <td><code>{{ $sib->nisn }}</code></td>
                        <td>Kelas {{ $sib->grade_level }} – {{ $sib->major_name }}</td>
                        <td>
                             @if($sib->status === 'aktif')
                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                            @elseif($sib->status === 'lulus')
                                <span class="badge badge-sm bg-gradient-primary">Lulus</span>
                            @else
                                <span class="badge badge-sm bg-gradient-danger">Keluar</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Rincian per Anak --}}
    <div class="sakti-form-card mb-4">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-list-ul"></i></div>
                <div><h3>Rincian Tagihan per Anak</h3><p>Detail biaya yang ditagihkan</p></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">NISN</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Nama Siswa</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Jenis</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge badge-dot mr-4"><i class="bg-info"></i> {{ $item->nisn }}</span></td>
                        <td><b>{{ $item->student_name }}</b></td>
                        <td>{{ $item->payment_type_name }}</td>
                        <td>Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Pembayaran --}}
    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-history"></i></div>
                <div><h3>Riwayat Pembayaran</h3><p>Semua pembayaran yang sudah dicatat</p></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Tanggal</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Jumlah</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Metode</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">No. Referensi</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Diverifikasi Oleh</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</td>
                        <td><b>Rp {{ number_format($payment->amount, 0, ',', '.') }}</b></td>
                        <td>
                             @if($payment->payment_method === 'cash')
                            <span class="badge badge-sm bg-gradient-success">Tunai</span>
                            @else
                            <span class="badge badge-sm bg-gradient-info">QRIS</span>
                            @endif
                        </td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                        <td>{{ $payment->verified_by_name ?? '-' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                    @empty
                        <x-empty-state />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection