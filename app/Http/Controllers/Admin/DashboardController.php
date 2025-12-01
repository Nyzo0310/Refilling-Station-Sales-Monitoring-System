<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\TblSalesWalkin;
use App\Models\TblBackwashStatus;
use App\Models\TblBackwashLog;

class DashboardController extends Controller
{
    public function index()
    {
        $today      = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        // ===== WALK-IN METRICS =====
        $todayRevenue = TblSalesWalkin::whereDate('sold_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $todayGallons = TblSalesWalkin::whereDate('sold_at', $today)
            ->where('payment_status', 'paid')
            ->sum('quantity');

        $monthRevenue = TblSalesWalkin::where('sold_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $monthExpenses = 0; // later: real expenses table
        $monthProfit   = $monthRevenue - $monthExpenses;

        $recentWalkins = TblSalesWalkin::orderByDesc('sold_at')
            ->limit(5)
            ->get();

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
            'recentWalkins',
            'backwashStatus',
            'gallonsSinceLast',
            'thresholdGallons',
            'backwashPercent',
            'lastBackwashAt',
            'lastBackwashLog',
        ));
    }
}
