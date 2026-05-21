@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush


@section('content')
<div class="container-fluid mt--6">
    {{-- Filter --}}
    <div class="card sakti-card mb-4">
        <div class="card-header bg-white border-0 pb-0 pt-4 px-4">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Laporan Transaksi Keuangan</h3>
        </div>
        <div class="card-body bg-white border-bottom py-4 px-4">
            <form method="GET" action="{{ route('admin.reports.transaction') }}">
                <div class="row align-items-end">
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control select2" id="academic_year_select">
                                <option value="">-- Opsional --</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($filters['academic_year_id'] ?? '') == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Semester</label>
                            <select name="semester_id" class="form-control select2" id="semester_select">
                                <option value="">Semua</option>
                                @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ ($filters['semester_id'] ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Bulan</label>
                            @php $bulan = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des']; @endphp
                            <select name="month" class="form-control select2">
                                <option value="">Semua</option>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ ($filters['month'] ?? '') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Tahun</label>
                            <input type="number" name="year" class="form-control" value="{{ $filters['year'] ?? now()->year }}" min="2024" max="2030">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <div class="form-group mb-0">
                            <label class="form-control-label text-xs font-weight-bold text-sakti-green text-uppercase opacity-8">Tipe</label>
                            <select name="type" class="form-control select2">
                                <option value="">Semua</option>
                                <option value="income" {{ ($filters['type'] ?? '') === 'income' ? 'selected' : '' }}>Uang Masuk</option>
                                <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>Uang Keluar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sakti-primary btn-block w-100 mb-0">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil --}}
    @if($filtered)
    {{-- Ringkasan --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card sakti-card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Uang Masuk</h5>
                            <span class="h2 font-weight-bold text-success mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card sakti-card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Uang Keluar</h5>
                            <span class="h2 font-weight-bold text-danger mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card sakti-card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Saldo</h5>
                            <span class="h2 font-weight-bold mb-0 {{ ($totalIncome - $totalExpense) >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card sakti-card">
        <div class="card-header bg-white border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Detail Transaksi ({{ $data->count() }} data)</h3>
            @if($data->count() > 0)
            <div>
                <a href="{{ route('admin.reports.transaction.excel', request()->query()) }}" class="btn btn-sm btn-outline-success" style="border-color: #2dce89; color: #2dce89;">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
                <a href="{{ route('admin.reports.transaction.pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger" style="border-color: #f5365c; color: #f5365c;">
                    <i class="fas fa-file-pdf mr-2"></i> Export PDF
                </a>
            </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table align-items-center table-flush table-sm">
                <thead class="thead-light">
                    <tr>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">No</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Tanggal</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Tipe</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Kategori</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Keterangan</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Uang Masuk</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Uang Keluar</th>
                        <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $trx)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                        <td>
                            @if($trx->type === 'income')
                            <span class="badge badge-sm bg-gradient-success">Masuk</span>
                            @else
                            <span class="badge badge-sm bg-gradient-danger">Keluar</span>
                            @endif
                        </td>
                        <td>{{ $trx->category ?? '-' }}</td>
                        <td>{{ $trx->description }}</td>
                        <td>{{ $trx->type === 'income' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                        <td>{{ $trx->type === 'expense' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                        <td>{{ $trx->recorded_by_name ?? '-' }}</td>
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
