@extends('layouts.app')

@section('content')
    <style>
        /* ------------------------------------------------------------------
         * Walk-in Sales – page specific styling
         * ------------------------------------------------------------------ */

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 18px;
        }
        @media (min-width: 960px) {
            .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .card {
            background: radial-gradient(circle at top left, #ffffff 0, #f1f5f9 45%, #e0f2fe 100%);
            border-radius: 18px;
            border: 1px solid #dbeafe;
            box-shadow:
                0 18px 40px rgba(15, 23, 42, 0.12),
                0 0 0 1px rgba(148, 163, 184, 0.10);
            padding: 20px 22px;
        }

        .card-label {
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .card-value-xl {
            margin-top: 10px;
            font-size: 26px;
            font-weight: 800;
            color: #022c44;
        }

        .card-subtext-muted {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
        }

        .filters-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .filters-left,
        .filters-right {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .pill-filter {
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid #dbeafe;
            background: linear-gradient(to bottom, #f8fafc, #e5f0ff);
            font-size: 12px;
            color: #0f172a;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.04em;
            transition: all .16s ease;
        }
        .pill-filter.active {
            background: linear-gradient(to right, #0ea5e9, #0369a1);
            border-color: #0284c7;
            color: #f9fbff;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }
        .pill-filter:not(.active):hover {
            background: #e0f2fe;
        }

        .select-sm {
            border-radius: 999px;
            border: 1px solid #dbeafe;
            padding: 6px 30px 6px 12px;
            font-size: 12px;
            background: #ffffff;
            color: #0f172a;
            outline: none;
            font-weight: 500;
            min-width: 170px;
        }

        .input-search {
            border-radius: 999px;
            border: 1px solid #dbeafe;
            padding: 7px 14px;
            font-size: 12px;
            min-width: 210px;
            background: #ffffff;
            outline: none;
        }

        .btn-primary {
            border-radius: 999px;
            border: none;
            background: radial-gradient(circle at 10% 0, #7dd3fc 0, #0ea5e9 35%, #0369a1 80%);
            color: #f9fbff;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.45);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary:hover { filter: brightness(1.05); transform: translateY(-0.5px); }

        .btn-primary::before {
            content: '+';
            font-weight: 900;
            font-size: 13px;
        }

        .table-card { margin-top: 16px; }

        table.sales-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .sales-table thead {
            background: linear-gradient(to right, #e5f0ff, #e0f2fe);
        }

        .sales-table th,
        .sales-table td {
            padding: 9px 10px;
            text-align: left;
        }
        .sales-table th {
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid #dbeafe;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .sales-table tbody tr:nth-child(even) { background: #f9fbff; }
        .sales-table tbody tr:hover {
            background: #e0f2fe;
            box-shadow: inset 3px 0 0 #0ea5e9;
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

        .recent-card-title {
            font-size: 15px;
            font-weight: 800;
            color: #022c44;
        }
        .recent-card-sub {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        /* ---- SweetAlert2 premium modal tweaks ---- */

        .walkin-popup.swal2-popup {
            border-radius: 26px !important;
            padding: 22px 26px 24px !important;
            box-shadow:
                0 30px 80px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(148, 163, 184, 0.18);
        }

        .walkin-popup .swal2-title {
            font-size: 20px !important;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #022c44;
        }

        .walkin-popup .swal2-html-container {
            margin-top: 6px;
        }

        .walkin-popup .swal2-input {
            border-radius: 14px;
            border: 1px solid #dbeafe;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.08);
            padding: 8px 10px;
            font-size: 13px;
        }

        .walkin-popup .swal-row {
            display: flex;
            gap: 8px;
        }
        .walkin-popup .swal-col {
            flex: 1;
        }

        .walkin-popup label {
            display: block;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .walkin-popup .total-bar {
            margin-top: 6px;
            padding: 8px 12px;
            border-radius: 14px;
            background: linear-gradient(to right, #e0f2fe, #bfdbfe);
            font-size: 12px;
            font-weight: 600;
            color: #022c44;
        }

        .walkin-popup .btn-ghost {
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 600;
        }
         /* ---------- SweetAlert water theme (darker, easier to read) ---------- */
        .swal-water.swal2-popup {
            border-radius: 18px !important; /* was 24 – less “pill”, more card */
            padding: 22px 26px 20px !important;
            background: radial-gradient(circle at top, #0b1120 0, #020617 55%, #020617 100%) !important;
            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.75),
                0 0 0 1px rgba(30, 64, 175, 0.45);
            color: #e5e7eb !important;
        }

        .swal-water .swal2-title {
            font-size: 21px !important;
            font-weight: 700 !important;
            color: #e5f3ff !important;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 8px !important;
        }

        .swal-water .swal2-html-container {
            margin: 10px 0 0 !important;
            padding-top: 4px;
        }

        /* Form layout */
        .swal-water .swal-form {
            font-size: 14px;
            color: #e5e7eb;
        }

        .swal-water .swal-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .swal-water .swal-field {
            flex: 1 1 0;
            min-width: 0;
        }

        .swal-water .swal-field.full {
            flex-basis: 100%;
        }

        /* Labels */
        .swal-water .swal-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #cbd5f5;
            margin-bottom: 4px;
        }

        /* Inputs + select – less rounded + taller */
        .swal-water .swal2-input,
        .swal-water .swal-select {
            box-sizing: border-box;
            width: 100% !important;
            margin: 0 0 6px !important;
            border-radius: 10px !important;          /* was 999px – now card-like */
            border: 1px solid #1f2937 !important;
            padding: 11px 14px !important;           /* taller fields */
            min-height: 44px;                        /* makes sure text fits nicely */
            font-size: 14px !important;
            background: #020617 !important;
            color: #e5e7eb !important;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.9);
        }

        .swal-water .swal2-input::placeholder {
            color: #64748b;
        }

        .swal-water .swal2-input:focus,
        .swal-water .swal-select:focus {
            border-color: #38bdf8 !important;
            box-shadow:
                0 0 0 1px rgba(56, 189, 248, 0.6),
                0 0 0 4px rgba(15, 118, 178, 0.7) !important;
        }

        /* Total bar – slightly less rounded, comfy height */
        .swal-water .swal-total-wrap {
            margin-top: 10px;
        }

        .swal-water .swal-total-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 4px;
        }

        .swal-water .swal-total-bar {
            border-radius: 12px;
            background: linear-gradient(to right, #0f172a, #0b3a5c);
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .swal-water .swal-total-main {
            font-size: 14px;
            font-weight: 700;
            color: #e5f3ff;
        }

        .swal-water .swal-total-sub {
            font-size: 12px;
            color: #7dd3fc;
            font-weight: 500;
        }

        /* Buttons */
        .swal-water .swal2-actions {
            margin-top: 18px !important;
            gap: 10px;
        }

        .swal-water .swal-confirm {
            border-radius: 999px !important;
            background: linear-gradient(to right, #0ea5e9, #0369a1) !important;
            padding: 9px 24px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.65);
            color: #f9fbff !important;
        }

        .swal-water .swal-confirm:hover {
            filter: brightness(1.05);
        }

        .swal-water .swal-cancel {
            border-radius: 999px !important;
            background: #020617 !important;
            color: #e5e7eb !important;
            border: 1px solid #1f2937 !important;
            padding: 9px 20px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
        }

        /* Darker overlay behind the popup */
        .swal2-container.swal2-center {
            background-color: rgba(15, 23, 42, 0.70) !important;
        }
    </style>

    {{-- Top bar --}}
    <header class="admin-topbar">
        <div>
            <div class="admin-topbar-title">Walk-in Sales</div>
            <div class="admin-topbar-sub">
                Track gallons sold, revenue, and customers for over-the-counter refills.
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="pill-date">{{ now()->format('M d, Y') }}</span>
            <div class="avatar-small">A</div>
        </div>
    </header>

    <div class="admin-body">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filters + Summary --}}
        <div class="card">
            <form method="GET" action="{{ route('admin.walkin.index') }}" class="filters-bar">
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
                        <option value="crew_ship" {{ $customerType === 'crew_ship' ? 'selected' : '' }}>Ship crew (walk-in)</option>
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

            <div class="grid-3">
                <div>
                    <div class="card-label">Gallons Sold ({{ $rangeLabel }})</div>
                    <div class="card-value-xl">{{ $gallons }} gal</div>
                    <div class="card-subtext-muted">
                        Across {{ $transactionsCount }} transaction{{ $transactionsCount === 1 ? '' : 's' }}
                    </div>
                </div>

                <div>
                    <div class="card-label">Revenue ({{ $rangeLabel }})</div>
                    <div class="card-value-xl">₱ {{ number_format($revenue, 2) }}</div>
                    <div class="card-subtext-muted">
                        Includes normal &amp; crew pricing
                    </div>
                </div>

                <div>
                    <div class="card-label">Average Price per Gallon</div>
                    <div class="card-value-xl">₱ {{ number_format($avgPricePerGallon, 2) }}</div>
                    <div class="card-subtext-muted">
                        Auto-computed from selected range
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card table-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                <div class="recent-card-title">Walk-in Sales Log</div>
                <div class="recent-card-sub">
                    Showing latest transactions ({{ $sales->total() }} record{{ $sales->total() === 1 ? '' : 's' }})
                </div>
            </div>

            <table class="sales-table">
                <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Container Type</th>
                    <th>Qty</th>
                    <th>Price / Container</th>
                    <th>Total</th>
                    <th>Customer Type</th>
                    <th>Status</th>
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
                        <td>{{ $sale->sold_at?->format('M d, Y h:i A') }}</td>
                        <td>{{ $label }}</td>
                        <td>{{ $sale->container_type ?? '—' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>₱ {{ number_format($sale->price_per_container, 2) }}</td>
                        <td>₱ {{ number_format($sale->total_amount, 2) }}</td>
                        <td>{{ $label }}</td>
                        <td>
                            <span class="badge-status {{ $sale->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding:16px 10px;color:#64748b;">
                            No walk-in sales recorded for this range yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            @if ($sales->hasPages())
                <div style="margin-top:10px;">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('btnAddWalkin');
        if (!btn) return;

        btn.addEventListener('click', function () {
            const csrf = '{{ csrf_token() }}';

            Swal.fire({
                title: 'ADD WALK-IN SALE',
                html: `
                    <div class="swal-form">
                        <div class="swal-row">
                            <div class="swal-field full">
                                <div class="swal-label">Customer type</div>
                                <select id="swal_customer_type" class="swal2-input swal-select">
                                    <option value="neighbor">Neighbor</option>
                                    <option value="non_neighbor">Non-neighbor</option>
                                    <option value="crew_ship">Ship crew (walk-in)</option>
                                </select>
                            </div>
                        </div>

                        <div class="swal-row">
                            <div class="swal-field full">
                                <div class="swal-label">Container type</div>
                                <input id="swal_container_type"
                                    class="swal2-input"
                                    placeholder="e.g. 5 gal blue">
                            </div>
                        </div>

                        <div class="swal-row">
                            <div class="swal-field">
                                <div class="swal-label">Quantity</div>
                                <input id="swal_qty"
                                    type="number"
                                    min="1"
                                    value="1"
                                    class="swal2-input">
                            </div>
                            <div class="swal-field">
                                <div class="swal-label">Price / container (₱)</div>
                                <input id="swal_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="swal2-input">
                            </div>
                        </div>

                        <div class="swal-row">
                            <div class="swal-field full">
                                <div class="swal-label">Note (optional)</div>
                                <input id="swal_note"
                                    class="swal2-input"
                                    placeholder="short internal note">
                            </div>
                        </div>

                        <div class="swal-total-wrap">
                            <div class="swal-total-label">Total</div>
                            <div id="swal_total_bar" class="swal-total-bar">
                                <div id="swal_total_main" class="swal-total-main">₱ 0.00</div>
                                <div id="swal_total_sub" class="swal-total-sub">
                                    0 × ₱ 0.00 per container
                                </div>
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
                    const typeEl  = document.getElementById('swal_customer_type');
                    const qtyEl   = document.getElementById('swal_qty');
                    const priceEl = document.getElementById('swal_price');
                    const totalMain = document.getElementById('swal_total_main');
                    const totalSub  = document.getElementById('swal_total_sub');

                    const baseNormal = 25;
                    const baseCrew   = 30;

                    function updateSuggestedPrice() {
                        if (!priceEl.dataset.dirty || priceEl.dataset.dirty === '0') {
                            priceEl.value = (typeEl.value === 'crew_ship') ? baseCrew : baseNormal;
                        }
                        updateTotal();
                    }

                    function updateTotal() {
                        const q = parseFloat(qtyEl.value || '0');
                        const p = parseFloat(priceEl.value || '0');
                        const t = q * p;

                        totalMain.textContent = '₱ ' + t.toFixed(2);
                        totalSub.textContent  = q + ' × ₱ ' + p.toFixed(2) + ' per container';
                    }

                    typeEl.addEventListener('change', updateSuggestedPrice);
                    qtyEl.addEventListener('input', updateTotal);
                    priceEl.addEventListener('input', () => {
                        priceEl.dataset.dirty = '1';
                        updateTotal();
                    });

                    // initial values
                    updateSuggestedPrice();
                },
                preConfirm: () => {
                    const typeEl  = document.getElementById('swal_customer_type');
                    const contEl  = document.getElementById('swal_container_type');
                    const qtyEl   = document.getElementById('swal_qty');
                    const priceEl = document.getElementById('swal_price');
                    const noteEl  = document.getElementById('swal_note');

                    const qty   = parseInt(qtyEl.value || '0', 10);
                    const price = parseFloat(priceEl.value || '0');

                    if (!qty || qty < 1) {
                        Swal.showValidationMessage('Quantity must be at least 1.');
                        return false;
                    }
                    if (isNaN(price) || price <= 0) {
                        Swal.showValidationMessage('Price per container must be greater than 0.');
                        return false;
                    }

                    return {
                        customer_type: typeEl.value,
                        container_type: contEl.value,
                        quantity: qty,
                        price_per_container: price,
                        payment_status: 'paid',
                        note: noteEl.value
                    };
                }
            }).then(result => {
                if (!result.isConfirmed || !result.value) return;

                const payload  = result.value;
                const formData = new FormData();
                formData.append('_token', csrf);
                Object.keys(payload).forEach(k => formData.append(k, payload[k]));

                fetch('{{ route('admin.walkin.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(resp => {
                    if (!resp.ok) throw new Error('Request failed');
                    return resp.json().catch(() => ({}));
                })
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Walk-in sale recorded.',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not save sale. Please try again.'
                    });
                });
            });
        });
    });
    </script>
@endsection
