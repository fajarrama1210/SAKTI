{{-- ============================================================
    SAKTI — Student Mobile Top Bar
    Minimalist top bar for mobile student portal.
    ============================================================ --}}

@php
    $navAvatarUrl = null;
    if (Auth::check() && Auth::user()->avatar && Auth::user()->avatar !== '0') {
        try {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists(Auth::user()->avatar)) {
                $navAvatarUrl = asset('storage/' . Auth::user()->avatar);
            } else {
                $navAvatarUrl = \Illuminate\Support\Facades\Storage::disk('s3')->url(Auth::user()->avatar);
            }
        } catch (\Exception $e) {
            $navAvatarUrl = asset('storage/' . Auth::user()->avatar);
        }
    }
@endphp

<div class="mobile-topbar" id="mobileTopbar">
    {{-- Logo --}}
    <a href="{{ route('student.dashboard') }}" class="mobile-topbar-brand">
        <img src="{{ asset('assets/img/SAKTI.png') }}" alt="SAKTI">
        <span>SAKTI</span>
    </a>

    {{-- User --}}
    <div class="mobile-topbar-user" id="mobileUserTrigger">
        <div style="line-height: 1.2; text-align: right;">
            <div class="mobile-topbar-name">{{ Auth::user()->name }}</div>
            <div class="mobile-topbar-role">Siswa</div>
        </div>
        <div class="mobile-topbar-avatar">
            @if ($navAvatarUrl)
                <img src="{{ $navAvatarUrl }}" alt="Avatar">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>
    </div>

    {{-- Dropdown --}}
    <div class="mobile-topbar-dropdown" id="mobileUserDropdown">
        <a href="{{ route('student.profile') }}">
            <div class="dropdown-icon"><i class="fas fa-user"></i></div>
            <span>Profil Saya</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="logout-item">
                <div class="dropdown-icon"><i class="fas fa-sign-out-alt"></i></div>
                <span style="font-weight: 600;">Logout</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var trigger = document.getElementById('mobileUserTrigger');
    var dropdown = document.getElementById('mobileUserDropdown');
    var topbar = document.getElementById('mobileTopbar');

    if (trigger && dropdown) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    }

    // Add shadow on scroll
    if (topbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                topbar.classList.add('scrolled');
            } else {
                topbar.classList.remove('scrolled');
            }
        }, { passive: true });
    }
});
</script>
