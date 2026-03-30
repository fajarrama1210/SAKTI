@extends('_admin.layouts.app')

@section('content')
<div class="container-fluid mt--6">
    <div class="card">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Daftar Tarif Pembayaran</h3>
            <a href="{{ route('admin.payment-rates.create') }}" class="btn btn-sm btn-primary">Tambah Tarif</a>
        </div>


        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Jenis</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Tarif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentRates as $index => $pr)
                    <tr>
                        <td>{{ $paymentRates->firstItem() + $index }}</td>
                        <td>{{ $pr->academic_year_name }}</td>
                        <td><b>{{ $pr->payment_type_name }}</b></td>
                        <td>Kelas {{ $pr->grade_level }}</td>
                        <td>{{ $pr->major_name ?? 'Semua Jurusan' }}</td>
                        <td><b>Rp {{ number_format($pr->amount, 0, ',', '.') }}</b></td>
                        <td>
                            <a href="{{ route('admin.payment-rates.edit', $pr->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.payment-rates.destroy', $pr->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data tarif pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer py-4">
            {{ $paymentRates->links() }}
        </div>
    </div>
</div>
@endsection