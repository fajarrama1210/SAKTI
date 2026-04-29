<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ url('/') }}">
            <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Creative Tim</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <style>
        .navbar-nav .nav-item .nav-link {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 2px 15px;
            padding: 10px 15px;
            color: #67748e;
        }

        /* Hover Effect */
        .navbar-nav .nav-item .nav-link:hover {
            background-color: rgba(94, 114, 228, 0.1);
            color: #5e72e4 !important;
            transform: translateX(5px);
        }

        .navbar-nav .nav-item .nav-link:hover .icon i,
        .navbar-nav .nav-item .nav-link:hover .icon svg {
            color: #5e72e4 !important;
            fill: #5e72e4 !important;
        }

        /* Active State */
        .navbar-nav .nav-item .nav-link.active-menu {
            background-color: #5e72e4;
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(94, 114, 228, 0.3);
        }

        .navbar-nav .nav-item .nav-link.active-menu .icon i,
        .navbar-nav .nav-item .nav-link.active-menu .icon svg {
            color: #fff !important;
            fill: #fff !important;
        }

        .navbar-nav .nav-item .nav-link.active-menu:hover {
            transform: none;
            background-color: #5e72e4;
        }
    </style>
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main" style="height: calc(100vh - 120px) !important;">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}" href="{{ route('dashboard') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.dashboard')
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data Master</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active-menu' : '' }}" href="{{ route('admin.academic-years.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.calendar')
                    </div>
                    <span class="nav-link-text ms-1">Tahun Ajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.semesters.*') ? 'active-menu' : '' }}" href="{{ route('admin.semesters.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.semester')
                    </div>
                    <span class="nav-link-text ms-1">Semester</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.majors.*') ? 'active-menu' : '' }}" href="{{ route('admin.majors.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.major')
                    </div>
                    <span class="nav-link-text ms-1">Jurusan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.classrooms.*') ? 'active-menu' : '' }}" href="{{ route('admin.classrooms.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.classroom')
                    </div>
                    <span class="nav-link-text ms-1">Kelas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active-menu' : '' }}" href="{{ route('admin.students.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.student')
                    </div>
                    <span class="nav-link-text ms-1">Siswa (Master)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active-menu' : '' }}" href="{{ route('admin.enrollments.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.classroom')
                    </div>
                    <span class="nav-link-text ms-1">Penempatan Siswa</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Keuangan</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.payment-types.*') ? 'active-menu' : '' }}" href="{{ route('admin.payment-types.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.tag')
                    </div>
                    <span class="nav-link-text ms-1">Jenis Pembayaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.payment-rates.*') ? 'active-menu' : '' }}" href="{{ route('admin.payment-rates.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.money')
                    </div>
                    <span class="nav-link-text ms-1">Tarif Pembayaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.spp.*') ? 'active-menu' : '' }}" href="{{ route('admin.spp.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.invoice')
                    </div>
                    <span class="nav-link-text ms-1">Pembayaran SPP</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active-menu' : '' }}" href="{{ route('admin.transactions.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.transaction')
                    </div>
                    <span class="nav-link-text ms-1">Transaksi</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Laporan</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->url() == route('admin.reports.payment') ? 'active-menu' : '' }}" href="{{ route('admin.reports.payment') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.report')
                    </div>
                    <span class="nav-link-text ms-1">Laporan Pembayaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->url() == route('admin.reports.transaction') ? 'active-menu' : '' }}" href="{{ route('admin.reports.transaction') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.report')
                    </div>
                    <span class="nav-link-text ms-1">Laporan Transaksi</span>
                </a>
            </li>
        </ul>
    </div>
</aside>