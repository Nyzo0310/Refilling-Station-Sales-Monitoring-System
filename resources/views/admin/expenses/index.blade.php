@extends('layouts.app')

@section('content')
    <style>
        .expenses-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .expenses-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .expenses-table thead {
            background: #f1f5f9;
        }

        .expenses-table th, 
        .expenses-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .expenses-table th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
        }

        .badge-expense {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background: #f1f5f9;
            color: #475569;
        }

        /* SweetAlert Form Styling */
        .swal-expenses .swal2-popup {
            border-radius: 24px !important;
            background: radial-gradient(circle at top left, #0b1120 0%, #020617 100%) !important;
            padding: 30px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            color: #f8fafc !important;
        }

        .swal-expenses .swal2-title {
            color: #f8fafc !important;
            font-weight: 800 !important;
            letter-spacing: -0.025em;
        }

        .swal-expenses .swal-label {
            display: block;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 6px;
            margin-top: 15px;
        }

        .swal-expenses .swal2-input, 
        .swal-expenses .swal2-select,
        .swal-expenses .swal2-textarea {
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid #1e293b !important;
            border-radius: 12px !important;
            color: #f8fafc !important;
            font-size: 14px !important;
            margin: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .swal-expenses .swal2-confirm {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3) !important;
        }

        .swal-expenses .swal2-cancel {
            background: transparent !important;
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }
    </style>

    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-toggle">☰</button>
            <div>
                <div class="admin-topbar-title">Expenses</div>
                <div class="admin-topbar-sub">Manage and track all business-related costs.</div>
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="pill-date">{{ now()->format('M d, Y') }}</span>
            <div class="avatar-small">{{ substr(Auth::user()->name, 0, 1) }}</div>
        </div>
    </header>

    <div class="admin-body">
        <div class="grid-3" style="margin-bottom: 25px;">
            <div class="card">
                <div class="card-label">{{ $rangeLabel }} Expenses</div>
                <div class="card-value-xl">₱ {{ number_format($totalExpenses, 2) }}</div>
                <div class="card-subtext-muted">Total recorded for this period.</div>
            </div>

            <div class="card" style="display: flex; align-items: center; justify-content: center;">
                <button id="btnAddExpense" class="admin-btn-primary" style="width: 100%; height: 50px; border-radius: 15px; font-weight: 700; font-size: 15px;">
                    + Record New Expense
                </button>
            </div>
        </div>

        <div class="card">
            <div class="filters-bar" style="margin-bottom: 20px;">
                <div class="filters-left">
                    <a href="?range=today" class="pill-filter {{ $range === 'today' ? 'active' : '' }}">Today</a>
                    <a href="?range=week" class="pill-filter {{ $range === 'week' ? 'active' : '' }}">This Week</a>
                    <a href="?range=month" class="pill-filter {{ $range === 'month' ? 'active' : '' }}">This Month</a>
                    <a href="?range=all" class="pill-filter {{ $range === 'all' ? 'active' : '' }}">All Time</a>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="expenses-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Date</th>
                            <th>Expense Type</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td style="font-weight: 600; color: #0f172a;">
                                    {{ $expense->date->format('M d, Y') }}
                                </td>
                                <td>
                                    <span class="badge-expense">{{ ucfirst($expense->expense_type) }}</span>
                                </td>
                                <td style="font-weight: 700; color: #b91c1c;">
                                    ₱ {{ number_format($expense->amount, 2) }}
                                </td>
                                <td style="color: #64748b; font-size: 13px;">
                                    {{ $expense->remarks ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                                    No expenses recorded for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div style="margin-top:20px;">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btnAddExpense');
            if (!btn) return;

            btn.addEventListener('click', function() {
                Swal.fire({
                    title: 'RECORD EXPENSE',
                    customClass: {
                        container: 'swal-expenses',
                        popup: 'swal2-popup'
                    },
                    html: `
                        <div style="text-align: left;">
                            <label class="swal-label">Expense Type</label>
                            <select id="expense_type" class="swal2-select">
                                <option value="machine maintenance">Machine Maintenance</option>
                                <option value="electricity">Electricity</option>
                                <option value="water source">Water Source</option>
                                <option value="salary">Salary</option>
                                <option value="gas">Gas</option>
                                <option value="misc">Miscellaneous</option>
                            </select>

                            <label class="swal-label">Amount (₱)</label>
                            <input type="number" id="amount" class="swal2-input" placeholder="0.00" step="0.01">

                            <label class="swal-label">Date</label>
                            <input type="date" id="expense_date" class="swal2-input" value="{{ date('Y-m-d') }}">

                            <label class="swal-label">Remarks (Optional)</label>
                            <textarea id="remarks" class="swal2-textarea" placeholder="Describe the expense..."></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Expense',
                    preConfirm: () => {
                        const type = document.getElementById('expense_type').value;
                        const amount = document.getElementById('amount').value;
                        const date = document.getElementById('expense_date').value;
                        const remarks = document.getElementById('remarks').value;

                        if (!amount || amount <= 0) {
                            Swal.showValidationMessage('Please enter a valid amount');
                            return false;
                        }

                        return { expense_type: type, amount, date, remarks };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Recording...',
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        fetch('{{ route("admin.expenses.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(result.value)
                        })
                        .then(async res => {
                            if (!res.ok) {
                                const errData = await res.json();
                                throw new Error(errData.message || 'Validation failed');
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: err.message || 'Something went wrong.'
                            });
                        });
                    }
                });
            });
        });
    </script>
@endsection
