<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\TblSalesWalkin;
use App\Models\TblShipDelivery;
use App\Models\TblExpense;
use App\Models\TblBackwashStatus;
use App\Models\TblBackwashLog;

class DashboardController extends Controller
{
    public function index()
    {
        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        // ===== COMBINED METRICS (WALK-IN + SHIP) =====
        
        // Today
        $todayWalkinRev = TblSalesWalkin::whereDate('sold_at', $today)
            ->sum('total_amount');
        $todayShipRev = TblShipDelivery::whereDate('delivered_at', $today)
            ->sum('total_amount');
        $todayRevenue = $todayWalkinRev + $todayShipRev;

        $todayWalkinGal = TblSalesWalkin::whereDate('sold_at', $today)
            ->sum('quantity');
        $todayShipGal = TblShipDelivery::whereDate('delivered_at', $today)
            ->sum('quantity');
        $todayGallons = $todayWalkinGal + $todayShipGal;

        // Month
        $monthWalkinRev = TblSalesWalkin::where('sold_at', '>=', $monthStart)
            ->sum('total_amount');
        $monthShipRev = TblShipDelivery::where('delivered_at', '>=', $monthStart)
            ->sum('total_amount');
        $monthRevenue = $monthWalkinRev + $monthShipRev;

        // Expenses
        $monthExpenses = TblExpense::where('date', '>=', $monthStart)
            ->sum('amount');
            
        $monthProfit   = $monthRevenue - $monthExpenses;

        $recentWalkins = TblSalesWalkin::orderByDesc('sold_at')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $sale->type = 'Walk-in';
                $sale->date = $sale->sold_at;
                return $sale;
            });

        $recentShips = TblShipDelivery::orderByDesc('delivered_at')
            ->limit(10)
            ->get()
            ->map(function($sale) {
                $sale->type = 'Ship';
                $sale->date = $sale->delivered_at;
                return $sale;
            });

        $overallSales = $recentWalkins->concat($recentShips)
            ->sortByDesc('date')
            ->take(10);

        // ===== BACKWASH STATUS (SINGLE ROW) =====
        $backwashStatus = TblBackwashStatus::first();

        // if wala pang row, create default
        if (!$backwashStatus) {
            $backwashStatus = TblBackwashStatus::create([
                'last_backwash_at'   => null,
                'gallons_since_last' => 0,
                'threshold_gallons'  => 200,
            ]);
        }

        $gallonsSinceLast  = (float) $backwashStatus->gallons_since_last;
        $thresholdGallons  = (int) ($backwashStatus->threshold_gallons ?: 200);
        $backwashPercent   = $thresholdGallons > 0
            ? min(100, round($gallonsSinceLast / $thresholdGallons * 100))
            : 0;
        $lastBackwashAt    = $backwashStatus->last_backwash_at;

        // optional: last log entry if you want remarks later
        $lastBackwashLog = TblBackwashLog::orderByDesc('backwash_at')->first();

        return view('admin.dashboard', compact(
            'todayRevenue',
            'todayGallons',
            'monthRevenue',
            'monthExpenses',
            'monthProfit',
            'overallSales',
            'backwashStatus',
            'gallonsSinceLast',
            'thresholdGallons',
            'backwashPercent',
            'lastBackwashAt',
            'lastBackwashLog',
        ));
    }
}
