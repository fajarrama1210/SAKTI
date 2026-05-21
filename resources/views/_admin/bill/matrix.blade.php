@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')


    <div class="container-fluid mt--6">
        <div class="card sakti-card mb-4">
            <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-1 text-sakti-green font-weight-bold">Matrix Pembayaran SPP</h3>
                        <p class="text-sm text-muted mb-0">Rekap status lunas per bulan dalam satu tampilan grid.</p>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <div class="card-body bg-white border-bottom py-4 px-4">
                <form method="GET" action="{{ route('admin.spp.matrix') }}" class="row align-items-end">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Pilih Kelas</label>
                        <select name="classroom_id" class="form-control select2" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classrooms as $c)
                                <option value="{{ $c->id }}"
                                    {{ ($filters['classroom_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->grade_level }} – {{ $c->name }} ({{ $c->major_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                        <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Pilih Semester</label>
                        <select name="semester_id" class="form-control select2" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach ($semesters as $s)
                                <option value="{{ $s->id }}"
                                    {{ ($filters['semester_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->academic_year_name }} – {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sakti-primary btn-block w-100 mb-0">
                            <i class="fas fa-search mr-2"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            @if ($data)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px" class="text-center text-sakti-green text-xs font-weight-bold text-uppercase">No</th>
                                <th style="min-width: 250px" class="text-sakti-green text-xs font-weight-bold text-uppercase">Nama Siswa</th>
                                @foreach ($data['months'] as $m)
                                    <th class="text-center text-sakti-green text-xs font-weight-bold text-uppercase">
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
                                        <div class="font-weight-bold text-dark">{{ $student->name }}</div>
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
                                                    <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-sm btn-icon-only btn-success rounded-circle" title="Lunas">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                @elseif($bill->status === 'partial')
                                                    <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-sm btn-icon-only btn-warning rounded-circle" title="Sebagian">
                                                        <i class="fas fa-adjust"></i>
                                                    </a>
                                                @elseif($bill->status === 'cancelled')
                                                    <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-sm btn-icon-only btn-secondary rounded-circle" title="Dibatalkan">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                @else
                                                    @php $isPast = ($bill->status !== 'cancelled' && \Carbon\Carbon::parse($bill->due_date)->isPast()); @endphp
                                                    <a href="{{ route('admin.spp.student', $student->id) }}" class="btn btn-sm btn-icon-only {{ $isPast ? 'btn-danger' : 'btn-outline-secondary' }} rounded-circle" title="{{ $isPast ? 'Menunggak' : 'Belum Bayar' }}">
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

                {{-- Footer Legend & Action --}}
                <div class="card-footer bg-white py-4 px-4">
                    <div class="row align-items-center">
                        <div class="col-lg-9 mb-3 mb-lg-0">
                            <h5 class="text-uppercase text-muted tracking-wide text-xs mb-3 font-weight-bold">Keterangan Status:</h5>
                            <div class="legend-wrapper">

                                <div class="legend-item">
                                    <span class="legend-icon icon-lunas"><i class="fas fa-check"></i></span>
                                    <span class="legend-text">Lunas</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-icon icon-sebagian"><i class="fas fa-adjust"></i></span>
                                    <span class="legend-text">Sebagian</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-icon icon-menunggak"><i class="fas fa-times"></i></span>
                                    <span class="legend-text">Menunggak</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-icon icon-belum"><i class="fas fa-times"></i></span>
                                    <span class="legend-text">Belum Bayar</span>
                                </div>

                                <div class="legend-item">
                                    <span class="legend-icon icon-batal"><i class="fas fa-ban"></i></span>
                                    <span class="legend-text">Dibatalkan</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-3 text-lg-right">
                            <button class="btn btn-sm btn-outline-success btn-block w-100 mt-5" onclick="window.print()" style="border-color: #07814e; color: #07814e;">
                                <i class="fas fa-print mr-2"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- State saat data belum dipilih --}}
                <div class="card-body text-center py-7 bg-white">
                    <div class="mb-4 text-sakti-green" style="opacity: 0.2;">
                        <i class="fas fa-table fa-5x"></i>
                    </div>
                    <h4 class="text-dark font-weight-bold mb-2">Belum Ada Data Tertampil</h4>
                    <p class="text-sm text-muted mx-auto" style="max-width: 450px;">
                        Silakan pilih <strong>Kelas</strong> dan <strong>Semester</strong> pada filter di atas terlebih dahulu untuk memuat tampilan grid status rekap pembayaran seluruh siswa.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
