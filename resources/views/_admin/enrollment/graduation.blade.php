@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="card sakti-card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
            <h3 class="mb-0 text-sakti-green font-weight-bold">Kelulusan Massal Siswa</h3>
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>

        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Penting:</strong> Halaman ini digunakan untuk meluluskan siswa (biasanya kelas XII) secara massal di akhir tahun ajaran.
            </div>

            <form action="{{ route('admin.enrollments.graduation.process') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tahun Ajaran Kelulusan</label>
                            <select name="academic_year_id" class="form-control" readonly>
                                @foreach($academicYears as $ay)
                                    @if($selectedAY == $ay->id)
                                        <option value="{{ $ay->id }}" selected>{{ $ay->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Kelulusan</label>
                            <input type="date" name="graduation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="tableGraduation">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase">NISN</th>
                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Nama Siswa</th>
                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Kelas</th>
                                <th class="text-sakti-green text-xs font-weight-bold text-uppercase">Status Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $row)
                            <tr>
                                <td>
                                    <input type="checkbox" name="student_ids[]" value="{{ $row->student_id }}" class="check-item">
                                </td>
                                <td>{{ $row->nisn }}</td>
                                <td>{{ $row->student_name }}</td>
                                <td>{{ $row->classroom_name }}</td>
                                <td>
                                    @if($row->status == 'aktif')
                                        <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                    @elseif($row->status == 'lulus')
                                        <span class="badge badge-sm bg-gradient-primary">Lulus</span>
                                    @elseif($row->status == 'do')
                                        <span class="badge badge-sm bg-gradient-danger">DO / Keluar</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">{{ $row->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <x-empty-state />
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-sakti-primary btn-block" onclick="return confirm('Apakah Anda yakin ingin meluluskan siswa terpilih?')">
                        Proses Kelulusan Siswa Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const items = document.querySelectorAll('.check-item');

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            items.forEach(item => {
                item.checked = checkAll.checked;
            });
        });
    }
});
</script>
@endsection
