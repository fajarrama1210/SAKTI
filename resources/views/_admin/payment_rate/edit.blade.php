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
                            <input type="number" name="amount" class="form-control" value="{{ old('amount', $paymentRate->amount) }}" min="0" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('admin.payment-rates.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection