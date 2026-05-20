@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Welcome Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card bg-gradient-success border-0 shadow-lg text-white">
                <div class="card-body p-4">
                    <div class="d-md-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-white mb-1 font-weight-bold">Halo, {{ $student->name }}! 👋</h3>
                            <p class="text-white opacity-8 mb-0">Selamat datang kembali di portal siswa SAKTI. Di sini Anda dapat memantau pembayaran SPP dan jadwal pelajaran Anda.</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <span class="badge bg-white text-success px-3 py-2 font-weight-bold" style="font-size: 0.9rem;">
                                Kelas {{ $student->grade_level }} - {{ $student->classroom_name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial & Info Widgets -->
    <div class="row">
        <!-- Card 1: Total Tagihan -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Total Bulan Tagihan</p>
                                <h4 class="font-weight-bolder mb-0 text-dark mt-1">
                                    {{ $totalBillsCount }} Bulan
                                </h4>
                                <span class="text-xs text-muted">Seluruh tagihan semester ini</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center rounded-circle d-flex align-items-center justify-content-center float-end">
                                <i class="fas fa-file-invoice text-white" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Tunggakan -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Total Tunggakan</p>
                                <h4 class="font-weight-bolder mb-0 text-danger mt-1">
                                    Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                                </h4>
                                <span class="text-xs text-danger font-weight-bold">Harus segera dilunasi</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow text-center rounded-circle d-flex align-items-center justify-content-center float-end">
                                <i class="fas fa-exclamation-circle text-white" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Terbayar -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Total Terbayar</p>
                                <h4 class="font-weight-bolder mb-0 text-success mt-1">
                                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                </h4>
                                <span class="text-xs text-success font-weight-bold">Pembayaran yang sah</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center rounded-circle d-flex align-items-center justify-content-center float-end">
                                <i class="fas fa-check-circle text-white" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Jurusan -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Jurusan Anda</p>
                                <h5 class="font-weight-bolder mb-0 text-dark mt-1" style="font-size: 1.1rem;">
                                    {{ $student->major_name }}
                                </h5>
                                <span class="text-xs text-muted">Program keahlian terdaftar</span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center rounded-circle d-flex align-items-center justify-content-center float-end">
                                <i class="fas fa-graduation-cap text-white" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Profile & QR Quickview -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header pb-0 bg-transparent border-0">
                    <h6 class="font-weight-bold text-dark mb-0">Kartu Pelajar Digital</h6>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-4">
                    <div class="bg-light p-3 border-radius-lg mb-3 shadow-inner">
                        <!-- Simulated QR Code / Barcode -->
                        <div class="d-flex flex-column align-items-center justify-content-center bg-white p-3 border-radius-md" style="width: 160px; height: 160px; border: 1px dashed #ced4da;">
                            <i class="fas fa-qrcode fa-5x text-dark"></i>
                            <span class="text-xs font-weight-bold text-muted mt-2">{{ $student->qr_code }}</span>
                        </div>
                    </div>
                    <h5 class="font-weight-bold mb-0 text-dark">{{ $student->name }}</h5>
                    <p class="text-sm text-muted mb-3">NISN: {{ $student->nisn }}</p>
                    <hr class="horizontal dark w-100 my-2">
                    <div class="text-start w-100 px-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs text-muted font-weight-bold">Nomor KK:</span>
                            <span class="text-xs text-dark font-weight-bold">{{ $student->family_card_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs text-muted font-weight-bold">No. Identitas:</span>
                            <span class="text-xs text-dark font-weight-bold">{{ $student->id_number ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-xs text-muted font-weight-bold">Status:</span>
                            <span class="badge bg-success-soft text-success text-xxs font-weight-bold uppercase">{{ $student->status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments Table -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header pb-0 bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-dark mb-0">Histori Pembayaran Terkini</h6>
                    <a href="{{ route('student.bills') }}" class="btn btn-link text-success text-xs font-weight-bold p-0">Semua Tagihan &rarr;</a>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Periode Tagihan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Bayar</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Metode</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jumlah</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Referensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <span class="text-xs font-weight-bold text-dark">
                                                {{ \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F') }} {{ $payment->year }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-muted">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d M Y H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark text-xxs font-weight-bold text-uppercase">
                                                {{ $payment->payment_method }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-success">
                                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-xs text-muted font-weight-bold">
                                                {{ $payment->reference_number ?? '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                                            <p class="text-xs text-muted mb-0">Belum ada riwayat pembayaran yang tercatat.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
