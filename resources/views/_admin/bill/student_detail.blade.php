@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="row">
        {{-- Profil Siswa --}}
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center pt-5 pb-4">
                    <div class="rounded-circle bg-gradient-primary d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width:80px;height:80px;">
                        <i class="fas fa-user-graduate text-white fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $student->name }}</h4>
                    <p class="text-sm text-muted mb-2">NISN: <code>{{ $student->nisn }}</code></p>
                    <p class="text-sm text-muted mb-0">No. KK: <code>{{ $student->family_card_number }}</code></p>
                    
                    <div class="mt-4">
                        <span class="badge badge-lg bg-{{ $student->status === 'aktif' ? 'success' : 'secondary' }} text-white px-4">
                            Siswa {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Saudara SeKK --}}
            @if($siblings->count() > 1)
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-users text-warning"></i> Saudara ({{ $siblings->count() }} orang se-KK)</h5>
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

            <a href="{{ route('admin.spp.index') }}" class="btn btn-outline-secondary btn-block mt-4 shadow-none">
                <i class="fas fa-arrow-left"></i> Kembali ke Pencarian
            </a>
        </div>

        {{-- Kalender SPP --}}
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary"><i class="fas fa-calendar-alt"></i> Kalender SPP</h4>
                    <span class="text-xs text-muted">Tahun Ajaran: {{ $bills->first()->academic_year_name ?? '-' }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Periode</th>
                                <th class="text-center">Nominal</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Aksi</th>
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
                                $periodName = \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('X'); if ($periodName == 'X') $periodName = \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y');
                            @endphp
                            <tr class="{{ $isCurrentMonth ? 'bg-light' : '' }}">
                                <td>
                                    <span class="text-sm font-weight-bold text-dark">
                                        {{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}
                                    </span>
                                    @if($isCurrentMonth)
                                        <span class="badge badge-pill badge-primary ml-1" style="font-size:0.6rem">BULAN INI</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-sm text-dark font-weight-600">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($bill->status === 'paid')
                                        <span class="badge badge-pill badge-success"><i class="fas fa-check"></i> Lunas</span>
                                    @elseif($isPastDue)
                                        <span class="badge badge-pill badge-danger"><i class="fas fa-exclamation-triangle"></i> Terlambat</span>
                                    @else
                                        <span class="badge badge-pill badge-warning text-white"><i class="fas fa-clock"></i> Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($bill->status !== 'paid')
                                    <button type="button" 
                                            class="btn btn-sm btn-success btn-pay shadow-none" 
                                            data-id="{{ $bill->id }}" 
                                            data-period="{{ \Carbon\Carbon::createFromDate($bill->year, $bill->month, 1)->translatedFormat('F Y') }}"
                                            data-amount="{{ number_format($bill->total_amount, 0, ',', '.') }}">
                                        <i class="fas fa-money-bill-wave"></i> Bayar
                                    </button>
                                    @else
                                        <span class="text-success text-sm font-weight-bold"><i class="fas fa-check-double"></i> Verified</span>
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
