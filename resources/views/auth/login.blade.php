<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Login ke Sistem Akademik Terpadu Informasi (SAKTI) untuk mengelola data akademik.">

    <title>Login — {{ config('app.name', 'SAKTI') }}</title>

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --teal-50: #f0fdfa;
            --teal-100: #ccfbf1;
            --teal-200: #99f6e4;
            --teal-400: #2dd4bf;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
            --teal-700: #0f766e;
            --teal-800: #115e59;
            --teal-900: #134e4a;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: #ffffff;
            color: #1e293b;
            margin: 0;
        }

        /* ===== FULL LAYOUT ===== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
        }

        /* ===== LEFT PANEL (illustration) ===== */
        .login-left {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 30%, #134e4a 60%, #042f2e 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 2rem;
            /* Hidden on xs/sm, visible md+ */
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%; right: -30%;
            width: 80%; height: 100%;
            background: radial-gradient(ellipse, rgba(20,184,166,.15) 0%, transparent 70%);
            animation: floatGlow 8s ease-in-out infinite;
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: -20%; left: -20%;
            width: 60%; height: 60%;
            background: radial-gradient(ellipse, rgba(45,212,191,.1) 0%, transparent 70%);
            animation: floatGlow 6s ease-in-out infinite reverse;
        }

        @keyframes floatGlow {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -20px) scale(1.05); }
        }

        .brand-logo {
            position: absolute; top: 2rem; left: 2.5rem;
            display: flex; align-items: center; gap: 0.75rem; z-index: 10;
        }
        .brand-logo .logo-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,.15); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(10px);
        }
        .brand-logo .logo-icon i { color: #fff; font-size: 1.1rem; }
        .brand-logo span { color: #fff; font-size: 1.3rem; font-weight: 700; letter-spacing: 1.5px; }

        .illustration-wrapper {
            position: relative; z-index: 5;
            max-width: 420px; width: 100%;
            animation: floatIllustration 6s ease-in-out infinite;
        }
        .illustration-wrapper img {
            width: 100%; height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,.3));
        }
        @keyframes floatIllustration {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Decorative particles */
        .particle {
            position: absolute; border-radius: 50%;
            background: rgba(45,212,191,.3);
            animation: particleFloat 10s ease-in-out infinite; z-index: 2;
        }
        .particle:nth-child(1) { width: 6px; height: 6px; top: 20%; left: 15%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 4px; height: 4px; top: 60%; left: 10%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 8px; height: 8px; top: 30%; right: 20%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; top: 70%; right: 15%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 3px; height: 3px; top: 85%; left: 40%; animation-delay: 3s; }

        @keyframes particleFloat {
            0%, 100% { transform: translateY(0) translateX(0); opacity: .3; }
            25% { transform: translateY(-30px) translateX(10px); opacity: .8; }
            50% { transform: translateY(-15px) translateX(-5px); opacity: .5; }
            75% { transform: translateY(-40px) translateX(15px); opacity: .7; }
        }

        /* ===== RIGHT PANEL (form) ===== */
        .login-right {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2rem 1.5rem;
            background: #ffffff;
            position: relative;
        }

        .login-form-wrapper {
            width: 100%; max-width: 420px;
        }

        /* Mobile brand (only visible on xs/sm when left panel is hidden) */
        .login-mobile-brand {
            display: flex; align-items: center; justify-content: center;
            gap: 0.65rem; margin-bottom: 1.5rem;
        }
        .login-mobile-brand .mob-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #0f766e, #2dd4bf);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
        }
        .login-mobile-brand .mob-logo-icon i { color: #fff; font-size: 1.1rem; }
        .login-mobile-brand span { font-size: 1.4rem; font-weight: 800; color: #0f766e; letter-spacing: 1px; }

        .login-heading h1 {
            font-size: clamp(1.6rem, 4vw, 2.25rem);
            font-weight: 700; color: var(--teal-700);
            line-height: 1.2; margin-bottom: 0.75rem;
        }
        .login-heading p {
            font-size: 0.925rem; color: #64748b; line-height: 1.6; margin-bottom: 2rem;
        }

        /* Session status / errors */
        .session-status {
            background: var(--teal-50); border: 1px solid var(--teal-200);
            color: var(--teal-700); padding: 0.75rem 1rem;
            border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.875rem;
        }
        .error-message {
            color: #dc2626; font-size: 0.8rem; margin-top: 0.4rem;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .error-message i { font-size: 0.7rem; }

        /* Form Groups */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block; font-size: 0.85rem; font-weight: 500;
            color: #475569; margin-bottom: 0.5rem;
        }
        .form-group .label-row {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 0.5rem;
        }
        .form-group .label-row label { margin-bottom: 0; }

        .forgot-link {
            font-size: 0.8rem; color: var(--teal-600);
            text-decoration: none; font-weight: 500; transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--teal-800); text-decoration: underline; }

        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper .input-icon {
            position: absolute; left: 1rem; color: #94a3b8;
            font-size: 0.9rem; transition: color 0.2s; pointer-events: none;
        }
        .input-wrapper input {
            width: 100%; padding: 0.85rem 1rem 0.85rem 2.75rem;
            border: 1.5px solid #e2e8f0; border-radius: 12px;
            font-size: 0.9rem; font-family: 'Inter', sans-serif;
            color: #1e293b; background: #fff; transition: all 0.3s ease; outline: none;
        }
        .input-wrapper input::placeholder { color: #94a3b8; }
        .input-wrapper input:focus {
            border-color: var(--teal-500);
            box-shadow: 0 0 0 3px rgba(20,184,166,.12);
        }

        .toggle-password {
            position: absolute; right: 1rem;
            background: none; border: none; color: #94a3b8;
            cursor: pointer; font-size: 0.95rem; padding: 0.25rem;
            transition: color 0.2s;
        }
        .toggle-password:hover { color: #64748b; }

        /* Remember Me */
        .remember-row { display: flex; align-items: center; margin-bottom: 1.75rem; }
        .remember-row input[type="checkbox"] {
            width: 16px; height: 16px; border-radius: 4px;
            border: 1.5px solid #cbd5e1; accent-color: var(--teal-600);
            cursor: pointer; margin-right: 0.5rem;
        }
        .remember-row label { font-size: 0.85rem; color: #64748b; cursor: pointer; }

        /* Submit Button */
        .btn-login {
            width: 100%; padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, var(--teal-600), var(--teal-700));
            color: #fff; border: none; border-radius: 12px;
            font-size: 0.95rem; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: ''; position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.15), transparent);
            transition: left 0.5s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--teal-700), var(--teal-800));
            box-shadow: 0 8px 25px rgba(13,148,136,.35);
            transform: translateY(-1px);
        }
        .btn-login:hover::before { left: 100%; }
        .btn-login:active { transform: translateY(0); box-shadow: 0 4px 15px rgba(13,148,136,.25); }

        /* Back to Home */
        .back-to-home {
            display: flex; align-items: center; justify-content: center; margin-top: 1.75rem;
        }
        .back-to-home a {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 50px; font-size: 0.85rem;
            color: #64748b; font-weight: 500; text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-to-home a:hover {
            background: #f1f5f9; color: var(--teal-600);
            border-color: var(--teal-200);
            box-shadow: 0 4px 12px rgba(0,0,0,.05);
        }
        .back-to-home a i { font-size: 0.8rem; transition: transform 0.3s ease; }
        .back-to-home a:hover i { transform: translateX(-3px); }

        /* ===== BREAKPOINT CONTROLS ===== */

        /* xs + sm (<768px): hide left panel, show mobile brand, full-width form */
        @media (max-width: 767.98px) {
            .login-left { display: none !important; }
            .login-mobile-brand { display: flex !important; }
            .login-right {
                flex: 1; min-height: 100vh;
                padding: 2rem 1.25rem;
            }
        }

        /* md (768px–991px): show left panel at 40% width */
        @media (min-width: 768px) {
            .login-mobile-brand { display: none !important; }
            .login-left { flex: 0 0 40%; max-width: 40%; display: flex !important; }
            .login-right { flex: 1; padding: 2.5rem 2rem; }
        }

        /* lg (992px–1199px): left panel 45% */
        @media (min-width: 992px) {
            .login-left { flex: 0 0 45%; max-width: 45%; }
        }

        /* xl (1200px+): left panel 50/50 */
        @media (min-width: 1200px) {
            .login-left { flex: 1; max-width: none; }
            .login-right { flex: 1; padding: 3rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">

        <!-- ===== LEFT PANEL ===== -->
        <div class="login-left">
            <div class="brand-logo">
                <div class="logo-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span>SAKTI</span>
            </div>

            <!-- Decorative particles -->
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>

            <div class="illustration-wrapper">
                <img src="{{ asset('assets/img/elemen login.png') }}" alt="SAKTI Analytics Illustration">
            </div>
        </div>

        <!-- ===== RIGHT PANEL ===== -->
        <div class="login-right">
            <div class="login-form-wrapper">

                <!-- Mobile Brand (visible only on xs/sm) -->
                <div class="login-mobile-brand">
                    <div class="mob-logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span>SAKTI</span>
                </div>

                <div class="login-heading">
                    <h1>Selamat Datang</h1>
                    <p>Masukkan username dan password untuk login ke sistem SAKTI</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="session-status">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input
                                id="email"
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Email"
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <div class="label-row">
                            <label for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                            @endif
                        </div>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="remember-row">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Ingat Saya</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-login" id="btnLogin">
                        Masuk ke Sakti <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <!-- Back to Welcome -->
                <div class="back-to-home">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-chevron-left"></i> Kembali ke Halaman Utama
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // SweetAlert2 Notifications
        @if ($errors->any())
            let errorMsg = '{{ $errors->first() }}';
            @if ($errors->has('email') && $errors->has('password') && $errors->first('email') == 'Data tidak boleh kosong')
                errorMsg = 'Data tidak boleh kosong';
            @endif

            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: errorMsg,
                confirmButtonColor: '#0d9488'
            });
        @endif

        // Client-side validation
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = passwordInput.value.trim();

            if (!email || !password) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Data tidak boleh kosong',
                    confirmButtonColor: '#0d9488'
                });
            }
        });
    </script>
</body>
</html>
