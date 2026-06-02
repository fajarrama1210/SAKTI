<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - {{ config('app.name', 'SAKTI') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(243, 252, 246) 0%, rgb(222, 247, 232) 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Ambient glowing background shapes */
        .glow-1, .glow-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            opacity: 0.6;
        }

        .glow-1 {
            top: -10%;
            left: -10%;
            width: 400px;
            height: 400px;
            background-color: #a7f3d0;
            animation: float-slow 15s ease-in-out infinite alternate;
        }

        .glow-2 {
            bottom: -10%;
            right: -10%;
            width: 450px;
            height: 450px;
            background-color: #6ee7b7;
            animation: float-slow 12s ease-in-out infinite alternate-reverse;
        }

        @keyframes float-slow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(50px, 30px) scale(1.1); }
        }

        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 25px 50px -12px rgba(4, 120, 87, 0.15);
            border-radius: 32px;
            padding: 4rem 2.5rem;
            max-width: 540px;
            width: 100%;
            text-align: center;
            z-index: 10;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -15px rgba(4, 120, 87, 0.25);
        }

        .image-container {
            margin-bottom: 2.5rem;
            position: relative;
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-width: 320px;
        }

        .image-bg-effect {
            position: absolute;
            inset: -10px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            animation: pulse-ring 3s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .illustration {
            max-width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .illustration:hover {
            transform: rotate(-5deg) scale(1.08);
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #047857 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.75rem;
            letter-spacing: -0.04em;
        }

        .error-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #064e3b;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .divider {
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, #10b981, #059669);
            margin: 0 auto 1.75rem auto;
            border-radius: 999px;
        }

        .error-message {
            color: #374151;
            font-size: 1.15rem;
            line-height: 1.6;
            margin-bottom: 2.75rem;
            font-weight: 400;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 1rem 2.5rem;
            border-radius: 100px;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.6);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .btn-home:active {
            transform: translateY(-1px);
        }

        .btn-home svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.2s ease;
        }

        .btn-home:hover svg {
            transform: translateX(-3px);
        }
    </style>
</head>
<body>
    <!-- Background Glow Effects -->
    <div class="glow-1"></div>
    <div class="glow-2"></div>

    <!-- Error Card Container -->
    <div class="card">
        <div class="image-container">
            <div class="image-bg-effect"></div>
            <img src="{{ asset('assets/img/rejected.svg') }}" alt="Akses Ditolak" class="illustration">
        </div>
        
        <h1 class="error-code">403</h1>
        <h2 class="error-title">Akses Ditolak</h2>
        <div class="divider"></div>
        
        <p class="error-message">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>Silakan kembali ke halaman utama.
        </p>
        
        <a href="{{ url('/') }}" class="btn-home">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Home
        </a>
    </div>
</body>
</html>
