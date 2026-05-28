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
        .pay-type-btn:hover { border-color: #07814e; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(7,129,78,.12); }
        .pay-type-btn.active { border-color: #07814e; background: linear-gradient(135deg,#07814e,#2dce89); color: #fff; box-shadow: 0 8px 25px rgba(7,129,78,.30); }
        .pay-type-btn.active .pay-icon, .pay-type-btn.active small { color: #fff !important; }
        .pay-type-btn .pay-icon { font-size: 1.8rem; margin-bottom: 8px; }

        /* ── Progress Bar Cicilan ── */
        .progress-cicilan { height: 8px; border-radius: 50px; background: #e9ecef; overflow: hidden; }
        .progress-cicilan .fill { height: 100%; border-radius: 50px; transition: width .6s ease; }

        /* ── Bill Row ── */
        .bill-row { transition: background .2s; }
        .bill-row:hover { background: #f8fff9 !important; }

        /* ── Modal ── */
        #modalBayar .modal-content { border-radius: 20px; border: none; overflow: hidden; }
        #modalBayar .modal-header { background: linear-gradient(135deg,#07814e,#2dce89); }

        /* ── Amount Input ── */
        .amount-input-wrap { position: relative; }
        .amount-input-wrap .prefix { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #6c757d; }
        .amount-input-wrap input { padding-left: 42px; font-size: 1.2rem; font-weight: 700; }

        /* ── Badge Partial ── */
        .badge-partial { background: linear-gradient(135deg,#f7931e,#f5a623); color:#fff; }
        .badge-paid    { background: linear-gradient(135deg,#2dce89,#07814e); color:#fff; }
        .badge-unpaid  { background: linear-gradient(135deg,#f5365c,#d63031); color:#fff; }
        .badge-late    { background: linear-gradient(135deg,#fb6340,#e55039); color:#fff; }
    </style>
@endpush

@section('content')
<div class="container-fluid mt--6">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius:12px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius:12px;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- HEADER -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
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
            @if($siblings->count() > 1)
            <div class="card sakti-card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 text-sakti-green font-weight-bold">
                        <i class="fas fa-users text-warning"></i> Saudara ({{ $siblings->count() }} orang se-KK)
                    </h5>
                    <div class="alert alert-warning mt-2 mb-0 py-2 px-3 border-0" style="font-size:.75rem;">
                        <i class="fas fa-info-circle"></i> Membayar <b>LUNAS</b> salah satu = <b>Semua saudara otomatis lunas</b>.
                    </div>
                </div>
                <div class="card-body pt-3">
                    @foreach($siblings as $sib)
                    <div class="d-flex align-items-center mb-3 p-2 rounded {{ $sib->id == $student->id ? 'bg-light' : '' }}">
                        <div class="avatar avatar-sm rounded-circle bg-{{ $sib->id == $student->id ? 'primary' : 'secondary' }} mr-3 text-white">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm mb-0 {{ $sib->id == $student->id ? 'font-weight-bold' : '' }}">{{ $sib->name }}</p>
                            <span class="text-xs text-muted">Kelas {{ $sib->grade_level }} {{ $sib->major_name }}</span>
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
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-calendar-alt me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Kalender SPP
                    </h3>
                    <span class="badge" style="background: rgba(45,206,137,.15); color: #2dce89; font-weight: 600; padding: 6px 12px; font-size: 0.8rem;">{{ $bills->first()->academic_year_name ?? '-' }}</span>
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
                                @php $currentMonth = now()->month; $currentYear = now()->year; @endphp
                                @forelse($bills as $bill)
                                @php
                                    $isCurrentMonth = ($bill->month == $currentMonth && $bill->year == $currentYear);
                                    $isPastDue = ($bill->status !== 'paid' && \Carbon\Carbon::parse($bill->due_date)->isPast());
                                    $paidAmt = $bill->paid_amount ?? 0;
                                    $remaining = $bill->total_amount - $paidAmt;
                                    $pct = $bill->total_amount > 0 ? min(100, round($paidAmt / $bill->total_amount * 100)) : 0;
                                @endphp
                                <tr class="bill-row {{ $isCurrentMonth ? 'bg-light' : '' }}">
                                    <td class="text-center align-middle">
                                        <span class="text-sm font-weight-bold text-dark">
                                            {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                        </span>
                                        @if($isCurrentMonth)
                                            <span class="badge" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4; font-weight: 600; font-size: 0.65rem; margin-left: 6px;">Bulan Ini</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-dark font-weight-bold">Rp {{ number_format($bill->total_amount,0,',','.') }}</span>
                                    </td>
                                    <td class="align-middle text-center" style="min-width:130px;">
                                        @if($bill->status === 'paid')
                                            <div class="progress-cicilan"><div class="fill bg-success" style="width:100%"></div></div>
                                            <small class="text-success font-weight-bold">Lunas</small>
                                        @elseif($bill->status === 'partial')
                                            <div class="progress-cicilan"><div class="fill" style="width:{{ $pct }}%;background:linear-gradient(90deg,#f7931e,#2dce89)"></div></div>
                                            <small class="text-warning font-weight-bold">{{ $pct }}% — Sisa Rp {{ number_format($remaining,0,',','.') }}</small>
                                        @else
                                            <div class="progress-cicilan"><div class="fill bg-danger" style="width:0%"></div></div>
                                            <small class="text-muted">Belum ada cicilan</small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($bill->status === 'paid')
                                            <span class="badge badge-paid px-2 py-1"><i class="fas fa-check me-1"></i>Lunas</span>
                                        @elseif($bill->status === 'partial')
                                            <span class="badge badge-partial px-2 py-1"><i class="fas fa-clock me-1"></i>Dicicil</span>
                                        @elseif($bill->status === 'cancelled')
                                            <span class="badge bg-secondary px-2 py-1"><i class="fas fa-ban me-1"></i>Batal</span>
                                        @elseif($isPastDue)
                                            <span class="badge badge-late px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i>Terlambat</span>
                                        @else
                                            <span class="badge badge-unpaid px-2 py-1"><i class="fas fa-times me-1"></i>Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($bill->status === 'paid')
                                            @php
                                                $lastPayment = \Illuminate\Support\Facades\DB::table('payments')
                                                    ->where('bill_id', $bill->id)
                                                    ->orderByDesc('payment_date')
                                                    ->first();
                                            @endphp
                                            <div class="d-flex gap-1 justify-content-center">
                                                <span class="text-success text-sm"><i class="fas fa-check-double me-1"></i>Verified</span>
                                                @if($lastPayment)
                                                <a href="{{ route('admin.spp.invoice', $lastPayment->id) }}"
                                                   class="btn btn-sm btn-outline-info mb-0"
                                                   target="_blank"
                                                   title="Lihat Invoice">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                @endif
                                            </div>
                                        @elseif($bill->status === 'cancelled')
                                            <span class="text-secondary text-sm"><i class="fas fa-minus-circle me-1"></i>Cancelled</span>
                                        @elseif($bill->status === 'partial')
                                            @php
                                                $lastPayment = \Illuminate\Support\Facades\DB::table('payments')
                                                    ->where('bill_id', $bill->id)
                                                    ->orderByDesc('payment_date')
                                                    ->first();
                                            @endphp
                                            <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                <button type="button" class="btn btn-sm btn-sakti-primary btn-pay mb-0"
                                                    data-id="{{ $bill->id }}"
                                                    data-period="{{ \Carbon\Carbon::createFromDate($bill->year,$bill->month,1)->translatedFormat('F Y') }}"
                                                    data-total="{{ $bill->total_amount }}"
                                                    data-paid="{{ $paidAmt }}"
                                                    data-remaining="{{ $remaining }}">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Lanjut Cicil
                                                </button>
                                                @if($lastPayment)
                                                <a href="{{ route('admin.spp.invoice', $lastPayment->id) }}"
                                                   class="btn btn-sm btn-outline-info mb-0"
                                                   target="_blank"
                                                   title="Lihat Invoice">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                @endif
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-sm btn-sakti-primary btn-pay mb-0"
                                                data-id="{{ $bill->id }}"
                                                data-period="{{ \Carbon\Carbon::createFromDate($bill->year,$bill->month,1)->translatedFormat('F Y') }}"
                                                data-total="{{ $bill->total_amount }}"
                                                data-paid="{{ $paidAmt }}"
                                                data-remaining="{{ $remaining }}">
                                                <i class="fas fa-money-bill-wave me-1"></i>Bayar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-light text-center border m-3">
                                                <i class="fas fa-info-circle text-muted mb-2 fa-lg"></i>
                                                <p class="text-sm text-muted mb-0">Belum ada tagihan untuk siswa ini.</p>
                                            </div>
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

{{-- ====== MODAL PEMBAYARAN ====== --}}
<div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <div class="modal-content shadow-lg">

            {{-- Header --}}
            <div class="modal-header border-0 text-white">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0" id="modalBayarLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>Catat Pembayaran SPP
                    </h5>
                    <small id="modal-period-label" style="opacity:.85;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4">

                {{-- Info Ringkas --}}
                <div class="rounded-3 p-3 mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-xs text-muted mb-1">Total Tagihan</div>
                            <div class="font-weight-bold text-dark" id="info-total"></div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-muted mb-1">Sudah Dibayar</div>
                            <div class="font-weight-bold text-success" id="info-paid"></div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-muted mb-1">Sisa Tagihan</div>
                            <div class="font-weight-bold text-danger" id="info-remaining"></div>
                        </div>
                    </div>
                    <div class="progress-cicilan mt-3">
                        <div class="fill" id="info-bar" style="background:linear-gradient(90deg,#f7931e,#2dce89);"></div>
                    </div>
                </div>

                {{-- Pilihan Tipe Pembayaran --}}
                <label class="text-xs font-weight-bold text-uppercase text-muted mb-2 d-block">Pilih Metode Pembayaran</label>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="pay-type-btn active" id="btn-full" onclick="setPayType('full')">
                            <div class="pay-icon text-sakti-green">💳</div>
                            <div class="font-weight-bold text-sm">Bayar Lunas</div>
                            <small class="text-muted">Bayar semua sisa sekaligus</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pay-type-btn" id="btn-partial" onclick="setPayType('partial')">
                            <div class="pay-icon text-warning">⏳</div>
                            <div class="font-weight-bold text-sm">Cicil Sebagian</div>
                            <small class="text-muted">Bayar sejumlah tertentu</small>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form id="pay-form" method="POST">
                    @csrf
                    <input type="hidden" name="payment_type" id="input-payment-type" value="full">

                    {{-- Jumlah Bayar (hanya muncul saat cicil) --}}
                    <div id="amount-section" style="display:none;" class="mb-3">
                        <label class="form-label text-sm font-weight-bold">Jumlah Cicilan (Rp)</label>
                        <div class="amount-input-wrap">
                            <span class="prefix">Rp</span>
                            <input type="number" name="amount" id="input-amount"
                                   class="form-control form-control-lg"
                                   placeholder="0" min="1" step="1000">
                        </div>
                        <small class="text-muted">Masukkan nominal cicilan yang akan dibayar.</small>
                    </div>

                    {{-- Lunas info --}}
                    <div id="full-info" class="mb-3 p-3 rounded-3" style="background:#ecfdf5;border:1px solid #a7f3d0;">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span class="text-sm text-success font-weight-bold">Akan membayar seluruh sisa tagihan sekaligus.</span>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">Metode Pembayaran</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash">💵 Tunai (Cash)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">Tanggal Pembayaran</label>
                            <input type="date" name="payment_date" class="form-control"
                                   value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm font-weight-bold">No. Referensi <small class="text-muted">(Opsional)</small></label>
                        <input type="text" name="reference_number" class="form-control" placeholder="KW-2026-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm font-weight-bold">Catatan <small class="text-muted">(Opsional)</small></label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-sakti-primary btn-lg font-weight-bold" id="btn-submit">
                            <i class="fas fa-save me-2"></i>Simpan Pembayaran
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
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

    function setPayType(type) {
        document.getElementById('input-payment-type').value = type;
        const amountSec = document.getElementById('amount-section');
        const fullInfo  = document.getElementById('full-info');
        const btnFull   = document.getElementById('btn-full');
        const btnPart   = document.getElementById('btn-partial');
        const inp       = document.getElementById('input-amount');

        if (type === 'full') {
            btnFull.classList.add('active'); btnPart.classList.remove('active');
            amountSec.style.display = 'none'; fullInfo.style.display = 'block';
            inp.removeAttribute('required'); inp.value = '';
        } else {
            btnPart.classList.add('active'); btnFull.classList.remove('active');
            amountSec.style.display = 'block'; fullInfo.style.display = 'none';
            inp.setAttribute('required', 'required');
            inp.setAttribute('max', currentRemaining);
            inp.focus();
        }
    }

    document.querySelectorAll('.btn-pay').forEach(btn => {
        btn.addEventListener('click', function () {
            const billId    = this.dataset.id;
            const period    = this.dataset.period;
            const total     = parseInt(this.dataset.total);
            const paid      = parseInt(this.dataset.paid);
            const remaining = parseInt(this.dataset.remaining);
            currentRemaining = remaining;

            document.getElementById('modal-period-label').textContent = 'Periode: ' + period;
            document.getElementById('info-total').textContent     = formatRp(total);
            document.getElementById('info-paid').textContent      = formatRp(paid);
            document.getElementById('info-remaining').textContent = formatRp(remaining);

            const pct = total > 0 ? Math.min(100, Math.round(paid / total * 100)) : 0;
            document.getElementById('info-bar').style.width = pct + '%';

            document.getElementById('pay-form').action = '/admin/spp/' + billId + '/bayar';
            // Reset ke full
            setPayType('full');

            const modal = new bootstrap.Modal(document.getElementById('modalBayar'));
            modal.show();
        });
    });

    // Validasi amount tidak melebihi sisa
    document.getElementById('input-amount').addEventListener('input', function () {
        const val = parseInt(this.value) || 0;
        const btnSubmit = document.getElementById('btn-submit');
        if (val > currentRemaining) {
            this.classList.add('is-invalid');
            btnSubmit.disabled = true;
        } else {
            this.classList.remove('is-invalid');
            btnSubmit.disabled = false;
        }
    });
</script>
@endpush
