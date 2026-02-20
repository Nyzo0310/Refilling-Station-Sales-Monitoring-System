<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TblShipDelivery;
use App\Support\BackwashUpdater;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipDeliveryController extends Controller
{
    // ========== LIST / PAGE ==========
    public function index(Request $request)
    {
        $range         = $request->query('range', 'all');      // today|week|month|all|custom
        $paymentStatus = $request->query('payment_status', '');  // '', paid, unpaid, partial
        $q             = trim((string) $request->query('q', ''));

        $tz  = config('app.timezone', 'Asia/Manila');
        $now = Carbon::now($tz);

        $start = null;
        $end   = null;

        switch ($range) {
            case 'week':
                $start      = $now->copy()->startOfWeek();
                $end        = $now->copy()->endOfWeek();
                $rangeLabel = 'This Week';
                break;

            case 'month':
                $start      = $now->copy()->startOfMonth();
                $end        = $now->copy()->endOfMonth();
                $rangeLabel = 'This Month';
                break;

            case 'all':
                $rangeLabel = 'All Time';
                break;

            case 'custom':
                $fromDate = $request->query('from_date');
                $toDate   = $request->query('to_date');
                if ($fromDate && $toDate) {
                    $start      = Carbon::parse($fromDate, $tz)->startOfDay();
                    $end        = Carbon::parse($toDate, $tz)->endOfDay();
                    $rangeLabel = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
                } else {
                    $range      = 'today';
                    $start      = $now->copy()->startOfDay();
                    $end        = $now->copy()->endOfDay();
                    $rangeLabel = 'Today';
                }
                break;

            default:
                $range      = 'all';
                $rangeLabel = 'All Time';
                break;
        }

        $base = TblShipDelivery::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('delivered_at', [$start, $end]);
            })
            ->when($paymentStatus !== '', function ($q2) use ($paymentStatus) {
                $q2->where('payment_status', $paymentStatus);
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->where('ship_name', 'like', "%{$q}%")
                        ->orWhere('crew_name', 'like', "%{$q}%")
                        ->orWhere('container_type', 'like', "%{$q}%")
                        ->orWhere('remarks', 'like', "%{$q}%");
                });
            });

        $summaryQuery = clone $base;
        $deliveriesQuery = clone $base;

        $transactionsCount   = (clone $summaryQuery)->count();
        $totalContainers     = (clone $summaryQuery)->sum('quantity');
        $totalRevenue        = (clone $summaryQuery)->sum('money_received');
        $avgPricePerContainer = $totalContainers > 0
            ? $totalRevenue / $totalContainers
            : 0;

        $deliveries = $deliveriesQuery
            ->orderByDesc('delivered_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.ship_deliveries.index', [
            'deliveries'            => $deliveries,
            'range'                 => $range,
            'rangeLabel'            => $rangeLabel,
            'q'                     => $q,
            'paymentStatus'         => $paymentStatus,
            'transactionsCount'     => $transactionsCount,
            'totalContainers'       => $totalContainers,
            'totalRevenue'          => $totalRevenue,
            'avgPricePerContainer'  => $avgPricePerContainer,
        ]);
    }

    // ========== STORE / SAVE ==========
    public function store(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Manila');

        $data = $request->validate([
            'ship_name'             => ['required', 'string', 'max:255'],
            'crew_name'             => ['nullable', 'string', 'max:255'],
            'contact_number'        => ['nullable', 'string', 'max:50'],
            'container_type'        => ['nullable', 'string', 'max:255'],
            'quantity'              => ['required', 'integer', 'min:1'],
            'price_per_container'   => ['required', 'numeric', 'min:0.01'],
            'payment_status'        => ['nullable', 'in:paid,unpaid,partial'],
            'money_received'        => ['nullable', 'numeric', 'min:0'],
            'remarks'               => ['nullable', 'string', 'max:500'],
            'delivered_at'          => ['nullable', 'date'],
        ]);

        if (!empty($data['delivered_at'])) {
            $data['delivered_at'] = Carbon::parse($data['delivered_at'], $tz)->setTimeFrom(Carbon::now($tz));
        } else {
            $data['delivered_at'] = Carbon::now($tz);
        }
        $data['total_amount']          = $data['quantity'] * $data['price_per_container'];
        $data['payment_status']        = $data['payment_status'] ?? 'paid';

        // Auto-sync money_received based on payment status
        if ($data['payment_status'] === 'unpaid') {
            $data['money_received'] = 0;
        } elseif ($data['payment_status'] === 'paid') {
            $data['money_received'] = $data['total_amount'];
        }
        // partial: keep whatever the user entered
        $data['container_size_liters'] = 3.785; // Default to 1 gallon

        $delivery = TblShipDelivery::create($data);

        // 🔵 1 qty = 1 gallon (Usage-based: tracks every gallon dispensed)
        BackwashUpdater::addGallons((float) $delivery->quantity);

        // 🔵 AJAX / fetch → JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'id'  => $delivery->id,
                'msg' => 'Ship delivery recorded.',
            ]);
        }

        // Fallback for normal form POST
        return redirect()
            ->route('admin.ship-deliveries.index')
            ->with('success', 'Ship delivery recorded.');
    }

    public function update(Request $request, TblShipDelivery $delivery)
    {
        $data = $request->validate([
            'ship_name'             => ['required', 'string', 'max:255'],
            'crew_name'             => ['nullable', 'string', 'max:255'],
            'contact_number'        => ['nullable', 'string', 'max:50'],
            'container_type'        => ['nullable', 'string', 'max:255'],
            'quantity'              => ['required', 'integer', 'min:1'],
            'price_per_container'   => ['required', 'numeric', 'min:0.01'],
            'payment_status'        => ['required', 'in:paid,unpaid,partial'],
            'money_received'        => ['nullable', 'numeric', 'min:0'],
            'remarks'               => ['nullable', 'string', 'max:500'],
            'delivered_at'          => ['nullable', 'date'],
        ]);

        if (!empty($data['delivered_at'])) {
            $tz = config('app.timezone', 'Asia/Manila');
            $data['delivered_at'] = Carbon::parse($data['delivered_at'], $tz)->setTimeFrom($delivery->delivered_at);
        }

        $oldStatus   = $delivery->payment_status;
        $oldQuantity = (float) $delivery->quantity;
        $newStatus   = $data['payment_status'];
        $newQuantity = (float) $data['quantity'];

        $data['total_amount'] = $newQuantity * $data['price_per_container'];

        // Auto-sync money_received based on payment status
        if ($data['payment_status'] === 'unpaid') {
            $data['money_received'] = 0;
        } elseif ($data['payment_status'] === 'paid') {
            $data['money_received'] = $data['total_amount'];
        }
        // partial: keep whatever the user entered
        
        $delivery->update($data);

        // 🔵 Synchronize Backwash Monitor (Usage-based: only adjusts if quantity changes)
        if ($newQuantity > $oldQuantity) {
            BackwashUpdater::addGallons($newQuantity - $oldQuantity);
        } elseif ($newQuantity < $oldQuantity) {
            BackwashUpdater::subtractGallons($oldQuantity - $newQuantity);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'msg' => 'Delivery updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Request $request, TblShipDelivery $delivery)
    {
        // 🔵 Reconcile backwash (usage-based)
        BackwashUpdater::subtractGallons((float) $delivery->quantity);

        $delivery->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'msg' => 'Delivery deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Delivery deleted successfully.');
    }
}
