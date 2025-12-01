<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TblSalesWalkin; 
use App\Models\TblBackwashStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WalkinSalesController extends Controller
{
    public function index(Request $request)
    {
        $range        = $request->query('range', 'today');     // today|week|month
        $customerType = $request->query('customer_type', '');
        $q            = trim((string) $request->query('q', ''));

        // Use app timezone (set in config/app.php or .env) – fallback PH
        $tz  = config('app.timezone', 'Asia/Manila');
        $now = Carbon::now($tz);

        // ----- Resolve date window based on range -----
        switch ($range) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                $rangeLabel = 'This Week';
                break;

            case 'month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $rangeLabel = 'This Month';
                break;

            default:
                $range = 'today';
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                $rangeLabel = 'Today';
                break;
        }

        // ----- Base query for this range + filters -----
        $base = TblSalesWalkin::query()
            ->whereBetween('sold_at', [$start, $end])
            ->when($customerType !== '', function ($q2) use ($customerType) {
                $q2->where('customer_type', $customerType);
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->where('container_type', 'like', "%{$q}%")
                          ->orWhere('note', 'like', "%{$q}%");
                });
            });

        $summaryQuery = clone $base;
        $salesQuery   = clone $base;

        $transactionsCount = (clone $summaryQuery)->count();
        $gallons           = (clone $summaryQuery)->sum('quantity');      // 1 qty = 1 gal
        $revenue           = (clone $summaryQuery)->sum('total_amount');
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
            'customer_type'        => ['required', 'in:neighbor,non_neighbor,crew_ship'],
            'container_type'       => ['nullable', 'string', 'max:255'],
            'quantity'             => ['required', 'integer', 'min:1'],
            'price_per_container'  => ['required', 'numeric', 'min:0.01'],
            'payment_status'       => ['nullable', 'in:paid,unpaid'],
            'note'                 => ['nullable', 'string', 'max:255'],
        ]);

        $data['sold_at']        = Carbon::now($tz);
        $data['total_amount']   = $data['quantity'] * $data['price_per_container'];
        $data['payment_status'] = $data['payment_status'] ?? 'paid';

        $sale = TblSalesWalkin::create($data);  

        if ($sale->payment_status === 'paid') {
            $status = TblBackwashStatus::first();

            if (!$status) {
                $status = TblBackwashStatus::create([
                    'last_backwash_at'   => null,
                    'gallons_since_last' => 0,
                    'threshold_gallons'  => 200,
                ]);
            }

            // add the gallons of this sale to the running total
            $status->increment('gallons_since_last', $sale->quantity);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $sale->id]);
        }

        return redirect()
            ->route('admin.walkin.index')
            ->with('success', 'Walk-in sale recorded.');
    }
}
