@extends('student.layouts.app-mobile')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Tagihan & Pembayaran SPP
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Lihat detail tagihan dan lakukan pembayaran melalui QRIS.
                </p>
            </div>
        </div>
    </div>

    {{-- Info Siswa --}}
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="summary-card sc-blue h-100">
                <div class="sc-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="sc-label">Nama Siswa</div>
                <div class="sc-value" style="font-size: 1.1rem;">{{ $student->name }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 mb-3">
            <div class="summary-card sc-purple h-100">
                <div class="sc-icon"><i class="fas fa-id-card"></i></div>
                <div class="sc-label">NISN</div>
                <div class="sc-value">{{ $student->nisn }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-4 mb-3">
            @php
                $totalTunggakan = collect($bills)->filter(fn($b) => in_array($b->status, ['partial','unpaid']))->sum(fn($b) => $b->total_amount - $b->paid_amount);
            @endphp
            <div class="summary-card {{ $totalTunggakan > 0 ? 'sc-red' : 'sc-green' }} h-100">
                <div class="sc-icon"><i class="fas {{ $totalTunggakan > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i></div>
                <div class="sc-label">Total Tunggakan</div>
                <div class="sc-value" style="font-size: 1.1rem;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs mb-4" role="alert" style="border-radius:10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Daftar Tagihan --}}
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-receipt me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Riwayat Tagihan Bulanan
                    </h3>
                    <span class="badge px-3 py-2" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-weight: 700; border-radius: 50px;">{{ count($bills) }} Tagihan</span>
                </div>
                <div class="card-body p-0">

                    @forelse($bills as $index => $bill)
                    @php
                        $remaining = $bill->total_amount - $bill->paid_amount;
                        $percent   = $bill->total_amount > 0 ? round(($bill->paid_amount / $bill->total_amount) * 100) : 0;
                        $bulan     = \Carbon\Carbon::create()->month($bill->month)->translatedFormat('F');
                    @endphp

                    <div class="border-bottom">
                        <div class="px-3 px-md-4 py-3 d-flex align-items-center gap-2 gap-md-3 flex-wrap bill-row-inner"
                             style="cursor:pointer; transition:background .15s;"
                             onmouseover="this.style.background='#f8f9fa'"
                             onmouseout="this.style.background='#fff'"
                             data-bs-toggle="collapse"
                             data-bs-target="#billDetail-{{ $bill->id }}">

                            <div style="min-width:140px;">
                                <div class="font-weight-bold text-dark" style="font-size:14px;">{{ $bulan }} {{ $bill->year }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $bill->academic_year_name }}</div>
                            </div>

                            <div style="min-width:100px;">
                                @if($bill->status === 'paid')
                                    <span class="badge badge-sm bg-gradient-success">Lunas</span>
                                @elseif($bill->status === 'partial')
                                    <span class="badge badge-sm bg-gradient-warning">Sebagian</span>
                                @else
                                    <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                                @endif
                            </div>

                            <div class="flex-grow-1 d-none d-md-block" style="max-width:250px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-xs text-muted">Terbayar {{ $percent }}%</span>
                                </div>
                                <div class="progress" style="height:5px; border-radius:10px;">
                                    <div class="progress-bar bg-{{ $bill->status === 'paid' ? 'success' : ($bill->status === 'partial' ? 'warning' : 'danger') }}"
                                         style="width:{{ $percent }}%"></div>
                                </div>
                            </div>

                            <div class="ms-auto text-end">
                                <div class="font-weight-bold text-dark" style="font-size:14px;">
                                    Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                </div>
                                @if($remaining > 0)
                                <div class="text-danger" style="font-size:11px; font-weight:600;">
                                    Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}
                                </div>
                                @endif
                            </div>

                            <div class="text-muted"><i class="fas fa-chevron-down text-xs"></i></div>
                        </div>

                        <div class="collapse" id="billDetail-{{ $bill->id }}">
                            <div class="px-4 py-4" style="background:#f8f9fa;">
                                <div class="row g-4">

                                    {{-- Rincian --}}
                                    <div class="col-12 col-lg-4">
                                        <p class="text-xs text-uppercase text-muted font-weight-bold mb-3" style="letter-spacing:.5px;">Rincian Tagihan</p>
                                        <div class="card border-0 shadow-xs" style="border-radius:8px;">
                                            <div class="card-body p-3">
                                                @foreach($bill->items as $item)
                                                <div class="d-flex justify-content-between py-2 border-bottom">
                                                    <span class="text-sm text-dark">{{ $item->payment_type_name }}</span>
                                                    <span class="text-sm font-weight-bold text-dark">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                                </div>
                                                @endforeach
                                                <div class="d-flex justify-content-between pt-2">
                                                    <span class="text-sm font-weight-bold text-dark">Total</span>
                                                    <span class="text-sm font-weight-bold text-dark">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-muted mt-2 mb-0">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            Jatuh tempo: <strong>{{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') }}</strong>
                                        </p>
                                    </div>

                                    {{-- Riwayat --}}
                                    <div class="col-12 col-lg-4">
                                        <p class="text-xs text-uppercase text-muted font-weight-bold mb-3" style="letter-spacing:.5px;">Riwayat Pembayaran</p>
                                        @if(count($bill->payments) > 0)
                                             @foreach($bill->payments as $pay)
                                             <div class="d-flex align-items-start gap-3 mb-3">
                                                 <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-gradient-success rounded-circle"
                                                      style="width:32px;height:32px;">
                                                     <i class="fas fa-check text-white" style="font-size:10px;"></i>
                                                 </div>
                                                 <div class="flex-grow-1">
                                                     <div class="text-sm font-weight-bold text-dark">Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                                                     <div class="text-xs text-muted">
                                                         {{ \Carbon\Carbon::parse($pay->payment_date)->translatedFormat('d M Y') }}
                                                         @if($pay->payment_method)
                                                             · <span class="text-uppercase">{{ $pay->payment_method }}</span>
                                                         @endif
                                                     </div>
                                                     @if($pay->reference_number)
                                                     <div class="text-xs text-muted">Ref: {{ $pay->reference_number }}</div>
                                                     @endif
                                                     <div class="mt-1">
                                                         <a href="{{ route('student.invoice.show', $pay->id) }}"
                                                            class="text-xs text-primary font-weight-bold"
                                                            target="_blank">
                                                             <i class="fas fa-file-invoice me-1"></i>Lihat Invoice
                                                         </a>
                                                     </div>
                                                 </div>
                                             </div>
                                             @endforeach
                                        @else
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-receipt fa-2x mb-2 opacity-5"></i>
                                                <p class="text-xs mb-0">Belum ada pembayaran</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Aksi Bayar --}}
                                    <div class="col-12 col-lg-4 d-flex flex-column align-items-center justify-content-center text-center">
                                        @if($bill->status !== 'paid')
                                        <div class="card border-0 w-100" style="border-radius:10px; border: 1px solid #e9ecef;">
                                            <div class="card-body p-4">
                                                <p class="text-xs text-muted text-uppercase font-weight-bold mb-3" style="letter-spacing:.5px;">Bayar Tagihan</p>
                                                <p class="text-sm text-muted mb-4">Bayar tagihan ini secara instan menggunakan QRIS dari aplikasi dompet digital Anda.</p>
                                                <button
                                                    class="btn btn-success btn-sm w-100 btn-pay-qris"
                                                    data-bill-id="{{ $bill->id }}"
                                                    data-bill-amount="{{ $remaining }}"
                                                    data-bill-label="{{ $bulan }} {{ $bill->year }}"
                                                    style="border-radius:8px; font-weight:700; padding:10px;">
                                                    <i class="fas fa-qrcode me-2"></i> Bayar via QRIS
                                                </button>
                                                <p class="text-xs text-muted mt-2 mb-0">
                                                    <i class="fas fa-lock me-1 text-success"></i> Aman & terenkripsi
                                                </p>
                                            </div>
                                        </div>
                                        @else
                                        <div>
                                            <div class="d-flex align-items-center justify-content-center bg-gradient-success rounded-circle mx-auto mb-3"
                                                 style="width:64px;height:64px;">
                                                <i class="fas fa-check text-white fa-xl"></i>
                                            </div>
                                            <h6 class="font-weight-bold text-success mb-1">Tagihan Lunas</h6>
                                            <p class="text-xs text-muted mb-0">Pembayaran telah dikonfirmasi.</p>
                                        </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3 opacity-5"></i>
                        <h6 class="text-muted">Tidak Ada Tagihan</h6>
                        <p class="text-sm text-muted">Belum ada tagihan yang tercatat untuk akun Anda.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL QRIS KUSTOM (Tanpa UI Midtrans) ===== --}}
