<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SAKTI - Sistem Manajemen Akademik Terpadu Informasi. Platform modern untuk mengelola keuangan dan administrasi sekolah yang efisien, transparan, dan akuntabel.">
    <meta name="keywords" content="SAKTI, Sistem Keuangan Terintegrasi, Aplikasi Sekolah, Manajemen Sekolah, Keuangan Sekolah, SPP Online, Administrasi Akademik, Software Pendidikan">
    <meta name="author" content="Bolo Tuhan">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Sakti | Sistem Keuangan Terintegrasi">
    <meta property="og:description" content="Platform manajemen akademik modern untuk efisiensi administrasi dan keuangan institusi pendidikan Anda.">
    <meta property="og:image" content="{{ asset('assets/img/elemen login.webp') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Sakti | Sistem Keuangan Terintegrasi">
    <meta property="twitter:description" content="Platform manajemen akademik modern untuk efisiensi administrasi dan keuangan institusi pendidikan Anda.">
    <meta property="twitter:image" content="{{ asset('assets/img/elemen login.webp') }}">

    <title>Sakti | Sistem Keuangan Terintegrasi</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/sakti favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sakti favicon/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/sakti favicon/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/sakti favicon/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="SAKTI" />
    <link rel="manifest" href="{{ asset('assets/sakti favicon/site.webmanifest') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --green-50: #ecfdf5;
            --green-100: #d1fae5;
            --green-200: #a7f3d0;
            --green-400: #34d399;
            --green-500: #10b981;
            --green-600: #059669;
            --green-700: #047857;
            --green-800: #065f46;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-900: #111827;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #fff;
            color: var(--gray-600);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .sakti-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1050;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            transition: box-shadow 0.3s ease;
            padding: 0.75rem 0;
        }
        .sakti-navbar.scrolled { box-shadow: 0 4px 30px rgba(0,0,0,0.08); }

        .sakti-navbar .navbar-brand-custom {
            display: flex; align-items: center; gap: 0.6rem; text-decoration: none;
        }
        .sakti-navbar .navbar-brand-custom img { height: 32px; width: auto; }
        .sakti-navbar .navbar-brand-custom span {
            font-size: 1.25rem; font-weight: 800;
            color: var(--gray-900); letter-spacing: 1px;
        }

        .sakti-navbar .navbar-toggler {
            border: 1.5px solid var(--green-600);
            border-radius: 8px; padding: 5px 10px;
        }
        .sakti-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='%23059669' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .btn-portal {
            display: inline-flex; align-items: center; gap: 0.45rem;
            padding: 0.55rem 1.4rem;
            border: 2px solid var(--green-600); border-radius: 10px;
            color: var(--green-700); font-size: 0.875rem; font-weight: 600;
            text-decoration: none; transition: all 0.3s ease; white-space: nowrap;
        }
        .btn-portal:hover {
            background: var(--green-600); color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(5,150,105,0.3);
        }

        /* ===== HERO ===== */
        .hero {
            padding-top: 80px;
            min-height: 100vh; display: flex; align-items: center;
            background: linear-gradient(160deg, #f8fffe 0%, #f0fdf4 25%, #ecfdf5 55%, #fafffe 100%);
            position: relative; overflow: hidden;
            border-bottom: 1px solid rgba(5,150,105,0.08);
        }
        /* Accent line at the very top */
        .hero::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, transparent, var(--green-500), var(--green-400), transparent);
            pointer-events: none;
            z-index: 2;
        }
        .hero::after {
            content: ''; position: absolute;
            bottom: -100px; left: -100px; width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5,150,105,.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.4rem 1rem; background: var(--green-600); color: #fff;
            border-radius: 50px; font-size: 0.7rem; font-weight: 600;
            letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 1.25rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .hero-title {
            font-size: clamp(1.85rem, 5vw, 3.5rem);
            font-weight: 800; line-height: 1.15; color: var(--gray-900);
            margin-bottom: 1.25rem;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }
        .hero-title .highlight {
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        .hero-desc {
            font-size: 0.975rem; color: var(--gray-500); line-height: 1.8;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .hero-actions {
            display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .btn-primary-green {
            display: inline-flex; align-items: center; gap: 0.6rem;
            padding: 0.8rem 1.8rem;
            background: linear-gradient(135deg, var(--green-600), var(--green-700));
            color: #fff !important; border: none; border-radius: 12px;
            font-size: 0.95rem; font-weight: 600; text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(5,150,105,.3);
        }
        .btn-primary-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5,150,105,.4);
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
        }

        .btn-secondary-link {
            display: inline-flex; align-items: center; gap: 0.4rem;
            color: var(--gray-600); font-size: 0.9rem; font-weight: 500;
            text-decoration: none; transition: all 0.3s ease;
        }
        .btn-secondary-link:hover { color: var(--green-700); }

        .hero-image {
            display: flex; align-items: center; justify-content: center;
            animation: fadeInRight 0.8s ease-out 0.3s both;
        }
        .hero-image img {
            width: 100%; max-width: 500px; height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,.08));
            animation: floatImage 6s ease-in-out infinite;
        }

        @keyframes floatImage {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ===== FEATURES ===== */
        .section-features { padding: 5rem 0; background: #fff; }

        .feature-card {
            background: #fff; border: 1px solid var(--gray-200);
            border-radius: 16px; padding: 2rem 1.5rem; text-align: center;
            transition: all 0.4s ease; position: relative; overflow: hidden;
            height: 100%;
        }
        .feature-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--green-400), var(--green-600));
            transform: scaleX(0); transition: transform 0.4s ease;
        }
        .feature-card:hover {
            border-color: var(--green-200);
            box-shadow: 0 10px 40px rgba(0,0,0,.06);
            transform: translateY(-4px);
        }
        .feature-card:hover::before { transform: scaleX(1); }

        .feature-icon {
            width: 60px; height: 60px; margin: 0 auto 1.25rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 14px; background: var(--green-50);
            color: var(--green-600); font-size: 1.4rem; transition: all 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            background: var(--green-600); color: #fff; transform: scale(1.05);
        }
        .feature-title {
            font-size: 1.05rem; font-weight: 700;
            color: var(--gray-900); margin-bottom: 0.65rem;
        }
        .feature-desc { font-size: 0.875rem; color: var(--gray-500); line-height: 1.7; margin: 0; }

        /* ===== FOOTER ===== */
        .footer-section {
            background: var(--gray-50); border-top: 1px solid var(--gray-200);
        }
        .footer-main { padding: 4rem 0 2.5rem; }
        .footer-brand-row { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.75rem; }
        .footer-brand-row img { height: 30px; }
        .footer-brand-row span { font-size: 1.1rem; font-weight: 800; color: var(--gray-900); letter-spacing: 1px; }
        .footer-desc { font-size: 0.85rem; color: var(--gray-500); line-height: 1.7; margin-bottom: 1.25rem; }
        .footer-socials { display: flex; gap: 0.6rem; }
        .footer-socials a {
            width: 36px; height: 36px; border-radius: 10px;
            background: var(--gray-200); display: flex; align-items: center; justify-content: center;
            color: var(--gray-600); text-decoration: none; transition: all 0.3s ease; font-size: 0.85rem;
        }
        .footer-socials a:hover { background: var(--green-600); color: #fff; transform: translateY(-2px); }

        .footer-col-title { font-size: 0.9rem; font-weight: 700; color: var(--gray-900); margin-bottom: 1rem; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 0.6rem; }
        .footer-links a { font-size: 0.85rem; color: var(--gray-500); text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: var(--green-600); }

        .footer-contact-item { display: flex; align-items: flex-start; gap: 0.6rem; margin-bottom: 0.65rem; }
        .footer-contact-item i { color: var(--green-600); font-size: 0.85rem; margin-top: 3px; flex-shrink: 0; }
        .footer-contact-item span { font-size: 0.85rem; color: var(--gray-500); line-height: 1.5; }

        .footer-bottom {
            border-top: 1px solid var(--gray-200); padding: 1.25rem 0; text-align: center;
        }
        .footer-bottom p { font-size: 0.8rem; color: var(--gray-400); margin: 0; }

        /* ===== xs (<576px) tweaks ===== */
        @media (max-width: 575.98px) {
            /* Hero: compact on mobile, no excessive whitespace */
            .hero {
                min-height: auto;
                padding-top: 72px;
                padding-bottom: 2rem;
                align-items: flex-start;
            }

            .hero-inner-row { padding-top: 1.5rem !important; }

            .hero-badge {
                font-size: 0.65rem;
                padding: 0.35rem 0.85rem;
                margin-bottom: 1rem;
            }

            .hero-title {
                font-size: clamp(1.65rem, 8vw, 2.2rem);
                margin-bottom: 0.85rem;
            }

            .hero-desc {
                font-size: 0.875rem;
                margin-bottom: 1.5rem;
                /* Shorten desc on mobile */
            }

            .hero-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .btn-primary-green {
                width: 100%;
                justify-content: center;
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }

            .btn-secondary-link {
                justify-content: center;
            }

            /* Hero image: hidden on xs, replaced by decorative blob */
            .hero-image-col {
                display: none !important;
            }

            /* Decorative floating card visible only on xs */
            .hero-mobile-visual {
                display: flex !important;
            }

            .section-features { padding: 2.5rem 0; }

            /* Feature cards: 1-per-row on xs */
            .feature-card { padding: 1.5rem 1.25rem; }
            .feature-icon { width: 52px; height: 52px; font-size: 1.2rem; }
        }

        /* ===== sm (576px-767px) tweaks ===== */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .hero {
                min-height: auto;
                padding-top: 72px;
                padding-bottom: 2.5rem;
            }

            .hero-title { font-size: clamp(1.9rem, 6vw, 2.5rem); }

            /* Hero image: show but smaller */
            .hero-image-col { display: block !important; }
            .hero-mobile-visual { display: none !important; }
            .hero-image img { max-width: 260px; }

            .hero-actions { flex-direction: column; align-items: stretch; gap: 0.75rem; }
            .btn-primary-green { width: 100%; justify-content: center; }
            .btn-secondary-link { justify-content: center; }

            .section-features { padding: 3rem 0; }
        }

        /* ===== md (768px–991px) tweaks ===== */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .hero-image img { max-width: 360px; }
            .hero-mobile-visual { display: none !important; }
        }

        /* ===== lg+ (992px+) ===== */
        @media (min-width: 992px) {
            .hero-mobile-visual { display: none !important; }
            .hero-image-col { display: block !important; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="sakti-navbar" id="navbar">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">
                <a href="/" class="navbar-brand-custom" id="brand-logo">
                    <img src="{{ asset('assets/img/SAKTI.svg') }}" alt="SAKTI Logo">
                    <span>SAKTI</span>
                </a>

                <!-- Mobile Toggler -->
                <button class="navbar-toggler d-lg-none" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarMain"
                        aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Desktop Nav -->
                <div class="d-none d-lg-flex align-items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-portal" id="btn-dashboard">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-portal" id="btn-masuk-portal">
                                <i class="fas fa-user"></i> Masuk Portal
                            </a>
                        @endauth
                    @endif
                </div>
            </div>

            <!-- Mobile Collapse Menu -->
            <div class="collapse" id="navbarMain">
                <div class="py-3 border-top mt-2 text-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-portal" id="btn-dashboard-mobile">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-portal" id="btn-masuk-portal-mobile">
                                <i class="fas fa-user"></i> Masuk Portal
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero" id="hero">
        <div class="container position-relative" style="z-index:1;">
            <div class="row align-items-center g-4 py-4 hero-inner-row">

                {{-- Hero Content: full width on xs/sm/md, left on lg+ --}}
                <div class="col-12 col-lg-6 order-1">
                    {{-- Mobile decorative visual (xs only) --}}
                    <div class="hero-mobile-visual d-none align-items-center gap-3 mb-4 p-3 rounded-4"
                         style="background: rgba(5,150,105,0.07); border: 1px solid rgba(5,150,105,0.12);">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#059669,#34d399);">
                            <i class="fas fa-chart-line text-white" style="font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.72rem;color:#64748b;font-weight:600;letter-spacing:.5px;text-transform:uppercase;">Platform Keuangan</div>
                            <div style="font-size:1rem;font-weight:800;color:#111827;">Terintegrasi & Aman</div>
                        </div>
                        <span class="ms-auto badge rounded-pill px-2 py-1"
                              style="background:#dcfce7;color:#16a34a;font-size:0.68rem;font-weight:700;">
                            <i class="fas fa-circle" style="font-size:0.4rem;vertical-align:middle;"></i> Live
                        </span>
                    </div>

                    <div class="hero-badge">
                        <i class="fas fa-circle-check"></i>
                        EDUCATION &amp; FINANCE
                    </div>
                    <h1 class="hero-title">
                        Solusi Cerdas<br>
                        Kelola <span class="highlight">Keuangan<br>Sekolah.</span>
                    </h1>
                    <p class="hero-desc">
                        SAKTI (Sistem Keuangan Terintegrasi) hadir sebagai solusi universal bagi berbagai institusi pendidikan guna menyatukan seluruh aliran data keuangan dalam satu platform pusat yang efisien, transparan, dan akuntabel.
                    </p>

                    {{-- Stats pills — compact info before CTA --}}
                    <div class="d-flex gap-3 flex-wrap mb-3 d-none d-sm-flex">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                             style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <i class="fas fa-shield-halved text-success" style="font-size:.85rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;color:#16a34a;">Data Terenkripsi</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                             style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <i class="fas fa-bolt text-success" style="font-size:.85rem;"></i>
                            <span style="font-size:.78rem;font-weight:600;color:#16a34a;">Real-Time Sync</span>
                        </div>
                    </div>

                    <div class="hero-actions justify-content-start">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary-green" id="btn-hero-dashboard">
                                    Buka Dashboard <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary-green" id="btn-hero-mulai">
                                    Mulai Sekarang <i class="fas fa-arrow-right"></i>
                                </a>
                            @endauth
                        @endif
                        <a href="#features" class="btn-secondary-link d-none d-sm-inline-flex" id="btn-pelajari">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                {{-- Hero Image: shows on sm+, hidden on xs (replaced by hero-mobile-visual) --}}
                <div class="col-12 col-sm-8 col-md-7 col-lg-6 order-2 mx-auto mx-lg-0 hero-image-col">
                    <div class="hero-image">
                        <img src="{{ asset('assets/img/elemen login.webp') }}" alt="SAKTI Financial Dashboard Illustration">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section class="section-features" id="features">
        <div class="container">
            <div class="text-center mb-4 mb-md-5">
                <span class="badge rounded-pill px-3 py-2 mb-3 d-inline-block"
                      style="background:#f0fdf4;color:#059669;font-size:0.75rem;font-weight:700;letter-spacing:.8px;border:1px solid #bbf7d0;">
                    FITUR UNGGULAN
                </span>
                <h2 style="font-size:clamp(1.4rem,4vw,2rem);font-weight:800;color:#111827;margin-bottom:.75rem;">
                    Semua yang Anda Butuhkan
                </h2>
                <p class="mx-auto" style="max-width:480px;color:#6b7280;font-size:.9rem;">
                    Platform lengkap untuk mengelola keuangan sekolah secara efisien dan transparan.
                </p>
            </div>
            <div class="row g-3 g-md-4 justify-content-center">

                <div class="col-12 col-sm-6 col-md-4" id="feature-realtime">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h3 class="feature-title">Real-Time</h3>
                        <p class="feature-desc">Data keuangan terupdate secara instan dari semua unit sekolah.</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4" id="feature-security">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                        <h3 class="feature-title">Keamanan Tinggi</h3>
                        <p class="feature-desc">Enkripsi data tingkat lanjut untuk melindungi aset informasi sekolah.</p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mx-auto mx-sm-0" id="feature-report">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                        <h3 class="feature-title">Laporan Akurat</h3>
                        <p class="feature-desc">Generate laporan BOS dan SPP secara otomatis dan akurat.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer-section" id="footer">
        <div class="footer-main">
            <div class="container">
                <div class="row g-4">

                    {{-- Brand: full width on xs/sm, 4 cols on md, 4 cols on lg --}}
                    <div class="col-12 col-md-12 col-lg-4">
                        <div class="footer-brand-row">
                            <img src="{{ asset('assets/img/SAKTI.svg') }}" alt="SAKTI Logo">
                            <span>SAKTI</span>
                        </div>
                        <p class="footer-desc">
                            Sistem Manajemen Akademik Terintegrasi yang memimpin untuk keunggulan pendidikan modern dan efisiensi administrasi.
                        </p>
                        <div class="footer-socials">
                            <a href="#" aria-label="Instagram" id="social-ig"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="Facebook" id="social-fb"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="LinkedIn" id="social-linkedin"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>

                    {{-- Akademik: half on xs, 3-col on md, 2-col on lg --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <h4 class="footer-col-title">Akademik</h4>
                        <ul class="footer-links">
                            <li><a href="#">Program Keahlian</a></li>
                            <li><a href="#">Fasilitas Praktik</a></li>
                            <li><a href="#">Ekstrakurikuler</a></li>
                            <li><a href="#">Prestasi Siswa</a></li>
                        </ul>
                    </div>

                    {{-- Informasi: half on xs, 3-col on md, 2-col on lg --}}
                    <div class="col-6 col-md-4 col-lg-2">
                        <h4 class="footer-col-title">Informasi</h4>
                        <ul class="footer-links">
                            <li><a href="#">Profil Sekolah</a></li>
                            <li><a href="#">Visi &amp; Misi</a></li>
                            <li><a href="#">Info PPDB</a></li>
                            <li><a href="#">Hubungi Kami</a></li>
                        </ul>
                    </div>

                    {{-- Kontak: full on xs, 4-col on md, 4-col on lg --}}
                    <div class="col-12 col-md-4 col-lg-4">
                        <h4 class="footer-col-title">Kontak Kami</h4>
                        <div class="footer-contact-item">
                            <i class="fas fa-location-dot"></i>
                            <span>Jl. Pendidikan No. 123, Indonesia</span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>admin@smkakbar.sch.id</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p>&copy; {{ date('Y') }} SAKTI Academic System by Bolo Tuhan. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Intersection Observer for feature cards animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.5s ease ${index * 0.15}s`;
            observer.observe(card);
        });
    </script>
</body>
</html>
