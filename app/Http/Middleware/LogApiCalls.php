<?php

namespace App\Http\Middleware;

use App\Models\ApiCallLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiCalls
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $executionTime = round((microtime(true) - $startTime) * 1000);

        // Don't log KPI endpoints to avoid infinite logging
        if (!str_starts_with($request->path(), 'api/kpi')
            && !str_starts_with($request->path(), 'api/dashboard-counts')
            && !str_starts_with($request->path(), 'api/user')) {
            ApiCallLog::create([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => $executionTime,
            ]);
        }

        return $response;
    }
}
