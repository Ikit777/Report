<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
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
            margin-bottom: 1.25rem;
        }

        .suggestions {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.25rem;
            text-align: left;
        }

        .suggestions h3 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .suggestions ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .suggestions li {
            padding: 0.35rem 0;
            color: #0369a1;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 0.4rem;
            line-height: 1.4;
        }

        .suggestions li:before {
            content: "•";
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
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

            .suggestions li {
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
        <div class="error-code">404</div>
        
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>

        <div class="error-card">
            <h1>Halaman Tidak Ditemukan</h1>
            <p class="error-message">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Kemungkinan halaman telah dipindahkan, dihapus, atau URL yang Anda masukkan salah.
            </p>

            <div class="suggestions">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    Apa yang bisa Anda lakukan?
                </h3>
                <ul>
                    <li>Periksa kembali URL yang Anda masukkan</li>
                    <li>Kembali ke halaman sebelumnya menggunakan tombol browser</li>
                    <li>Kembali ke halaman dashboard untuk memulai lagi</li>
                    <li>Hubungi administrator jika masalah terus berlanjut</li>
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
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Halaman Sebelumnya
                </button>
            </div>
        </div>
    </div>
</body>
</html>
