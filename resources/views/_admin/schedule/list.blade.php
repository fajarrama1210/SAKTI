@extends('_admin.layouts.app')

@section('content')
    <div class="container-fluid mt--6">
        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Jadwal Pelajaran</h3>
                <a href="{{ route('admin.schedules.create') }}" class="btn btn-sm btn-primary">Tambah Jadwal</a>
            </div>

            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col" class="text-center">Kelas</th>
                            <th scope="col" class="text-center">Hari</th>
                            <th scope="col" class="text-center">Jam</th>
                            <th scope="col" class="text-center">Durasi</th>
                            <th scope="col" class="text-center">Mata Pelajaran</th>
                            <th scope="col" class="text-center">Ruang</th>
                            <th scope="col" class="text-center">Guru Pengajar</th>
                            <th scope="col" class="text-center">Aksi</th>
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
                                <td scope="col" class="text-center">{{ $schedules->firstItem() + $index }}</td>
                                <td scope="col" class="text-center"><b>{{ $schedule->classroom_name }}</b></td>
                                <td scope="col" class="text-center">
                                    <span class="badge bg-gradient-success">
                                        {{ $daysMapping[$schedule->day] ?? $schedule->day }}
                                    </span>
                                </td>
                                <td scope="col" class="text-center">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </td>
                                <td scope="col" class="text-center">{{ $schedule->duration }} Jam</td>
                                <td scope="col" class="text-center">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="badge bg-gradient-warning text-xxs px-3 py-2">
                                            <i class="fas fa-coffee me-1"></i> Istirahat
                                        </span>
                                    @else
                                        {{ $schedule->subject }}
                                    @endif
                                </td>
                                <td scope="col" class="text-center">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="text-muted">-</span>
                                    @else
                                        <span class="badge bg-gradient-info">{{ $schedule->room }}</span>
                                    @endif
                                </td>
                                <td scope="col" class="text-center">
                                    @if($schedule->subject === 'Istirahat')
                                        <span class="text-muted">-</span>
                                    @else
                                        {{ $schedule->teacher_name }}
                                    @endif
                                </td>
                                <td scope="col" class="text-center">
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
            <div class="card-footer py-4">
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
@endsection
