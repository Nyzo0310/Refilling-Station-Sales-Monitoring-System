@extends('layouts.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .reports-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        @media (max-width: 1024px) {
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .stats-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .stats-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .stats-item:last-child {
            border-bottom: none;
        }

        .stats-label {
            font-size: 14px;
            font-weight: 500;
            color: #475569;
        }

        .stats-value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .ship-leaderboard {
            margin-top: 15px;
        }

        .ship-rank {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #e0f2fe;
            color: #0369a1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            margin-right: 12px;
        }
    </style>

    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-toggle">☰</button>
            <div>
                <div class="admin-topbar-title">Analytics Reports</div>
                <div class="admin-topbar-sub">Visual insights into your business performance.</div>
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="pill-date">{{ $now->format('M d, Y') }} | {{ Auth::user()->name }}</span>
            <div class="avatar-small">{{ substr(Auth::user()->name, 0, 1) }}</div>
        </div>
    </header>

    <div class="admin-body">
        <div class="reports-grid">
            {{-- Monthly Trend Chart --}}
            <div class="card">
                <div class="card-label">Monthly Performance (Revenue vs Expenses)</div>
                <div class="chart-container" style="margin-top: 15px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            {{-- Sales Distribution Doughnut --}}
            <div class="card">
                <div class="card-label">Sales Source distribution</div>
                <div class="chart-container" style="margin-top: 15px; height: 260px;">
                    <canvas id="salesSourceChart"></canvas>
                </div>
                <div style="margin-top: 15px; display: flex; justify-content: space-around;">
                    <div style="text-align: center;">
                        <div style="font-size: 11px; color: #64748b; font-weight: 600;">Walk-in</div>
                        <div style="font-size: 14px; font-weight: 700; color: #0ea5e9;">₱ {{ number_format($salesSource['walkin'], 2) }}</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 11px; color: #64748b; font-weight: 600;">Ship</div>
                        <div style="font-size: 14px; font-weight: 700; color: #0369a1;">₱ {{ number_format($salesSource['ship'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-3">
            {{-- Expense Categories --}}
            <div class="card">
                <div class="card-label">Expenses by category</div>
                <div class="stats-list" style="margin-top: 10px;">
                    @forelse ($expenseBreakdown as $exp)
                        <div class="stats-item">
                            <span class="stats-label">{{ ucfirst($exp->expense_type) }}</span>
                            <span class="stats-value">₱ {{ number_format($exp->total, 2) }}</span>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">No expense data for this month.</div>
                    @endforelse
                </div>
            </div>

            {{-- Top Ships Leaderboard --}}
            <div class="card">
                <div class="card-label">Top Profitable Ships</div>
                <div class="ship-leaderboard">
                    @forelse ($topShips as $index => $ship)
                        <div class="stats-item">
                            <div style="display: flex; align-items: center;">
                                <div class="ship-rank">{{ $index + 1 }}</div>
                                <div>
                                    <div class="stats-label" style="color: #0f172a;">{{ $ship->ship_name ?: 'Unknown Ship' }}</div>
                                    <div style="font-size: 11px; color: #64748b;">{{ $ship->total_gallons }} gallons delivered</div>
                                </div>
                            </div>
                            <span class="stats-value">₱ {{ number_format($ship->total_revenue, 2) }}</span>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 20px;">No ship deliveries this month.</div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Summary Card --}}
            <div class="card card-dark" style="background: radial-gradient(circle at top right, #0369a1 0%, #022c44 100%);">
                <div class="card-label" style="color: #bfdbfe;">Monthly Profit Summary</div>
                <div style="margin-top: 20px;">
                    <div class="card-value-xl" style="color: #fff;">
                       ₱ {{ number_format($salesSource['walkin'] + $salesSource['ship'] - $expenseBreakdown->sum('total'), 2) }}
                    </div>
                    <div style="font-size: 12px; color: #7dd3fc; margin-top: 4px; font-weight: 600;">
                        Net profit for {{ $now->format('F Y') }}
                    </div>
                </div>
                
                <div style="margin-top: 25px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #94a3b8;">
                        <span>Gross Revenue:</span>
                        <span style="color: #fff; font-weight: 600;">₱ {{ number_format($salesSource['walkin'] + $salesSource['ship'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #94a3b8; margin-top: 8px;">
                        <span>Total Expenses:</span>
                        <span style="color: #fecaca; font-weight: 600;">- ₱ {{ number_format($expenseBreakdown->sum('total'), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data from PHP
            const monthlyLabels = @json(array_column($monthlyData, 'month'));
            const revenueData = @json(array_column($monthlyData, 'revenue'));
            const expenseData = @json(array_column($monthlyData, 'expenses'));

            const salesSourceData = [@json($salesSource['walkin']), @json($salesSource['ship'])];

            // 1. Revenue & Expenses Trend Chart
            const ctxTrend = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Total Revenue',
                            data: revenueData,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            borderWidth: 3
                        },
                        {
                            label: 'Total Expenses',
                            data: expenseData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            borderWidth: 2,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            padding: 12,
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 13, weight: '700' },
                            bodyFont: { size: 13 },
                            cornerRadius: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { size: 11 },
                                callback: function(value) { return '₱' + value.toLocaleString(); }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });

            // 2. Sales Source Chart
            const ctxSource = document.getElementById('salesSourceChart').getContext('2d');
            new Chart(ctxSource, {
                type: 'doughnut',
                data: {
                    labels: ['Walk-in Sales', 'Ship Deliveries'],
                    datasets: [{
                        data: salesSourceData,
                        backgroundColor: ['#0ea5e9', '#0369a1'],
                        hoverOffset: 12,
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    label += '₱' + context.raw.toLocaleString();
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