<div class="modal fade" id="modalQris" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">

            {{-- State: Loading --}}
            <div id="stateLoading" class="text-center p-5">
                <div class="spinner-border text-success mb-3" style="width:36px;height:36px;" role="status"></div>
                <p class="text-sm font-weight-bold text-dark mb-1">Membuat kode QRIS...</p>
                <p class="text-xs text-muted mb-0">Mohon tunggu sebentar</p>
            </div>

            {{-- State: Tampilkan QR --}}
            <div id="stateQr" style="display:none;">
                {{-- Header --}}
                <div class="px-4 pt-4 pb-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0">Bayar via QRIS</h6>
                        <p class="text-xs text-muted mb-0" id="qrBillLabel">SPP -</p>
                    </div>
                    <button type="button" class="btn-close" id="btnCloseQr"></button>
                </div>

                <div class="px-4 py-3">
                    {{-- Nominal --}}
                    <div class="d-flex align-items-center justify-content-between bg-light p-3 mb-3" style="border-radius:10px;">
                        <span class="text-sm text-muted font-weight-bold">Total Bayar</span>
                        <span class="font-weight-bold text-dark" id="qrAmount" style="font-size:18px;">Rp 0</span>
                    </div>

                    {{-- QR Code (di-render via QRCode.js dari qr_string) --}}
                    <div class="text-center mb-3">
                        <div style="display:inline-block; padding:12px; border:2px solid #e9ecef; border-radius:12px; background:#fff;">
                            <div id="qrCodeContainer" style="width:220px;height:220px;"></div>
                        </div>
                        <div class="mt-2">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/QRIS_logo.svg/120px-QRIS_logo.svg.png"
                                 alt="QRIS" style="height:22px; opacity:.8;">
                        </div>
                        {{-- Tombol copy untuk testing di simulator --}}
                        <div class="mt-2">
                            <button type="button" id="copySimulatorUrl" class="btn btn-sm btn-outline-primary" style="font-size: 10px; padding: 4px 8px; border-radius: 6px;">
                                <i class="fas fa-copy"></i> Salin URL Simulator
                            </button>
                        </div>
                    </div>

                    {{-- Timer --}}
                    <div class="text-center mb-3">
                        <span class="text-xs text-muted">Kode kedaluwarsa dalam</span>
                        <span class="font-weight-bold text-danger ms-1" id="qrTimer">15:00</span>
                    </div>

                    {{-- Instruksi --}}
                    <div class="border-top pt-3">
                        <p class="text-xs text-muted font-weight-bold mb-2">Cara membayar:</p>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-gradient-success" style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;">1</span>
                            <span class="text-xs text-dark">Buka aplikasi dompet digital (GoPay, OVO, Dana, dll)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-gradient-success" style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;">2</span>
                            <span class="text-xs text-dark">Pilih menu "Scan" atau "Bayar"</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-gradient-success" style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;">3</span>
                            <span class="text-xs text-dark">Arahkan kamera ke QR Code di atas</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-gradient-success" style="width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0;">4</span>
                            <span class="text-xs text-dark">Konfirmasi pembayaran di aplikasi Anda</span>
                        </div>
                    </div>

                    {{-- Status polling indicator --}}
                    <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top">
                        <div class="spinner-border spinner-border-sm text-success flex-shrink-0" style="width:14px;height:14px;border-width:2px;"></div>
                        <span class="text-xs text-muted">Menunggu konfirmasi pembayaran...</span>
                    </div>
                </div>
            </div>

            {{-- State: Error --}}
            <div id="stateError" class="text-center p-5" style="display:none;">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-sm font-weight-bold text-dark mb-1">Gagal Membuat QRIS</p>
                <p class="text-xs text-muted mb-3" id="errorMsg">Terjadi kesalahan.</p>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCloseError" style="border-radius:8px;">Tutup</button>
            </div>

            {{-- State: Sukses --}}
            <div id="stateSuccess" class="text-center p-5" style="display:none;">
                <div class="d-flex align-items-center justify-content-center bg-gradient-success rounded-circle mx-auto mb-3"
                     style="width:72px;height:72px;">
                    <i class="fas fa-check text-white" style="font-size:28px;"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-1">Pembayaran Berhasil!</h6>
                <p class="text-xs text-muted mb-3">Tagihan Anda telah terlunasi.</p>
                <div class="spinner-border spinner-border-sm text-success"></div>
                <p class="text-xs text-muted mt-2 mb-0">Memperbarui halaman...</p>
            </div>

        </div>
    </div>
