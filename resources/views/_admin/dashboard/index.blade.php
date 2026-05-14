@extends('_admin.layouts.app')

@section('content')
<!-- Header -->
<div class="header pb-6" style="background: linear-gradient(135deg, #155d3e 0%, #1a8a5c 40%, #2dce89 100%);">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <h6 class="h2 text-white d-inline-block mb-0">Dashboard SAKTI</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Card stats (Minimalist & Modern) -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-1 font-weight-bold">Total Siswa</h6>
                                    <span class="h3 font-weight-bold mb-0 text-dark">{{ $totalStudents }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-light text-success rounded-circle shadow-sm">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-1 font-weight-bold">Kelas & Jurusan</h6>
                                    <span class="h3 font-weight-bold mb-0 text-dark">{{ $totalClassrooms }} <small class="text-sm text-muted">/ {{ $totalMajors }}</small></span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-light text-warning rounded-circle shadow-sm">
                                        <i class="fas fa-chalkboard"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-1 font-weight-bold">Pemasukan Bulan Ini</h6>
                                    <span class="h4 font-weight-bold mb-0 text-success">Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-light text-success rounded-circle shadow-sm">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase text-muted mb-1 font-weight-bold">Pengeluaran Bulan Ini</h6>
                                    <span class="h4 font-weight-bold mb-0 text-danger">Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-light text-danger rounded-circle shadow-sm">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page content -->
<div class="container-fluid mt--6">
    <div class="row">
        <!-- Chart Section -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0">
                    <h3 class="mb-0">Statistik Keuangan (6 Bulan Terakhir)</h3>
                </div>
                <div class="card-body">
                    <!-- Chart wrapper -->
                    <div class="chart">
                        <canvas id="financeChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0">
                    <h3 class="mb-0">Aksi Cepat</h3>
                </div>
                <div class="card-body pb-0">
                    <div class="row text-center mb-0">
                        <div class="col-6 mb-4">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-light text-primary btn-block p-4 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center border-0" style="border-radius: 15px;">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                <span class="text-sm font-weight-bold">Siswa Baru</span>
                            </a>
                        </div>
                        <div class="col-6 mb-4">
                            <a href="{{ route('admin.transactions.create') }}" class="btn btn-light text-success btn-block p-4 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center border-0" style="border-radius: 15px;">
                                <i class="fas fa-money-bill-wave fa-2x mb-2 d-block"></i>
                                <span class="text-sm font-weight-bold">Catat Transaksi</span>
                            </a>
                        </div>
                        <div class="col-6 mb-4">
                            <a href="{{ route('admin.reports.payment') }}" class="btn btn-light text-info btn-block p-4 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center border-0" style="border-radius: 15px;">
                                <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                                <span class="text-sm font-weight-bold">Laporan Kas</span>
                            </a>
                        </div>
                        <div class="col-6 mb-4">
                            <a href="{{ route('admin.spp.index') }}" class="btn btn-light text-warning btn-block p-4 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center border-0" style="border-radius: 15px;">
                                <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                                <span class="text-sm font-weight-bold">Cek Tagihan SPP</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Histori Pembayaran SPP Terbaru</h3>
                    <a href="{{ route('admin.spp.recap') }}" class="btn btn-sm btn-success px-3 rounded-pill text-uppercase">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. KK / Keluarga</th>
                                <th>Metode Bayar</th>
                                <th>Jumlah Dibayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $pay)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm text-white rounded-circle shadow-sm mr-3" style="background: linear-gradient(135deg, #1a8a5c, #2dce89);">
                                            <i class="fas fa-calendar-alt fa-xs"></i>
                                        </div>
                                        <span class="font-weight-bold">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $pay->family_card_number }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-dot mr-4">
                                        <i class="bg-success"></i>
                                        <span class="status">{{ $pay->payment_method }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success">+ Rp {{ number_format($pay->amount, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @empty
                                <x-empty-state />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('financeChart').getContext('2d');

        var chartLabels = @js($chartLabels);
        var chartIncome = @js($chartIncome);
        var chartExpense = @js($chartExpense);

        var financeChart = new Chart(ctx, {
            type: 'bar', // Anda juga bisa mengubah ke 'line' jika lebih suka *line chart*
            data: {
                labels: chartLabels,
                datasets: [{
                        label: 'Pemasukan',
                        data: chartIncome,
                        backgroundColor: 'rgba(45, 206, 137, 0.8)', // Success green
                        borderColor: 'rgba(45, 206, 137, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran',
                        data: chartExpense,
                        backgroundColor: 'rgba(245, 54, 92, 0.8)', // Danger red
                        borderColor: 'rgba(245, 54, 92, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + ' Jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000) + ' Rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection