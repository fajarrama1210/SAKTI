@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
    <div class="container-fluid mt--6">
        <div class="card sakti-card">
            <div class="card-header border-0 bg-white">
                <h3 class="mb-0 text-sakti-green font-weight-bold">Edit Jadwal Pelajaran</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label class="form-control-label" for="classroom_id">Kelas</label>
                        <select name="classroom_id" id="classroom_id" class="form-control @error('classroom_id') is-invalid @enderror" required>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id', $schedule->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-control-label" for="day">Hari</label>
                        <select name="day" id="day" class="form-control @error('day') is-invalid @enderror" required>
                            <option value="monday" {{ old('day', $schedule->day) == 'monday' ? 'selected' : '' }}>Senin</option>
                            <option value="tuesday" {{ old('day', $schedule->day) == 'tuesday' ? 'selected' : '' }}>Selasa</option>
                            <option value="wednesday" {{ old('day', $schedule->day) == 'wednesday' ? 'selected' : '' }}>Rabu</option>
                            <option value="thursday" {{ old('day', $schedule->day) == 'thursday' ? 'selected' : '' }}>Kamis</option>
                            <option value="friday" {{ old('day', $schedule->day) == 'friday' ? 'selected' : '' }}>Jumat</option>
                            <option value="saturday" {{ old('day', $schedule->day) == 'saturday' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                        @error('day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-control-label" for="start_time">Jam Mulai</label>
                                <input type="time" name="start_time" id="start_time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-control-label" for="duration">Durasi (Jam)</label>
                                <input type="number" name="duration" id="duration"
                                    class="form-control @error('duration') is-invalid @enderror"
                                    placeholder="Contoh: 2" value="{{ old('duration', $schedule->duration) }}" min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-control-label" for="subject">Mata Pelajaran</label>
                        <input type="text" name="subject" id="subject"
                            class="form-control @error('subject') is-invalid @enderror"
                            placeholder="Contoh: Matematika" value="{{ old('subject', $schedule->subject) }}" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-control-label" for="room">Ruang</label>
                        <input type="text" name="room" id="room"
                            class="form-control @error('room') is-invalid @enderror"
                            placeholder="Contoh: Lab Komputer 1 / Ruang 104" value="{{ old('room', $schedule->room) }}" required>
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-control-label" for="teacher_name">Guru Pengajar</label>
                        <input type="text" name="teacher_name" id="teacher_name"
                            class="form-control @error('teacher_name') is-invalid @enderror"
                            placeholder="Contoh: Budi Santoso, S.Pd." value="{{ old('teacher_name', $schedule->teacher_name) }}" required>
                        @error('teacher_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-sakti-primary mt-3">Update Data</button>
                    <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
