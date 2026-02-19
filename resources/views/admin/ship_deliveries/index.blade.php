@extends('layouts.app')

@section('content')
    <style>
        /* Ship Delivery specific tweaks */
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

        .delivery-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .delivery-table th { background: #f8fafc; padding: 12px; font-size: 11px; color: #64748b; text-transform: uppercase; text-align: left; }
        .delivery-table td { padding: 12px; border-top: 1px solid #f1f5f9; }
    </style>

    {{-- Top bar --}}
    <header class="admin-topbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mobile-toggle">☰</button>
            <div>
                <div class="admin-topbar-title">Ship Deliveries</div>
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
            <div class="mb-3 px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-3 px-4 py-2 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters Section --}}
        <div class="card" style="margin-bottom: 20px;">
            <form method="GET"
                action="{{ route('admin.ship-deliveries.index') }}"
                class="filters-bar-standard" style="margin-bottom: 0;">

                <div class="filters-group">
                    <div class="filter-pills-row">
                        <button type="submit" name="range" value="today"
                                class="pill-filter {{ $range === 'today' ? 'active' : '' }}">
                            Today
                        </button>
                        <button type="submit" name="range" value="week"
                                class="pill-filter {{ $range === 'week' ? 'active' : '' }}">
                            Week
                        </button>
                        <button type="submit" name="range" value="month"
                                class="pill-filter {{ $range === 'month' ? 'active' : '' }}">
                            Month
                        </button>
                    </div>

                    <select name="payment_status" class="select-sm" onchange="this.form.submit()">
                        <option value="" {{ $paymentStatus === '' ? 'selected' : '' }}>All payment status</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ $paymentStatus === 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>

                <div class="filters-group" style="flex: 1; justify-content: flex-end;">
                    <input type="text"
                           name="q"
                           class="input-search"
                           placeholder="Search ship, crew, container..."
                           value="{{ $q }}"
                           style="flex: 1; min-width: 200px;">
                    <button type="button" class="btn-primary" id="btnAddDelivery">
                        + Ship Delivery
                    </button>
                </div>
            </form>
        </div>

        {{-- Metrics Section --}}
        <div class="grid-3" style="margin-bottom: 20px;">
            <div class="card">
                <div class="card-label">Containers Delivered ({{ $rangeLabel }})</div>
                <div class="card-value-xl">
                    {{ $totalContainers }} container{{ $totalContainers == 1 ? '' : 's' }}
                </div>
                <div class="card-subtext-muted">
                    Across {{ $deliveries->total() }} transaction{{ $deliveries->total() == 1 ? '' : 's' }}
                </div>
            </div>

            <div class="card">
                <div class="card-label">Revenue ({{ $rangeLabel }})</div>
                <div class="card-value-xl">
                    ₱ {{ number_format($totalRevenue, 2) }}
                </div>
                <div class="card-subtext-muted">
                    Includes delivery fee when encoded.
                </div>
            </div>

            <div class="card">
                <div class="card-label">Avg Price / Container</div>
                <div class="card-value-xl">
                    ₱ {{ number_format($avgPricePerContainer, 2) }}
                </div>
                <div class="card-subtext-muted">
                    Auto-computed from paid deliveries.
                </div>
            </div>
        </div>

        {{-- Ship deliveries table --}}
        <div class="card" style="margin-top:24px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <h3 style="margin:0; font-size:18px;">Ship Delivery Log</h3>
                <span style="font-size:12px; color:#64748b;">{{ $deliveries->total() }} records total</span>
            </div>

            <div class="table-responsive">
                <table class="delivery-table" style="min-width: 900px;">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Ship / Crew</th>
                            <th>Container</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($deliveries as $d)
                        <tr>
                            <td style="white-space:nowrap; font-weight:500;">{{ $d->delivered_at?->format('M d, h:i A') }}</td>
                            <td>
                                <strong style="color:var(--water-deep);">{{ $d->ship_name }}</strong>
                                <div style="font-size:11px; color:#64748b;">Crew: {{ $d->crew_name ?: '—' }}</div>
                            </td>
                            <td>
                                {{ $d->container_type ?: '—' }}
                                <div style="font-size:11px; color:#64748b;">{{ number_format($d->container_size_liters, 2) }}L</div>
                            </td>
                            <td style="font-weight:700;">{{ $d->quantity }}</td>
                            <td>₱{{ number_format($d->price_per_container, 2) }}</td>
                            <td style="font-weight:700; color:var(--water-deep);">₱{{ number_format($d->total_amount, 2) }}</td>
                            <td style="font-weight:700; color:#15803d;">₱{{ number_format($d->money_received ?? 0, 2) }}</td>
                            <td>
                                <span class="badge-status {{ strtolower($d->payment_status ?? '') }}">
                                    {{ ucfirst($d->payment_status ?: 'n/a') }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <button class="btn-edit-delivery" 
                                            data-id="{{ $d->id }}"
                                            data-ship="{{ $d->ship_name }}"
                                            data-crew="{{ $d->crew_name }}"
                                            data-contact="{{ $d->contact_number }}"
                                            data-container="{{ $d->container_type }}"
                                            data-qty="{{ $d->quantity }}"
                                            data-price="{{ $d->price_per_container }}"
                                            data-status="{{ $d->payment_status }}"
                                            data-money="{{ $d->money_received }}"
                                            data-remarks="{{ $d->remarks }}"
                                            style="color:var(--water-accent); padding:4px;" title="Edit">
                                        ✏️
                                    </button>
                                    <button class="btn-delete-delivery" 
                                            data-id="{{ $d->id }}"
                                            style="color:#ef4444; padding:4px;" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:32px; text-align:center; color:#64748b;">
                                No deliveries found for this criteria.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveries->hasPages())
                <div style="margin-top:10px;">
                    {{ $deliveries->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    (function() {
        function initShipDeliveries() {
            const btn = document.getElementById('btnAddDelivery');
            if (!btn) return;

            const csrf = '{{ csrf_token() }}';

            btn.addEventListener('click', function () {
                Swal.fire({
                    title: 'ADD SHIP DELIVERY',
                    html: `
                        <div class="swal-form">
                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">What is the Ship's Name?</div>
                                    <input id="swal_ship_name" class="swal2-input" placeholder="e.g. MV Masagana">
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Who is the Crew? (Optional)</div>
                                    <input id="swal_crew_name" class="swal2-input" placeholder="Name of recipient">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">Contact Number</div>
                                    <input id="swal_contact" class="swal2-input" placeholder="Optional ph/radio">
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Container Description</div>
                                    <input id="swal_container_type" class="swal2-input" placeholder="e.g. 200L Drum">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">How many gallons?</div>
                                    <input id="swal_qty" type="number" min="1" class="swal2-input" value="1">
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Price per gallon (₱)</div>
                                    <input id="swal_price" type="number" min="0" step="0.01" class="swal2-input" placeholder="0.00">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field">
                                    <div class="swal-label">Payment status</div>
                                    <select id="swal_status" class="swal2-input swal-select">
                                        <option value="paid">✅ Fully Paid</option>
                                        <option value="unpaid">⏳ Unpaid / Credit</option>
                                        <option value="partial">🌗 Partial Payment</option>
                                    </select>
                                </div>
                                <div class="swal-field">
                                    <div class="swal-label">Amount Received (₱)</div>
                                    <input id="swal_received" type="number" min="0" step="0.01" class="swal2-input" placeholder="0.00">
                                </div>
                            </div>

                            <div class="swal-row">
                                <div class="swal-field full">
                                    <div class="swal-label">Additional Remarks (Optional)</div>
                                    <input id="swal_remarks" class="swal2-input" placeholder="Specify any extra details">
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
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-water',
                        confirmButton: 'swal-confirm',
                        cancelButton: 'swal-cancel'
                    },
                    didOpen: () => {
                        const qtyEl      = document.getElementById('swal_qty');
                        const priceEl    = document.getElementById('swal_price');
                        const receivedEl = document.getElementById('swal_received');
                        const totalMain  = document.getElementById('swal_total_main');
                        const totalSub   = document.getElementById('swal_total_sub');

                        function updateTotal() {
                            const q = parseFloat(qtyEl.value || '0');
                            const p = parseFloat(priceEl.value || '0');
                            const r = parseFloat(receivedEl.value || '0');
                            const t = q * p;
                            const change = Math.max(0, r - t);

                            totalMain.textContent = '₱ ' + t.toFixed(2);
                            let subText = q + ' × ₱ ' + p.toFixed(2) + ' each';
                            if (r > t) {
                                subText += ' | Change: ₱ ' + change.toFixed(2);
                            }
                            totalSub.textContent = subText;
                        }

                        receivedEl.addEventListener('input', () => {
                            updateTotal();
                            const q = parseFloat(qtyEl.value || '0');
                            const p = parseFloat(priceEl.value || '0');
                            const r = parseFloat(receivedEl.value || '0');
                            const t = q * p;
                            const statusEl = document.getElementById('swal_status');

                            if (r === 0) statusEl.value = 'unpaid';
                            else if (r > 0 && r < t) statusEl.value = 'partial';
                            else if (r >= t && t > 0) statusEl.value = 'paid';
                        });
                        updateTotal();
                    },
                    preConfirm: () => {
                        const shipEl   = document.getElementById('swal_ship_name');
                        const qtyEl    = document.getElementById('swal_qty');
                        const priceEl  = document.getElementById('swal_price');

                        const ship = shipEl.value.trim();
                        const qty  = parseInt(qtyEl.value || '0', 10);
                        const price = parseFloat(priceEl.value || '0');

                        if (!ship) {
                            Swal.showValidationMessage('Ship name is required.');
                            return false;
                        }
                        if (!qty || qty < 1) {
                            Swal.showValidationMessage('Quantity must be at least 1.');
                            return false;
                        }
                        if (isNaN(price) || price <= 0) {
                            Swal.showValidationMessage('Price per item must be greater than 0.');
                            return false;
                        }

                        return {
                            ship_name: ship,
                            crew_name: document.getElementById('swal_crew_name').value,
                            contact_number: document.getElementById('swal_contact').value,
                            container_type: document.getElementById('swal_container_type').value,
                            quantity: qty,
                            price_per_container: price,
                            payment_status: document.getElementById('swal_status').value,
                            money_received: document.getElementById('swal_received').value || 0,
                            remarks: document.getElementById('swal_remarks').value,
                        };
                    }
                }).then(result => {
                    if (!result.isConfirmed || !result.value) return;

                    const payload  = result.value;
                    const formData = new FormData();
                    formData.append('_token', csrf);
                    Object.keys(payload).forEach(k => formData.append(k, payload[k]));

                    fetch('{{ route('admin.ship-deliveries.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(resp => {
                        if (!resp.ok) throw new Error('Request failed: ' + resp.status);
                        return resp.json();
                    })
                    .then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Delivery saved',
                            text: 'Ship delivery recorded successfully.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'swal-water' }
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not save delivery. Please try again.',
                            customClass: { popup: 'swal-water' }
                        });
                    });
                });
            });

            // Handle Edit Delivery
            document.querySelectorAll('.btn-edit-delivery').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    
                    const initialData = {
                        ship: this.dataset.ship,
                        crew: this.dataset.crew || '',
                        contact: this.dataset.contact || '',
                        container: this.dataset.container || '',
                        qty: this.dataset.qty,
                        price: this.dataset.price,
                        status: this.dataset.status,
                        money: this.dataset.money || 0,
                        remarks: this.dataset.remarks || ''
                    };

                    Swal.fire({
                        title: 'EDIT SHIP DELIVERY',
                        html: `
                            <div class="swal-form">
                                <div class="swal-row">
                                    <div class="swal-field">
                                        <div class="swal-label">Ship name</div>
                                        <input id="edit_ship_name" class="swal2-input" value="${initialData.ship}">
                                    </div>
                                    <div class="swal-field">
                                        <div class="swal-label">Crew name</div>
                                        <input id="edit_crew_name" class="swal2-input" value="${initialData.crew}">
                                    </div>
                                </div>
                                <div class="swal-row">
                                    <div class="swal-field">
                                        <div class="swal-label">Contact number</div>
                                        <input id="edit_contact" class="swal2-input" value="${initialData.contact}">
                                    </div>
                                    <div class="swal-field">
                                        <div class="swal-label">Container type</div>
                                        <input id="edit_container_type" class="swal2-input" value="${initialData.container}">
                                    </div>
                                </div>
                                <div class="swal-row">
                                    <div class="swal-field">
                                        <div class="swal-label">Quantity</div>
                                        <input id="edit_qty" type="number" min="1" class="swal2-input" value="${initialData.qty}">
                                    </div>
                                    <div class="swal-field">
                                        <div class="swal-label">Price / unit (₱)</div>
                                        <input id="edit_price" type="number" min="0" step="0.01" class="swal2-input" value="${initialData.price}">
                                    </div>
                                </div>
                                <div class="swal-row">
                                    <div class="swal-field">
                                        <div class="swal-label">Payment status</div>
                                        <select id="edit_status" class="swal2-input swal-select">
                                            <option value="paid" ${initialData.status === 'paid' ? 'selected' : ''}>Paid</option>
                                            <option value="unpaid" ${initialData.status === 'unpaid' ? 'selected' : ''}>Unpaid</option>
                                            <option value="partial" ${initialData.status === 'partial' ? 'selected' : ''}>Partial</option>
                                        </select>
                                    </div>
                                    <div class="swal-field">
                                        <div class="swal-label">Money received (₱)</div>
                                        <input id="edit_received" type="number" min="0" step="0.01" class="swal2-input" value="${initialData.money}">
                                    </div>
                                </div>
                                <div class="swal-row">
                                    <div class="swal-field full">
                                        <div class="swal-label">Remarks</div>
                                        <input id="edit_remarks" class="swal2-input" value="${initialData.remarks}">
                                    </div>
                                </div>
                                <div class="swal-total-wrap">
                                    <div class="swal-total-label">Updated Total</div>
                                    <div class="swal-total-bar">
                                        <div id="edit_total_main" class="swal-total-main">₱ 0.00</div>
                                        <div id="edit_total_sub" class="swal-total-sub">0 × ₱ 0.00</div>
                                    </div>
                                </div>
                            </div>
                        `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Update Delivery',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'swal-water',
                            confirmButton: 'swal-confirm',
                            cancelButton: 'swal-cancel'
                        },
                        didOpen: () => {
                            const qtyEl = document.getElementById('edit_qty');
                            const priceEl = document.getElementById('edit_price');
                            const receivedEl = document.getElementById('edit_received');
                            const totalMain = document.getElementById('edit_total_main');
                            const totalSub = document.getElementById('edit_total_sub');

                            function updateTotal() {
                                const q = parseFloat(qtyEl.value || '0');
                                const p = parseFloat(priceEl.value || '0');
                                const r = parseFloat(receivedEl.value || '0');
                                const t = q * p;
                                const change = Math.max(0, r - t);
                                totalMain.textContent = '₱ ' + t.toFixed(2);
                                totalSub.textContent = `${q} × ₱ ${p.toFixed(2)} | Change: ₱ ${change.toFixed(2)}`;
                            }

                            receivedEl.addEventListener('input', () => {
                                updateTotal();
                                const q = parseFloat(qtyEl.value || '0');
                                const p = parseFloat(priceEl.value || '0');
                                const r = parseFloat(receivedEl.value || '0');
                                const t = q * p;
                                const statusEl = document.getElementById('edit_status');

                                if (r === 0) statusEl.value = 'unpaid';
                                else if (r > 0 && r < t) statusEl.value = 'partial';
                                else if (r >= t && t > 0) statusEl.value = 'paid';
                            });
                            updateTotal();
                        },
                        preConfirm: () => {
                            return {
                                ship_name: document.getElementById('edit_ship_name').value,
                                crew_name: document.getElementById('edit_crew_name').value,
                                contact_number: document.getElementById('edit_contact').value,
                                container_type: document.getElementById('edit_container_type').value,
                                quantity: document.getElementById('edit_qty').value,
                                price_per_container: document.getElementById('edit_price').value,
                                payment_status: document.getElementById('edit_status').value,
                                money_received: document.getElementById('edit_received').value,
                                remarks: document.getElementById('edit_remarks').value
                            };
                        }
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        const formData = new FormData();
                        formData.append('_token', csrf);
                        formData.append('_method', 'PATCH');
                        Object.keys(result.value).forEach(k => formData.append(k, result.value[k]));

                        fetch(`{{ url('/admin/ship-deliveries') }}/${id}`, {
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

            // Handle Delete Delivery
            document.querySelectorAll('.btn-delete-delivery').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    Swal.fire({
                        title: 'DELETE DELIVERY?',
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

                            fetch(`{{ url('/admin/ship-deliveries') }}/${id}`, {
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
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initShipDeliveries);
        } else {
            initShipDeliveries();
        }
    })();
    </script>
@endsection
