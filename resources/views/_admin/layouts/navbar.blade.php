@php
    $routeName = request()->route() ? request()->route()->getName() : '';
    $routeParts = explode('.', $routeName);
    $routeBase = in_array($routeParts[0], ['admin', 'student']) && isset($routeParts[1]) ? $routeParts[1] : $routeParts[0];
    
    $pageTitle = match($routeBase) {
        'dashboard' => 'Dashboard',
        'transactions' => 'Transaksi',
        'students' => 'Data Siswa',
        'payment-types' => 'Jenis Pembayaran',
        'payment-rates' => 'Tarif Pembayaran',
        'classrooms' => 'Data Kelas',
        'majors' => 'Data Jurusan',
        'enrollments' => 'Kenaikan Kelas',
        'users' => 'Data Pengguna',
        'academic-years' => 'Tahun Ajaran',
        'semesters' => 'Data Semester',
        'spp' => 'SPP / Tagihan',
        'bills' => 'Tagihan SPP',
        'schedules' => 'Jadwal Pelajaran',
        'profile' => 'Profil Saya',
        'reports' => 'Laporan',
        default => 'Dashboard',
    };
@endphp

<style>
    /* Realtime Clock */
    .realtime-clock-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        padding: 8px 16px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #1e293b;
        font-weight: 500;
        font-size: 13px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .realtime-clock-container .clock-icon {
        color: #1a8a5c;
        font-size: 14px;
    }

    .realtime-clock-container .clock-date {
        color: #475569;
    }

    .realtime-clock-container .clock-time {
        color: #1a8a5c;
        font-weight: 700;
    }
    
    .realtime-clock-container:hover {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    /* User Dropdown Trigger */
    .user-dropdown-trigger {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.6);
        padding: 6px 14px 6px 6px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .user-dropdown-trigger:hover {
        background: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .user-dropdown-trigger .user-name {
        color: #1e293b;
        font-weight: 600;
        font-size: 13px;
    }

    .user-dropdown-trigger .user-role {
        color: #64748b;
        font-size: 10px;
    }

    .user-dropdown-trigger .chevron-icon {
        color: #94a3b8;
        font-size: 10px;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 13px;
        background: linear-gradient(135deg, #1a8a5c, #2dce89);
        box-shadow: 0 4px 6px rgba(45, 206, 137, 0.2);
    }

    /* Dropdown Menu */
    .modern-dropdown {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 8px;
        min-width: 260px;
        overflow: hidden;
        animation: dropdownFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header-custom {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 8px;
    }

    .dropdown-header-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        background: linear-gradient(135deg, #1a8a5c, #2dce89);
        box-shadow: 0 4px 6px rgba(45, 206, 137, 0.2);
        flex-shrink: 0;
    }

    .modern-dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        color: #475569;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s ease;
        text-decoration: none;
        margin-bottom: 4px;
    }

    .modern-dropdown-item:hover {
        background: #f8fafc;
        color: #1a8a5c;
        transform: translateX(4px);
    }

    .modern-dropdown-item .icon-container {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .modern-dropdown-item:hover .icon-container {
        background: #E6F9F0;
        color: #1a8a5c;
    }

    /* Logout Button */
    .logout-btn {
        background: #fff1f2;
        color: #e11d48;
        width: 100%;
        border: none;
        text-align: left;
        margin-bottom: 0;
    }

    .logout-btn .icon-container {
        background: #ffe4e6;
        color: #e11d48;
    }

    .logout-btn:hover {
        background: #ffe4e6;
        color: #be123c;
        transform: translateY(-1px) translateX(0);
        box-shadow: 0 4px 6px rgba(225, 29, 72, 0.1);
    }

    .logout-btn:hover .icon-container {
        background: #fecdd3;
        color: #be123c;
    }
</style>

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $pageTitle }}</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0">{{ $pageTitle }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                
                {{-- Live Realtime Clock --}}
                <div class="realtime-clock-container d-none d-sm-flex">
                    <i class="fas fa-clock clock-icon"></i>
                    <div>
                        <span id="realtime-date" class="me-1 clock-date">Memuat...</span> &bull; 
                        <span id="realtime-time" class="ms-1 clock-time">--:--:--</span>
                    </div>
                </div>

            </div>
            <ul class="navbar-nav justify-content-end align-items-center">

                {{-- User Dropdown --}}
                <li class="nav-item dropdown d-flex align-items-center pe-2">

                    {{-- Trigger --}}
                    <a href="javascript:;" class="nav-link p-0 text-white" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-dropdown-trigger">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-sm-block lh-sm text-start">
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <i class="fa fa-chevron-down chevron-icon ms-1"></i>
                        </div>
                    </a>

                    {{-- Dropdown --}}
                    <ul class="dropdown-menu dropdown-menu-end modern-dropdown"
                        id="navbarUserDropdown" aria-labelledby="dropdownMenuButton">

                        {{-- Header --}}
                        <li class="dropdown-header-custom">
                            <div class="dropdown-header-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:14px;">{{ Auth::user()->name }}</div>
                                <div class="text-secondary" style="font-size:11px;">{{ Auth::user()->email }}</div>
                                <span class="badge rounded-pill mt-1"
                                    style="font-size:10px; background:#E6F9F0; color:#1a8a5c; font-weight:600;">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                        </li>

                        <li class="px-2 pb-2">
                            <a href="{{ auth()->user()->role === 'student' ? route('student.profile') : '#' }}" class="dropdown-item modern-dropdown-item">
                                <div class="icon-container">
                                    <i class="fas fa-user" style="font-size:13px;"></i>
                                </div>
                                <span>Profile Saya</span>
                            </a>

                            <a href="#" class="dropdown-item modern-dropdown-item">
                                <div class="icon-container">
                                    <i class="fas fa-cog" style="font-size:13px;"></i>
                                </div>
                                <span>Pengaturan</span>
                            </a>

                            <hr class="my-2 opacity-10">

                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item modern-dropdown-item logout-btn">
                                    <div class="icon-container">
                                        <i class="fas fa-sign-out-alt" style="font-size:13px;"></i>
                                    </div>
                                    <span class="fw-bold">Logout</span>
                                </button>
                            </form>

                        </li>
                    </ul>
                </li>

                {{-- Sidenav Toggle (mobile) --}}
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                        </div>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeDisplay = document.getElementById('realtime-time');
        const dateDisplay = document.getElementById('realtime-date');
        
        function updateClock() {
            if (!timeDisplay || !dateDisplay) return;

            const now = new Date();
            
            // Format time
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeDisplay.textContent = `${hours}:${minutes}:${seconds}`;
            
            // Format date (Indonesian)
            const days = ['Ming', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            
            dateDisplay.textContent = `${dayName}, ${date} ${monthName}`;
        }
        
        // Initial call
        updateClock();
        
        // Update every second
        setInterval(updateClock, 1000);
    });
</script>
