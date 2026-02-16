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
        $range         = $request->query('range', 'today');      // today|week|month
        $paymentStatus = $request->query('payment_status', '');  // '', paid, unpaid, partial
        $q             = trim((string) $request->query('q', ''));

        $tz  = config('app.timezone', 'Asia/Manila');
        $now = Carbon::now($tz);

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

            default:
                $range      = 'today';
                $start      = $now->copy()->startOfDay();
                $end        = $now->copy()->endOfDay();
                $rangeLabel = 'Today';
                break;
        }

        $base = TblShipDelivery::query()
            ->whereBetween('delivered_at', [$start, $end])
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
        $totalRevenue        = (clone $summaryQuery)->sum('total_amount');
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
        ]);

        $data['delivered_at']          = Carbon::now($tz);
        $data['total_amount']          = $data['quantity'] * $data['price_per_container'];
        $data['payment_status']        = $data['payment_status'] ?? 'paid';
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
        ]);

        $oldStatus   = $delivery->payment_status;
        $oldQuantity = (float) $delivery->quantity;
        $newStatus   = $data['payment_status'];
        $newQuantity = (float) $data['quantity'];

        $data['total_amount'] = $newQuantity * $data['price_per_container'];
        
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
