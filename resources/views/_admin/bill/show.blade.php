@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">

    {{-- Header Info --}}
    <div class="card mb-4">
        <div class="card-header border-0">
            <h3 class="mb-0">Detail Tagihan</h3>
        </div>
        <div class="card-body">
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
                            <td><b class="text-primary" style="font-size: 1.2em;">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</b></td>
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
                                @else
                                <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @if($bill->status !== 'paid')
            <a href="{{ route('admin.bills.pay-form', $bill->id) }}" class="btn btn-success">
                <i class="fas fa-money-bill-wave"></i> Catat Pembayaran
            </a>
            @endif
            <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    {{-- Siswa dalam KK Ini --}}
    @if($siblings->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header border-0 bg-light">
            <h3 class="mb-0"><i class="fas fa-users text-primary"></i> Siswa dalam Nomor KK Ini ({{ $bill->family_card_number }})</h3>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Status</th>
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
    <div class="card mb-4">
        <div class="card-header border-0">
            <h3 class="mb-0">Rincian Tagihan per Anak</h3>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
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
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Riwayat Pembayaran</h3>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>No. Referensi</th>
                        <th>Diverifikasi Oleh</th>
                        <th>Catatan</th>
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