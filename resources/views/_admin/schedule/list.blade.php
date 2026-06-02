@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
    <div class="container-fluid mt--6">
    <!-- HEADER -->
    <div class="sakti-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
            <div>
                <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                    <i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">
                    Kelola jadwal pelajaran untuk semua kelas.
                </p>
            </div>
            <a href="{{ route('admin.schedules.create') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-plus me-1"></i> Tambah Jadwal
            </a>
        </div>
    </div>

    <!-- FILTER -->
    <div class="card dashboard-card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.schedules.index') }}" class="row align-items-end">
                <div class="col-md-5 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Filter Jurusan</label>
                    <select name="major_id" id="filter_major" class="form-control select2">
                        <option value="">-- Semua Jurusan --</option>
                        @foreach ($majors as $m)
                            <option value="{{ $m->id }}"
                                {{ ($filters['major_id'] ?? '') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mb-3 mb-md-0">
                    <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Filter Kelas</label>
                    <select name="classroom_id" id="filter_classroom" class="form-control select2">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($classrooms as $c)
                            <option value="{{ $c->id }}" data-major-id="{{ $c->major_id }}"
                                {{ ($filters['classroom_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sakti-primary w-100 mb-0">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.schedules.index') }}" class="btn btn-light w-100 mb-0">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-4 pb-2 px-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-list-ul me-2" style="color: var(--primary-green); opacity: .7;"></i>
                        Daftar Jadwal
                    </h3>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table letters-table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Hari</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Durasi</th>
                                    <th class="text-center">Mata Pelajaran</th>
                                    <th class="text-center">Ruang</th>
                                    <th class="text-center">Guru Pengajar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                    <tbody>
                        @php
                            $daysMapping = [
                                'monday' => 'Senin',
                                'tuesday' => 'Selasa',
                                'wednesday' => 'Rabu',
                                'thursday' => 'Kamis',
                                'friday' => 'Jumat',
                                'saturday' => 'Sabtu',
                            ];
                        @endphp
                        @forelse($schedules as $index => $schedule)
                            <tr @if($schedule->subject === 'Istirahat') style="background-color: #f8f9fe;" @endif>
                                <td class="text-center align-middle">{{ $schedules->firstItem() + $index }}</td>
                                <td class="text-center align-middle">
                                    <span style="font-weight: 700; color: var(--dark-text);">{{ $schedule->classroom_name }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-gradient-success px-3 py-2" style="border-radius: 50px;">
                                        <i class="fas fa-calendar-day me-1"></i> {{ $daysMapping[$schedule->day] ?? $schedule->day }}
                                    </span>
                                </td>
                                <td class="text-center align-middle" style="font-weight: 600; color: var(--dark-text);">
                                    <i class="fas fa-clock me-1 text-muted"></i> 
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </td>
                                <td class="text-center align-middle" style="color: var(--muted-text);">{{ $schedule->duration }} Jam</td>
                                <td class="text-center align-middle" style="font-weight: 600; color: var(--dark-text);">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="badge px-3 py-2" style="background: #fef3c7; color: #d97706; border-radius: 50px;">
                                            <i class="fas fa-coffee me-1"></i> Istirahat
                                        </span>
                                    @else
                                        {{ $schedule->subject }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="text-muted">-</span>
                                    @else
                                        <span class="badge" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; font-weight: 600;">
                                            <i class="fas fa-door-open me-1"></i> {{ $schedule->room }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="color: var(--dark-text);">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="text-muted">-</span>
                                    @else
                                        <i class="fas fa-chalkboard-teacher me-1 text-muted"></i> {{ $schedule->teacher_name }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="dropdown">
                                        <a href="#" class="cursor-pointer text-secondary px-2" id="dropdownAksi{{ $schedule->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="dropdownAksi{{ $schedule->id }}">
                                            <li>
                                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="dropdown-item border-radius-md">
                                                    <i class="fas fa-edit text-info me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="delete-form m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item border-radius-md text-danger">
                                                        <i class="fas fa-trash me-2"></i> Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-state />
                        @endforelse
                    </tbody>
                </table>
            </div>
                </div>
                <div class="card-footer bg-white border-0 py-4 px-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const $majorSelect = $('#filter_major');
    const $classroomSelect = $('#filter_classroom');

    if ($majorSelect.length && $classroomSelect.length) {
        // Store all original classroom options
        const allClassrooms = [];
        $classroomSelect.find('option').each(function () {
            const $opt = $(this);
            allClassrooms.push({
                value: $opt.val(),
                text: $opt.text(),
                majorId: $opt.attr('data-major-id')
            });
        });

        function updateClassrooms() {
            const selectedMajorId = $majorSelect.val();
            const currentSelectedValue = $classroomSelect.val();

            $classroomSelect.empty();
            
            // Add default option
            $classroomSelect.append('<option value="">-- Semua Kelas --</option>');

            allClassrooms.forEach(classroom => {
                if (!classroom.value) return; // skip placeholder
                if (!selectedMajorId || classroom.majorId == selectedMajorId) {
                    const $opt = $('<option></option>')
                        .val(classroom.value)
                        .text(classroom.text)
                        .attr('data-major-id', classroom.majorId);
                    if (classroom.value == currentSelectedValue) {
                        $opt.attr('selected', 'selected');
                    }
                    $classroomSelect.append($opt);
                }
            });

            // Trigger change for select2 update
            if ($classroomSelect.hasClass('select2')) {
                $classroomSelect.trigger('change.select2');
            }
        }

        $majorSelect.on('change', function () {
            // Reset classroom selection if it doesn't match selected major
            const selectedMajorId = $(this).val();
            const $selectedOpt = $classroomSelect.find('option:selected');
            if (selectedMajorId && $selectedOpt.length && $selectedOpt.attr('data-major-id') !== selectedMajorId) {
                $classroomSelect.val('');
            }
            updateClassrooms();
            if ($classroomSelect.hasClass('select2')) {
                $classroomSelect.trigger('change.select2');
            }
        });

        // Initialize options on load
        updateClassrooms();
    }
});
</script>
@endpush
