<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSalesWalkin;
use App\Models\TblShipDelivery;
use App\Models\TblExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $tz = 'Asia/Manila';
        $now = Carbon::now($tz);
        $monthStart = $now->copy()->startOfMonth();
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

        // 1. Line Chart Data: Monthly Revenue vs Expenses (Last 6 Months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $walkin = TblSalesWalkin::whereBetween('sold_at', [$start, $end])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $ship = TblShipDelivery::whereBetween('delivered_at', [$start, $end])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $expenses = TblExpense::whereBetween('date', [$start, $end])
                ->sum('amount');

            $monthlyData[] = [
                'month'    => $month->format('M Y'),
                'revenue'  => (float)($walkin + $ship),
                'expenses' => (float)$expenses,
                'profit'   => (float)($walkin + $ship - $expenses)
            ];
        }

        // 2. Doughnut Chart: Sales Source (Current Month)
        $monthWalkin = TblSalesWalkin::where('sold_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $monthShip = TblShipDelivery::where('delivered_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $salesSource = [
            'walkin' => (float)$monthWalkin,
            'ship'   => (float)$monthShip
        ];

        // 3. Expense Breakdown (Current Month)
        $expenseBreakdown = TblExpense::where('date', '>=', $monthStart)
            ->select('expense_type', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_type')
            ->orderByDesc('total')
            ->get();

        // 4. Top Ships (Current Month)
        $topShips = TblShipDelivery::where('delivered_at', '>=', $monthStart)
            ->where('payment_status', 'paid')
            ->select('ship_name', DB::raw('SUM(total_amount) as total_revenue'), DB::raw('SUM(quantity) as total_gallons'))
            ->groupBy('ship_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'monthlyData',
            'salesSource',
            'expenseBreakdown',
            'topShips',
            'now'
        ));
    }
}
