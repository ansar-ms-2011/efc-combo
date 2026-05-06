<?php

namespace App\Http\Controllers;

use App\Models\ApiCallLog;
use App\Models\Application;
use App\Models\FailedLogin;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function getActiveUsers()
    {
        $cacheKey = 'kpi.active_users';

        $activeUsers = Cache::remember($cacheKey, now()->addMinutes(2), function () {
            return User::where('last_activity', '>=', now()->subMinutes(2))->count();
        });

        $totalUsers = Cache::remember('kpi.total_users', now()->addMinutes(10), function () {
            return User::count();
        });

        return response()->json([
            'active_users' => $activeUsers,
            'total_users' => $totalUsers,
            'percentage_active' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0,
        ]);
    }

    public function getTransactionVolume()
    {
        $cacheKey = 'kpi.transaction_volume';

        $stats = Cache::remember($cacheKey, now()->addMinutes(2), function () {
            // Today
            $today = Application::whereDate('created_at', today())
                ->select(
                    DB::raw('COUNT(*) as count'),
                )
                ->first();

            // This week
            $thisWeek = Application::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->select(
                    DB::raw('COUNT(*) as count'),
                )
                ->first();

            // This month
            $thisMonth = Application::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->select(
                    DB::raw('COUNT(*) as count'),
                )
                ->first();

            // Last 7-day trend
            $last7Days = Application::where('created_at', '>=', now()->subDays(7))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'today' => [
                    'count' => (int) ($today->count ?? 0),
                ],
                'this_week' => [
                    'count' => (int) ($thisWeek->count ?? 0),
                ],
                'this_month' => [
                    'count' => (int) ($thisMonth->count ?? 0),
                ],
                'last_7_days_trend' => $last7Days,
            ];
        });

        return response()->json($stats);
    }

    public function getApiCalls()
    {
        $cacheKey = 'kpi.api_calls';

        $stats = Cache::remember($cacheKey, now()->addMinutes(2), function () {
            // Today
            $today = ApiCallLog::whereDate('created_at', today())->count();

            // Average response time today
            $avgResponseTime = ApiCallLog::whereDate('created_at', today())
                ->avg('response_time_ms') ?? 0;

            // Top 5 endpoints
            $topEndpoints = ApiCallLog::whereDate('created_at', today())
                ->select('endpoint', DB::raw('COUNT(*) as call_count'))
                ->groupBy('endpoint')
                ->orderBy('call_count', 'desc')
                ->limit(5)
                ->get();

            // Last 24-hour hourly breakdown
            $last24Hours = ApiCallLog::where('created_at', '>=', now()->subHours(24))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%h %p") as hour'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('hour')
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get();

            // Status code breakdown
            $statusCodes = ApiCallLog::whereDate('created_at', today())
                ->select('status_code', DB::raw('COUNT(*) as count'))
                ->groupBy('status_code')
                ->get();

            return [
                'today' => $today,
                'avg_response_time_ms' => round($avgResponseTime, 2),
                'top_endpoints' => $topEndpoints,
                'hourly_breakdown' => $last24Hours,
                'status_codes' => $statusCodes,
            ];
        });

        return response()->json($stats);
    }

    public function getFailedLogins()
    {
        $cacheKey = 'kpi.failed_logins';

        $stats = Cache::remember($cacheKey, now()->addMinutes(2), function () {
            // Today
            $today = FailedLogin::whereDate('created_at', today())->count();

            // Last 7-day trend
            $last7Days = FailedLogin::where('created_at', '>=', now()->subDays(7))
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top offending IPs
            $topIPs = FailedLogin::whereDate('created_at', today())
                ->select('ip', DB::raw('COUNT(*) as attempt_count'))
                ->groupBy('ip')
                ->orderBy('attempt_count', 'desc')
                ->limit(5)
                ->get();

            // Top attempted emails
            $topEmails = FailedLogin::whereDate('created_at', today())
                ->select('email', DB::raw('COUNT(*) as attempt_count'))
                ->groupBy('email')
                ->orderBy('attempt_count', 'desc')
                ->limit(5)
                ->get();

            // Compare with last week
            $lastWeek = FailedLogin::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
            $change = $lastWeek > 0 ? round((($today - $lastWeek) / $lastWeek) * 100, 1) : 0;

            return [
                'today' => $today,
                'last_7_days_trend' => $last7Days,
                'top_offending_ips' => $topIPs,
                'top_attempted_emails' => $topEmails,
                'change_from_last_week_percentage' => $change,
            ];
        });

        return response()->json($stats);
    }
}
