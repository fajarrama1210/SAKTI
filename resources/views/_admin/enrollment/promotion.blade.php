@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card shadow-sm">
        <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="mb-0"><i class="fas fa-angle-double-up text-info"></i> Kenaikan Kelas Massal</h3>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-body pt-2">
            {{-- Info Banner --}}
            <div class="alert alert-info border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon-container me-3">
                        <i class="fas fa-info-circle fa-2x"></i>
                    </div>
                    <div>
                        <strong>Petunjuk Kenaikan Kelas Massal:</strong>
                        <ul class="mb-0 ps-3 mt-1 text-sm">
                            <li>Pilih <strong>Tahun Ajaran Asal</strong> dan <strong>Kelas Asal</strong> siswa saat ini.</li>
                            <li>Pilih <strong>Tahun Ajaran Tujuan</strong> (tahun ajaran baru berikutnya).</li>
                            <li>Tentukan <strong>Kelas Tujuan</strong> masing-masing siswa yang dipilih, lalu jalankan proses kenaikan kelas.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Filter Form --}}
            <div class="bg-light p-4 rounded-3 mb-4 shadow-sm border">
                <h5 class="text-muted mb-3"><i class="fas fa-filter text-info"></i> Langkah 1: Tentukan Asal & Tujuan</h5>
                <form method="GET" action="{{ route('admin.enrollments.promotion') }}" class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3 mb-md-0">
                            <label class="form-control-label text-xs">Tahun Ajaran Asal</label>
                            <select name="from_academic_year_id" class="form-control" required>
                                <option value="">-- Pilih TA Asal --</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $fromAY == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-3 mb-md-0">
                            <label class="form-control-label text-xs">Kelas Asal</label>
                            <select name="classroom_id" class="form-control" required>
                                <option value="">-- Pilih Kelas Asal --</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}" {{ $classroomId == $c->id ? 'selected' : '' }}>
                                        Kelas {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-3 mb-md-0">
                            <label class="form-control-label text-xs">Tahun Ajaran Tujuan (Baru)</label>
                            <select name="to_academic_year_id" class="form-control" required>
                                <option value="">-- Pilih TA Baru --</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ $toAY == $ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group w-100 mb-3 mb-md-0">
                            <button type="submit" class="btn btn-primary w-100 mb-0" style="height: 40px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <i class="fas fa-search"></i> Tampilkan Daftar Siswa
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Main Form Kenaikan Kelas --}}
            @if($fromAY && $classroomId && $toAY)
                @if($students->isNotEmpty())
                    <form id="promoteForm" action="{{ route('admin.enrollments.promotion.process') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="from_academic_year_id" value="{{ $fromAY }}">
                        <input type="hidden" name="to_academic_year_id" value="{{ $toAY }}">

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="mb-0 text-dark"><i class="fas fa-users text-primary"></i> Langkah 2: Pilih Siswa & Kelas Baru</h5>
                            {{-- Premium bulk setup feature --}}
                            <div class="d-flex align-items-center gap-2 p-2 bg-light border rounded">
                                <label class="mb-0 text-xs text-muted me-2 font-weight-bold text-nowrap"><i class="fas fa-bolt text-warning"></i> Set Semua Kelas Tujuan:</label>
                                <select id="bulkTargetClass" class="form-control form-control-sm py-0" style="height: 30px; width: 220px;">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classrooms as $c)
                                        <option value="{{ $c->id }}">
                                            Kelas {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive border rounded">
                            <table class="table align-items-center table-flush table-hover" id="tablePromotion">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">
                                            <div class="form-check m-0 p-0 d-flex justify-content-center">
                                                <input type="checkbox" id="checkAll" class="form-check-input shadow-none cursor-pointer" checked>
                                            </div>
                                        </th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas Asal</th>
                                        <th>Kelas Tujuan Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $row)
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check m-0 p-0 d-flex justify-content-center">
                                                <input type="checkbox" name="promotions[{{ $row->student_id }}][selected]" value="1" class="form-check-input check-item shadow-none cursor-pointer" checked>
                                                <input type="hidden" name="promotions[{{ $row->student_id }}][student_id]" value="{{ $row->student_id }}">
                                            </div>
                                        </td>
                                        <td><code>{{ $row->nisn }}</code></td>
                                        <td><b>{{ $row->student_name }}</b></td>
                                        <td>Kelas {{ $row->grade_level }} – {{ $row->classroom_name }}</td>
                                        <td>
                                            <select name="promotions[{{ $row->student_id }}][new_classroom_id]" class="form-control form-control-sm target-class-select">
                                                <option value="">-- Pilih Kelas Baru --</option>
                                                @foreach($classrooms as $c)
                                                    <option value="{{ $c->id }}">
                                                        Kelas {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-info btn-block shadow-sm">
                                <i class="fas fa-angle-double-up me-1"></i> Proses Kenaikan Kelas Siswa Terpilih
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-5 bg-light rounded-3 mt-4 border border-dashed">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Tidak ada siswa aktif yang ditemukan di kelas asal terpilih.</h4>
                        <p class="text-sm text-muted">Pastikan siswa sudah memiliki penempatan di tahun ajaran asal dan statusnya aktif.</p>
                    </div>
                @endif
            @else
                <div class="text-center py-6 bg-light rounded-3 mt-4 border border-dashed">
                    <i class="fas fa-angle-double-up fa-4x text-light mb-3"></i>
                    <h4 class="text-muted">Tentukan Filter Tahun Ajaran & Kelas Asal</h4>
                    <p class="text-sm text-muted">Gunakan form filter di atas untuk menampilkan daftar siswa yang akan dipromosikan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const items = document.querySelectorAll('.check-item');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            items.forEach(item => {
                item.checked = checkAll.checked;
            });
        });
    }

    // Custom bulk set target class logic
    const bulkTargetSelect = document.getElementById('bulkTargetClass');
    if (bulkTargetSelect) {
        bulkTargetSelect.addEventListener('change', function() {
            const val = this.value;
            if (val) {
                document.querySelectorAll('.target-class-select').forEach(select => {
                    const rowCheckbox = select.closest('tr').querySelector('.check-item');
                    if (!rowCheckbox || rowCheckbox.checked) {
                        select.value = val;
                        // Trigger change event to ensure any UI framework re-renders successfully
                        select.dispatchEvent(new Event('change'));
                    }
                });
            }
        });
    }

    // SweetAlert2 Validation & Form Submission Confirmation
    const promoteForm = document.getElementById('promoteForm');
    if (promoteForm) {
        promoteForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop native HTML form submit

            // 1. Validate if at least one student is selected
            const selectedStudents = document.querySelectorAll('.check-item:checked');
            if (selectedStudents.length === 0) {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Silakan pilih minimal 1 siswa untuk dinaikkan kelas.',
                    icon: 'warning',
                    confirmButtonColor: '#11cdef'
                });
                return;
            }

            // 2. Validate if all selected students have a new classroom chosen
            let allClassroomsSelected = true;
            selectedStudents.forEach(checkbox => {
                const select = checkbox.closest('tr').querySelector('.target-class-select');
                if (select && !select.value) {
                    allClassroomsSelected = false;
                }
            });

            if (!allClassroomsSelected) {
                Swal.fire({
                    title: 'Kelas Belum Ditentukan!',
                    text: 'Silakan tentukan kelas tujuan baru untuk seluruh siswa yang Anda pilih.',
                    icon: 'warning',
                    confirmButtonColor: '#11cdef'
                });
                return;
            }

            // 3. Prompt user with premium SweetAlert2 dialog
            Swal.fire({
                title: 'Konfirmasi Kenaikan Kelas',
                text: `Anda akan memproses kenaikan kelas untuk ${selectedStudents.length} siswa terpilih. Apakah Anda yakin?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#11cdef',
                cancelButtonColor: '#8898aa',
                confirmButtonText: 'Ya, Proses Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    promoteForm.submit(); // Proceed with form submit
                }
            });
        });
    }

    // Tampilkan error validasi Laravel dengan SweetAlert2 jika ada
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memproses Kenaikan Kelas',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#11cdef'
        });
    @endif
});
</script>
@endsection
