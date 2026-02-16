<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Refilling Monitoring System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <style>
        :root {
            --water-deep: #022c44;
            --water-main: #0369a1;
            --water-light: #e0f2fe;
            --water-accent: #0ea5e9;
            --water-accent-soft: #7dd3fc;
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

        /* ---- Sidebar ---- */
        .admin-sidebar {
            width: 260px;
            background: radial-gradient(circle at top, #0ea5e9 0, #0369a1 40%, #022c44 100%);
            color: #e5f3ff;
            display: flex;
            flex-direction: column;
            box-shadow: 8px 0 30px rgba(15, 23, 42, 0.18);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
        }

        @media (max-width: 959px) {
            .admin-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
        }

        .admin-sidebar-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-pill {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: var(--water-main);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logo-text-title { font-size: 16px; font-weight: 700; color: white; }
        .logo-text-sub { font-size: 11px; opacity: 0.8; color: #e0f2fe; }

        .admin-nav { flex: 1; padding: 20px 12px; }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 4px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .admin-nav-link .nav-icon {
            width: 20px;
            height: 20px;
            filter: invert(1) brightness(2);
            opacity: 0.7;
        }

        .admin-nav-link span.label {
            flex: 1;
        }

        .admin-nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .admin-nav-link.active .nav-icon { opacity: 1; }

        .admin-nav-link:not(.active):hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
        }

        .admin-sidebar-footer {
            border-top: 1px solid rgba(148, 163, 184, 0.2);
            padding: 16px 20px;
            font-size: 13px;
            color: #94a3b8;
            background: rgba(15, 23, 42, 0.2);
        }

        .admin-sidebar-footer span {
            font-weight: 700;
            color: #f9fbff;
            display: block;
            font-size: 14px;
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
        }

        .mobile-toggle {
            display: none;
            background: #f1f5f9;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            color: var(--water-deep);
            font-size: 20px;
        }


        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .sidebar-overlay.visible { opacity: 1; visibility: visible; }

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
            padding: 6px 12px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-weight: 600;
        }

        .avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #e5f3ff;
            border: 1px solid #bae6fd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #022c44;
        }

        .admin-body { padding: 24px; }
        @media (max-width: 640px) { .admin-body { padding: 16px; } }

        /* --- Global Media Queries (Last for high specificity) --- */
        @media (max-width: 959px) {
            .admin-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }

            .mobile-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-topbar { padding: 12px 16px; }
            .admin-topbar-title { font-size: 18px; }
            .admin-topbar-sub { display: none; }
        }

        /* ---- Generic cards + grids (used by all pages) ---- */
        .card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        @media (max-width: 1024px) { .grid-3 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .grid-3 { grid-template-columns: 1fr; gap: 16px; } }

        .card-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-value-xl { font-size: 28px; font-weight: 800; color: #1e293b; margin-top: 8px; }

        .badge-status {
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-status.paid { background: #dcfce7; color: #15803d; }
        .badge-status.unpaid { background: #fee2e2; color: #b91c1c; }
        .badge-status.partial { background: #fef9c3; color: #854d0e; }

        .badge-chip {
            display: inline-flex;
            padding: 4px 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        /* Table Responsiveness */
        .table-responsive {
            margin: 0 -24px;
            padding: 0 24px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 640px) {
            .table-responsive { margin: 0 -16px; padding: 0 16px; }
        }

        /* Utility visibility */
        @media (max-width: 640px) {
            .hidden-mobile { display: none !important; }
        }

        /* ---- SweetAlert "Water" Theme (Centralized) ---- */
        .swal-water.swal2-popup {
            border-radius: 24px !important;
            padding: 32px !important;
            background: radial-gradient(circle at top right, #0b1120 0, #020617 100%) !important;
            color: #f8fafc !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            width: 500px !important;
            max-width: 95% !important;
        }

        .swal-water .swal2-title { color: white !important; font-size: 24px !important; }
        .swal-water .swal2-html-container { color: #94a3b8 !important; }

        .swal-water .swal-form { text-align: left; }
        .swal-water .swal-label { font-size: 11px; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 6px; }
        .swal-water .swal2-input, .swal-water .swal-select {
            background: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: white !important;
            border-radius: 12px !important;
            margin: 0 0 16px 0 !important;
            width: 100% !important;
        }

        .swal-water .swal-row { display: flex; gap: 16px; }
        @media (max-width: 480px) { .swal-water .swal-row { flex-direction: column; gap: 0; } }

        .swal-water .swal-confirm {
            background: var(--water-accent) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 32px !important;
        }
        .swal-water .swal-cancel {
            background: transparent !important;
            color: #94a3b8 !important;
        }

        .progress-track {
            margin-top: 14px;
            height: 6px;
            width: 100%;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.85);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(to right, #22d3ee, #22c55e);
        }

        .recent-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #022c44;
        }

        .recent-card-sub {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .recent-card-text {
            margin-top: 8px;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }
    </style>
</head>
<body class="antialiased">
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="admin-shell">
    {{-- Global sidebar (water theme) --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-header">
            <div class="logo-pill">RS</div>
            <div>
                <div class="logo-text-title">Refilling Admin</div>
                <div class="logo-text-sub">Sales Monitoring System</div>
            </div>
        </div>

        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <img src="{{ asset('icons/admin/dashboard.png') }}" alt="Dashboard" class="nav-icon">
                <span class="label">Dashboard</span>
            </a>

            <a href="{{ route('admin.walkin.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.walkin.index') ? 'active' : '' }}">
                <img src="{{ asset('icons/admin/walkin.png') }}" alt="Walk-in Sales" class="nav-icon">
                <span class="label">Walk-in Sales</span>
            </a>

            <a href="{{ route('admin.ship-deliveries.index') }}"
            class="admin-nav-link {{ request()->routeIs('admin.ship-deliveries.index') ? 'active' : '' }}">
                <img src="{{ asset('icons/admin/shipdelivery.png') }}" alt="Ship Deliveries" class="nav-icon">
                <span class="label">Ship Deliveries</span>
            </a>


            <a href="{{ route('admin.expenses.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.expenses.index') ? 'active' : '' }}">
                <img src="{{ asset('icons/admin/expenses.png') }}" alt="Expenses" class="nav-icon">
                <span class="label">Expenses</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="admin-nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                <img src="{{ asset('icons/admin/reports.png') }}" alt="Reports" class="nav-icon">
                <span class="label">Reports</span>
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            Logged in as <span>Admin</span>
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
        const toggles = document.querySelectorAll('.mobile-toggle');

        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('visible');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }

        toggles.forEach(btn => btn.addEventListener('click', toggleSidebar));
        overlay.addEventListener('click', toggleSidebar);

        // Close sidebar on link click (mobile)
        sidebar.querySelectorAll('.admin-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (sidebar.classList.contains('open')) toggleSidebar();
            });
        });
    });
</script>

</html>

</html>
