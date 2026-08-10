<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; color: #111827; }
        .sidebar { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #111827; color: white; overflow-y: auto; }
        .logo { padding: 22px 20px; font-size: 22px; font-weight: bold; border-bottom: 1px solid #374151; }
        .logo span { color: #60a5fa; }
        .menu { padding-top: 15px; }
        .menu-title { padding: 15px 20px 8px; font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .menu a { display: block; padding: 12px 20px; color: #d1d5db; text-decoration: none; font-size: 14px; }
        .menu a:hover, .menu a.active { background: #1f2937; color: white; }
        .logout-form { margin-top: 10px; }
        .logout-btn { width: 100%; border: none; background: transparent; color: #d1d5db; text-align: left; padding: 12px 20px; font-size: 14px; cursor: pointer; }
        .logout-btn:hover { background: #1f2937; color: white; }
        .main { margin-left: 250px; min-height: 100vh; background: #f3f4f6; }
        .topbar { height: 70px; background: white; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 30px; }
        .topbar h2 { margin: 0; font-size: 20px; }
        .profile { display: flex; align-items: center; gap: 10px; }
        .profile-icon { width: 38px; height: 38px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .content { padding: 28px; }
        @media (max-width: 880px) { .sidebar { width: 220px; } .main { margin-left: 220px; } }
        @media (max-width: 680px) { .sidebar { position: relative; width: 100%; height: auto; } .main { margin-left: 0; } .topbar { padding: 0 16px; } .content { padding: 16px; } }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <aside class="sidebar">
            <div class="logo">Admin <span>Portal</span></div>
            <div class="menu">
                <div class="menu-title">Navigation</div>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
                <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">👥 Clients</a>
                <a href="{{ route('admin.daily.import') }}" class="{{ request()->routeIs('admin.daily.import*') ? 'active' : '' }}">📂 Excel Upload</a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">📊 Reports</a>
                <a href="{{ route('admin.audit') }}" class="{{ request()->routeIs('admin.audit') ? 'active' : '' }}">📝 Audit Log</a>
                <a href="{{ route('admin.hosts.index') }}" class="{{ request()->routeIs('admin.hosts.*') ? 'active' : '' }}">✅ Hosts</a>
                <div class="menu-title">Account</div>
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">🚪 Logout</button>
                </form>
            </div>
        </aside>
        <main class="main">
            <div class="topbar">
                <h2>@yield('page-heading', 'Admin Dashboard')</h2>
                <div class="profile">
                    <div class="profile-icon">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div><strong>{{ auth()->user()->name }}</strong></div>
                </div>
            </div>
            <div class="content">@yield('content')</div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
