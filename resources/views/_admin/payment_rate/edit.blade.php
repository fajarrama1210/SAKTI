@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">Edit Tarif Pembayaran</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.payment-rates.update', $paymentRate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Tahun Ajaran</label>
                            <select name="academic_year_id" class="form-control" required>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ old('academic_year_id', $paymentRate->academic_year_id) == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Jenis Pembayaran</label>
                            <select name="payment_type_id" class="form-control" required>
                                @foreach($paymentTypes as $pt)
                                <option value="{{ $pt->id }}" {{ old('payment_type_id', $paymentRate->payment_type_id) == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Kelas</label>
                            <select name="grade_level" class="form-control" required>
                                @foreach([10, 11, 12, 13] as $grade)
                                <option value="{{ $grade }}" {{ old('grade_level', $paymentRate->grade_level) == $grade ? 'selected' : '' }}>Kelas {{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Jurusan <small class="text-muted">(Opsional)</small></label>
                            <select name="major_id" class="form-control">
                                <option value="">Semua Jurusan</option>
                                @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id', $paymentRate->major_id) == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Tarif (Rp)</label>
                            <input type="text" name="amount" id="input-rupiah" class="form-control" value="{{ old('amount', $paymentRate->amount) }}" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('admin.payment-rates.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var inputTarif = document.getElementById('input-rupiah');
        if (inputTarif) {
            inputTarif.value = formatRupiah(inputTarif.value);
            inputTarif.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
            });
        }

        function formatRupiah(angka, prefix) {
            if (!angka) return '';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    });
</script>
@endpush

@endsection