@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
    <div class="container-fluid mt--6">

        <div class="sakti-page-header mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
                <div>
                    <h3 class="text-white font-weight-bold mb-1" style="font-size: 1.3rem; letter-spacing: -0.02em;">
                        <i class="fas fa-clock me-2"></i> Tambah Jadwal Pelajaran
                    </h3>
                    <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Buat jadwal pelajaran baru untuk kelas.</p>
                </div>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="sakti-form-card">
            <div class="form-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon"><i class="fas fa-plus"></i></div>
                    <div><h3>Formulir Tambah Jadwal</h3><p>Tambahkan satu atau lebih baris jadwal sekaligus</p></div>
                </div>
            </div>
            <div class="form-card-body">
                <form action="{{ route('admin.schedules.store') }}" method="POST" id="bulk-schedule-form">
                    @csrf

                    <div class="form-group mb-4">
                        <label class="form-control-label" for="classroom_id" style="font-size: 1rem;">Pilih Kelas</label>
                        <select name="classroom_id" id="classroom_id" class="form-control form-control-lg @error('classroom_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="border-top pt-3">
                        <h4 class="mb-3 text-primary font-weight-bold">Detail Jadwal Pelajaran</h4>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush" id="schedules-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 140px;">Hari</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 110px;">Jam Mulai</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 90px;">Durasi (Jam)</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 180px;">Mata Pelajaran</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 160px;">Ruang</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase" style="min-width: 180px;">Guru Pengajar</th>
                                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase text-center" style="width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="schedules-tbody">
                                    {{-- Row template --}}
                                    <tr class="schedule-row" id="row-0">
                                        <td>
                                            <select name="schedules[0][day]" class="form-control" required>
                                                <option value="monday">Senin</option>
                                                <option value="tuesday">Selasa</option>
                                                <option value="wednesday">Rabu</option>
                                                <option value="thursday">Kamis</option>
                                                <option value="friday">Jumat</option>
                                                <option value="saturday">Sabtu</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="time" name="schedules[0][start_time]" class="form-control" value="07:00" required>
                                        </td>
                                        <td>
                                            <input type="number" name="schedules[0][duration]" class="form-control" value="2" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="schedules[0][subject]" class="form-control" placeholder="Matematika" required>
                                        </td>
                                        <td>
                                            <input type="text" name="schedules[0][room]" class="form-control" placeholder="Lab Komputer 1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="schedules[0][teacher_name]" class="form-control" placeholder="Budi Santoso, S.Pd." required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon-only btn-outline-danger remove-row-btn" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-sm" id="add-row-btn">
                                <i class="fas fa-plus me-1"></i> Tambah Baris Jadwal
                            </button>
                            <button type="button" class="btn btn-info btn-sm ms-2" id="add-break-btn">
                                <i class="fas fa-coffee me-1"></i> Tambah Istirahat
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3">
                        <button type="submit" class="btn btn-sakti-primary">Simpan Semua Jadwal</button>
                        <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let rowCount = 1;
        const tbody = document.getElementById('schedules-tbody');
        const addRowBtn = document.getElementById('add-row-btn');
        const addBreakBtn = document.getElementById('add-break-btn');

        // Helper to add hours to a time string (HH:MM)
        function addHoursToTime(timeStr, hoursToAdd) {
            if (!timeStr) return "07:00";
            let parts = timeStr.split(':');
            let hours = parseInt(parts[0], 10);
            let minutes = parseInt(parts[1], 10);
            
            hours = (hours + parseInt(hoursToAdd, 10)) % 24;
            
            let hh = String(hours).padStart(2, '0');
            let mm = String(minutes).padStart(2, '0');
            return `${hh}:${mm}`;
        }

        // Function to recalculate all start times for adjacent identical days
        function recalculateSchedules() {
            const rows = Array.from(tbody.querySelectorAll('.schedule-row'));
            for (let i = 1; i < rows.length; i++) {
                const prevRow = rows[i - 1];
                const currentRow = rows[i];
                
                const prevDay = prevRow.querySelector('select[name*="[day]"]').value;
                const currentDaySelect = currentRow.querySelector('select[name*="[day]"]');
                const currentDay = currentDaySelect.value;
                
                if (currentDay === prevDay) {
                    const prevStartTime = prevRow.querySelector('input[name*="[start_time]"]').value;
                    const prevDuration = prevRow.querySelector('input[name*="[duration]"]').value;
                    
                    if (prevStartTime && prevDuration) {
                        const computedStartTime = addHoursToTime(prevStartTime, prevDuration);
                        currentRow.querySelector('input[name*="[start_time]"]').value = computedStartTime;
                    }
                }
            }
        }

        // Function to update the disabled status of remove buttons
        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll('.schedule-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-row-btn');
                if (rows.length === 1) {
                    btn.disabled = true;
                } else {
                    btn.disabled = false;
                }
            });
        }

        // Add Row Click Event
        addRowBtn.addEventListener('click', function() {
            const rows = Array.from(tbody.querySelectorAll('.schedule-row'));
            let defaultDay = 'monday';
            let defaultStartTime = '07:00';
            let defaultDuration = '2';
            
            if (rows.length > 0) {
                const lastRow = rows[rows.length - 1];
                defaultDay = lastRow.querySelector('select[name*="[day]"]').value;
                const lastStartTime = lastRow.querySelector('input[name*="[start_time]"]').value;
                const lastDuration = lastRow.querySelector('input[name*="[duration]"]').value;
                
                defaultStartTime = addHoursToTime(lastStartTime, lastDuration);
                defaultDuration = lastDuration;
            }

            const newRow = document.createElement('tr');
            newRow.className = 'schedule-row';
            newRow.id = `row-${rowCount}`;
            newRow.innerHTML = `
                <td>
                    <select name="schedules[${rowCount}][day]" class="form-control" required>
                        <option value="monday" ${defaultDay === 'monday' ? 'selected' : ''}>Senin</option>
                        <option value="tuesday" ${defaultDay === 'tuesday' ? 'selected' : ''}>Selasa</option>
                        <option value="wednesday" ${defaultDay === 'wednesday' ? 'selected' : ''}>Rabu</option>
                        <option value="thursday" ${defaultDay === 'thursday' ? 'selected' : ''}>Kamis</option>
                        <option value="friday" ${defaultDay === 'friday' ? 'selected' : ''}>Jumat</option>
                        <option value="saturday" ${defaultDay === 'saturday' ? 'selected' : ''}>Sabtu</option>
                    </select>
                </td>
                <td>
                    <input type="time" name="schedules[${rowCount}][start_time]" class="form-control" value="${defaultStartTime}" required>
                </td>
                <td>
                    <input type="number" name="schedules[${rowCount}][duration]" class="form-control" value="${defaultDuration}" min="1" required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][subject]" class="form-control" placeholder="Mata Pelajaran" required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][room]" class="form-control" placeholder="Ruangan" required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][teacher_name]" class="form-control" placeholder="Nama Guru" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon-only btn-outline-danger remove-row-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            recalculateSchedules();
        });

        // Add Break Row Click Event
        addBreakBtn.addEventListener('click', function() {
            const rows = Array.from(tbody.querySelectorAll('.schedule-row'));
            let defaultDay = 'monday';
            let defaultStartTime = '07:00';
            let defaultDuration = '1'; // Break time defaults to 1 hour
            
            if (rows.length > 0) {
                const lastRow = rows[rows.length - 1];
                defaultDay = lastRow.querySelector('select[name*="[day]"]').value;
                const lastStartTime = lastRow.querySelector('input[name*="[start_time]"]').value;
                const lastDuration = lastRow.querySelector('input[name*="[duration]"]').value;
                
                defaultStartTime = addHoursToTime(lastStartTime, lastDuration);
            }

            const newRow = document.createElement('tr');
            newRow.className = 'schedule-row';
            newRow.id = `row-${rowCount}`;
            newRow.innerHTML = `
                <td>
                    <select name="schedules[${rowCount}][day]" class="form-control" required>
                        <option value="monday" ${defaultDay === 'monday' ? 'selected' : ''}>Senin</option>
                        <option value="tuesday" ${defaultDay === 'tuesday' ? 'selected' : ''}>Selasa</option>
                        <option value="wednesday" ${defaultDay === 'wednesday' ? 'selected' : ''}>Rabu</option>
                        <option value="thursday" ${defaultDay === 'thursday' ? 'selected' : ''}>Kamis</option>
                        <option value="friday" ${defaultDay === 'friday' ? 'selected' : ''}>Jumat</option>
                        <option value="saturday" ${defaultDay === 'saturday' ? 'selected' : ''}>Sabtu</option>
                    </select>
                </td>
                <td>
                    <input type="time" name="schedules[${rowCount}][start_time]" class="form-control" value="${defaultStartTime}" required>
                </td>
                <td>
                    <input type="number" name="schedules[${rowCount}][duration]" class="form-control" value="${defaultDuration}" min="1" required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][subject]" class="form-control" value="Istirahat" readonly required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][room]" class="form-control" value="-" readonly required>
                </td>
                <td>
                    <input type="text" name="schedules[${rowCount}][teacher_name]" class="form-control" value="-" readonly required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon-only btn-outline-danger remove-row-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
            recalculateSchedules();
        });

        // Event delegation for input changes
        tbody.addEventListener('change', function(e) {
            const target = e.target;
            
            // If day is changed
            if (target.matches('select[name*="[day]"]')) {
                const currentRow = target.closest('.schedule-row');
                const rows = Array.from(tbody.querySelectorAll('.schedule-row'));
                const index = rows.indexOf(currentRow);
                
                if (index > 0) {
                    const prevRow = rows[index - 1];
                    const prevDay = prevRow.querySelector('select[name*="[day]"]').value;
                    // Reset start time to 07:00 if the day is different from the previous row
                    if (target.value !== prevDay) {
                        currentRow.querySelector('input[name*="[start_time]"]').value = "07:00";
                    }
                }
                recalculateSchedules();
            }
            
            // If start_time or duration is changed
            if (target.matches('input[name*="[start_time]"]') || target.matches('input[name*="[duration]"]')) {
                recalculateSchedules();
            }
        });

        // Event delegation for Remove Row buttons
        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-row-btn');
            if (btn) {
                const row = btn.closest('.schedule-row');
                if (row) {
                    row.remove();
                    updateRemoveButtons();
                    recalculateSchedules();
                }
            }
        });

        // Initial setup
        updateRemoveButtons();
    });
</script>
@endpush
