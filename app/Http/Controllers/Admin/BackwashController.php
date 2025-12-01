<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TblBackwashStatus;
use App\Models\TblBackwashLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackwashController extends Controller
{
    public function store(Request $request)
    {
        $status = TblBackwashStatus::first();

        if (!$status) {
            // safety: create a record if missing
            $status = TblBackwashStatus::create([
                'gallons_since_last' => 0,
                'threshold_gallons'  => 200,
                'last_backwash_at'   => null,
            ]);
        }

        if ($status->gallons_since_last < $status->threshold_gallons) {
            return redirect()
                ->back()
                ->with('error', 'You can only log backwash once you reach '
                    . $status->threshold_gallons . ' gallons.');
        }

        DB::transaction(function () use ($status, $request) {
            TblBackwashLog::create([
                'backwash_at' => now(),
                'remarks'     => $request->input('remarks'),
            ]);

            $status->update([
                'last_backwash_at'   => now(),
                'gallons_since_last' => 0,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Backwash logged and counter reset.');
    }
}
