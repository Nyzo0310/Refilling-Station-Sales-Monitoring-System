<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblExpense;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'all');
        $tz = 'Asia/Manila';
        $today = Carbon::today($tz);
        
        $query = TblExpense::query();

        if ($range === 'today') {
            $query->whereDate('date', $today);
            $rangeLabel = "Today";
        } elseif ($range === 'week') {
            $query->whereBetween('date', [
                Carbon::now($tz)->startOfWeek(),
                Carbon::now($tz)->endOfWeek()
            ]);
            $rangeLabel = "This Week";
        } elseif ($range === 'month') {
            $query->whereMonth('date', Carbon::now($tz)->month)
                  ->whereYear('date', Carbon::now($tz)->year);
            $rangeLabel = "This Month";
        } else {
            $rangeLabel = "All time";
        }

        $expenses = $query->orderByDesc('date')->paginate(15);
        $totalExpenses = $query->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'totalExpenses', 'range', 'rangeLabel'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_type' => ['required', 'string', 'max:50'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'remarks'      => ['nullable', 'string', 'max:500'],
            'date'         => ['nullable', 'date'],
        ]);

        $data['date'] = $data['date'] ?? Carbon::now('Asia/Manila')->toDateString();

        TblExpense::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Expense recorded successfully.']);
        }

        return redirect()->back()->with('success', 'Expense recorded successfully.');
    }

    public function update(Request $request, TblExpense $expense)
    {
        $data = $request->validate([
            'expense_type' => ['required', 'string', 'max:50'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'remarks'      => ['nullable', 'string', 'max:500'],
            'date'         => ['nullable', 'date'],
        ]);

        $expense->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Expense updated successfully.']);
        }

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(TblExpense $expense)
    {
        $expense->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Expense deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }
}
