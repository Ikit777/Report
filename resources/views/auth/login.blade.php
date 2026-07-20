<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ config('app.name') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="brand-logo" style="margin: 0 auto 1rem auto; float: none;">DR</div>
                <h1>{{ config('app.name') }}</h1>
                <p>Warehouse & Inventory Site Sungai Putting</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 0.75rem 1rem;">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                    <input type="checkbox" name="remember" id="remember" style="cursor: pointer;">
                    <label for="remember" style="margin: 0; cursor: pointer; font-size: 0.85rem; user-select: none;">Ingat Saya di Perangkat Ini</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">Masuk</button>
            </form>
            
        </div>
    </div>
</body>
</html>
