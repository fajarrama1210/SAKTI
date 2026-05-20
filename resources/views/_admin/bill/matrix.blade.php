@extends('_admin.layouts.app')

@section('content')
    <div class="container-fluid mt--6">
        <div class="card shadow">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Matrix Pembayaran SPP</h3>
                        <p class="text-sm mb-0">Rekap status lunas per bulan dalam satu tampilan grid.</p>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="card-body bg-secondary border-top border-bottom py-3">
                <form method="GET" action="{{ route('admin.spp.matrix') }}" class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-control-label">Pilih Kelas</label>
                        <select name="classroom_id" class="form-control form-control-sm select2" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $c)
                                <option value="{{ $c->id }}"
                                    {{ ($filters['classroom_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-control-label">Pilih Semester</label>
                        <select name="semester_id" class="form-control form-control-sm select2" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}"
                                    {{ ($filters['semester_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->academic_year_name }} – {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-primary btn-block w-100">
                            <i class="fas fa-search"></i> Tampilkan Matrix
                        </button>
                    </div>
                </form>
            </div>

            @if ($data)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px" class="text-center">No</th>
                                <th style="min-width: 250px">Nama Siswa</th>
                                @foreach ($data['months'] as $m)
                                    <th class="text-center">
                                        {{ \Carbon\Carbon::createFromDate($m['year'], $m['month'], 1)->translatedFormat('M Y') }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['students'] as $index => $student)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ $student->name }}</span><br>
                                        <small class="text-muted">{{ $student->nisn }}</small>
                                    </td>
                                    @foreach ($data['months'] as $m)
                                        @php
                                            $bill = $data['bills']
                                                ->get($student->id)
                                                ?->firstWhere('month', $m['month']);
                                        @endphp
                                        <td class="text-center">
                                            @if ($bill)
                                                @if ($bill->status === 'paid')
                                                    <a href="{{ route('admin.spp.student', $student->id) }}"
                                                        class="btn btn-sm btn-icon-only btn-success rounded-circle"
                                                        title="Lunas">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @elseif($bill->status === 'partial')
                                                    <a href="{{ route('admin.spp.student', $student->id) }}"
                                                        class="btn btn-sm btn-icon-only btn-warning rounded-circle"
                                                        title="Sebagian">
                                                        <i class="fas fa-adjust"></i>
                                                    </a>
                                                @elseif($bill->status === 'cancelled')
                                                    <a href="{{ route('admin.spp.student', $student->id) }}"
                                                        class="btn btn-sm btn-icon-only btn-secondary rounded-circle"
                                                        title="Dibatalkan">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                @else
                                                    @php $isPast = ($bill->status !== 'cancelled' && \Carbon\Carbon::parse($bill->due_date)->isPast()); @endphp
                                                    <a href="{{ route('admin.spp.student', $student->id) }}"
                                                        class="btn btn-sm btn-icon-only {{ $isPast ? 'btn-danger' : 'btn-outline-secondary' }} rounded-circle"
                                                        title="{{ $isPast ? 'Menunggak' : 'Belum Bayar' }}">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2">Keterangan:</h4>
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                                    <span class="btn btn-sm btn-icon-only btn-success rounded-circle mr-2">
                                        <i class="fas fa-check text-white"></i>
                                    </span>
                                    <span class="text-sm font-weight-bold text-dark">Lunas</span>
                                </div>
                                <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                                    <span class="btn btn-sm btn-icon-only btn-warning rounded-circle mr-2">
                                        <i class="fas fa-adjust text-white"></i>
                                    </span>
                                    <span class="text-sm font-weight-bold text-dark">Sebagian</span>
                                </div>
                                <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                                    <span class="btn btn-sm btn-icon-only btn-danger rounded-circle mr-2">
                                        <i class="fas fa-times text-white"></i>
                                    </span>
                                    <span class="text-sm font-weight-bold text-dark">Menunggak</span>
                                </div>
                                <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                                    <span class="btn btn-sm btn-icon-only btn-outline-secondary rounded-circle mr-2">
                                        <i class="fas fa-times"></i>
                                    </span>
                                    <span class="text-sm font-weight-bold text-dark">Belum Bayar</span>
                                </div>
                                <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                                    <span class="btn btn-sm btn-icon-only btn-secondary rounded-circle mr-2">
                                        <i class="fas fa-ban text-white"></i>
                                    </span>
                                    <span class="text-sm font-weight-bold text-dark">Dibatalkan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-sm btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak Laporan Matrix
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body text-center py-6">
                    <div class="mb-3">
                        <i class="fas fa-th-large fa-4x text-light"></i>
                    </div>
                    <h4 class="text-muted">Pilih Kelas dan Semester untuk melihat matrix pembayaran.</h4>
                    <p class="text-sm text-muted">Data ini akan menampilkan status lunas seluruh siswa dalam satu kelas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
