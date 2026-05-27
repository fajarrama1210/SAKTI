@extends('_admin.layouts.app')

@push('styles')
    @include('_admin.layouts.sakti-custom')
@endpush

@section('content')
<div class="container-fluid mt--6">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="dashboard-card text-center py-5">

                {{-- Decorative Icon --}}
                <div class="mb-4">
                    <div class="stats-icon bg-primary text-white mx-auto" style="width: 80px; height: 80px; font-size: 1.8rem;">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                </div>

                {{-- Greeting --}}
                <h2 class="font-weight-bold mb-2" style="color: var(--dark-text);">
                    Selamat Datang di SAKTI
                </h2>
                <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Sistem Administrasi Keuangan Terpadu & Informasi — Silakan gunakan menu di sidebar untuk menavigasi.
                </p>

                {{-- Quick Navigation --}}
                <div class="row justify-content-center mt-4 px-4">
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('dashboard') }}" class="quick-action d-flex flex-column align-items-center justify-content-center p-4 text-primary text-decoration-none">
                            <i class="fas fa-chart-pie fa-2x mb-3"></i>
                            <span class="font-weight-bold text-center">Dashboard Utama</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
