<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | {{ config('app.name') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
        }

        .error-container {
            max-width: 550px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
            max-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
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

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1;
            margin-bottom: 0.75rem;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: glitch 3s ease-in-out infinite;
        }

        @keyframes glitch {
            0%, 100% {
                transform: translate(0);
                text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
            20% {
                transform: translate(-2px, 2px);
                text-shadow: 2px -2px 0 #f59e0b, -2px 2px 0 #d97706;
            }
            40% {
                transform: translate(-2px, -2px);
                text-shadow: 2px 2px 0 #f59e0b, -2px -2px 0 #d97706;
            }
            60% {
                transform: translate(2px, 2px);
                text-shadow: -2px -2px 0 #f59e0b, 2px 2px 0 #d97706;
            }
            80% {
                transform: translate(2px, -2px);
                text-shadow: -2px 2px 0 #f59e0b, 2px -2px 0 #d97706;
            }
        }

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .error-icon svg {
            width: 40px;
            height: 40px;
            stroke: white;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }

        .error-message {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .warning-box {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #fde68a;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .warning-box-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .warning-box-header svg {
            width: 18px;
            height: 18px;
            stroke: #d97706;
            flex-shrink: 0;
        }

        .warning-box h3 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #92400e;
            margin: 0;
        }

        .warning-box p {
            color: #b45309;
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 0;
        }

        .info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .info-box h3 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .info-box h3 svg {
            width: 16px;
            height: 16px;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-box li {
            padding: 0.35rem 0;
            color: #0369a1;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 0.4rem;
            line-height: 1.4;
        }

        .info-box li:before {
            content: "→";
            color: #0284c7;
            font-weight: bold;
            font-size: 1rem;
            line-height: 1.4;
            flex-shrink: 0;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.65rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #475569;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .error-code {
                font-size: 4.5rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .error-card {
                padding: 1.25rem;
            }

            .error-message {
                font-size: 0.9rem;
            }

            .warning-box p,
            .info-box li {
                font-size: 0.8rem;
            }

            .btn {
                font-size: 0.85rem;
                padding: 0.6rem 1.25rem;
            }
        }

        /* Floating animation for background elements */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            bottom: 15%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 80px;
            height: 80px;
            top: 60%;
            left: 80%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(180deg);
            }
        }
    </style>
</head>
<body>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <div class="error-container">
        <div class="error-code">500</div>
        
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
            </svg>
        </div>

        <div class="error-card">
            <h1>Terjadi Kesalahan Server</h1>
            <p class="error-message">
                Maaf, terjadi kesalahan pada server kami. Tim teknis kami telah diberitahu dan sedang bekerja untuk memperbaiki masalah ini secepat mungkin.
            </p>

            <div class="warning-box">
                <div class="warning-box-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <h3>Apa yang Terjadi?</h3>
                </div>
                <p>
                    Server mengalami masalah internal saat memproses permintaan Anda. Ini bukan kesalahan Anda, dan tim teknis kami sedang menangani masalah ini.
                </p>
            </div>

            <div class="info-box">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    Apa yang bisa Anda lakukan?
                </h3>
                <ul>
                    <li>Tunggu beberapa saat dan coba kembali</li>
                    <li>Refresh halaman untuk mencoba lagi</li>
                    <li>Kembali ke halaman dashboard</li>
                    <li>Hubungi administrator jika masalah berlanjut</li>
                    <li>Simpan pekerjaan Anda dan coba lagi nanti</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Kembali ke Dashboard
                </a>
                <button onclick="location.reload()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    Refresh Halaman
                </button>
            </div>
        </div>
    </div>
</body>
</html>
