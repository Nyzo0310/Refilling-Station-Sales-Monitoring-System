@extends('layouts.app')

@section('content')
    {{-- Flaticon CDN --}}
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>

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

        /* Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.18s ease;
            font-size: 16px;
            text-decoration: none;
        }

        .action-btn-edit {
            background: #eff6ff;
            color: #2563eb;
        }

        .action-btn-edit:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .action-btn-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .action-btn-delete:hover {
            background: #dc2626;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .actions-cell {
            display: flex;
            gap: 6px;
            align-items: center;
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

            <div class="card">
                <div class="card-label">Average Expense / Day</div>
                <div class="card-value-xl">₱ {{ number_format($totalExpenses / max(1, $expenses->total()), 2) }}</div>
                <div class="card-subtext-muted">Based on selected range.</div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 25px;">
            <div class="filters-bar-standard">
                <div class="filter-pills-row">
                    <a href="?range=today" class="pill-filter {{ $range === 'today' ? 'active' : '' }}">Today</a>
                    <a href="?range=week" class="pill-filter {{ $range === 'week' ? 'active' : '' }}">Week</a>
                    <a href="?range=month" class="pill-filter {{ $range === 'month' ? 'active' : '' }}">Month</a>
                    <a href="?range=all" class="pill-filter {{ $range === 'all' ? 'active' : '' }}">All Time</a>
                </div>
                
                <button id="btnAddExpense" class="admin-btn-primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 700;">
                    + Record Expense
                </button>
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
                            <th style="width: 100px; text-align: center;">Actions</th>
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
                                    @if($expense->remarks)
                                        @if(preg_match('/Walk-in sale #(\d+)/', $expense->remarks, $matches))
                                            <a href="{{ route('admin.walkin.index', ['q' => $matches[1]]) }}" style="color: #0284c7; text-decoration: none; font-weight: 600;">
                                                {{ $expense->remarks }}
                                            </a>
                                        @elseif(preg_match('/Port delivery #(\d+)/', $expense->remarks, $matches))
                                            <a href="{{ route('admin.ship-deliveries.index', ['q' => $matches[1]]) }}" style="color: #0284c7; text-decoration: none; font-weight: 600;">
                                                {{ $expense->remarks }}
                                            </a>
                                        @else
                                            {{ $expense->remarks }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <div class="actions-cell" style="justify-content: center;">
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
                                <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
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
    </script>
@endsection
