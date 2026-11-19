<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'vendor' || ! $user->vendor_approved_at || $user->is_suspended) {
            return response()->json(['message' => 'An approved vendor account is required.'], 403);
        }

        return $next($request);
    }
}
