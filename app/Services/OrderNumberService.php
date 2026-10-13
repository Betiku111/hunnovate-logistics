<?php

namespace App\Services;

use App\Models\Order;

class OrderNumberService
{
    /**
     * Generate a unique tracking number like HNL-2026-000245.
     * Uses sequential per-year numbering with retry on collision.
     */
    public function generate(): string
    {
        $year = now()->year;
        $prefix = "HNL-{$year}-";

        // Find the highest existing number for the year
        $lastNumber = Order::withTrashed()
            ->where('tracking_number', 'like', $prefix.'%')
            ->orderByDesc('tracking_number')
            ->value('tracking_number');

        $next = 1;
        if ($lastNumber) {
            $suffix = (int) substr($lastNumber, strlen($prefix));
            $next = $suffix + 1;
        }

        // Retry loop to handle race conditions
        $attempts = 0;
        do {
            $candidate = $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            $exists = Order::withTrashed()->where('tracking_number', $candidate)->exists();
            if (! $exists) {
                return $candidate;
            }
            $next++;
            $attempts++;
        } while ($attempts < 100);

        // Extreme fallback — random suffix
        return $prefix.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
