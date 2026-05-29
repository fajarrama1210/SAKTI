<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover">
    <title>SAKTI | Portal Siswa</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/sakti favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sakti favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/sakti favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/sakti favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="SAKTI" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="{{ asset('assets/sakti favicon/site.webmanifest') }}" />

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- Argon CSS -->
    <link id="pagestyle" href="{{ asset('assets/css/argon-dashboard.css') }}?v=2.1.0" rel="stylesheet" />

    <!-- SAKTI Responsive -->
    <link href="{{ asset('assets/css/sakti-responsive.css') }}" rel="stylesheet" />

    <!-- SAKTI Mobile -->
    <link href="{{ asset('assets/css/sakti-mobile.css') }}" rel="stylesheet" />

    @stack('styles')

    <style>
        body, h1, h2, h3, h4, h5, h6, p, span, a, td, th,
        .btn, input, select, textarea, label, .nav-link {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }

        /* Pagination — same as admin */
        .pagination { gap: 6px; display: flex; align-items: center; }
        .page-item .page-link, .page-item span {
            border: none !important; border-radius: 8px !important;
            color: #67748e; font-weight: 600;
            padding: 8px 16px !important;
            transition: all 0.3s ease;
            background-color: #f8f9fa !important;
            width: auto !important; height: auto !important;
            display: inline-flex !important; align-items: center; justify-content: center;
            font-size: 0.875rem; white-space: nowrap !important; margin: 0 !important;
        }
        .page-item .page-link:hover {
            background-color: #059669 !important; color: #ffffff !important;
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.2) !important; transform: translateY(-1px);
        }
        .page-item.active .page-link {
            background-color: #059669 !important; color: #ffffff !important;
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3) !important;
        }
        .page-item.disabled .page-link, .page-item.disabled span {
            color: #94a3b8 !important; background-color: #f1f5f9 !important;
            opacity: 0.6; pointer-events: none;
        }

        #navbarUserDropdown { display: none !important; opacity: 0 !important; animation: none !important; }
        #navbarUserDropdown.open { display: block !important; opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; }
        .table-responsive { min-height: 300px; }
        .dropdown-menu:not(.show) { display: none !important; }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100 student-mobile">

    {{-- Green gradient header bar — visible desktop, hidden mobile via CSS --}}
    <div class="min-height-300 position-absolute w-100"
        style="background: linear-gradient(135deg, #064e3b 0%, #059669 50%, #34d399 100%);"></div>

    {{-- Sidebar — visible desktop only (d-none d-lg-block handled by sakti-responsive.css + sakti-mobile.css) --}}
    @include('_admin.layouts.sidebar')

    {{-- Mobile Top Bar — visible mobile only --}}
    <div class="d-lg-none">
        @include('student.layouts.top-bar')
    </div>

    <main class="main-content position-relative border-radius-lg">
        {{-- Desktop Navbar — visible desktop only --}}
        <div class="d-none d-lg-block">
            @include('_admin.layouts.navbar')
        </div>

        <div class="container-fluid py-4">
            @yield('content')

            {{-- Footer — visible desktop only (hidden on mobile via CSS) --}}
            <footer class="footer pt-4 pb-4 mt-150">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-3">
                            <div class="copyright text-center text-lg-start text-dark"
                                style="font-size: .95rem; letter-spacing: .4px;">
                                © <script>document.write(new Date().getFullYear())</script>
                                made with <i class="fa fa-heart text-danger"></i>
                                by <span class="font-weight text-dark">Bolo Tuhan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    {{-- Mobile Bottom Navigation — visible mobile only --}}
    <div class="d-lg-none">
        @include('student.layouts.bottom-nav')
    </div>

    <!-- Scripts — same as admin layout -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script>
        if (typeof PerfectScrollbar !== 'undefined') {
            const OriginalPS = window.PerfectScrollbar;
            window.PerfectScrollbar = function(element, options) {
                if (element && (
                    element.classList.contains('sidenav') ||
                    element.classList.contains('navbar-collapse') ||
                    element.id === 'sidenav-collapse-main'
                )) {
                    return { update: function() {}, destroy: function() {} };
                }
                return new OriginalPS(element, options);
            };
        }
    </script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/argon-dashboard.min.js') }}?v=2.1.0"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // SweetAlert: Success Toast
            @if (session('success'))
                const Toast = Swal.mixin({
                    toast: true, position: "top-end", showConfirmButton: false,
                    timer: 3000, timerProgressBar: true,
                    didOpen: (toast) => { toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer; }
                });
                Toast.fire({ icon: "success", title: {!! json_encode(session('success')) !!} });
            @endif

            @if (session('error'))
                Swal.fire({ icon: "error", title: "Oops...", text: {!! json_encode(session('error')) !!} });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: "error", title: "Gagal Menyimpan Data",
                    html: `<ul class="text-start mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`
                });
            @endif

            // Delete Confirmation
            document.querySelectorAll('form.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Yakin ingin menghapus?', text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning', showCancelButton: true,
                        confirmButtonColor: '#f5365c', cancelButtonColor: '#8898aa',
                        confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal', reverseButtons: true
                    }).then((result) => { if (result.isConfirmed) form.submit(); });
                });
            });

            // Desktop dropdown (same as admin)
            var trigger = document.getElementById('dropdownMenuButton');
            var menu = document.getElementById('navbarUserDropdown');
            if (trigger && menu) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    menu.classList.toggle('open');
                    trigger.setAttribute('aria-expanded', menu.classList.contains('open'));
                });
                document.addEventListener('click', function(e) {
                    if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                        menu.classList.remove('open');
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Sidebar scroll persistence
            var sidebar = document.getElementById('sidenav-collapse-main');
            if (sidebar) {
                var savedScroll = sessionStorage.getItem('sidebarScrollTop');
                if (savedScroll !== null) sidebar.scrollTop = parseInt(savedScroll, 10);
                window.addEventListener('beforeunload', function() {
                    sessionStorage.setItem('sidebarScrollTop', sidebar.scrollTop);
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
