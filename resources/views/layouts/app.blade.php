<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Life Vest Tracker' }} - GMF AeroAsia</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS & JS -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>

<body>
    <!-- Sticky Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-container">
            <!-- Left: Logo -->
            <div class="navbar-left">
                <a href="{{ route('dashboard') }}" class="navbar-brand">
                    <span class="navbar-logo">🛡️</span>
                    <span class="navbar-title">Life Vest Tracker</span>
                </a>
                <span class="navbar-badge">GMF AeroAsia</span>
            </div>

            <!-- Center: Search & Filter (only on dashboard) -->
            @if(request()->routeIs('dashboard'))
                <div class="navbar-center">
                    <div class="navbar-search">
                        <span class="search-icon">🔍</span>
                        <input type="text" id="searchInput" placeholder="Cari registrasi..." autocomplete="off">
                    </div>
                    <div class="navbar-filter">
                        <select id="typeFilter">
                            <option value="all">Semua Tipe</option>
                            <option value="B737">B737</option>
                            <option value="B777">B777</option>
                            <option value="A330">A330</option>
                            <option value="ATR72">ATR72</option>
                        </select>
                        <select id="statusFilter">
                            <option value="all">Semua Status</option>
                            <option value="active">Active</option>
                            <option value="prolong">Prolong</option>
                        </select>
                    </div>
                </div>
            @else
                <div class="navbar-center">
                    @yield('header-right')
                </div>
            @endif

            <!-- Right: Last Update -->
            <div class="navbar-right">
                @if(isset($lastUpdate))
                    <div class="navbar-update">
                        <span class="update-label">🕐 Last Update:</span>
                        <span class="update-value">{{ $lastUpdate->format('d M Y, H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div class="navbar-spacer"></div>

    <div class="app-container">
        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast">
        <span class="toast-icon">✓</span>
        <span class="toast-message">Success!</span>
    </div>

    @stack('scripts')
</body>

</html>