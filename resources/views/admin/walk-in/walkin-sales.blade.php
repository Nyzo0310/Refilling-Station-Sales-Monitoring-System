@extends('layouts.app')

@section('content')
    <style>
        /* Walk-in specific tweaks */
        .filters-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            .filters-bar { flex-direction: column; align-items: stretch; }
            .filters-left, .filters-right { width: 100%; flex-direction: column; align-items: stretch; }
            .pill-filter { text-align: center; }
            .input-search, .select-sm { width: 100% !important; }
        }

        .pill-filter {
            padding: 8px 16px;
            border-radius: 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }
        .pill-filter.active { background: var(--water-accent); color: white; border-color: var(--water-accent); }

        .select-sm, .input-search {
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--water-accent);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .sales-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .sales-table th { background: #f8fafc; padding: 12px; font-size: 11px; color: #64748b; text-transform: uppercase; text-align: left; }
        .sales-table td { padding: 12px; border-top: 1px solid #f1f5f9; }
    </style>

    {{-- Top bar --}}
    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-toggle">☰</button>
            <div>
                <div class="admin-topbar-title">Walk-in Sales</div>
                <div class="admin-topbar-sub">
                    Track gallons sold, revenue, and customers for over-the-counter refills.
                </div>
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="pill-date">{{ now()->format('M d, Y') }}</span>
            <div class="avatar-small">{{ substr(Auth::user()->name, 0, 1) }}</div>
        </div>
    </header>

    <div class="admin-body">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters Section --}}
        <div class="card" style="margin-bottom: 20px;">
            <form method="GET" action="{{ route('admin.walkin.index') }}" class="filters-bar" style="margin-bottom: 0;">
                <div class="filters-left">
                    <button type="submit" name="range" value="today"
                            class="pill-filter {{ $range === 'today' ? 'active' : '' }}">
                        Today
                    </button>
                    <button type="submit" name="range" value="week"
                            class="pill-filter {{ $range === 'week' ? 'active' : '' }}">
                        This Week
                    </button>
                    <button type="submit" name="range" value="month"
                            class="pill-filter {{ $range === 'month' ? 'active' : '' }}">
                        This Month
                    </button>
                    <span class="pill-filter" style="opacity:.6;cursor:default;">
                        Custom range (soon)
                    </span>

                    <select name="customer_type" class="select-sm" onchange="this.form.submit()">
                        <option value="" {{ $customerType === '' ? 'selected' : '' }}>All customer types</option>
                        <option value="neighbor" {{ $customerType === 'neighbor' ? 'selected' : '' }}>Neighbor</option>
                        <option value="non_neighbor" {{ $customerType === 'non_neighbor' ? 'selected' : '' }}>Non-neighbor</option>
                    </select>
                </div>

                <div class="filters-right">
                    <input type="text"
                           name="q"
                           class="input-search"
                           placeholder="Search container / note..."
                           value="{{ $q }}">
                    <button type="button" class="btn-primary" id="btnAddWalkin">
                        Walk-in Sale
                    </button>
                </div>
            </form>
        </div>

        {{-- Metrics Section --}}
        <div class="grid-3" style="margin-bottom: 20px;">
            <div class="card">
                <div class="card-label">Gallons Sold ({{ $rangeLabel }})</div>
                <div class="card-value-xl">{{ $gallons }} gal</div>
                <div class="card-subtext-muted">
                    Across {{ $transactionsCount }} transaction{{ $transactionsCount === 1 ? '' : 's' }}
                </div>
            </div>

            <div class="card">
                <div class="card-label">Revenue ({{ $rangeLabel }})</div>
                <div class="card-value-xl">₱ {{ number_format($revenue, 2) }}</div>
                <div class="card-subtext-muted">
                    Includes normal &amp; crew pricing
                </div>
            </div>

            <div class="card">
                <div class="card-label">Average Price / Gal</div>
                <div class="card-value-xl">₱ {{ number_format($avgPricePerGallon, 2) }}</div>
                <div class="card-subtext-muted">
                    Auto-computed from selected range
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card" style="margin-top: 24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <h3 style="margin:0; font-size:18px;">Walk-in Sales Log</h3>
                <span style="font-size:12px; color:#64748b;">{{ $sales->total() }} records total</span>
            </div>
            
            <div class="table-responsive">
                <table class="sales-table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Container</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            @php
                                $label = match ($sale->customer_type) {
                                    'neighbor'     => 'Neighbor',
                                    'non_neighbor' => 'Non-neighbor',
                                    'crew_ship'    => 'Ship crew',
                                    default        => ucfirst($sale->customer_type),
                                };
                            @endphp
                            <tr>
                                <td style="white-space:nowrap; font-weight:500;">{{ $sale->sold_at?->format('M d, h:i A') }}</td>
                                <td style="font-weight:600;">{{ $label }}</td>
                                <td>{{ $sale->container_type ?? '—' }}</td>
                                <td style="font-weight:700;">{{ $sale->quantity }}</td>
                                <td>₱{{ number_format($sale->price_per_container, 2) }}</td>
                                <td style="font-weight:700; color:var(--water-deep);">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge-status {{ $sale->payment_status }}">
                                        {{ ucfirst($sale->payment_status) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <button class="btn-edit-sale" 
                                                data-id="{{ $sale->id }}"
                                                data-type="{{ $sale->customer_type }}"
                                                data-container="{{ $sale->container_type }}"
                                                data-qty="{{ $sale->quantity }}"
                                                data-price="{{ $sale->price_per_container }}"
                                                data-status="{{ $sale->payment_status }}"
                                                data-note="{{ $sale->note }}"
                                                style="color:var(--water-accent); padding:4px;" title="Edit">
                                            ✏️
                                        </button>
                                        <button class="btn-delete-sale" 
                                                data-id="{{ $sale->id }}"
                                                style="color:#ef4444; padding:4px;" title="Delete">
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding:32px; text-align:center; color:#64748b;">
                                    No sales found for this criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->hasPages())
                <div style="margin-top:10px;">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 🔵 Add Walk-in Sale
        const btnAdd = document.getElementById('btnAddWalkin');
        if (btnAdd) {
            btnAdd.addEventListener('click', function() {
                const csrf = '{{ csrf_token() }}';
                Swal.fire({
                    title: 'ADD WALK-IN SALE',
                    html: `
                        <div class="swal-form">
                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">Customer Category</div>
                                    <select id="swal_customer_type" class="swal2-input swal-select">
                                        <option value="neighbor">🏠 Neighbor (Local Area)</option>
                                        <option value="non_neighbor">🚶 Non-neighbor (Walk-in)</option>
                                    </select>
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Container Description</div>
                                    <input id="swal_container_type" class="swal2-input" placeholder="e.g. 5 Gallon Slim Blue">
                                </div>
                            </div>
                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">How many gallons/containers?</div>
                                    <input id="swal_qty" type="number" min="1" value="1" class="swal2-input">
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Price per item (₱)</div>
                                    <input id="swal_price" type="number" step="0.01" min="0" class="swal2-input" placeholder="0.00">
                                </div>
                            </div>
                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Payment Status</div>
                                    <select id="swal_payment_status" class="swal2-input swal-select">
                                        <option value="paid">✅ Fully Paid Now</option>
                                        <option value="unpaid">⏳ Pay Later / Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Internal Notes (Optional)</div>
                                    <input id="swal_note" class="swal2-input" placeholder="e.g. borrowed container, special request">
                                </div>
                            </div>
                            <div class="swal-total-wrap">
                                <div class="swal-total-label">Grand Total</div>
                                <div class="swal-total-bar">
                                    <div id="swal_total_main" class="swal-total-main">₱ 0.00</div>
                                    <div id="swal_total_sub" class="swal-total-sub">0 × ₱ 0.00 each</div>
                                </div>
                            </div>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Save Sale',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-water',
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel'
                    },
                    didOpen: () => {
                        const qtyEl = document.getElementById('swal_qty');
                        const priceEl = document.getElementById('swal_price');
                        const totalMain = document.getElementById('swal_total_main');
                        const totalSub = document.getElementById('swal_total_sub');

                        function updateTotal() {
                            const q = parseFloat(qtyEl.value || '0');
                            const p = parseFloat(priceEl.value || '0');
                            const t = q * p;
                            totalMain.textContent = '₱ ' + t.toFixed(2);
                            totalSub.textContent = q + ' × ₱ ' + p.toFixed(2) + ' each';
                        }
                        qtyEl.addEventListener('input', updateTotal);
                        priceEl.addEventListener('input', updateTotal);
                    },
                    preConfirm: () => {
                        return {
                            customer_type: document.getElementById('swal_customer_type').value,
                            container_type: document.getElementById('swal_container_type').value,
                            quantity: parseInt(document.getElementById('swal_qty').value || '0', 10),
                            price_per_container: parseFloat(document.getElementById('swal_price').value || '0'),
                            payment_status: document.getElementById('swal_payment_status').value,
                            note: document.getElementById('swal_note').value
                        };
                    }
                }).then(result => {
                    if (!result.isConfirmed || !result.value) return;
                    const formData = new FormData();
                    formData.append('_token', csrf);
                    Object.keys(result.value).forEach(k => formData.append(k, result.value[k]));

                    fetch('{{ route("admin.walkin.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.ok) {
                            Swal.fire({ icon: 'success', title: 'Saved', text: data.msg, timer: 1500, showConfirmButton: false, customClass: { popup: 'swal-water' }})
                            .then(() => window.location.reload());
                        } else {
                            throw new Error(data.msg || 'Save failed');
                        }
                    })
                    .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message, customClass: { popup: 'swal-water' }}));
                });
            });
        }

        // 🟢 Edit Sale
        document.querySelectorAll('.btn-edit-sale').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = this.dataset.type;
                const container = this.dataset.container;
                const qty = this.dataset.qty;
                const price = this.dataset.price;
                const status = this.dataset.status;
                const note = this.dataset.note;
                const csrf = '{{ csrf_token() }}';

                Swal.fire({
                    title: 'EDIT WALK-IN SALE',
                    html: `
                        <div class="swal-form">
                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Customer Category</div>
                                    <select id="edit_customer_type" class="swal2-input swal-select">
                                        <option value="neighbor" ${type === 'neighbor' ? 'selected' : ''}>🏠 Neighbor (Local Area)</option>
                                        <option value="non_neighbor" ${type === 'non_neighbor' ? 'selected' : ''}>🚶 Non-neighbor (Walk-in)</option>
                                        <option value="crew_ship" ${type === 'crew_ship' ? 'selected' : ''}>🚢 Ship Crew</option>
                                    </select>
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Container Description</div>
                                    <input id="edit_container_type" class="swal2-input" value="${container || ''}" placeholder="e.g. 5 Gallon Slim Blue">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">How many?</div>
                                    <input id="edit_qty" type="number" min="1" value="${qty}" class="swal2-input">
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Price per item (₱)</div>
                                    <input id="edit_price" type="number" step="0.01" min="0" value="${price}" class="swal2-input">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Payment Status</div>
                                    <select id="edit_payment_status" class="swal2-input swal-select">
                                        <option value="paid" ${status === 'paid' ? 'selected' : ''}>✅ Fully Paid Now</option>
                                        <option value="unpaid" ${status === 'unpaid' ? 'selected' : ''}>⏳ Pay Later / Credit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Internal Notes (Optional)</div>
                                    <input id="edit_note" class="swal2-input" value="${note || ''}" placeholder="short internal note">
                                </div>
                            </div>

                            <div class="swal-total-wrap">
                                <div class="swal-total-label">Updated Grand Total</div>
                                <div id="edit_total_bar" class="swal-total-bar">
                                    <div id="edit_total_main" class="swal-total-main">₱ 0.00</div>
                                    <div id="edit_total_sub" class="swal-total-sub">0 × ₱ 0.00 each</div>
                                </div>
                            </div>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Update Sale',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-water',
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel'
                    },
                    didOpen: () => {
                        const qtyEl = document.getElementById('edit_qty');
                        const priceEl = document.getElementById('edit_price');
                        const totalMain = document.getElementById('edit_total_main');
                        const totalSub = document.getElementById('edit_total_sub');

                        function updateTotal() {
                            const q = parseFloat(qtyEl.value || '0');
                            const p = parseFloat(priceEl.value || '0');
                            const t = q * p;
                            totalMain.textContent = '₱ ' + t.toFixed(2);
                            totalSub.textContent = q + ' × ₱ ' + p.toFixed(2) + ' each';
                        }

                        qtyEl.addEventListener('input', updateTotal);
                        priceEl.addEventListener('input', updateTotal);
                        updateTotal();
                    },
                    preConfirm: () => {
                        return {
                            customer_type: document.getElementById('edit_customer_type').value,
                            container_type: document.getElementById('edit_container_type').value,
                            quantity: parseInt(document.getElementById('edit_qty').value || '0', 10),
                            price_per_container: parseFloat(document.getElementById('edit_price').value || '0'),
                            payment_status: document.getElementById('edit_payment_status').value,
                            note: document.getElementById('edit_note').value
                        };
                    }
                }).then(result => {
                    if (!result.isConfirmed || !result.value) return;

                    const formData = new FormData();
                    formData.append('_token', csrf);
                    formData.append('_method', 'PATCH');
                    Object.keys(result.value).forEach(k => formData.append(k, result.value[k]));

                    fetch(`{{ url('/admin/walkin-sales') }}/${id}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.ok) {
                            Swal.fire({ icon: 'success', title: 'Updated', text: data.msg, timer: 1500, showConfirmButton: false, customClass: { popup: 'swal-water' }})
                            .then(() => window.location.reload());
                        } else {
                            throw new Error(data.msg || 'Update failed');
                        }
                    })
                    .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message, customClass: { popup: 'swal-water' }}));
                });
            });
        });

        // 🔴 Delete Sale
        document.querySelectorAll('.btn-delete-sale').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const csrf = '{{ csrf_token() }}';

                Swal.fire({
                    title: 'DELETE SALE?',
                    text: 'This will permanently remove this record and reconcile the backwash monitor if it was paid.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Keep it',
                    customClass: {
                        popup: 'swal-water',
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel'
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('_token', csrf);
                        formData.append('_method', 'DELETE');

                        fetch(`{{ url('/admin/walkin-sales') }}/${id}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(resp => resp.json())
                        .then(data => {
                            if (data.ok) {
                                Swal.fire({ icon: 'success', title: 'Deleted', text: data.msg, timer: 1500, showConfirmButton: false, customClass: { popup: 'swal-water' }})
                                .then(() => window.location.reload());
                            } else {
                                throw new Error(data.msg || 'Delete failed');
                            }
                        })
                        .catch(err => Swal.fire({ icon: 'error', title: 'Error', text: err.message, customClass: { popup: 'swal-water' }}));
                    }
                });
            });
        });
    });
    </script>
@endsection
