<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Portal')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
            overflow-x: hidden;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #111827;
            color: white;
            padding: 20px 0;
            overflow-y: auto;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .logo {
            padding: 0 20px 25px;
            font-size: 21px;
            font-weight: bold;
            border-bottom: 1px solid #374151;
        }

        .logo span {
            color: #60a5fa;
        }

        .menu {
            margin-top: 15px;
        }

        .menu-title {
            padding: 12px 20px 7px;
            font-size: 11px;

        .menu .menu-disabled {
            display: block;
            padding: 12px 20px;
            color: #6b7280;
            font-size: 14px;
            cursor: not-allowed;
        }
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu a {
            display: block;
            padding: 12px 20px;
            color: #d1d5db;
            text-decoration: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #1f2937;
            color: white;
        }

        .logout-form {
            margin-top: 20px;
        }

        .logout-btn {
            width: 100%;
            border: none;
            background: transparent;
            color: #d1d5db;
            text-align: left;
            padding: 12px 20px;
            font-size: 14px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #1f2937;
            color: white;
        }

        .main {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            min-height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 24px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar h2 {
            font-size: 20px;
            margin: 0;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-icon {
            width: 38px;
            height: 38px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .mobile-menu-btn {
            display: none;
            border: 1px solid #e5e7eb;
            background: white;
            color: #111827;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
        }

        .content {
            padding: clamp(16px, 3vw, 30px);
            flex: 1;
        }

        .content > *:first-child {
            margin-top: 0;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }
        }

        @media (max-width: 650px) {
            .app-shell {
                display: block;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: min(86vw, 280px);
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                padding: 20px 0;
            }

            .sidebar.open {
                transform: translateX(0);
            }

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

            .sidebar-overlay.open {
                opacity: 1;
                visibility: visible;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            .topbar {
                padding: 0 15px;
            }

            .content {
                padding: 15px;
            }

            .profile > div:last-child {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                Client <span>Portal</span>
            </div>

            <div class="menu">
                <div class="menu-title">
                    Main Menu
                </div>

                <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                    📊 Data Dashboard
                </a>

                <a href="{{ route('client.hosts.create') }}" class="{{ request()->routeIs('client.hosts.create') ? 'active' : '' }}">
                    ➕ Add Host
                </a>

                <a href="{{ route('client.hosts.audit') }}" class="{{ request()->routeIs('client.hosts.audit') ? 'active' : '' }}">
                    🔍 Host Audit Results
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'daily_report']) }}" class="{{ request()->routeIs('client.daily.reports') && request('report_type', 'daily_report') === 'daily_report' ? 'active' : '' }}">
                    📅 Daily Reports
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'payment_report']) }}" class="{{ request()->routeIs('client.daily.reports') && request('report_type') === 'payment_report' ? 'active' : '' }}">
                    💰 Payment Report
                </a>

                <a href="{{ route('client.daily.reports', ['report_type' => 'violation_records']) }}" class="{{ request()->routeIs('client.daily.reports') && request('report_type') === 'violation_records' ? 'active' : '' }}">
                    ⚠️ Violation Records
                </a>

                <div class="menu-title">
                    Account
                </div>

                <a href="{{ route('client.bank-card') }}" class="{{ request()->routeIs('client.bank-card') ? 'active' : '' }}">
                    💳 Bank Card
                </a>

                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        🚪 Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <main class="main">
            <div class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" type="button" aria-label="Open menu">☰</button>
                    <h2>@yield('page-heading', 'Client Dashboard')</h2>
                </div>
                <div class="profile">
                    <div class="profile-icon">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                </div>
            </div>

            <div class="content">
                @yield('content')
            </div>
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
                    if (window.innerWidth <= 650) {
                        closeMenu();
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 650) {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>
