<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; color: #111827; overflow-x: hidden; }
        .app-shell { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; min-height: 100vh; background: #111827; color: white; overflow-y: auto; flex-shrink: 0; position: sticky; top: 0; z-index: 20; }
        .logo { padding: 22px 20px; font-size: 22px; font-weight: bold; border-bottom: 1px solid #374151; }
        .logo span { color: #60a5fa; }
        .menu { padding-top: 15px; }
        .menu-title { padding: 15px 20px 8px; font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .menu a { display: block; padding: 12px 20px; color: #d1d5db; text-decoration: none; font-size: 14px; }
        .menu a:hover, .menu a.active { background: #1f2937; color: white; }
        .menu .menu-disabled { display: block; padding: 12px 20px; color: #6b7280; font-size: 14px; cursor: not-allowed; }
        .logout-form { margin-top: 10px; }
        .logout-btn { width: 100%; border: none; background: transparent; color: #d1d5db; text-align: left; padding: 12px 20px; font-size: 14px; cursor: pointer; }
        .logout-btn:hover { background: #1f2937; color: white; }
        .main { flex: 1; min-width: 0; min-height: 100vh; background: #f3f4f6; display: flex; flex-direction: column; }
        .topbar { min-height: 70px; background: white; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 0 24px; }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar h2 { margin: 0; font-size: 20px; }
        .profile { display: flex; align-items: center; gap: 10px; }
        .profile-icon { width: 38px; height: 38px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .mobile-menu-btn { display: none; border: 1px solid #e5e7eb; background: white; color: #111827; width: 42px; height: 42px; border-radius: 10px; align-items: center; justify-content: center; cursor: pointer; }
        .sidebar-overlay { display: none; }
        .content { padding: clamp(16px, 3vw, 28px); flex: 1; }
        .content > *:first-child { margin-top: 0; }
        @media (max-width: 880px) { .sidebar { width: 220px; } .topbar { padding: 0 20px; } }
        @media (max-width: 680px) {
            .app-shell { display: block; }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: min(86vw, 280px);
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(17, 24, 39, 0.55);
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease, visibility 0.25s ease;
                z-index: 15;
            }
            .sidebar-overlay.open { opacity: 1; visibility: visible; }
            .main { margin-left: 0; }
            .mobile-menu-btn { display: inline-flex; }
            .topbar { padding: 0 16px; }
            .content { padding: 16px; }
            .profile > div:last-child { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="logo">Admin <span>Portal</span></div>
            <div class="menu">
                <div class="menu-title">Navigation</div>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">👥 Clients</a>
                <a href="{{ route('admin.daily.import') }}" class="{{ request()->routeIs('admin.daily.import*') ? 'active' : '' }}">� Import Reports</a>
                <a href="{{ route('admin.reports', ['report_type' => 'daily_report']) }}" class="{{ request()->routeIs('admin.reports') && request('report_type', 'daily_report') === 'daily_report' ? 'active' : '' }}">📅 Daily Report</a>
                <a href="{{ route('admin.reports', ['report_type' => 'payment_report']) }}" class="{{ request()->routeIs('admin.reports') && request('report_type') === 'payment_report' ? 'active' : '' }}">💰 Payment Report</a>
                <a href="{{ route('admin.reports', ['report_type' => 'violation_records']) }}" class="{{ request()->routeIs('admin.reports') && request('report_type') === 'violation_records' ? 'active' : '' }}">⚠️ Violation Records</a>
                <a href="{{ route('admin.audit') }}" class="{{ request()->routeIs('admin.audit') ? 'active' : '' }}">📝 Audit Log</a>
                <a href="{{ route('admin.hosts.index') }}" class="{{ request()->routeIs('admin.hosts.*') ? 'active' : '' }}">✅ Hosts</a>
                <a href="{{ route('admin.bank-details') }}" class="{{ request()->routeIs('admin.bank-details') ? 'active' : '' }}">💳 Bank Details</a>
                <div class="menu-title">Account</div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">🚪 Logout</button>
                </form>
            </div>
        </aside>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" type="button" aria-label="Open menu">☰</button>
                    <h2>@yield('page-heading', 'Admin Dashboard')</h2>
                </div>
                <div class="profile">
                    <div class="profile-icon">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div><strong>{{ auth()->user()->name }}</strong></div>
                </div>
            </div>
            <div class="content">@yield('content')</div>
        </main>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (!btn || !sidebar || !overlay) {
                return;
            }

            const closeMenu = () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            };

            btn.addEventListener('click', function () {
                const isOpen = sidebar.classList.toggle('open');
                overlay.classList.toggle('open', isOpen);
                document.body.style.overflow = isOpen ? 'hidden' : '';
            });

            overlay.addEventListener('click', closeMenu);

            document.querySelectorAll('.menu a, .logout-btn').forEach(function (item) {
                item.addEventListener('click', function () {
                    if (window.innerWidth <= 680) {
                        closeMenu();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 680) {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>
