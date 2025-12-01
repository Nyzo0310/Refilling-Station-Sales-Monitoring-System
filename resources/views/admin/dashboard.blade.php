@extends('layouts.app')

@section('content')
    <style>
        /* Mas friendly na layout para sa Recent Walk-in card */
        .recent-walkin-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .recent-summary-line {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
        }
        .recent-summary-line strong {
            color: #0f172a;
        }

        .recent-walkin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .recent-walkin-table thead {
            background: #f1f5f9;
        }

        .recent-walkin-table th,
        .recent-walkin-table td {
            padding: 8px 10px;
            vertical-align: middle;
            text-align: left; /* default */
        }

        .recent-walkin-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
        }

        .recent-walkin-table tbody tr:nth-child(even) {
            background: #f9fbff;
        }

        /* === Alignment fixes === */
        .recent-walkin-table th.col-qty,
        .recent-walkin-table td.col-qty,
        .recent-walkin-table th.col-total,
        .recent-walkin-table td.col-total {
            text-align: center;
        }
        .recent-walkin-table th.col-status,
        .recent-walkin-table td.col-status {
            text-align: center;
        }

        .recent-col-date {
            white-space: nowrap;
            font-weight: 600;
            color: #0f172a;
        }
        .recent-col-customer {
            font-weight: 600;
            color: #0f172a;
        }
        .recent-col-customer small {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: #64748b;
        }

        .badge-status {
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
        }
        .badge-status.paid   { background: #dcfce7; color: #166534; }
        .badge-status.unpaid { background: #fee2e2; color: #b91c1c; }

        /* ========== Premium SweetAlert for backwash ========== */
        .swal-backwash.swal2-popup {
            border-radius: 20px !important;
            padding: 22px 24px 20px !important;
            background: radial-gradient(circle at top, #0b1120 0, #020617 55%, #020617 100%) !important;
            box-shadow:
                0 22px 55px rgba(15, 23, 42, 0.85),
                0 0 0 1px rgba(37, 99, 235, 0.45);
            color: #e5e7eb !important;
        }
        .swal-backwash .swal2-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #e5f3ff !important;
        }
        .swal-backwash .swal2-html-container {
            margin-top: 6px !important;
            font-size: 14px;
            color: #cbd5f5 !important;
        }
        .swal-backwash .swal2-icon.swal2-success {
            border-color: #22c55e !important;
            color: #22c55e !important;
        }
        .swal-backwash .swal2-icon.swal2-warning {
            border-color: #f97316 !important;
            color: #fed7aa !important;
        }
        .swal-backwash .swal2-confirm {
            border-radius: 999px !important;
            background: linear-gradient(to right, #0ea5e9, #0369a1) !important;
            padding: 8px 22px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.75);
        }
        .swal-backwash .swal2-confirm:focus {
            box-shadow:
                0 0 0 3px rgba(15, 23, 42, 0.8),
                0 0 0 5px rgba(56, 189, 248, 0.9) !important;
        }
        .swal2-container.swal2-center.swal-backwash-container {
            background-color: rgba(15, 23, 42, 0.75) !important;
        }
    </style>

    {{-- Top bar --}}
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
                        <div class="card-label">TODAY'S SALES</div>
                        <div class="card-value-xl">
                            ₱ {{ number_format($todayRevenue ?? 0, 2) }}
                        </div>
                        <div class="card-subtext-muted">
                            {{ $todayGallons ?? 0 }}
                            gallon{{ ($todayGallons ?? 0) == 1 ? '' : 's' }} sold today
                        </div>
                    </div>
                    <span class="badge-live">
                        <span style="font-size:10px;margin-right:4px;">●</span> Live
                    </span>
                </div>
            </div>

            {{-- This Month --}}
            <div class="card">
                <div class="card-label">THIS MONTH (WALK-IN)</div>
                <div class="card-value-xl">
                    ₱ {{ number_format($monthRevenue ?? 0, 2) }}
                </div>

                <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:6px;">
                    <span class="badge-chip">
                        Sales: ₱ {{ number_format($monthRevenue ?? 0, 2) }}
                    </span>
                    <span class="badge-chip">
                        Expenses: ₱ {{ number_format($monthExpenses ?? 0, 2) }}
                    </span>
                    <span class="badge-chip badge-chip-dark">
                        Profit: ₱ {{ number_format($monthProfit ?? 0, 2) }}
                    </span>
                </div>

                <div class="card-subtext-muted" style="margin-top:6px;">
                    Based on paid walk-in refills this month.
                </div>
            </div>

            {{-- Backwash Status --}}
            @php
                $currentGallons = (float) ($gallonsSinceLast ?? 0);
                $threshold      = (int)   ($thresholdGallons ?? 200);
                $canLogBackwash = $currentGallons >= $threshold;
            @endphp

            <div class="card card-dark">
                <div class="card-label">BACKWASH STATUS</div>

                <div class="card-value-lg">
                    Gallons since last:
                    <span style="color:var(--water-accent-soft);font-weight:700;">
                        {{ (int) $currentGallons }}
                    </span>
                    / {{ $threshold }}
                </div>

                <div style="margin-top:6px;font-size:12px;color:#bfdbfe;">
                    Last backwash:
                    @if($lastBackwashAt)
                        {{ $lastBackwashAt->format('M d, Y h:i A') }}
                    @else
                        —
                    @endif
                </div>

                <div class="progress-track" style="margin-top:10px;">
                    <div class="progress-fill"
                         style="width: {{ $backwashPercent ?? 0 }}%;"></div>
                </div>

                <div style="margin-top:10px;font-size:12px;
                    color: {{ $canLogBackwash ? '#fecaca' : '#bfdbfe' }};">
                    @if ($canLogBackwash)
                        Backwash needed now. Please clean the filters and log it below.
                    @else
                        About {{ max($threshold - (int) $currentGallons, 0) }}
                        gallons left before backwash is due.
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.backwash.store') }}" style="margin-top:12px;">
                    @csrf

                    @if ($canLogBackwash)
                        <button type="submit"
                                class="badge-chip badge-chip-dark"
                                style="cursor:pointer;border:none;">
                            Log backwash now
                        </button>
                    @else
                        <button type="button"
                                disabled
                                class="badge-chip"
                                style="border:none;opacity:.6;cursor:not-allowed;">
                            Log backwash (wait until {{ $threshold }} gal)
                        </button>
                    @endif
                </form>
            </div>
        </div> {{-- CLOSE grid-3 --}}

        {{-- Recent Walk-in Sales --}}
        <div class="card" style="margin-top:22px;">
            <div class="recent-walkin-header">
                <div>
                    <div class="recent-card-title">Recent Walk-in Sales</div>
                    <div class="recent-card-sub">
                        Latest over-the-counter refills
                    </div>
                </div>

                @if (isset($recentWalkins) && $recentWalkins->isNotEmpty())
                    @php
                        $recentCount   = $recentWalkins->count();
                        $recentGallons = $recentWalkins->sum('quantity');
                        $recentTotal   = $recentWalkins->sum('total_amount');
                    @endphp
                    <div class="recent-summary-line">
                        <strong>{{ $recentCount }}</strong> refill{{ $recentCount > 1 ? 's' : '' }}
                        • <strong>{{ $recentGallons }}</strong> gallon{{ $recentGallons > 1 ? 's' : '' }}
                        • <strong>₱ {{ number_format($recentTotal, 2) }}</strong> total
                    </div>
                @endif
            </div>

            @if (isset($recentWalkins) && $recentWalkins->isNotEmpty())
                <table class="recent-walkin-table">
                    <thead>
                        <tr>
                            <th style="width:28%;">Date &amp; Time</th>
                            <th style="width:32%;">Customer &amp; Container</th>
                            <th class="col-qty"    style="width:12%;">Qty (gal)</th>
                            <th class="col-total"  style="width:16%;">Total</th>
                            <th class="col-status" style="width:12%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentWalkins as $sale)
                            @php
                                $label = match ($sale->customer_type) {
                                    'neighbor'     => 'Neighbor',
                                    'non_neighbor' => 'Non-neighbor',
                                    'crew_ship'    => 'Ship crew',
                                    default        => ucfirst($sale->customer_type),
                                };
                            @endphp
                            <tr>
                                <td class="recent-col-date">
                                    {{ $sale->sold_at?->format('M d, Y h:i A') }}
                                </td>
                                <td class="recent-col-customer">
                                    {{ $label }}
                                    <small>
                                        Container: {{ $sale->container_type ?? '—' }}
                                    </small>
                                </td>
                                <td class="col-qty">
                                    {{ $sale->quantity }}
                                </td>
                                <td class="col-total">
                                    ₱ {{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="col-status">
                                    <span class="badge-status {{ $sale->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                                        {{ ucfirst($sale->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="recent-card-text">
                    No walk-in sales recorded yet. Once you start adding walk-in transactions,
                    they will appear here with the date, customer, gallons, amount, and status.
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert for backwash success/error --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Backwash logged',
                    text: @json(session('success')),
                    customClass: {
                        popup: 'swal-backwash',
                        container: 'swal-backwash-container'
                    },
                    confirmButtonText: 'Okay'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot log backwash yet',
                    text: @json(session('error')),
                    customClass: {
                        popup: 'swal-backwash',
                        container: 'swal-backwash-container'
                    },
                    confirmButtonText: 'Got it'
                });
            @endif
        });
    </script>
@endsection
