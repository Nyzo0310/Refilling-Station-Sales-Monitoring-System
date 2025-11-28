<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TblSalesWalkin;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->timezone('Asia/Manila')->startOfDay();
        $monthStart = Carbon::now()->timezone('Asia/Manila')->startOfMonth();

        // ============================
        // 1) TODAY'S SALES
        // ============================
        $walkinToday = TblSalesWalkin::where('sold_at', '>=', $today);

        $todayRevenue = $walkinToday->sum('total_amount');
        $todayGallons = $walkinToday->sum('quantity');
        $todayTransactions = $walkinToday->count();

        // ============================
        // 2) THIS MONTH'S SALES
        // ============================
        $walkinMonth = TblSalesWalkin::where('sold_at', '>=', $monthStart);

        $monthRevenue = $walkinMonth->sum('total_amount');
        $monthGallons = $walkinMonth->sum('quantity');

        // Later:
        $monthExpenses = 0;
        $monthProfit = $monthRevenue - $monthExpenses;

        // ============================
        // 3) BACKWASH STATUS (placeholder)
        // ============================
        $lastBackwash = null;      // next module
        $gallonsSinceBackwash = $walkinMonth->sum('quantity'); // temporary
        $backwashLimit = 200;

        return view('admin.dashboard', compact(
            'todayRevenue',
            'todayGallons',
            'todayTransactions',
            'monthRevenue',
            'monthGallons',
            'monthExpenses',
            'monthProfit',
            'gallonsSinceBackwash',
            'backwashLimit',
            'lastBackwash'
        ));
    }
}
