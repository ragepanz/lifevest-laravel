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
            <!-- Left: Logo & Back Button -->
            <div class="navbar-left" style="display: flex; align-items: center; gap: 4px;">
                @if(!request()->routeIs('dashboard'))
                    <a href="{{ route('dashboard') }}" class="btn-back" title="Back to Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @endif
                
                <a href="{{ route('dashboard') }}" class="navbar-brand">
                    <span class="navbar-logo">🛡️</span>
                    <span class="navbar-title">Life Vest Tracker</span>
                </a>
                <span class="navbar-badge">GMF AeroAsia</span>
            </div>

            <!-- Center: Header area -->
            <div class="navbar-center">
                @yield('header-right')
            </div>

            <!-- Right: Admin & Update -->
            <div class="navbar-right" style="display: flex; gap: 1rem; align-items: center;">
                <!-- Theme Toggle Switch -->
                <label class="theme-switch" title="Toggle Theme (Green = Light)">
                    <input type="checkbox" id="theme-toggle-checkbox">
                    <span class="slider"></span>
                </label>

                @if(request()->routeIs('dashboard'))
                    <a href="{{ route('fleet.index') }}" class="btn btn-sm btn-secondary"
                        style="text-decoration: none; display: flex; align-items: center; gap: 5px;">
                        ⚙️ Manage Fleet
                    </a>
                @endif

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

    <!-- SheetJS for Excel Export -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>

    @stack('scripts')
    
    <script>
        // Theme Toggle Logic (Switch)
        document.addEventListener('DOMContentLoaded', () => {
            const toggleCheckbox = document.getElementById('theme-toggle-checkbox');
            const html = document.documentElement;

            // Check saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                html.setAttribute('data-theme', 'light');
                toggleCheckbox.checked = true; // Set switch to ON (Green)
            } else {
                toggleCheckbox.checked = false; // Set switch to OFF (Dark)
            }

            toggleCheckbox.addEventListener('change', () => {
                if (toggleCheckbox.checked) {
                    // Switch to Light Mode
                    html.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                } else {
                    // Switch to Dark Mode
                    html.removeAttribute('data-theme');
                    localStorage.setItem('theme', 'dark');
                }
            });
        });
    </script>
</body>

</html>