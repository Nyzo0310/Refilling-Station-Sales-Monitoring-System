<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Refilling Monitoring System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--water-light);
            color: #0f172a;
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
        }

        .admin-sidebar-header {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.35);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-pill {
            width: 38px;
            height: 38px;
            border-radius: 16px;
            background: radial-gradient(circle at 20% 0, #e0f2fe 0, #38bdf8 35%, #0369a1 80%);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #022c44;
        }

        .logo-text-title {
            font-size: 16px;
            font-weight: 700;
        }

        .logo-text-sub {
            font-size: 12px;
            color: #bfdbfe;
            font-weight: 500;
        }

        .admin-nav {
            flex: 1;
            padding: 12px 10px 16px;
            font-size: 14px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 11px;
            border-radius: 12px;
            color: #e5f3ff;
            text-decoration: none;
            margin-bottom: 6px;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition:
                background 0.16s ease,
                color 0.16s ease,
                transform 0.1s ease,
                box-shadow 0.16s ease;
        }

        .admin-nav-link .nav-icon {
            width: 18px;
            height: 18px;
            object-fit: contain;
            display: block;
            filter: invert(1) brightness(2);
            opacity: 0.95;
        }

        .admin-nav-link span.label {
            flex: 1;
        }

        .admin-nav-link.active {
            background: rgba(15, 23, 42, 0.65);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.55);
            color: #f9fbff;
        }

        .admin-nav-link:not(.active):hover {
            background: rgba(15, 23, 42, 0.35);
            color: #f9fbff;
            transform: translateX(1px);
        }

        .admin-sidebar-footer {
            border-top: 1px solid rgba(148, 163, 184, 0.35);
            padding: 12px 16px;
            font-size: 12px;
            color: #bfdbfe;
        }

        .admin-sidebar-footer span {
            font-weight: 600;
            color: #e5f3ff;
        }

        /* ---- Main area ---- */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at top, #f9fbff 0, #e0f2fe 45%, #e5e7eb 100%);
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            background: linear-gradient(to right, rgba(248, 250, 252, 0.96), rgba(224, 242, 254, 0.96));
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #dbeafe;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-topbar-title {
            font-size: 22px;
            font-weight: 700;
            color: #022c44;
        }

        .admin-topbar-sub {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

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

        .admin-body {
            padding: 22px 28px 30px;
        }

        /* ---- Generic cards + grids (used by all pages) ---- */
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 18px;
        }

        @media (min-width: 960px) {
            .grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid #dbeafe;
            box-shadow:
                0 14px 30px rgba(148, 163, 184, 0.16),
                0 0 0 1px rgba(148, 163, 184, 0.10);
            padding: 20px 22px;
        }

        .card-dark {
            background: radial-gradient(circle at top left, #0f172a 0, #022c44 45%, #020617 100%);
            color: #e5f3ff;
            border-color: #020617;
            position: relative;
            overflow: hidden;
        }

        .card-dark::after {
            content: "";
            position: absolute;
            inset-y: -30%;
            right: -10%;
            width: 120px;
            background: radial-gradient(circle at center, rgba(56, 189, 248, 0.35), transparent 70%);
            pointer-events: none;
        }

        .card-label {
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .card-dark .card-label {
            color: #bfdbfe;
        }

        .card-value-xl {
            margin-top: 10px;
            font-size: 26px;
            font-weight: 700;
            color: #022c44;
        }

        .card-dark .card-value-xl,
        .card-dark .card-value-lg {
            color: #f9fbff;
        }

        .card-value-lg {
            margin-top: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .card-subtext-muted {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
        }

        .badge-live {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            background: #e0f2fe;
            font-size: 11px;
            color: #0f172a;
            font-weight: 600;
            margin-right: 4px;
        }

        .badge-chip-dark {
            background: #020617;
            color: #f9fbff;
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
<div class="admin-shell">
    {{-- Global sidebar (water theme) --}}
    <aside class="admin-sidebar">
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

            <a href="#"
               class="admin-nav-link">
                <img src="{{ asset('icons/admin/shipdelivery.png') }}" alt="Ship Deliveries" class="nav-icon">
                <span class="label">Ship Deliveries</span>
            </a>

            <a href="#" class="admin-nav-link">
                <img src="{{ asset('icons/admin/inventory.png') }}" alt="Inventory" class="nav-icon">
                <span class="label">Inventory</span>
            </a>

            <a href="#" class="admin-nav-link">
                <img src="{{ asset('icons/admin/expenses.png') }}" alt="Expenses" class="nav-icon">
                <span class="label">Expenses</span>
            </a>

            <a href="#" class="admin-nav-link">
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

</html>
