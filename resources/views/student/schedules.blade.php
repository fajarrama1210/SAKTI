@extends('student.layouts.app-mobile')

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
    {{-- Mobile: Tab-based day selector (visible < lg) --}}
    <div class="d-lg-none">
        <div class="mobile-day-tabs mb-3">
            <div class="d-flex overflow-auto gap-2 pb-2" style="-webkit-overflow-scrolling: touch; scrollbar-width: none;">
                @php $dayIndex = 0; @endphp
                @foreach($schedulesByDay as $dayName => $daySchedules)
                    <button class="mobile-day-tab {{ $dayIndex === 0 ? 'active' : '' }}" data-day="{{ $dayIndex }}" onclick="switchDay({{ $dayIndex }})">
                        {{ $dayName }}
                        <span class="mobile-day-count">{{ count($daySchedules) }}</span>
                    </button>
                    @php $dayIndex++; @endphp
                @endforeach
            </div>
        </div>

        @php $dayIndex = 0; @endphp
        @foreach($schedulesByDay as $dayName => $daySchedules)
            <div class="mobile-day-content {{ $dayIndex === 0 ? 'active' : '' }}" id="mobileDay{{ $dayIndex }}">
                @if(count($daySchedules) > 0)
                    @foreach($daySchedules as $schedule)
                        @php
                            $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                            if ($schedule->duration) {
                                $endTime = \Carbon\Carbon::parse($schedule->start_time)->addMinutes($schedule->duration)->format('H:i');
                            } else {
                                $endTime = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                            }
                        @endphp
                        <div class="mobile-schedule-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="mobile-schedule-time">
                                    <div class="time-start">{{ $startTime }}</div>
                                    <div class="time-divider"></div>
                                    <div class="time-end">{{ $endTime }}</div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 font-weight-bold" style="color: #1e293b; font-size: .9rem;">{{ $schedule->subject }}</h6>
                                    <p class="mb-1 text-xs" style="color: #64748b;"><i class="fas fa-user-tie me-1"></i> {{ $schedule->teacher_name }}</p>
                                    @if($schedule->room)
                                        <span class="badge" style="background: #ecfdf5; color: #059669; font-size: .7rem;">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $schedule->room }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-minus fa-2x mb-2" style="color: #cbd5e1;"></i>
                        <p class="text-xs mb-0" style="color: #94a3b8;">Tidak ada jadwal pelajaran.</p>
                    </div>
                @endif
            </div>
            @php $dayIndex++; @endphp
        @endforeach
    </div>

    {{-- Desktop: Original card grid (visible >= lg) --}}
    <div class="row d-none d-lg-flex">
        @foreach($schedulesByDay as $dayName => $daySchedules)
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 mb-4">
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
                                            <h6 class="text-dark text-xs font-weight-bold mb-0">{{ $schedule->subject }}</h6>
                                            <p class="text-secondary text-xxs font-weight-bold mt-1 mb-0">
                                                <i class="fas fa-user-tie me-1"></i> {{ $schedule->teacher_name }}
                                            </p>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge me-2 text-xxs" style="background: #f1f5f9; color: #334155;">{{ $startTime }} - {{ $endTime }}</span>
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

@push('scripts')
<script>
function switchDay(index) {
    document.querySelectorAll('.mobile-day-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.mobile-day-content').forEach(function(c) { c.classList.remove('active'); });
    document.querySelector('.mobile-day-tab[data-day="' + index + '"]').classList.add('active');
    document.getElementById('mobileDay' + index).classList.add('active');
}
</script>
@endpush
