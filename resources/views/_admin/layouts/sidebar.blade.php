<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main" style="overflow: hidden !important;">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/SAKTI.png') }}" class="navbar-brand-img" alt="SAKTI Logo" style="max-height: 36px;">
            <span class="ms-2 font-weight-bold" style="font-size: 1.5rem; color: #1a8a5c;">SAKTI</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <style>
        .navbar-nav .nav-item .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            margin: 2px 15px;
            padding: 10px 15px;
            color: #67748e;
        }

        /* Hover Effect - Green */
        .navbar-nav .nav-item .nav-link:hover {
            background-color: rgba(45, 206, 137, 0.1);
            color: #1a8a5c !important;
            transform: translateX(5px);
        }

        .navbar-nav .nav-item .nav-link:hover .icon i,
        .navbar-nav .nav-item .nav-link:hover .icon svg {
            color: #2dce89 !important;
            fill: #2dce89 !important;
        }

        .navbar-nav .nav-item .nav-link:hover .nav-link-text {
            color: #1a8a5c !important;
        }

        /* Active State - Green */
        .navbar-nav .nav-item .nav-link.active-menu {
            background: linear-gradient(135deg, #1a8a5c, #2dce89);
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(45, 206, 137, 0.35);
        }

        .navbar-nav .nav-item .nav-link.active-menu .icon i,
        .navbar-nav .nav-item .nav-link.active-menu .icon svg {
            color: #fff !important;
            fill: #fff !important;
        }

        .navbar-nav .nav-item .nav-link.active-menu .nav-link-text {
            color: #fff !important;
        }

        .navbar-nav .nav-item .nav-link.active-menu:hover {
            transform: none;
            background: linear-gradient(135deg, #1a8a5c, #2dce89);
        }

        /* Section headers */
        .navbar-nav .nav-item h6 {
            color: #1a8a5c !important;
            letter-spacing: 0.08em;
        }

        /* Custom Minimalist Scrollbar for Sidebar */
        #sidenav-collapse-main {
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(26, 138, 92, 0.3) transparent;
        }

        #sidenav-collapse-main::-webkit-scrollbar {
            width: 4px;
        }

        #sidenav-collapse-main::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidenav-collapse-main::-webkit-scrollbar-thumb {
            background-color: rgba(26, 138, 92, 0.3);
            border-radius: 10px;
        }

        #sidenav-collapse-main::-webkit-scrollbar-thumb:hover {
            background-color: rgba(26, 138, 92, 0.7);
        }
    </style>
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main" style="height: calc(100vh - 120px) !important;">
        <ul class="navbar-nav">
            @if(auth()->user()->role === 'admin')
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
                    <span class="nav-link-text ms-1">Siswa</span>
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
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active-menu' : '' }}" href="{{ route('admin.schedules.index') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.schedule')
                    </div>
                    <span class="nav-link-text ms-1">Jadwal</span>
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
                <a class="nav-link {{ request()->routeIs('admin.spp.*') && !request()->routeIs('admin.spp.matrix') ? 'active-menu' : '' }}" href="{{ route('admin.spp.index') }}">
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
                <a class="nav-link {{ request()->routeIs('admin.spp.matrix') ? 'active-menu' : '' }}" href="{{ route('admin.spp.matrix') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.matrix')
                    </div>
                    <span class="nav-link-text ms-1">Matrix Pembayaran</span>
                </a>
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
            @elseif(auth()->user()->role === 'student')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active-menu' : '' }}" href="{{ route('student.dashboard') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.dashboard')
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Menu Siswa</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.bills') ? 'active-menu' : '' }}" href="{{ route('student.bills') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.invoice')
                    </div>
                    <span class="nav-link-text ms-1">Tagihan & Pembayaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.schedules') ? 'active-menu' : '' }}" href="{{ route('student.schedules') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.schedule')
                    </div>
                    <span class="nav-link-text ms-1">Jadwal Pelajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('student.profile') ? 'active-menu' : '' }}" href="{{ route('student.profile') }}">
                    <div class="icon icon-shape border-radius-md me-2 d-flex align-items-center justify-content-center">
                        @include('icon.student')
                    </div>
                    <span class="nav-link-text ms-1">Profil Saya</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</aside>