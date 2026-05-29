@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
    <style>
        /* ── Payment Type Selector ── */
        .pay-type-btn {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 20px;
            cursor: pointer;
            transition: all .25s;
            background: #fff;
            text-align: center;
        }

        .pay-type-btn:hover {
            border-color: #07814e;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(7, 129, 78, .12);
        }

        .pay-type-btn.active {
            border-color: #07814e;
            background: linear-gradient(135deg, #07814e, #2dce89);
            color: #fff;
            box-shadow: 0 8px 25px rgba(7, 129, 78, .30);
        }

        .pay-type-btn.active .pay-icon,
        .pay-type-btn.active small {
            color: #fff !important;
        }

        .pay-type-btn .pay-icon {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        /* ── Progress Bar Cicilan ── */
        .progress-cicilan {
            height: 8px;
            border-radius: 50px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-cicilan .fill {
            height: 100%;
            border-radius: 50px;
            transition: width .6s ease;
        }

        /* ── Bill Row ── */
        .bill-row {
            transition: background .2s;
        }

        .bill-row:hover {
            background: #f8fff9 !important;
        }

        /* ── Modal ── */
        #modalBayar .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
        }

        #modalBayar .modal-header {
            background: linear-gradient(135deg, #07814e, #2dce89);
            padding: 1.1rem 1.4rem;
        }

        #modalBayar .modal-body {
            padding: 1.4rem;
            background: #fafafa;
        }

        /* ── Amount Input ── */
        .amount-input-wrap {
            position: relative;
        }

        .amount-input-wrap .prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            color: #07814e;
            font-size: .95rem;
            pointer-events: none;
        }

        .amount-input-wrap input {
            padding-left: 46px;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: .5px;
        }

        /* ── Info Card ── */
        .modal-info-card {
            background: #fff;
            border: 1px solid #d1fae5;
            border-radius: 12px;
            padding: 1rem 1.1rem;
            margin-bottom: 1.1rem;
        }

        .modal-info-card .info-label {
            font-size: .72rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 3px;
        }

        .modal-info-card .info-value {
            font-size: .95rem;
            font-weight: 700;
        }

        /* ── Section Label ── */
        .modal-section-label {
            font-size: .72rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .5rem;
            display: block;
        }

        /* ── Pay Type Btn ── */
        .pay-type-btn {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 12px;
            cursor: pointer;
            transition: all .2s;
            background: #fff;
            text-align: center;
        }

        .pay-type-btn:hover {
            border-color: #07814e;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(7, 129, 78, .12);
        }

        .pay-type-btn.active {
            border-color: #07814e;
            background: linear-gradient(135deg, #07814e, #2dce89);
            color: #fff;
            box-shadow: 0 6px 18px rgba(7, 129, 78, .28);
        }

        .pay-type-btn.active small,
        .pay-type-btn.active .pay-icon {
            color: #fff !important;
        }

        .pay-type-btn .pay-icon {
            font-size: 1.6rem;
            margin-bottom: 6px;
        }

        /* ── Form Control ── */
        #modalBayar .form-control {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: .9rem;
        }

        #modalBayar .form-control:focus {
            border-color: #07814e;
            box-shadow: 0 0 0 3px rgba(7, 129, 78, .12);
        }

        #modalBayar label.form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .35rem;
        }

        /* ── Char Counter ── */
        .char-counter {
            font-size: .72rem;
            text-align: right;
            margin-top: 4px;
            transition: color .2s;
        }

        .char-counter.warn {
            color: #f59e0b;
        }

        .char-counter.danger {
            color: #ef4444;
        }

        /* ── Badge Partial ── */
        .badge-partial {
            background: linear-gradient(135deg, #f7931e, #f5a623);
            color: #fff;
        }

        .badge-paid {
            background: linear-gradient(135deg, #2dce89, #07814e);
            color: #fff;
        }

        .badge-unpaid {
            background: linear-gradient(135deg, #f5365c, #d63031);
            color: #fff;
        }

        .badge-late {
            background: linear-gradient(135deg, #fb6340, #e55039);
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt--6">

        {{-- @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius:12px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius:12px;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif --}}

        <!-- HEADER -->
        <div class="sakti-page-header mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative"
                style="z-index: 1;">
                <div>
                    <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                        <i class="fas fa-user-circle me-2"></i> Detail Pembayaran SPP
                    </h3>
                    <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                        Kelola tagihan dan catat pembayaran untuk siswa ini.
                    </p>
                </div>
                <a href="{{ route('admin.spp.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Profil Siswa --}}
            <div class="col-xl-4 mb-4">
                <div class="card sakti-card shadow-sm h-100">
                    <div class="card-body text-center pt-5 pb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4 text-white"
                            style="width:100px;height:100px;background:linear-gradient(135deg,#07814e,#2dce89);box-shadow:0 4px 15px rgba(7,129,78,.3);">
                            <i class="fas fa-user-graduate fa-3x"></i>
                        </div>
                        <h4 class="mb-1 text-dark font-weight-bold">{{ $student->name }}</h4>
                        <p class="text-sm text-muted mb-3">
                            <span class="badge bg-secondary">NISN: {{ $student->nisn }}</span>
                        </p>
                        <div class="d-flex flex-column gap-2 mb-4 text-start">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Nomor Kartu Keluarga</small>
                                <span class="text-dark font-weight-bold">{{ $student->family_card_number }}</span>
                            </div>
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Status Akademik</small>
                                <span class="badge bg-{{ $student->status === 'aktif' ? 'success' : 'secondary' }}">
                                    Siswa {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Saudara SeKK --}}
                @if ($siblings->count() > 1)
                    <div class="card sakti-card shadow-sm border-0 mt-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="mb-0 text-sakti-green font-weight-bold">
                                <i class="fas fa-users text-warning"></i> Saudara ({{ $siblings->count() }} orang se-KK)
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            @foreach ($siblings as $sib)
                                <div
                                    class="d-flex align-items-center mb-3 p-2 rounded {{ $sib->id == $student->id ? 'bg-light' : '' }}">
                                    <div
                                        class="avatar avatar-sm rounded-circle bg-{{ $sib->id == $student->id ? 'primary' : 'secondary' }} mr-3 text-white">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm mb-0 {{ $sib->id == $student->id ? 'font-weight-bold' : '' }}">
                                            {{ $sib->name }}</p>
                                        <span class="text-xs text-muted">Kelas {{ $sib->grade_level }}
                                            {{ $sib->major_name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kalender SPP --}}
            <div class="col-xl-8">
                <div class="card dashboard-card shadow-sm">
                    <div
                        class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                        <h3 class="section-title mb-0">
                            <i class="fas fa-calendar-alt me-2" style="color: var(--primary-green); opacity: .7;"></i>
                            Kalender SPP
                        </h3>
                        <span class="badge"
                            style="background: rgba(45,206,137,.15); color: #2dce89; font-weight: 600; padding: 6px 12px; font-size: 0.8rem;">{{ $bills->first()->academic_year_name ?? '-' }}</span>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table letters-table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Periode</th>
                                        <th class="text-center">Tagihan</th>
                                        <th class="text-center">Progres</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $currentMonth = now()->month;
                                        $currentYear = now()->year;
                                    @endphp
                                    @forelse($bills as $bill)
                                        @php
                                            $isCurrentMonth =
                                                $bill->month == $currentMonth && $bill->year == $currentYear;
                                            $isPastDue =
                                                $bill->status !== 'paid' &&
                                                \Carbon\Carbon::parse($bill->due_date)->isPast();
                                            $paidAmt = $bill->paid_amount ?? 0;
                                            $remaining = $bill->total_amount - $paidAmt;
                                            $pct =
                                                $bill->total_amount > 0
                                                    ? min(100, round(($paidAmt / $bill->total_amount) * 100))
                                                    : 0;
                                        @endphp
                                        <tr class="bill-row {{ $isCurrentMonth ? 'bg-light' : '' }}">
                                            <td class="text-center align-middle">
                                                <span class="text-sm font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                                </span>
                                                @if ($isCurrentMonth)
                                                    <span class="badge"
                                                        style="background: rgba(94, 114, 228, 0.1); color: #5e72e4; font-weight: 600; font-size: 0.65rem; margin-left: 6px;">Bulan
                                                        Ini</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-dark font-weight-bold">Rp
                                                    {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="align-middle text-center" style="min-width:130px;">
                                                @if ($bill->status === 'paid')
                                                    <div class="progress-cicilan">
                                                        <div class="fill bg-success" style="width:100%"></div>
                                                    </div>
                                                    <small class="text-success font-weight-bold">Lunas</small>
                                                @elseif($bill->status === 'partial')
                                                    <div class="progress-cicilan">
                                                        <div class="fill"
                                                            style="width:{{ $pct }}%;background:linear-gradient(90deg,#f7931e,#2dce89)">
                                                        </div>
                                                    </div>
                                                    <small class="text-warning font-weight-bold">{{ $pct }}% —
                                                        Sisa Rp {{ number_format($remaining, 0, ',', '.') }}</small>
                                                @else
                                                    <div class="progress-cicilan">
                                                        <div class="fill bg-danger" style="width:0%"></div>
                                                    </div>
                                                    <small class="text-muted">Belum ada cicilan</small>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($bill->status === 'paid')
                                                    <span class="badge badge-paid px-2 py-1"><i
                                                            class="fas fa-check me-1"></i>Lunas</span>
                                                @elseif($bill->status === 'partial')
                                                    <span class="badge badge-partial px-2 py-1"><i
                                                            class="fas fa-clock me-1"></i>Dicicil</span>
                                                @elseif($bill->status === 'cancelled')
                                                    <span class="badge bg-secondary px-2 py-1"><i
                                                            class="fas fa-ban me-1"></i>Batal</span>
                                                @elseif($isPastDue)
                                                    <span class="badge badge-late px-2 py-1"><i
                                                            class="fas fa-exclamation-triangle me-1"></i>Terlambat</span>
                                                @else
                                                    <span class="badge badge-unpaid px-2 py-1"><i
                                                            class="fas fa-times me-1"></i>Belum Bayar</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($bill->status === 'paid')
                                                    @php
                                                        $lastPayment = \Illuminate\Support\Facades\DB::table('payments')
                                                            ->where('bill_id', $bill->id)
                                                            ->orderByDesc('payment_date')
                                                            ->first();
                                                    @endphp
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <span class="text-success text-sm"><i
                                                                class="fas fa-check-double me-1"></i>Verified</span>
                                                        @if ($lastPayment)
                                                            <a href="{{ route('admin.spp.invoice', $lastPayment->id) }}"
                                                                class="btn btn-sm btn-outline-info mb-0" target="_blank"
                                                                title="Lihat Invoice">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @elseif($bill->status === 'cancelled')
                                                    <span class="text-secondary text-sm"><i
                                                            class="fas fa-minus-circle me-1"></i>Cancelled</span>
                                                @elseif($bill->status === 'partial')
                                                    @php
                                                        $lastPayment = \Illuminate\Support\Facades\DB::table('payments')
                                                            ->where('bill_id', $bill->id)
                                                            ->orderByDesc('payment_date')
                                                            ->first();
                                                    @endphp
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <button type="button"
                                                            class="btn btn-sm btn-sakti-primary btn-pay mb-0"
                                                            data-id="{{ $bill->id }}"
                                                            data-period="{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}"
                                                            data-total="{{ $bill->total_amount }}"
                                                            data-paid="{{ $paidAmt }}"
                                                            data-remaining="{{ $remaining }}">
                                                            <i class="fas fa-money-bill-wave me-1"></i>Lanjut Cicil
                                                        </button>
                                                        @if ($lastPayment)
                                                            <a href="{{ route('admin.spp.invoice', $lastPayment->id) }}"
                                                                class="btn btn-sm btn-outline-info mb-0" target="_blank"
                                                                title="Lihat Invoice">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-sakti-primary btn-pay mb-0"
                                                        data-id="{{ $bill->id }}"
                                                        data-period="{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}"
                                                        data-total="{{ $bill->total_amount }}"
                                                        data-paid="{{ $paidAmt }}"
                                                        data-remaining="{{ $remaining }}">
                                                        <i class="fas fa-money-bill-wave me-1"></i>Bayar
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <x-empty-state />
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== MODAL PEMBAYARAN ====== --}}
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
            <div class="modal-content">

                {{-- Header --}}
                <div class="modal-header border-0 text-white">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-white" id="modalBayarLabel"
                            style="font-size:1rem; color: #ffffff;">
                            Catat Pembayaran SPP
                        </h5>
                        <small id="modal-period-label" style="opacity:.8; font-size:.8rem;"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    {{-- Info Ringkas --}}
                    <div class="modal-info-card">
                        <div class="row text-center g-0">
                            <div class="col-4">
                                <div class="info-label">Total Tagihan</div>
                                <div class="info-value text-dark" id="info-total">—</div>
                            </div>
                            <div class="col-4" style="border-left:1px solid #d1fae5;border-right:1px solid #d1fae5;">
                                <div class="info-label">Sudah Dibayar</div>
                                <div class="info-value text-success" id="info-paid">—</div>
                            </div>
                            <div class="col-4">
                                <div class="info-label">Sisa Tagihan</div>
                                <div class="info-value text-danger" id="info-remaining">—</div>
                            </div>
                        </div>
                        <div class="progress-cicilan mt-3">
                            <div class="fill" id="info-bar"
                                style="background:linear-gradient(90deg,#f7931e,#2dce89);"></div>
                        </div>
                    </div>

                    {{-- Pilihan Tipe Pembayaran --}}
                    <span class="modal-section-label">Jenis Pembayaran</span>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="pay-type-btn active" id="btn-full" onclick="setPayType('full')">
                                <div class="pay-icon">💳</div>
                                <div style="font-size:.85rem;font-weight:600;">Bayar Lunas</div>
                                <small style="font-size:.75rem;">Bayar semua sisa sekaligus</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pay-type-btn" id="btn-partial" onclick="setPayType('partial')">
                                <div class="pay-icon">⏳</div>
                                <div style="font-size:.85rem;font-weight:600;">Cicil Sebagian</div>
                                <small style="font-size:.75rem;">Bayar sejumlah tertentu</small>
                            </div>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form id="pay-form" method="POST">
                        @csrf
                        <input type="hidden" name="payment_type" id="input-payment-type" value="full">
                        <input type="hidden" name="amount" id="input-amount-hidden" value="">

                        {{-- Jumlah Cicilan (hanya muncul saat cicil) --}}
                        <div id="amount-section" style="display:none;" class="mb-3">
                            <label class="form-label">Jumlah Cicilan</label>
                            <div class="amount-input-wrap">
                                <span class="prefix">Rp</span>
                                <input type="text" id="input-amount-display" class="form-control form-control-lg"
                                    placeholder="0" inputmode="numeric" autocomplete="off">
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">Masukkan nominal cicilan yang akan
                                dibayar.</small>
                            <div id="amount-error" class="text-danger" style="font-size:.75rem;display:none;"></div>
                        </div>

                        {{-- Lunas info --}}
                        <div id="full-info" class="mb-3 p-3 rounded-3"
                            style="background:#ecfdf5;border:1px solid #a7f3d0;font-size:.85rem;">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span class="text-success font-weight-bold">Akan membayar seluruh sisa tagihan
                                sekaligus.</span>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label">Metode Pembayaran</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="cash">💵 Tunai (Cash)</option>
                                    <option value="transfer">🏦 Transfer Bank</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tanggal Pembayaran</label>
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ now()->toDateString() }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <textarea name="notes" id="input-notes" class="form-control" rows="2" placeholder="Catatan tambahan..."
                                maxlength="300"></textarea>
                            <div class="char-counter" id="notes-counter">
                                <span id="notes-count">0</span>/300 karakter
                            </div>
                        </div>

                        <div class="d-grid gap-2 pt-1">
                            <button type="submit" class="btn btn-sakti-primary font-weight-bold" id="btn-submit"
                                style="border-radius:10px;padding:.65rem;font-size:.95rem;">
                                <i class="fas fa-save me-2"></i>Simpan Pembayaran
                            </button>
                            <button type="button" class="btn btn-light" style="border-radius:10px;font-size:.88rem;"
                                data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentRemaining = 0;

        function formatRp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function parseRpInput(str) {
            return parseInt(str.replace(/\D/g, '')) || 0;
        }

        function setPayType(type) {
            document.getElementById('input-payment-type').value = type;
            const amountSec = document.getElementById('amount-section');
            const fullInfo = document.getElementById('full-info');
            const btnFull = document.getElementById('btn-full');
            const btnPart = document.getElementById('btn-partial');
            const inpDisplay = document.getElementById('input-amount-display');
            const inpHidden = document.getElementById('input-amount-hidden');
            const amountError = document.getElementById('amount-error');

            if (type === 'full') {
                btnFull.classList.add('active');
                btnPart.classList.remove('active');
                amountSec.style.display = 'none';
                fullInfo.style.display = 'block';
                inpDisplay.value = '';
                inpHidden.value = '';
                amountError.style.display = 'none';
                document.getElementById('btn-submit').disabled = false;
            } else {
                btnPart.classList.add('active');
                btnFull.classList.remove('active');
                amountSec.style.display = 'block';
                fullInfo.style.display = 'none';
                inpDisplay.value = '';
                inpHidden.value = '';
                setTimeout(() => inpDisplay.focus(), 100);
            }
        }

        // Format Rupiah saat mengetik
        document.getElementById('input-amount-display').addEventListener('input', function() {
            const raw = parseRpInput(this.value);
            const errEl = document.getElementById('amount-error');
            const btn = document.getElementById('btn-submit');
            const hidden = document.getElementById('input-amount-hidden');

            // Reformat display
            if (raw > 0) {
                const formatted = raw.toLocaleString('id-ID');
                const cursorAt = this.selectionStart;
                const prevLen = this.value.length;
                this.value = formatted;
                // Pertahankan posisi kursor
                const diff = this.value.length - prevLen;
                this.setSelectionRange(cursorAt + diff, cursorAt + diff);
            } else {
                this.value = '';
            }

            hidden.value = raw;

            if (raw <= 0) {
                this.classList.add('is-invalid');
                errEl.textContent = 'Nominal harus lebih dari 0.';
                errEl.style.display = 'block';
                btn.disabled = true;
            } else if (raw > currentRemaining) {
                this.classList.add('is-invalid');
                errEl.textContent = 'Nominal melebihi sisa tagihan (' + formatRp(currentRemaining) + ').';
                errEl.style.display = 'block';
                btn.disabled = true;
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                errEl.style.display = 'none';
                btn.disabled = false;
            }
        });

        // Validasi form sebelum submit
        document.getElementById('pay-form').addEventListener('submit', function(e) {
            const type = document.getElementById('input-payment-type').value;
            const hidden = document.getElementById('input-amount-hidden');
            if (type === 'partial') {
                const val = parseInt(hidden.value) || 0;
                if (val <= 0 || val > currentRemaining) {
                    e.preventDefault();
                    document.getElementById('input-amount-display').focus();
                    return;
                }
            }
        });

        // Character counter untuk catatan
        const notesEl = document.getElementById('input-notes');
        const countEl = document.getElementById('notes-count');
        const counterEl = document.getElementById('notes-counter');

        notesEl.addEventListener('input', function() {
            const len = this.value.length;
            countEl.textContent = len;
            counterEl.classList.remove('warn', 'danger');
            if (len > 270) counterEl.classList.add('danger');
            else if (len > 200) counterEl.classList.add('warn');
            if (len > 300) this.value = this.value.substring(0, 300);
        });

        // Buka modal
        document.querySelectorAll('.btn-pay').forEach(btn => {
            btn.addEventListener('click', function() {
                const billId = this.dataset.id;
                const period = this.dataset.period;
                const total = parseInt(this.dataset.total);
                const paid = parseInt(this.dataset.paid);
                const remaining = parseInt(this.dataset.remaining);
                currentRemaining = remaining;

                document.getElementById('modal-period-label').textContent = 'Periode: ' + period;
                document.getElementById('info-total').textContent = formatRp(total);
                document.getElementById('info-paid').textContent = formatRp(paid);
                document.getElementById('info-remaining').textContent = formatRp(remaining);

                const pct = total > 0 ? Math.min(100, Math.round(paid / total * 100)) : 0;
                document.getElementById('info-bar').style.width = pct + '%';

                // Reset form
                document.getElementById('pay-form').action = '/admin/spp/' + billId + '/bayar';
                document.getElementById('pay-form').reset();
                countEl.textContent = '0';
                counterEl.classList.remove('warn', 'danger');
                document.getElementById('input-amount-display').classList.remove('is-invalid', 'is-valid');
                document.getElementById('amount-error').style.display = 'none';
                setPayType('full');

                new bootstrap.Modal(document.getElementById('modalBayar')).show();
            });
        });
    </script>
@endpush
