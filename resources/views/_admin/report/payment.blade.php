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
                    <div class="col-md-4">
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
                    <div class="col-md-3">
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
                        <th class="text-center">No</th>
                        <th class="text-center">Tahun Ajaran</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Nama Siswa</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">Jurusan</th>
                        <th class="text-center">No. KK</th>
                        <th class="text-center">Bulan</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Tagihan</th>
                        <th class="text-center">Dibayar</th>
                        <th class="text-center">Sisa</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $bulanFull = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @forelse($data as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $row->academic_year_name }}</td>
                        <td class="text-center">{{ $row->nisn }}</td>
                        <td class="text-center"><b>{{ $row->student_name }}</b></td>
                        <td class="text-center">{{ $row->grade_level ? 'Kelas ' . $row->grade_level : '-' }}</td>
                        <td class="text-center">{{ $row->major_name ?? '-' }}</td>
                        <td class="text-center">{{ $row->family_card_number }}</td>
                        <td class="text-center">{{ $bulanFull[$row->month] ?? $row->month }} {{ $row->year }}</td>
                        <td class="text-center">{{ $row->payment_type_name }}</td>
                        <td class="text-center">Rp {{ number_format($row->tagihan, 0, ',', '.') }}</td>
                        <td class="text-center">Rp {{ number_format($row->dibayar, 0, ',', '.') }}</td>
                        <td class="text-center">Rp {{ number_format($row->sisa, 0, ',', '.') }}</td>
                        <td class="text-center">
                             @if($row->status_text === 'Lunas')
                            <span class="badge badge-sm bg-gradient-success">Lunas</span>
                            @elseif($row->status_text === 'Sebagian')
                            <span class="badge badge-sm bg-gradient-warning">Sebagian</span>
                            @else
                            <span class="badge badge-sm bg-gradient-danger">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <x-empty-state />
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