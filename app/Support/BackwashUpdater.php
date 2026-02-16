<?php

namespace App\Support;

use App\Models\TblBackwashStatus;

class BackwashUpdater
{
    /**
     * Add gallons to the running backwash counter.
     */
    public static function addGallons(float $gallons): void
    {
        if ($gallons <= 0) {
            return;
        }

        $status = self::ensureStatus();
        $status->increment('gallons_since_last', $gallons);
    }

    /**
     * Subtract gallons from the running backwash counter.
     */
    public static function subtractGallons(float $gallons): void
    {
        if ($gallons <= 0) {
            return;
        }

        $status = self::ensureStatus();
        
        // Don't let it go below zero
        $newVal = max(0, $status->gallons_since_last - $gallons);
        $status->update(['gallons_since_last' => $newVal]);
    }

    private static function ensureStatus(): TblBackwashStatus
    {
        $status = TblBackwashStatus::first();

        if (!$status) {
            $status = TblBackwashStatus::create([
                'last_backwash_at'   => null,
                'gallons_since_last' => 0,
                'threshold_gallons'  => 200,
            ]);
        }

        return $status;
    }
}
