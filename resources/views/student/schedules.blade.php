@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran Kelas Anda
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Lihat jadwal pelajaran mingguan yang terdaftar untuk kelas Anda saat ini.
                </p>
            </div>
            <span class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-user me-1"></i> {{ $student->name }}
            </span>
        </div>
    </div>

    <!-- Schedules Section -->
    <div class="row">
        @foreach($schedulesByDay as $dayName => $daySchedules)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header border-0 p-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #059669, #34d399); border-radius: var(--card-radius) var(--card-radius) 0 0;">
                        <h6 class="text-white mb-0 font-weight-bold">{{ $dayName }}</h6>
                        <span class="badge bg-white font-weight-bold text-xxs px-2 py-1" style="color: #059669;">
                            {{ count($daySchedules) }} Sesi
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if(count($daySchedules) > 0)
                            <div class="timeline timeline-one-side">
                                @foreach($daySchedules as $schedule)
                                    @php
                                        $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                        if ($schedule->duration) {
                                            $endTime = \Carbon\Carbon::parse($schedule->start_time)->addMinutes($schedule->duration)->format('H:i');
                                        } else {
                                            $endTime = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                                        }
                                    @endphp
                                    <div class="timeline-block mb-3">
                                        <span class="timeline-step">
                                            <i class="fas fa-clock" style="color: #059669;"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-xs font-weight-bold mb-0">
                                                {{ $schedule->subject }}
                                            </h6>
                                            <p class="text-secondary text-xxs font-weight-bold mt-1 mb-0">
                                                <i class="fas fa-user-tie me-1"></i> {{ $schedule->teacher_name }}
                                            </p>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge me-2 text-xxs" style="background: #f1f5f9; color: #334155;">
                                                    {{ $startTime }} - {{ $endTime }}
                                                </span>
                                                @if($schedule->room)
                                                    <span class="badge text-xxs" style="background: #ecfdf5; color: #059669;">
                                                        <i class="fas fa-map-marker-alt me-1"></i> Ruang {{ $schedule->room }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-minus fa-2x mb-2" style="color: #cbd5e1;"></i>
                                <p class="text-xs mb-0" style="color: #94a3b8;">Tidak ada jadwal pelajaran.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
