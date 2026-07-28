<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-x: hidden;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
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
            font-size: 10rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: shake 3s ease-in-out infinite;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }

        .error-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .error-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
        }

        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-top: 2rem;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .error-message {
            font-size: 1.1rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .warning-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fecaca;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .warning-box-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .warning-box-header svg {
            width: 24px;
            height: 24px;
            stroke: #dc2626;
            flex-shrink: 0;
        }

        .warning-box h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #991b1b;
            margin: 0;
        }

        .warning-box p {
            color: #b91c1c;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .info-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .info-box h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            padding: 0.5rem 0;
            color: #0369a1;
            font-size: 0.95rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .info-box li:before {
            content: "✓";
            color: #0284c7;
            font-weight: bold;
            font-size: 1.2rem;
            line-height: 1;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 63, 94, 0.4);
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

        .user-info {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 2px solid #e9d5ff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-info svg {
            width: 20px;
            height: 20px;
            stroke: #7c3aed;
        }

        .user-info span {
            color: #6b21a8;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @media (max-width: 640px) {
            .error-code {
                font-size: 6rem;
            }

            h1 {
                font-size: 1.75rem;
            }

            .error-card {
                padding: 2rem 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
        <div class="error-code">403</div>
        
        <div class="error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>

        <div class="error-card">
            <h1>Akses Ditolak</h1>
            <p class="error-message">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Halaman yang Anda tuju memerlukan hak akses khusus yang tidak Anda miliki saat ini.
            </p>

            @auth
            <div class="user-info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Login sebagai: {{ Auth::user()->name }} 
                    @if(Auth::user()->role === 'fuelman')
                        (Fuelman)
                    @elseif(Auth::user()->role === 'gl')
                        (Group Leader)
                    @elseif(Auth::user()->role === 'spv')
                        (Supervisor)
                    @endif
                </span>
            </div>
            @endauth

            <div class="warning-box">
                <div class="warning-box-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <h3>Mengapa Saya Tidak Bisa Akses?</h3>
                </div>
                <p>
                    Halaman ini mungkin memerlukan role/jabatan tertentu (Fuelman, Group Leader, atau Supervisor), atau Anda mungkin mencoba mengakses data yang bukan milik Anda atau sudah tidak tersedia.
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
                    <li>Kembali ke halaman dashboard untuk melihat fitur yang tersedia</li>
                    <li>Pastikan Anda login dengan akun yang memiliki hak akses</li>
                    <li>Hubungi Supervisor atau Administrator untuk meminta akses</li>
                    <li>Periksa apakah Anda login dengan akun yang benar</li>
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
