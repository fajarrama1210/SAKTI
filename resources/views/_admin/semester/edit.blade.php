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
                    <i class="fas fa-book me-2"></i> Edit Semester
                </h3>
                <p class="text-white mb-0" style="opacity: .7; font-size: 0.88rem;">Perbarui data semester: <strong class="text-white">{{ $semester->name }}</strong></p>
            </div>
            <a href="{{ route('admin.semesters.index') }}" class="btn btn-sm btn-glass btn-glass-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="sakti-form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="fas fa-edit"></i></div>
                <div><h3>Formulir Edit Semester</h3><p>Ubah data semester lalu simpan</p></div>
            </div>
        </div>
        <div class="form-card-body">
            @if(session('error'))
            <div class="sakti-warning-box"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
            @endif

            @php
            $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            @endphp

            <form action="{{ route('admin.semesters.update', $semester->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control" required>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $semester->academic_year_id) == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Nama Semester</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $semester->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Bulan Mulai</label>
                            <select name="start_month" class="form-control" required>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ old('start_month', $semester->start_month) == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Bulan Akhir</label>
                            <select name="end_month" class="form-control" required>
                                @foreach($bulan as $num => $nama)
                                <option value="{{ $num }}" {{ old('end_month', $semester->end_month) == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-action-bar">
                    <button type="submit" class="btn btn-sakti-primary"><i class="fas fa-save me-1"></i> Update</button>
                    <a href="{{ route('admin.semesters.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection