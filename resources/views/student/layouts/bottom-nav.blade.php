{{-- ============================================================
    SAKTI — Student Mobile Bottom Navigation
    5-tab bottom nav: Dashboard, Tagihan, Jadwal, Surat, Profil
    ============================================================ --}}

@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';

    // Count unpaid bills for badge
    $unpaidCount = 0;
    if (isset($unpaidBillsCount)) {
        $unpaidCount = $unpaidBillsCount;
    }
@endphp

<nav class="mobile-bottomnav" id="mobileBottomNav">
    {{-- Dashboard --}}
    <a href="{{ route('student.dashboard') }}"
       class="mobile-nav-item {{ $currentRoute === 'student.dashboard' ? 'active' : '' }}">
        <i class="fas fa-home mobile-nav-icon"></i>
        <span class="mobile-nav-label">Dashboard</span>
    </a>

    {{-- Tagihan --}}
    <a href="{{ route('student.bills') }}"
       class="mobile-nav-item {{ $currentRoute === 'student.bills' ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar mobile-nav-icon"></i>
        <span class="mobile-nav-label">Tagihan</span>
        @if($unpaidCount > 0)
            <span class="mobile-nav-badge">{{ $unpaidCount > 9 ? '9+' : $unpaidCount }}</span>
        @endif
    </a>

    {{-- Jadwal --}}
    <a href="{{ route('student.schedules') }}"
       class="mobile-nav-item {{ $currentRoute === 'student.schedules' ? 'active' : '' }}">
        <i class="fas fa-calendar-alt mobile-nav-icon"></i>
        <span class="mobile-nav-label">Jadwal</span>
    </a>

    {{-- Surat --}}
    <a href="{{ route('student.letters.index') }}"
       class="mobile-nav-item {{ $currentRoute === 'student.letters.index' ? 'active' : '' }}">
        <i class="fas fa-envelope mobile-nav-icon"></i>
        <span class="mobile-nav-label">Surat</span>
    </a>

    {{-- Profil --}}
    <a href="{{ route('student.profile') }}"
       class="mobile-nav-item {{ $currentRoute === 'student.profile' ? 'active' : '' }}">
        <i class="fas fa-user-circle mobile-nav-icon"></i>
        <span class="mobile-nav-label">Profil</span>
    </a>
</nav>
