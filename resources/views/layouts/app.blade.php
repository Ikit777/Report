<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laporan Harian') | {{ config('app.name') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand-section">
                <div class="brand-logo">FM</div>
                <div>
                    <div class="brand-name">DAILY REPORT</div>
                    <div class="brand-sub">Warehouse & Inventory</div>
                </div>
            </div>

            <!-- Profile Info -->
            @auth
            <div class="user-profile-section">
                <div class="avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <span class="user-name" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                    <span class="user-role-badge">
                        @if(Auth::user()->isFuelman())
                            Fuelman
                        @elseif(Auth::user()->isGl())
                            Group Leader
                        @elseif(Auth::user()->isSpv())
                            Supervisor
                        @endif
                    </span>
                </div>
            </div>
            @endauth

            <!-- Nav Links -->
            <ul class="nav-menu">
                <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9"></rect>
                            <rect x="14" y="3" width="7" height="5"></rect>
                            <rect x="14" y="12" width="7" height="9"></rect>
                            <rect x="3" y="16" width="7" height="5"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('reports.index') || Route::is('reports.create') || Route::is('reports.edit') || Route::is('reports.show') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Laporan Harian</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('reports.analytics') ? 'active' : '' }}">
                    <a href="{{ route('reports.analytics') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        <span>Rekap & Analisis</span>
                    </a>
                </li>
                <li class="nav-item {{ Route::is('tanks.*') ? 'active' : '' }}">
                    <a href="{{ route('tanks.index') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
                        </svg>
                        <span>{{ Auth::user()->isSpv() ? 'Kelola Tangki' : 'Tangki BBM' }}</span>
                    </a>
                </li>
                @if(Auth::user()->isSpv())
                <li class="nav-item {{ Route::is('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </a>
                </li>
                @endif
            </ul>

            <!-- Logout -->
            @auth
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
            @endauth
        </aside>

        <!-- Main Content Wrapper -->
        <main class="main-content">
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- View Specific Content -->
            @yield('content')
        </main>
    </div>

    <!-- Global Custom Modal Confirmation -->
    <div id="customConfirmModal" class="custom-modal-overlay no-print">
        <div class="custom-modal-content">
            <h3 class="custom-modal-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--danger);">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                Konfirmasi Hapus
            </h3>
            <p class="custom-modal-text">Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="custom-modal-actions">
                <button type="button" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="closeCustomConfirm()">Batal</button>
                <button type="button" class="btn btn-danger" style="padding: 0.5rem 1rem;" id="btnConfirmDelete">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        let formToSubmit = null;

        function confirmDelete(event, formElement) {
            // Prevent form submission
            event.preventDefault();
            event.stopPropagation();
            
            formToSubmit = formElement;
            
            // Show modal
            const modal = document.getElementById('customConfirmModal');
            modal.classList.add('active');
            
            return false;
        }

        function closeCustomConfirm() {
            const modal = document.getElementById('customConfirmModal');
            modal.classList.remove('active');
            formToSubmit = null;
        }

        document.getElementById('btnConfirmDelete').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            closeCustomConfirm();
        });

        // Close modal when clicking overlay background
        document.getElementById('customConfirmModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeCustomConfirm();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
