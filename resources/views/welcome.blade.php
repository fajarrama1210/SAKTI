<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SAKTI - Sistem Keuangan Terintegrasi untuk institusi pendidikan. Solusi cerdas kelola keuangan sekolah.">

    <title>{{ config('app.name', 'SAKTI') }} — Sistem Keuangan Terintegrasi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --green-50: #ecfdf5;
            --green-100: #d1fae5;
            --green-200: #a7f3d0;
            --green-300: #6ee7b7;
            --green-400: #34d399;
            --green-500: #10b981;
            --green-600: #059669;
            --green-700: #047857;
            --green-800: #065f46;
            --green-900: #064e3b;
            --green-950: #022c22;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
        }

        .navbar-brand img {
            height: 36px;
            width: auto;
        }

        .navbar-brand span {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: 1px;
        }

        .navbar-cta {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.5rem;
            border: 2px solid var(--green-600);
            border-radius: 10px;
            color: var(--green-700);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-portal:hover {
            background: var(--green-600);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        .btn-portal i {
            font-size: 0.8rem;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            padding-top: 72px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f8fffe 0%, #f0fdf4 30%, #ecfdf5 60%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: var(--green-600);
            color: #fff;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .hero-badge i {
            font-size: 0.65rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-desc {
            font-size: 1rem;
            color: var(--gray-500);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 520px;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, var(--green-600), var(--green-700));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            background: linear-gradient(135deg, var(--green-700), var(--green-800));
        }

        .btn-secondary-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--gray-600);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-secondary-link:hover {
            color: var(--green-700);
        }

        .hero-image {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInRight 0.8s ease-out 0.3s both;
        }

        .hero-image img {
            width: 100%;
            max-width: 560px;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.08));
            animation: floatImage 6s ease-in-out infinite;
        }

        @keyframes floatImage {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            padding: 5rem 2rem;
            background: #ffffff;
        }

        .features-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--green-400), var(--green-600));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover {
            border-color: var(--green-200);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            transform: translateY(-4px);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: var(--green-50);
            color: var(--green-600);
            font-size: 1.4rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            background: var(--green-600);
            color: #fff;
            transform: scale(1.05);
        }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }

        .feature-desc {
            font-size: 0.875rem;
            color: var(--gray-500);
            line-height: 1.7;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .footer-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem 3rem;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
            gap: 3rem;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
        }

        .footer-brand-top {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1rem;
        }

        .footer-brand-top img {
            height: 32px;
            width: auto;
        }

        .footer-brand-top span {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: 1px;
        }

        .footer-brand p {
            font-size: 0.85rem;
            color: var(--gray-500);
            line-height: 1.7;
            margin-bottom: 1.25rem;
            max-width: 280px;
        }

        .footer-socials {
            display: flex;
            gap: 0.75rem;
        }

        .footer-socials a {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .footer-socials a:hover {
            background: var(--green-600);
            color: #fff;
            transform: translateY(-2px);
        }

        .footer-col h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.25rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 0.7rem;
        }

        .footer-col ul li a {
            font-size: 0.85rem;
            color: var(--gray-500);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-col ul li a:hover {
            color: var(--green-600);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }

        .footer-contact-item i {
            color: var(--green-600);
            font-size: 0.85rem;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .footer-contact-item span {
            font-size: 0.85rem;
            color: var(--gray-500);
            line-height: 1.5;
        }

        .footer-bottom {
            border-top: 1px solid var(--gray-200);
            padding: 1.25rem 2rem;
            text-align: center;
        }

        .footer-bottom p {
            font-size: 0.8rem;
            color: var(--gray-400);
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-inner {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .hero-title {
                font-size: 2.75rem;
            }

            .hero-desc {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-image {
                order: -1;
            }

            .hero-image img {
                max-width: 420px;
            }

            .features-inner {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar-inner {
                padding: 0 1.25rem;
                height: 64px;
            }

            .hero-inner {
                padding: 3rem 1.25rem;
            }

            .hero-title {
                font-size: 2.25rem;
            }

            .hero-desc {
                font-size: 0.925rem;
            }

            .features {
                padding: 3rem 1.25rem;
            }

            .footer-main {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 3rem 1.25rem 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.85rem;
            }

            .hero-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            .navbar-brand span {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar" id="navbar">
        <div class="navbar-inner">
            <a href="/" class="navbar-brand" id="brand-logo">
                <img src="{{ asset('assets/img/SAKTI.png') }}" alt="SAKTI Logo">
                <span>SAKTI</span>
            </a>
            <div class="navbar-cta">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-portal" id="btn-dashboard">
                            <i class="fas fa-th-large"></i>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-portal" id="btn-masuk-portal">
                            <i class="fas fa-user"></i>
                            Masuk Portal
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero" id="hero">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-circle-check"></i>
                    EDUCATION & FINANCE
                </div>
                <h1 class="hero-title">
                    Solusi Cerdas<br>
                    Kelola <span class="highlight">Keuangan<br>Sekolah.</span>
                </h1>
                <p class="hero-desc">
                    SAKTI (Sistem Keuangan Terintegrasi) hadir sebagai solusi universal bagi berbagai institusi pendidikan guna menyatukan seluruh aliran data keuangan dalam satu platform pusat yang efisien, transparan, dan akuntabel.
                </p>
                <div class="hero-actions">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary" id="btn-hero-dashboard">
                                Mulai Sekarang <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary" id="btn-hero-mulai">
                                Mulai Sekarang <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth
                    @endif
                    <a href="#features" class="btn-secondary-link" id="btn-pelajari">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('assets/img/elemen login.png') }}" alt="SAKTI Financial Dashboard Illustration">
            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section class="features" id="features">
        <div class="features-inner">
            <div class="feature-card" id="feature-realtime">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Real-Time</h3>
                <p class="feature-desc">Data keuangan terupdate secara instan dari semua unit sekolah.</p>
            </div>
            <div class="feature-card" id="feature-security">
                <div class="feature-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h3 class="feature-title">Keamanan Tinggi</h3>
                <p class="feature-desc">Enkripsi data tingkat lanjut untuk melindungi aset informasi sekolah.</p>
            </div>
            <div class="feature-card" id="feature-report">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="feature-title">Laporan Akurat</h3>
                <p class="feature-desc">Generate laporan BOS dan SPP secara otomatis dan akurat.</p>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer" id="footer">
        <div class="footer-main">
            <div class="footer-brand">
                <div class="footer-brand-top">
                    <img src="{{ asset('assets/img/SAKTI.png') }}" alt="SAKTI Logo">
                    <span>SAKTI</span>
                </div>
                <p>Sistem Manajemen Akademik Terintegrasi yang memimpin untuk keunggulan pendidikan modern dan efisiensi administrasi.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Instagram" id="social-ig"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook" id="social-fb"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="LinkedIn" id="social-linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Akademik</h4>
                <ul>
                    <li><a href="#">Program Keahlian</a></li>
                    <li><a href="#">Fasilitas Praktik</a></li>
                    <li><a href="#">Ekstrakurikuler</a></li>
                    <li><a href="#">Prestasi Siswa</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Informasi</h4>
                <ul>
                    <li><a href="#">Profil Sekolah</a></li>
                    <li><a href="#">Visi & Misi</a></li>
                    <li><a href="#">Info PPDB</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Kontak Kami</h4>
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

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} SAKTI Academic System by Bolo Tuhan. All rights reserved.</p>
        </div>
    </footer>

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
