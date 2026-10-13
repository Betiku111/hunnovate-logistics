<?php

namespace App\Http\Middleware;

use App\Enums\RiderStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRiderIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $rider = $user?->rider;

        if (! $rider) {
            abort(403, 'Rider profile not found.');
        }

        if ($rider->status !== RiderStatus::Verified) {
            return redirect()->route('rider.pending');
        }

        return $next($request);
    }
}