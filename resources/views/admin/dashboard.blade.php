@extends('layouts.app')

@section('content')
    <header class="admin-topbar">
        <div>
            <div class="admin-topbar-title">Dashboard</div>
            <div class="admin-topbar-sub">
                Overview of today’s sales and operations.
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="pill-date">{{ now()->format('M d, Y') }}</span>
            <div class="avatar-small">A</div>
        </div>
    </header>

    <div class="admin-body">
        {{-- Top metrics row --}}
        <div class="grid-3">
            {{-- Today’s Sales --}}
            <div class="card">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div class="card-label">TODAY’S SALES</div>
                        <div class="card-value-xl">₱ 0.00</div>
                        <div class="card-subtext-muted">0 gallons sold today</div>
                    </div>
                    <span class="badge-live">● Live</span>
                </div>
            </div>

            {{-- This Month --}}
            <div class="card">
                <div class="card-label">THIS MONTH</div>
                <div class="card-value-xl">₱ 0.00</div>
                <div style="margin-top:10px;">
                    <span class="badge-chip">Sales</span>
                    <span class="badge-chip">Expenses</span>
                    <span class="badge-chip badge-chip-dark">Profit</span>
                </div>
            </div>

            {{-- Backwash Status --}}
            <div class="card card-dark">
                <div class="card-label">BACKWASH STATUS</div>
                <div class="card-value-lg">
                    Gallons since last:
                    <span style="color:var(--water-accent-soft);">0</span> / 200
                </div>
                <div style="margin-top:6px;font-size:12px;color:#bfdbfe;">
                    Last backwash: —
                </div>

                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>

                <div style="margin-top:10px;font-size:12px;color:#bfdbfe;">
                    System will remind you once you reach the 200 gallon threshold.
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card" style="margin-top:22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div class="recent-card-title">Recent Activity</div>
                <div class="recent-card-sub">Logs &amp; transactions</div>
            </div>
            <div class="recent-card-text">
                Once we wire the database, this area will show the latest walk-in sales, ship deliveries,
                expenses, and backwash logs for your refilling station.
            </div>
        </div>
    </div>
@endsection