</div>

@push('scripts')
{{-- Library QRCode.js: generate QR di browser dari string, tanpa bergantung service eksternal --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
let currentBillId = null;
let pollInterval  = null;
let timerInterval = null;
const bsModal     = new bootstrap.Modal(document.getElementById('modalQris'));

// Klik tombol bayar
document.querySelectorAll('.btn-pay-qris').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        openQris(this.dataset.billId, parseInt(this.dataset.billAmount), this.dataset.billLabel);
    });
});

function openQris(billId, amount, label) {
    currentBillId = billId;
    showState('loading');
    bsModal.show();

    fetch("{{ url('student/bills') }}/" + billId + "/pay-qris", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            showError(data.message || 'Gagal membuat kode QRIS.');
            return;
        }

        document.getElementById('qrBillLabel').textContent = 'SPP ' + label;
        document.getElementById('qrAmount').textContent    = 'Rp ' + amount.toLocaleString('id-ID');

        // Hapus QR lama dan buat baru dari qr_string
        const container = document.getElementById('qrCodeContainer');
        container.innerHTML = '';
        new QRCode(container, {
            text: data.qr_string,
            width: 220,
            height: 220,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });

        // Event listener untuk copy URL ke simulator
        const copyBtn = document.getElementById('copySimulatorUrl');
        copyBtn.onclick = function() {
            navigator.clipboard.writeText(data.qr_image_url).then(() => {
                const oldHtml = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
                copyBtn.classList.replace('btn-outline-primary', 'btn-success');
                setTimeout(() => {
                    copyBtn.innerHTML = oldHtml;
                    copyBtn.classList.replace('btn-success', 'btn-outline-primary');
                }, 2000);
            });
        };
        console.log("=== URL UNTUK SIMULATOR MIDTRANS ===");
        console.log(data.qr_image_url);

        showState('qr');
        startTimer(data.expires_in || 900);
        startPolling(billId);
    })
    .catch(() => showError('Koneksi gagal. Periksa jaringan internet Anda.'));
}

