<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserStatusActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Authenticated user via token

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'You are unauthorized because your status is inactive'
            ], 403);
        }

        return $next($request);
    }
}
