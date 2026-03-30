@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-header border-0">
            <h3 class="mb-0">Laporan Pembayaran SPP</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.payment') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control" id="academic_year_select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Semester</label>
                            <select name="semester_id" class="form-control" id="semester_select">
                                <option value="">Semua</option>
                                @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ ($filters['semester_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">Bulan</label>
                            @php $bulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des']; @endphp
                            <select name="month" class="form-control">
                                <option value="">Semua</option>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ ($filters['month'] ?? '') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="form-control-label">Tahun</label>
                            <input type="number" name="year" class="form-control" value="{{ $filters['year'] ?? now()->year }}" min="2024" max="2030">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil & Export --}}
    @if($filtered)
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="mb-0">Hasil Laporan ({{ $data->count() }} data)</h3>
            @if($data->count() > 0)
            <div>
                <a href="{{ route('admin.reports.payment.excel', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="ni ni-archive-2"></i> Export Excel
                </a>
                <a href="{{ route('admin.reports.payment.pdf', request()->query()) }}" class="btn btn-sm btn-danger">
                    <i class="ni ni-single-copy-04"></i> Export PDF
                </a>
            </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table align-items-center table-flush table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>No. KK</th>
                        <th>Bulan</th>
                        <th>Jenis</th>
                        <th>Tagihan</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bulanFull = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @forelse($data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row->academic_year_name }}</td>
                        <td>{{ $row->nisn }}</td>
                        <td><b>{{ $row->student_name }}</b></td>
                        <td>{{ $row->grade_level ? 'Kelas ' . $row->grade_level : '-' }}</td>
                        <td>{{ $row->major_name ?? '-' }}</td>
                        <td>{{ $row->family_card_number }}</td>
                        <td>{{ $bulanFull[$row->month] ?? $row->month }} {{ $row->year }}</td>
                        <td>{{ $row->payment_type_name }}</td>
                        <td>Rp {{ number_format($row->tagihan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->dibayar, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->sisa, 0, ',', '.') }}</td>
                        <td>
                            @if($row->status_text === 'Lunas')
                            <span class="badge badge-success">Lunas</span>
                            @elseif($row->status_text === 'Sebagian')
                            <span class="badge badge-warning">Sebagian</span>
                            @else
                            <span class="badge badge-danger">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center">Tidak ada data ditemukan untuk filter yang dipilih.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
    document.getElementById('academic_year_select')?.addEventListener('change', function() {
        const ayId = this.value;
        const semesterSelect = document.getElementById('semester_select');
        semesterSelect.innerHTML = '<option value="">Semua</option>';
        if (ayId) {
            fetch('/admin/semesters/api/' + ayId)
                .then(r => r.json())
                .then(data => {
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        semesterSelect.appendChild(opt);
                    });
                });
        }
    });
</script>
@endsection