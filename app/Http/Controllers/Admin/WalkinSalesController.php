<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TblSalesWalkin;
use App\Models\TblExpense;
use App\Support\BackwashUpdater;   // ✅ keep this
use Carbon\Carbon;
use Illuminate\Http\Request;

class WalkinSalesController extends Controller
{
    public function index(Request $request)
    {
        $range        = $request->query('range', 'all'); // today|week|month|all|custom
        $customerType = $request->query('customer_type', '');
        $q            = trim((string) $request->query('q', ''));

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
                $rangeLabel = 'All Time';
                break;
        }

        $base = TblSalesWalkin::query()
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('sold_at', [$start, $end]);
            })
            ->when($customerType !== '', function ($q2) use ($customerType) {
                $q2->where('customer_type', $customerType);
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->where('container_type', 'like', "%{$q}%")
                          ->orWhere('note', 'like', "%{$q}%");
                });
            });

        $summaryQuery      = clone $base;
        $salesQuery        = clone $base;
        $transactionsCount = (clone $summaryQuery)->count();
        $gallons           = (clone $summaryQuery)->sum('quantity'); // 1 qty = 1 gal
        $revenue           = (clone $summaryQuery)->sum('money_received');
        $avgPricePerGallon = $gallons > 0 ? $revenue / $gallons : 0;

        $sales = $salesQuery
            ->orderByDesc('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.walk-in.walkin-sales', [
            'sales'              => $sales,
            'range'              => $range,
            'rangeLabel'         => $rangeLabel,
            'customerType'       => $customerType,
            'q'                  => $q,
            'transactionsCount'  => $transactionsCount,
            'gallons'            => $gallons,
            'revenue'            => $revenue,
            'avgPricePerGallon'  => $avgPricePerGallon,
        ]);
    }

    public function store(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Manila');

        $data = $request->validate([
            'customer_type'       => ['required', 'in:neighbor,non_neighbor,crew_ship'],
            'container_type'      => ['nullable', 'string', 'max:255'],
            'quantity'            => ['required', 'integer', 'min:1'],
            'price_per_container' => ['required', 'numeric', 'min:0.01'],
            'payment_status'      => ['nullable', 'in:paid,unpaid,partial'],
            'money_received'      => ['nullable', 'numeric', 'min:0'],
            'note'                => ['nullable', 'string', 'max:255'],
            'sold_at'             => ['nullable', 'date'],
        ]);

        if (!empty($data['sold_at'])) {
            $data['sold_at'] = Carbon::parse($data['sold_at'], $tz)->setTimeFrom($now = Carbon::now($tz));
        } else {
            $data['sold_at'] = Carbon::now($tz);
        }
        $data['total_amount']   = $data['quantity'] * $data['price_per_container'];
        $data['payment_status'] = $data['payment_status'] ?? 'paid';

        // Auto-sync money_received based on payment status
        if ($data['payment_status'] === 'unpaid') {
            $data['money_received'] = 0;
        } elseif ($data['payment_status'] === 'paid') {
            $data['money_received'] = $data['total_amount'];
        }
        // partial: keep whatever the user entered

        $sale = TblSalesWalkin::create($data);

        // 🔵 Auto-record delivery boy expense (₱5 per gallon)
        TblExpense::create([
            'date'         => $sale->sold_at->toDateString(),
            'expense_type' => 'Delivery Boy',
            'amount'       => $sale->quantity * 5,
            'remarks'      => 'Walk-in sale #' . $sale->id . ' (' . $sale->quantity . ' gal)',
        ]);

        // 🔵 Update backwash gallons (usage-based: tracks every gallon dispensed)
        BackwashUpdater::addGallons((float) $sale->quantity);

        // 🔵 IMPORTANT: kapag AJAX (SweetAlert + fetch), mag-return ng JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'id'  => $sale->id,
                'msg' => 'Walk-in sale recorded.',
            ]);
        }

        return redirect()
            ->route('admin.walkin.index')
            ->with('success', 'Walk-in sale recorded.');
    }

    public function update(Request $request, TblSalesWalkin $sale)
    {
        $data = $request->validate([
            'customer_type'       => ['required', 'in:neighbor,non_neighbor,crew_ship'],
            'container_type'      => ['nullable', 'string', 'max:255'],
            'quantity'            => ['required', 'integer', 'min:1'],
            'price_per_container' => ['required', 'numeric', 'min:0.01'],
            'payment_status'      => ['required', 'in:paid,unpaid,partial'],
            'money_received'      => ['nullable', 'numeric', 'min:0'],
            'note'                => ['nullable', 'string', 'max:255'],
        ]);

        $oldStatus   = $sale->payment_status;
        $oldQuantity = (float) $sale->quantity;
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
        
        $sale->update($data);

        // 🔵 Synchronize Backwash Monitor (Usage-based: only adjusts if quantity changes)
        if ($newQuantity > $oldQuantity) {
            BackwashUpdater::addGallons($newQuantity - $oldQuantity);
        } elseif ($newQuantity < $oldQuantity) {
            BackwashUpdater::subtractGallons($oldQuantity - $newQuantity);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'msg' => 'Sale updated successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Sale updated successfully.');
    }

    public function destroy(Request $request, TblSalesWalkin $sale)
    {
        // 🔵 Reconcile backwash (usage-based)
        BackwashUpdater::subtractGallons((float) $sale->quantity);

        $sale->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'  => true,
                'msg' => 'Sale deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Sale deleted successfully.');
    }
}
