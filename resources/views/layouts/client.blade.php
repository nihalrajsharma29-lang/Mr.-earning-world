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
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #111827;
            color: white;
            padding: 20px 0;
            overflow-y: auto;
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
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .topbar h2 {
            font-size: 20px;
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

        .content {
            padding: 30px;
        }

        .content > *:first-child {
            margin-top: 0;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 15px;
            }

            .content {
                padding: 15px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <aside class="sidebar">
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

            <a href="{{ route('client.daily.reports') }}" class="{{ request()->routeIs('client.daily.reports') ? 'active' : '' }}">
                📅 Daily Reports
            </a>

            <!-- Import Reports link removed for clients -->

            <div class="menu-title">
                Account
            </div>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <h2>@yield('page-heading', 'Client Dashboard')</h2>
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
</body>
</html>
