<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSupperAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user() || !auth()->user()?->hasRole('Super Admin')) {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        }
        return $next($request);
    }
}
