@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="d-flex mb-3">
        <a href="{{ route('admin.spp.index') }}" class="btn btn-sm btn-outline-white text-white border-white">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Pencarian
        </a>
    </div>
    <div class="row">
        {{-- Profil Siswa --}}
        <div class="col-xl-4 mb-4">
            <div class="card sakti-card shadow-sm h-100">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4 text-white" style="width: 100px; height: 100px; background: linear-gradient(135deg, #07814e 45%, #1e905f 100%); box-shadow: 0 4px 15px rgba(7, 129, 78, 0.3);">
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
                    <h5 class="mb-0 text-sakti-green font-weight-bold"><i class="fas fa-users text-warning"></i> Saudara ({{ $siblings->count() }} orang se-KK)</h5>
                    <div class="alert alert-warning mt-2 mb-0 py-2 px-3 border-0" style="font-size: 0.75rem;">
                        <i class="fas fa-info-circle"></i> Membayar salah satu = <b>Semua saudara otomatis lunas</b> di bulan yang sama.
                    </div>
                </div>
                <div class="card-body pt-3">
                    @foreach($siblings as $sib)
                    <div class="d-flex align-items-center mb-3 p-2 rounded {{ $sib->id == $student->id ? 'bg-light border-left border-primary' : '' }}">
                        <div class="avatar avatar-sm rounded-circle bg-{{ $sib->id == $student->id ? 'primary' : 'secondary' }} mr-3 text-white">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm mb-0 {{ $sib->id == $student->id ? 'font-weight-bold' : '' }}">{{ $sib->name }}</p>
                            <span class="text-xs text-muted">Kelas {{ $sib->grade_level }} {{ $sib->major_name }}</span>
                        </div>
                        @if($sib->id == $student->id)
                        <span class="badge badge-dot ml-auto"><i class="bg-primary"></i></span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Kalender SPP --}}
        <div class="col-xl-8">
            <div class="card sakti-card shadow-sm h-100">
                <div class="card-header pb-0 border-0 d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0 text-sakti-green font-weight-bold"><i class="fas fa-calendar-alt me-2"></i> Kalender SPP</h5>
                    <span class="badge btn-sakti-primary">Tahun Ajaran: {{ $bills->first()->academic_year_name ?? '-' }}</span>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="ps-4 text-sakti-green text-xs font-weight-bold text-uppercase">Periode Bulan</th>
                                    <th class="text-center text-sakti-green text-xs font-weight-bold text-uppercase">Nominal Tagihan</th>
                                    <th class="text-center text-sakti-green text-xs font-weight-bold text-uppercase">Status</th>
                                    <th class="text-center text-sakti-green text-xs font-weight-bold text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $currentMonth = now()->month;
                                    $currentYear  = now()->year;
                                @endphp
                                @forelse($bills as $bill)
                                @php
                                    $isCurrentMonth = ($bill->month == $currentMonth && $bill->year == $currentYear);
                                    $isPastDue = ($bill->status !== 'paid' && \Carbon\Carbon::parse($bill->due_date)->isPast());
                                @endphp
                                <tr class="{{ $isCurrentMonth ? 'bg-light' : '' }}">
                                    <td class="ps-4 align-middle">
                                        <span class="text-sm font-weight-bold text-dark">
                                            {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                        </span>
                                        @if($isCurrentMonth)
                                            <span class="badge bg-primary ms-2">Bulan Ini</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-dark font-weight-bold">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($bill->status === 'paid')
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Lunas</span>
                                        @elseif($bill->status === 'cancelled')
                                            <span class="badge bg-secondary"><i class="fas fa-ban"></i> Dibatalkan</span>
                                        @elseif($isPastDue)
                                            <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Terlambat</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($bill->status === 'paid')
                                            <span class="text-success text-sm font-weight-bold"><i class="fas fa-check-double me-1"></i> Verified</span>
                                        @elseif($bill->status === 'cancelled')
                                            <span class="text-secondary text-sm font-weight-bold"><i class="fas fa-minus-circle me-1"></i> Cancelled</span>
                                        @else
                                        <button type="button" 
                                                class="btn btn-sm btn-sakti-primary btn-pay mb-0" 
                                                data-id="{{ $bill->id }}" 
                                                data-period="{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}"
                                                data-amount="{{ number_format($bill->total_amount, 0, ',', '.') }}">
                                            <i class="fas fa-money-bill-wave me-1"></i> Bayar
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="alert alert-light text-center border m-3" role="alert">
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

{{-- Form Tersembunyi untuk Proses Bayar --}}
<form id="pay-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="payment_method" value="cash">
</form>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-pay').forEach(button => {
        button.addEventListener('click', function() {
            const billId = this.getAttribute('data-id');
            const period = this.getAttribute('data-period');
            const amount = this.getAttribute('data-amount');

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `Anda akan mencatat pembayaran SPP bulan <b>${period}</b> sebesar <b>Rp ${amount}</b> secara <b>LUNAS (TUNAI)</b>.<br><br><small class="text-success"><i class="fas fa-info-circle"></i> Sesuai kebijakan, saudara se-KK otomatis lunas.</small>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2dce89',
                cancelButtonColor: '#8898aa',
                confirmButtonText: 'Ya, Bayar Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('pay-form');
                    form.action = `/admin/spp/${billId}/bayar`;
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
