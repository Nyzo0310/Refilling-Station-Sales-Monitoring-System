<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Refilling Monitoring System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <style>
        :root {
            --water-deep: #022c44;
            --water-main: #0369a1;
            --water-light: #e0f2fe;
            --water-accent: #0ea5e9;
            --water-accent-soft: #7dd3fc;
            --sidebar-width: 280px;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, .admin-topbar-title, .logo-text-title, .card-value-xl, .card-value-lg {
            font-family: 'Outfit', sans-serif;
        }

        .admin-shell {
            display: flex;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #f0f9ff 0, #e0f2fe 40%, #dbeafe 100%);
        }

        /* ---- Enhanced Sidebar ---- */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, rgba(2, 44, 68, 1) 0%, rgba(3, 105, 161, 1) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: #e5f3ff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
        }

        @media (max-width: 959px) {
            .admin-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                width: 300px;
            }
            .admin-sidebar.open {
                transform: translateX(0);
                box-shadow: 20px 0 60px rgba(0, 0, 0, 0.3);
            }
        }

        .admin-sidebar-header {
            padding: 32px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-close-btn {
            display: none;
            position: absolute;
            right: 16px;
            top: 24px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-size: 20px;
            cursor: pointer;
        }

        @media (max-width: 959px) { .sidebar-close-btn { display: block; } }

        .logo-pill {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 800;
            color: var(--water-main);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .logo-text-title { font-size: 18px; font-weight: 800; color: white; letter-spacing: -0.02em; }
        .logo-text-sub { font-size: 12px; opacity: 0.7; color: #e0f2fe; margin-top: 2px; }

        .admin-nav { 
            flex: 1; 
            padding: 10px 16px;
            overflow-y: auto;
        }

        .nav-section-title {
            padding: 0 16px;
            margin: 24px 0 12px;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 16px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-nav-link .nav-icon-wrap {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-nav-link .nav-icon {
            width: 20px;
            height: 20px;
            filter: invert(1) brightness(2);
            opacity: 0.6;
            transition: all 0.3s;
        }

        .admin-nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }

        .admin-nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .admin-nav-link.active .nav-icon { opacity: 1; transform: scale(1.1); }

        /* ---- Profile Card at Bottom ---- */
        .admin-sidebar-profile {
            margin: 16px;
            padding: 16px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7dd3fc 0%, #0ea5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: white;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
        }

        .profile-info { flex: 1; min-width: 0; }
        .profile-name { 
            font-size: 14px; 
            font-weight: 700; 
            color: white; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
        .profile-role { font-size: 11px; color: rgba(255,255,255,0.5); font-weight: 500; }

        .btn-logout-icon {
            color: rgba(255,255,255,0.4);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            background: none;
            border: none;
            padding: 4px;
        }
        .btn-logout-icon:hover { color: #fca5a5; transform: scale(1.1); }

        /* ---- Overlays and Mobile Controls ---- */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 44, 68, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s;
        }
        .sidebar-overlay.visible { opacity: 1; visibility: visible; }

        .mobile-toggle {
            display: none;
            background: white;
            border: 1px solid #e2e8f0;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            cursor: pointer;
            color: var(--water-deep);
            font-size: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        .mobile-toggle:active { transform: scale(0.95); }

        @media (max-width: 959px) {
            .mobile-toggle { display: flex; align-items: center; justify-content: center; }
        }


        /* ---- Main area ---- */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: #f8fafc;
        }

        .admin-topbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
            transition: all 0.3s;
        }

        .admin-topbar-title { font-size: 20px; font-weight: 700; color: #1e293b; }
        .admin-topbar-sub { font-size: 13px; color: #64748b; }

        .admin-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #1e293b;
        }

        .pill-date {
            padding: 6px 14px;
            border-radius: 12px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-weight: 600;
        }

        .avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #e5f3ff;
            border: 1px solid #bae6fd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #022c44;
        }

        .admin-body { padding: 32px; }
        @media (max-width: 959px) { 
            .admin-body { padding: 20px; } 
            .admin-topbar { padding: 12px 16px; }
            .admin-topbar-sub { display: none; }
        }

        /* ---- Generic cards + grids ---- */
        .card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        @media (max-width: 1024px) { .grid-3 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .grid-3 { grid-template-columns: 1fr; gap: 16px; } }

        .card-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; }
        .card-value-xl { font-size: 32px; font-weight: 800; color: #022c44; margin-top: 12px; }
        .card-value-lg { font-size: 24px; font-weight: 800; color: #022c44; margin-top: 8px; }

        .badge-status {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-status.paid { background: #dcfce7; color: #15803d; }
        .badge-status.unpaid { background: #fee2e2; color: #b91c1c; }
        .badge-status.partial { background: #fef9c3; color: #854d0e; }

        .badge-chip {
            display: inline-flex;
            padding: 6px 12px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .table-responsive {
            margin: 0 -24px;
            padding: 0 24px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 640px) {
            .table-responsive { margin: 0 -16px; padding: 0 16px; }
        }

        .hidden-mobile { display: none !important; }
        @media (min-width: 641px) { .hidden-mobile { display: block !important; } }

        /* ---- SweetAlert "Water" Theme ---- */
        .swal-water.swal2-popup {
            border-radius: 32px !important;
            padding: 40px !important;
            background: radial-gradient(circle at top right, #0b1120 0, #020617 100%) !important;
            color: #f8fafc !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        }

        .swal-water .swal2-title { color: white !important; font-size: 28px !important; font-family: 'Outfit', sans-serif !important; }
        .swal-water .swal2-html-container { color: #94a3b8 !important; }

        .swal-water .swal-label { font-size: 11px; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 8px; }
        .swal-water .swal2-input, .swal-water .swal-select {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: white !important;
            border-radius: 14px !important;
            height: 50px !important;
        }

        .swal-water .swal-confirm {
            background: var(--water-accent) !important;
            border-radius: 14px !important;
            padding: 14px 40px !important;
            font-weight: 700 !important;
        }

        .progress-track {
            margin-top: 14px;
            height: 10px;
            width: 100%;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(to right, #0ea5e9, #22d3ee);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="antialiased">
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="admin-shell">
    {{-- Global sidebar (water theme) --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <button class="sidebar-close-btn" id="sidebarClose">✕</button>

        <div class="admin-sidebar-header">
            <div class="logo-pill">RS</div>
            <div>
                <div class="logo-text-title">Refilling Admin</div>
                <div class="logo-text-sub">Sales Monitoring System</div>
            </div>
        </div>

        <nav class="admin-nav">
            <div class="nav-section-title">Main Menu</div>
            
            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="nav-icon-wrap">
                    <img src="{{ asset('icons/admin/dashboard.png') }}" alt="" class="nav-icon">
                </div>
                <span class="label">Dashboard</span>
            </a>

            <a href="{{ route('admin.walkin.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.walkin.index') ? 'active' : '' }}">
                <div class="nav-icon-wrap">
                    <img src="{{ asset('icons/admin/walkin.png') }}" alt="" class="nav-icon">
                </div>
                <span class="label">Walk-in Sales</span>
            </a>

            <a href="{{ route('admin.ship-deliveries.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.ship-deliveries.index') ? 'active' : '' }}">
                <div class="nav-icon-wrap">
                    <img src="{{ asset('icons/admin/shipdelivery.png') }}" alt="" class="nav-icon">
                </div>
                <span class="label">Ship Deliveries</span>
            </a>

            <div class="nav-section-title">Management</div>

            <a href="{{ route('admin.expenses.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
                <div class="nav-icon-wrap">
                    <img src="{{ asset('icons/admin/expenses.png') }}" alt="" class="nav-icon">
                </div>
                <span class="label">Expenses</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                <div class="nav-icon-wrap">
                    <img src="{{ asset('icons/admin/reports.png') }}" alt="" class="nav-icon">
                </div>
                <span class="label">Reports</span>
            </a>
        </nav>

        {{-- Enhanced Profile Card --}}
        <div class="admin-sidebar-profile">
            <div class="profile-avatar">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ Auth::user()->name }}</div>
                <div class="profile-role">System Owner</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                @csrf
                <button type="submit" class="btn-logout-icon" title="Sign Out" style="display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('icons/admin/logout.svg') }}" alt="Logout icon" style="width: 20px; height: 20px; filter: invert(0.8) opacity(0.6);">
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="admin-main">
        @yield('content')
    </main>
</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarClose');
        const toggles = document.querySelectorAll('.mobile-toggle');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('visible');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
            document.body.style.overflow = '';
        }

        toggles.forEach(btn => btn.addEventListener('click', openSidebar));
        overlay.addEventListener('click', closeSidebar);
        closeBtn.addEventListener('click', closeSidebar);

        // Close sidebar on link click (mobile)
        sidebar.querySelectorAll('.admin-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 960) closeSidebar();
            });
        });
    });
</script>

</html>

</html>
