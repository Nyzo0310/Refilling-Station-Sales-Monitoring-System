@extends('layouts.app')

@section('content')
    {{-- Flaticon CDN --}}
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>

    <style>
        /* Stats Cards */
        .expense-stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .expense-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .expense-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #38bdf8, #0ea5e9);
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .stat-subtext {
            font-size: 13px;
            color: #94a3b8;
        }

        /* Filter Pills Row */
        .filter-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 16px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            margin-bottom: 25px;
        }

        .pill-group {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            gap: 4px;
        }

        .pill-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
        }

        .pill-btn:hover {
            color: #0f172a;
        }

        .pill-btn.active {
            background: #ffffff;
            color: #0ea5e9;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }

        /* Table Design */
        .table-wrapper {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .expenses-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .expenses-table thead {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .expenses-table th {
            padding: 16px 20px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 700;
            text-align: left;
        }

        .expenses-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .expenses-table tbody tr:hover {
            background: #f8fafc;
        }

        .expenses-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-date {
            font-weight: 600;
            color: #0f172a;
        }

        .text-amount {
            font-weight: 800;
            color: #ef4444; 
            font-family: inherit;
            font-size: 15px;
        }

        /* Dynamic Badges */
        .badge-dynamic {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-electricity { background: #fef3c7; color: #d97706; }
        .badge-water { background: #e0f2fe; color: #0284c7; }
        .badge-gas { background: #fee2e2; color: #dc2626; }
        .badge-salary { background: #dcfce7; color: #16a34a; }
        .badge-machine { background: #f3e8ff; color: #9333ea; }
        .badge-delivery { background: #ffedd5; color: #ea580c; }
        .badge-misc { background: #f1f5f9; color: #475569; }

        /* Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            text-decoration: none;
        }

        .action-btn-edit {
            background: #f1f5f9;
            color: #64748b;
        }

        .action-btn-edit:hover {
            background: #38bdf8;
            color: #fff;
            transform: translateY(-2px);
        }

        .action-btn-delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .action-btn-delete:hover {
            background: #ef4444;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
            display: block;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 14px;
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

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
                padding: 12px;
            }

            .pill-group {
                width: 100%;
                flex-wrap: wrap;
                justify-content: center;
            }

            .pill-btn {
                flex: 1 1 40%;
                text-align: center;
            }

            .btn-primary-gradient {
                width: 100%;
                justify-content: center;
            }

            .grid-3 {
                grid-template-columns: 1fr !important;
                gap: 12px;
            }

            .expense-stat-card {
                padding: 16px;
            }

            .stat-value {
                font-size: 28px;
            }

            .swal-expenses .swal2-popup {
                width: 95% !important;
                padding: 20px !important;
            }
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
        <div class="grid-3" style="margin-bottom: 25px; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div class="expense-stat-card">
                <div class="stat-label">{{ $rangeLabel }} Expenses</div>
                <div class="stat-value">₱ {{ number_format($totalExpenses, 2) }}</div>
                <div class="stat-subtext">Total recorded for this period.</div>
            </div>

            <div class="expense-stat-card">
                <div class="stat-label">Average Expense / Day</div>
                <div class="stat-value">₱ {{ number_format($totalExpenses / max(1, $expenses->total()), 2) }}</div>
                <div class="stat-subtext">Based on selected range.</div>
            </div>
        </div>

        <div class="filter-controls">
            <div class="pill-group">
                <a href="?range=today" class="pill-btn {{ $range === 'today' ? 'active' : '' }}">Today</a>
                <a href="?range=week" class="pill-btn {{ $range === 'week' ? 'active' : '' }}">Week</a>
                <a href="?range=month" class="pill-btn {{ $range === 'month' ? 'active' : '' }}">Month</a>
                <a href="?range=all" class="pill-btn {{ $range === 'all' ? 'active' : '' }}">All Time</a>
            </div>
            
            <button id="btnAddExpense" class="btn-primary-gradient">
                <i class="fi fi-rr-plus"></i> Record Expense
            </button>
        </div>

        <div class="table-wrapper">
            <div style="overflow-x: auto;">
                <table class="expenses-table">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Date</th>
                            <th>Expense Type</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th style="width: 120px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            @php
                                $typeLower = strtolower($expense->expense_type);
                                $badgeClass = 'badge-misc';
                                $icon = 'fi-rr-box';
                                
                                if (str_contains($typeLower, 'electricity')) { $badgeClass = 'badge-electricity'; $icon = 'fi-rr-bolt'; }
                                elseif (str_contains($typeLower, 'water source')) { $badgeClass = 'badge-water'; $icon = 'fi-rr-drop'; }
                                elseif (str_contains($typeLower, 'gas') || str_contains($typeLower, 'fuel')) { $badgeClass = 'badge-gas'; $icon = 'fi-rr-gas-pump'; }
                                elseif (str_contains($typeLower, 'salary')) { $badgeClass = 'badge-salary'; $icon = 'fi-rr-users'; }
                                elseif (str_contains($typeLower, 'machine')) { $badgeClass = 'badge-machine'; $icon = 'fi-rr-settings'; }
                                elseif (str_contains($typeLower, 'delivery')) { $badgeClass = 'badge-delivery'; $icon = 'fi-rr-truck-side'; }
                            @endphp
                            <tr>
                                <td class="text-date">
                                    {{ $expense->date->format('M d, Y') }}<br>
                                    <span style="font-size: 11px; color: #94a3b8; font-weight: 500;">{{ $expense->date->format('l') }}</span>
                                </td>
                                <td>
                                    <span class="badge-dynamic {{ $badgeClass }}">
                                        <i class="fi {{ $icon }}"></i> {{ ucfirst($expense->expense_type) }}
                                    </span>
                                </td>
                                <td class="text-amount">
                                    ₱ {{ number_format($expense->amount, 2) }}
                                </td>
                                <td style="color: #475569; font-size: 13px;">
                                    @if($expense->remarks)
                                        @if(preg_match('/Walk-in sale #(\d+)/', $expense->remarks, $matches))
                                            <a href="#" class="btn-view-transaction" data-type="walkin" data-id="{{ $matches[1] }}" style="color: #0ea5e9; text-decoration: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fi fi-rr-link" style="font-size: 12px;"></i> {{ $expense->remarks }}
                                            </a>
                                        @elseif(preg_match('/Port delivery #(\d+)/', $expense->remarks, $matches))
                                            <a href="#" class="btn-view-transaction" data-type="ship" data-id="{{ $matches[1] }}" style="color: #0ea5e9; text-decoration: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fi fi-rr-link" style="font-size: 12px;"></i> {{ $expense->remarks }}
                                            </a>
                                        @else
                                            {{ $expense->remarks }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        {{-- Edit Button --}}
                                        <button class="action-btn action-btn-edit btn-edit-expense"
                                            data-id="{{ $expense->id }}"
                                            data-type="{{ $expense->expense_type }}"
                                            data-amount="{{ $expense->amount }}"
                                            data-date="{{ $expense->date->format('Y-m-d') }}"
                                            data-remarks="{{ $expense->remarks }}"
                                            title="Edit">
                                            <i class="fi fi-rr-pencil"></i>
                                        </button>
                                        {{-- Delete Button --}}
                                        <button class="action-btn action-btn-delete btn-delete-expense"
                                            data-id="{{ $expense->id }}"
                                            data-type="{{ ucfirst($expense->expense_type) }}"
                                            title="Delete">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fi fi-rr-receipt"></i>
                                        <h3>No Expenses Recorded</h3>
                                        <p>There are no expenses for the selected period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($expenses->hasPages())
            <div style="margin-top:20px;">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
    </div>

    <script>
        const CSRF = '{{ csrf_token() }}';

        // ─── ADD EXPENSE ──────────────────────────────────────────────────────────
        document.getElementById('btnAddExpense').addEventListener('click', function () {
            openExpenseModal();
        });

        function openExpenseModal(prefill = {}) {
            const isEdit    = !!prefill.id;
            const knownTypes = ['machine maintenance', 'electricity', 'water source', 'salary', 'gas'];
            const typeVal    = prefill.type ? prefill.type.toLowerCase() : '';
            const isMisc     = typeVal && !knownTypes.includes(typeVal);

            Swal.fire({
                title: isEdit ? 'EDIT EXPENSE' : 'RECORD NEW EXPENSE',
                customClass: {
                    popup: 'swal-water swal-expenses',
                    confirmButton: 'swal-confirm',
                    cancelButton: 'swal-cancel'
                },
                html: `
                    <div style="text-align: left;">
                        <label class="swal-label">What kind of expense?</label>
                        <select id="expense_type" class="swal2-select swal-select">
                            <option value="machine maintenance" ${typeVal === 'machine maintenance' ? 'selected' : ''}>⚙️ Machine Maintenance</option>
                            <option value="electricity"        ${typeVal === 'electricity'        ? 'selected' : ''}>⚡ Electricity Bill</option>
                            <option value="water source"       ${typeVal === 'water source'       ? 'selected' : ''}>💧 Water Source / Supply</option>
                            <option value="salary"             ${typeVal === 'salary'             ? 'selected' : ''}>👥 Staff Salary</option>
                            <option value="gas"                ${typeVal === 'gas'                ? 'selected' : ''}>⛽ Fuel / Gas</option>
                            <option value="misc"               ${isMisc                           ? 'selected' : ''}>📦 Other / Miscellaneous</option>
                        </select>

                        <div id="specific_type_wrap" style="display: ${isMisc ? 'block' : 'none'};">
                            <label class="swal-label">Specific Title</label>
                            <input type="text" id="specific_type" class="swal2-input" placeholder="e.g. Office Supplies, Cleaning" value="${isMisc ? (prefill.type || '') : ''}">
                        </div>

                        <label class="swal-label">How much (₱)?</label>
                        <input type="number" id="amount" class="swal2-input" placeholder="0.00" step="0.01" value="${prefill.amount || ''}">

                        <label class="swal-label">When did this occur?</label>
                        <input type="date" id="expense_date" class="swal2-input" value="${prefill.date || '{{ date('Y-m-d') }}'}">

                        <label class="swal-label">Short Description (Optional)</label>
                        <textarea id="remarks" class="swal2-textarea swal2-input" placeholder="e.g. replaced filter, monthly electric bill...">${prefill.remarks || ''}</textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: isEdit ? 'Update Expense' : 'Save Expense',
                didOpen: () => {
                    const typeSelect   = document.getElementById('expense_type');
                    const specificWrap = document.getElementById('specific_type_wrap');
                    const specificInput = document.getElementById('specific_type');

                    typeSelect.addEventListener('change', () => {
                        if (typeSelect.value === 'misc') {
                            specificWrap.style.display = 'block';
                            specificInput.focus();
                        } else {
                            specificWrap.style.display = 'none';
                        }
                    });
                },
                preConfirm: () => {
                    let type         = document.getElementById('expense_type').value;
                    const specificType = document.getElementById('specific_type').value.trim();
                    const amount     = document.getElementById('amount').value;
                    const date       = document.getElementById('expense_date').value;
                    const remarks    = document.getElementById('remarks').value;

                    if (type === 'misc') {
                        if (!specificType) {
                            Swal.showValidationMessage('Please enter a specific title for "Other" expense');
                            return false;
                        }
                        type = specificType;
                    }

                    if (!amount || amount <= 0) {
                        Swal.showValidationMessage('Please enter a valid amount');
                        return false;
                    }

                    return { expense_type: type, amount, date, remarks };
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: isEdit ? 'Updating...' : 'Recording...',
                    didOpen: () => Swal.showLoading(),
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                const url    = isEdit
                    ? `/admin/expenses/${prefill.id}`
                    : '{{ route("admin.expenses.store") }}';
                const method = isEdit ? 'PATCH' : 'POST';

                fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(result.value)
                })
                .then(async res => {
                    if (!res.ok) {
                        const err = await res.json();
                        throw new Error(err.message || 'Validation failed');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: isEdit ? 'Updated!' : 'Saved!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Something went wrong.' });
                });
            });
        }

        // ─── EDIT EXPENSE ─────────────────────────────────────────────────────────
        document.querySelectorAll('.btn-edit-expense').forEach(btn => {
            btn.addEventListener('click', function () {
                openExpenseModal({
                    id:      this.dataset.id,
                    type:    this.dataset.type,
                    amount:  this.dataset.amount,
                    date:    this.dataset.date,
                    remarks: this.dataset.remarks
                });
            });
        });

        // ─── DELETE EXPENSE ───────────────────────────────────────────────────────
        document.querySelectorAll('.btn-delete-expense').forEach(btn => {
            btn.addEventListener('click', function () {
                const id   = this.dataset.id;
                const type = this.dataset.type;

                Swal.fire({
                    title: 'Delete Expense?',
                    html: `Are you sure you want to delete the <strong>${type}</strong> expense? This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    confirmButtonColor: '#dc2626',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Deleting...',
                        didOpen: () => Swal.showLoading(),
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    fetch(`/admin/expenses/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        if (!res.ok) {
                            const err = await res.json();
                            throw new Error(err.message || 'Delete failed');
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => window.location.reload());
                        }
                    })
                    .catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Something went wrong.' });
                    });
                });
            });
        });
        // ─── VIEW TRANSACTION MODAL ────────────────────────────────────────────────
        document.querySelectorAll('.btn-view-transaction').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const type = this.dataset.type;
                const id = this.dataset.id;
                
                const url = type === 'walkin' ? `/admin/walkin-sales/${id}` : `/admin/ship-deliveries/${id}`;
                
                Swal.fire({
                    title: 'Loading Data...',
                    didOpen: () => Swal.showLoading(),
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                fetch(url, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.ok ? res.json() : Promise.reject('Failed to load transaction'))
                .then(data => {
                    if (!data.ok) throw new Error('Transaction not found');
                    const t = data.data;
                    
                    let htmlContent = `<div style="text-align: left; font-size: 14px; padding-top: 10px;">`;
                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Transaction ID</span>
                        <span style="font-weight: 700; color: #38bdf8;">#${t.id}</span>
                    </div>`;
                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Date</span>
                        <span style="font-weight: 600; color: #f8fafc;">${t.date}</span>
                    </div>`;

                    if (type === 'walkin') {
                        htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #94a3b8; font-weight: 600;">Customer Type</span>
                            <span style="font-weight: 600; color: #f8fafc;">${t.customer_type}</span>
                        </div>`;
                    } else {
                        htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #94a3b8; font-weight: 600;">Ship Name</span>
                            <span style="font-weight: 600; color: #f8fafc;">${t.ship_name}</span>
                        </div>`;
                        if (t.crew_name) {
                            htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #94a3b8; font-weight: 600;">Crew Name</span>
                                <span style="font-weight: 600; color: #f8fafc;">${t.crew_name}</span>
                            </div>`;
                        }
                    }

                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Container Type</span>
                        <span style="font-weight: 600; color: #f8fafc;">${t.container_type}</span>
                    </div>`;
                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Quantity (Gallons)</span>
                        <span style="font-weight: 600; color: #f8fafc;">${t.quantity}</span>
                    </div>`;
                    htmlContent += `<hr style="border-color: #1e293b; margin: 15px 0;" />`;
                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Total Amount</span>
                        <span style="font-weight: 700; color: #10b981;">₱ ${parseFloat(t.total_amount).toFixed(2)}</span>
                    </div>`;
                    
                    let statusColor = t.payment_status === 'Paid' ? '#10b981' : (t.payment_status === 'Partial' ? '#f59e0b' : '#ef4444');
                    htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: #94a3b8; font-weight: 600;">Payment Status</span>
                        <span style="font-weight: 700; color: ${statusColor};">${t.payment_status}</span>
                    </div>`;

                    if (t.money_received > 0) {
                        htmlContent += `<div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #94a3b8; font-weight: 600;">Money Received</span>
                            <span style="font-weight: 600; color: #f8fafc;">₱ ${parseFloat(t.money_received).toFixed(2)}</span>
                        </div>`;
                    }

                    const notes = type === 'walkin' ? t.note : t.remarks;
                    if (notes) {
                        htmlContent += `<div style="margin-top: 15px;">
                            <span style="color: #94a3b8; font-weight: 600; display: block; margin-bottom: 5px;">Remarks/Notes</span>
                            <div style="background: rgba(15, 23, 42, 0.4); padding: 10px; border-radius: 8px; border: 1px solid #1e293b; color: #cbd5e1; font-style: italic;">
                                ${notes}
                            </div>
                        </div>`;
                    }

                    htmlContent += `</div>`;

                    Swal.fire({
                        title: type === 'walkin' ? 'Walk-in Sale Details' : 'Port Delivery Details',
                        html: htmlContent,
                        customClass: {
                            popup: 'swal-water swal-expenses',
                            confirmButton: 'swal-confirm'
                        },
                        confirmButtonText: 'Close',
                        width: '450px'
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Could not load transaction details.',
                        customClass: { popup: 'swal-water swal-expenses', confirmButton: 'swal-confirm' }
                    });
                });
            });
        });
    </script>
@endsection
