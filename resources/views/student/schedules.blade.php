@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">Jadwal Pelajaran Kelas Anda</h4>
                        <p class="text-sm text-muted mb-0">Lihat jadwal pelajaran mingguan yang terdaftar untuk kelas Anda saat ini.</p>
                    </div>
                    <div>
                        <span class="badge bg-gradient-success p-2 font-weight-bold">
                            {{ $student->name }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules Section -->
    <div class="row">
        @foreach($schedulesByDay as $dayName => $daySchedules)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-success p-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="text-white mb-0 font-weight-bold">{{ $dayName }}</h6>
                        <span class="badge bg-white text-success font-weight-bold text-xxs px-2 py-1">
                            {{ count($daySchedules) }} Sesi
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if(count($daySchedules) > 0)
                            <div class="timeline timeline-one-side">
                                @foreach($daySchedules as $schedule)
                                    @php
                                        // Calculate end time dynamically using duration if available, or fall back to end_time
                                        $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                        if ($schedule->duration) {
                                            $endTime = \Carbon\Carbon::parse($schedule->start_time)->addMinutes($schedule->duration)->format('H:i');
                                        } else {
                                            $endTime = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                                        }
                                    @endphp
                                    <div class="timeline-block mb-3">
                                        <span class="timeline-step">
                                            <i class="fas fa-clock text-success text-gradient"></i>
                                        </span>
                                        <div class="timeline-content">
                                            <h6 class="text-dark text-xs font-weight-bold mb-0">
                                                {{ $schedule->subject }}
                                            </h6>
                                            <p class="text-secondary text-xxs font-weight-bold mt-1 mb-0">
                                                <i class="fas fa-user-tie me-1"></i> {{ $schedule->teacher_name }}
                                            </p>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge bg-light text-dark text-xxs me-2">
                                                    {{ $startTime }} - {{ $endTime }}
                                                </span>
                                                @if($schedule->room)
                                                    <span class="badge bg-success-soft text-success text-xxs">
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
                                <i class="fas fa-calendar-minus fa-2x text-muted mb-2"></i>
                                <p class="text-xs text-muted mb-0">Tidak ada jadwal pelajaran.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
