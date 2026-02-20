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
        
        // Today (only count money actually received)
        $todayWalkinRev = TblSalesWalkin::whereDate('sold_at', $today)
            ->sum('money_received');
        $todayShipRev = TblShipDelivery::whereDate('delivered_at', $today)
            ->sum('money_received');
        $todayRevenue = $todayWalkinRev + $todayShipRev;

        $todayWalkinGal = TblSalesWalkin::whereDate('sold_at', $today)
            ->sum('quantity');
        $todayShipGal = TblShipDelivery::whereDate('delivered_at', $today)
            ->sum('quantity');
        $todayGallons = $todayWalkinGal + $todayShipGal;

        // Month (only count money actually received)
        $monthWalkinRev = TblSalesWalkin::where('sold_at', '>=', $monthStart)
            ->sum('money_received');
        $monthShipRev = TblShipDelivery::where('delivered_at', '>=', $monthStart)
            ->sum('money_received');
        $monthRevenue = $monthWalkinRev + $monthShipRev;

        // Overall (only count money actually received)
        $overallWalkinRev = TblSalesWalkin::sum('money_received');
        $overallShipRev   = TblShipDelivery::sum('money_received');
        $overallRevenue   = $overallWalkinRev + $overallShipRev;

        // Overall Expenses
        $overallExpenses = TblExpense::sum('amount');
        $overallProfit   = $overallRevenue - $overallExpenses;

        // Expenses (Month - for reference if needed, but we focus on overall now)
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
            'overallRevenue',
            'overallExpenses',
            'overallProfit',
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
