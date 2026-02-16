@extends('layouts.app')

@section('content')
    <style>
        /* Dashboard specific tweaks */
        .metric-gradient-blue { background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); }
        .metric-gradient-indigo { background: linear-gradient(135deg, #ffffff 0%, #eef2ff 100%); }

        @keyframes pulse-soft {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.02); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        .pulse-needed { animation: pulse-soft 2s infinite ease-in-out; }

        .card-icon-bg {
            position: absolute;
            right: -10px;
            bottom: -10px;
            width: 100px;
            height: 100px;
            opacity: 0.05;
            pointer-events: none;
        }
        .card-dark .card-icon-bg { opacity: 0.1; filter: invert(1); }

        .recent-walkin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .recent-walkin-table th { background: #f8fafc; padding: 12px; font-size: 11px; color: #64748b; text-transform: uppercase; text-align: left; }
        .recent-walkin-table td { padding: 12px; border-top: 1px solid #f1f5f9; }
    </style>

    {{-- Top bar --}}
    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-toggle">☰</button>
            <div>
                <div class="admin-topbar-title">Dashboard</div>
                <div class="admin-topbar-sub">
                    Welcome back, Admin! Here is the overview of today’s sales and operations.
                </div>
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
            <div class="card metric-gradient-blue" style="position: relative; overflow: hidden;">
                <img src="{{ asset('icons/admin/walkin.png') }}" class="card-icon-bg" alt="">
                <div style="display:flex;align-items:center;justify-content:space-between; position: relative; z-index: 1;">
                    <div>
                        <div class="card-label">TODAY'S SALES</div>
                        <div class="card-value-xl">
                            ₱ {{ number_format($todayRevenue ?? 0, 2) }}
                        </div>
                        <div class="card-subtext-muted">
                            <span style="color: #0369a1; font-weight: 700;">{{ $todayGallons ?? 0 }}</span>
                            gallon{{ ($todayGallons ?? 0) == 1 ? '' : 's' }} sold today
                        </div>
                    </div>
                    <span class="badge-live">
                        <span style="font-size:10px;margin-right:4px;">●</span> LIVE
                    </span>
                </div>
            </div>

            {{-- This Month --}}
            <div class="card metric-gradient-indigo" style="position: relative; overflow: hidden;">
                <img src="{{ asset('icons/admin/reports.png') }}" class="card-icon-bg" alt="">
                <div style="position: relative; z-index: 1;">
                    <div class="card-label">THIS MONTH SUMMARY</div>
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
                        <span class="badge-chip {{ ($monthProfit ?? 0) >= 0 ? 'badge-chip-dark' : '' }}" 
                              style="{{ ($monthProfit ?? 0) < 0 ? 'background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;' : '' }}">
                            Profit: ₱ {{ number_format($monthProfit ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="card-subtext-muted" style="margin-top:8px;">
                        Performance for <span style="font-weight: 600; color: #4338ca;">{{ now()->format('F Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Backwash Status --}}
            @php
                $currentGallons = (float) ($gallonsSinceLast ?? 0);
                $threshold      = (int)   ($thresholdGallons ?? 200);
                $canLogBackwash = $currentGallons >= $threshold;
            @endphp

            <div class="card card-dark {{ $canLogBackwash ? 'pulse-needed' : '' }}" style="position: relative; overflow: hidden;">
                <img src="{{ asset('icons/admin/dashboard.png') }}" class="card-icon-bg" alt="" style="opacity: 0.1; filter: invert(1);">
                <div style="position: relative; z-index: 1;">
                    <div class="card-label">BACKWASH MONITOR</div>

                    <div class="card-value-lg" style="letter-spacing: -0.01em;">
                        <span style="font-size: 14px; opacity: 0.8; font-weight: 500;">Progress:</span>
                        <span style="color:var(--water-accent-soft);font-weight:800; font-size: 24px;">
                            {{ (int) $currentGallons }}
                        </span>
                        <span style="opacity: 0.5;">/ {{ $threshold }} gal</span>
                    </div>

                    <div class="progress-track" style="margin-top:14px; height: 8px; background: rgba(255,255,255,0.1);">
                        <div class="progress-fill"
                             style="width: {{ $backwashPercent ?? 0 }}%; background: linear-gradient(to right, #7dd3fc, #22d3ee);"></div>
                    </div>

                    <div style="margin-top:12px; font-size:11px; color:#94a3b8; display: flex; align-items: center; gap: 6px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $canLogBackwash ? '#ef4444' : '#22c55e' }};"></span>
                        @if ($canLogBackwash)
                            <span style="color: #fca5a5; font-weight: 600;">Action required: Maintenance due.</span>
                        @else
                            <span>System healthy. Next due at {{ $threshold }} gal.</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.backwash.store') }}" style="margin-top:18px;">
                        @csrf
                        @if ($canLogBackwash)
                            <button type="submit"
                                    class="badge-chip badge-chip-dark"
                                    style="cursor:pointer; border: 1px solid #38bdf8; width: 100%; justify-content: center; padding: 10px; background: rgba(56, 189, 248, 0.1); color: #f9fbff;">
                                LOG BACKWASH COMPLETION
                            </button>
                        @else
                            <div style="font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                                Last clean: {{ $lastBackwashAt ? $lastBackwashAt->format('M d, h:i A') : 'N/A' }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div> {{-- CLOSE grid-3 --}}

        {{-- Overall Recent Sales --}}
        <div class="card" style="margin-top:22px;">
            <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; margin-bottom:16px; gap:12px;">
                <div>
                    <h3 style="margin:0; font-size:18px;">Overall Recent Sales</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:#64748b;">Latest transactions across all channels</p>
                </div>

                @if (isset($overallSales) && $overallSales->isNotEmpty())
                    @php
                        $recentCount   = $overallSales->count();
                        $recentGallons = $overallSales->sum('quantity');
                        $recentTotal   = $overallSales->sum('total_amount');
                    @endphp
                    <div style="font-size:12px; color:#64748b; background:#f1f5f9; padding:6px 14px; border-radius:10px;">
                        <span style="font-weight:700; color:#1e293b;">{{ $recentCount }}</span> orders • 
                        <span style="font-weight:700; color:#1e293b;">{{ $recentGallons }}</span> gal • 
                        <span style="font-weight:700; color:var(--water-main);">₱{{ number_format($recentTotal, 2) }}</span>
                    </div>
                @endif
            </div>

            @if (isset($overallSales) && $overallSales->isNotEmpty())
                <div class="table-responsive">
                    <table class="recent-walkin-table" style="min-width: 600px;">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Customer / Ship</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overallSales as $sale)
                                <tr>
                                    <td style="white-space:nowrap; font-weight:500;">{{ $sale->date?->format('M d, h:i A') }}</td>
                                    <td>
                                        <span class="badge-chip" style="background: {{ $sale->type === 'Walk-in' ? '#e0f2fe' : '#eef2ff' }}; color: {{ $sale->type === 'Walk-in' ? '#0369a1' : '#4338ca' }};">
                                            {{ $sale->type }}
                                        </span>
                                    </td>
                                    <td style="font-weight:600;">
                                        {{ $sale->type === 'Walk-in' ? ($sale->customer_type === 'neighbor' ? 'Neighbor' : 'Non-neighbor') : $sale->ship_name }}
                                        <div style="font-size:11px; font-weight:400; color:#64748b;">
                                            {{ $sale->type === 'Walk-in' ? ($sale->container_type ?: 'Standard') : ($sale->crew_name ?: 'Ship Crew') }}
                                        </div>
                                    </td>
                                    <td style="font-weight:700;">{{ $sale->quantity }}</td>
                                    <td style="font-weight:700; color:var(--water-deep);">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge-status {{ $sale->payment_status }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="recent-card-text">
                    No sales recorded yet. Once you start adding transactions, they will appear here.
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