function closeModal() {
    bsModal.hide();
    stopAll();
}

document.getElementById('btnCloseQr').addEventListener('click', closeModal);
document.getElementById('btnCloseError').addEventListener('click', closeModal);

// Reset saat modal ditutup paksa
document.getElementById('modalQris').addEventListener('hidden.bs.modal', stopAll);

function showState(state) {
    document.getElementById('stateLoading').style.display = state === 'loading' ? 'block' : 'none';
    document.getElementById('stateQr').style.display      = state === 'qr'      ? 'block' : 'none';
    document.getElementById('stateError').style.display   = state === 'error'   ? 'block' : 'none';
    document.getElementById('stateSuccess').style.display = state === 'success' ? 'block' : 'none';
}

function showError(msg) {
    document.getElementById('errorMsg').textContent = msg;
    showState('error');
    stopAll();
}

// Timer hitung mundur
function startTimer(seconds) {
    stopTimer();
    let remaining = seconds;
    updateTimerDisplay(remaining);
    timerInterval = setInterval(() => {
        remaining--;
        updateTimerDisplay(remaining);
        if (remaining <= 0) {
            stopTimer();
            showError('Kode QRIS sudah kedaluwarsa. Silakan buat ulang.');
        }
    }, 1000);
}

function updateTimerDisplay(sec) {
    const m = String(Math.floor(sec / 60)).padStart(2, '0');
    const s = String(sec % 60).padStart(2, '0');
    const el = document.getElementById('qrTimer');
    if (el) el.textContent = m + ':' + s;
}

function stopTimer() {
    if (timerInterval) { clearInterval(timerInterval); timerInterval = null; }
}

// Polling status pembayaran tiap 3 detik
function startPolling(billId) {
    stopPolling();
    pollInterval = setInterval(() => {
        fetch("{{ url('student/bills') }}/" + billId + "/payment-status", {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.is_paid) {
                stopAll();
                showState('success');
                setTimeout(() => window.location.reload(), 2500);
            }
        })
        .catch(() => {});
    }, 3000);

    // Safety stop setelah 20 menit
    setTimeout(stopPolling, 20 * 60 * 1000);
}

function stopPolling() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
}

function stopAll() {
    stopPolling();
    stopTimer();
}
</script>
@endpush
@endsection
