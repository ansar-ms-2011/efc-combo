<?php

namespace App\Http\Controllers;

use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $regionId = request('district_id') ?? auth()->user()->region_id;
        $districtId = request('district_id') ?? auth()->user()->district_id;
        $tehsilId = request('tehsil_id') ?? auth()->user()->tehsil_id;
        $centerId = request('center_id') ?? auth()->user()->center_id;

        $query = Application::query();

        $query->when($regionId, fn($q) => $q->where('region_id', $regionId))
            ->when($districtId, fn($q) => $q->where('district_id', $districtId))
            ->when($tehsilId, fn($q) => $q->where('tehsil_id', $tehsilId))
            ->when($centerId, fn($q) => $q->where('center_id', $centerId));

        $statusCounts = $this->fetchStatusCounts(clone $query);
        $typeCounts = $this->fetchTypeCounts(clone $query);
        $averageProcessingTime = round((clone $query)
            ->whereNotNull('processing_days')
            ->avg('processing_days'));

        return response()->json([
            'all' => $statusCounts->sum(),
            'pending' => $statusCounts['pending'] ?? 0,
            'submitted' => $statusCounts['submitted'] ?? 0,
            'verified' => $statusCounts['verified'] ?? 0,
            'approved' => $statusCounts['approved'] ?? 0,
            'delivery' => $statusCounts['ready_for_delivery'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
            'objected' => $statusCounts['objected'] ?? 0,
            'domicile_certificate' => $typeCounts['domicile'] ?? 0,
            'state_subject_certificate' => $typeCounts['state'] ?? 0,
            'average_processing_time' => $averageProcessingTime,
        ]);
    }


    // public function dashboardCounts()
    // {
    //     $query = Application::query();

    //     if (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Commissioner')) {
    //         $districtId = request('district_id');
    //         $tehsilId   = request('tehsil_id');
    //         $centerId   = request('center_id');

    //         $query->when($districtId, fn($q) => $q->where('district_id', $districtId))
    //             ->when($tehsilId,   fn($q) => $q->where('tehsil_id', $tehsilId))
    //             ->when($centerId,   fn($q) => $q->where('center_id', $centerId));

    //         $hasFilters = $districtId || $tehsilId || $centerId;
    //     } elseif (auth()->user()->hasRole('DC')) {
    //         $query->where('district_id', auth()->user()->district_id);
    //         $hasFilters = true;
    //     } else {
    //         // Match exactly what index() does — grouped OR
    //         $query->where(function ($q) {
    //             $q->where('district_id', auth()->user()->district_id)
    //                 ->where('tehsil_id', auth()->user()->tehsil_id);
    //         });
    //         $hasFilters = true;
    //     }

    //     $statusCounts = (!$hasFilters)
    //         ? Cache::rememberForever('dashboard_counts', fn() => $this->fetchCounts(clone $query))
    //         : $this->fetchCounts($query);

    //     // Certificate type counts (separate query)
    //     $typeCounts = $query->where('current_status', 'delivered')->select('certificate_type')
    //         ->selectRaw('COUNT(*) as total')
    //         ->groupBy('certificate_type')
    //         ->pluck('total', 'certificate_type');

    //     return response()->json([
    //         'all'                       => $statusCounts->sum(),
    //         'pending'                   => $statusCounts['pending'] ?? 0,
    //         'submitted'                 => $statusCounts['submitted'] ?? 0,
    //         'verified'                  => $statusCounts['verified'] ?? 0,
    //         'approved'                  => $statusCounts['approved'] ?? 0,
    //         'delivery'                  => $statusCounts['ready_for_delivery'] ?? 0,
    //         'delivered'                 => $statusCounts['delivered'] ?? 0,
    //         'objected'                  => $statusCounts['objected'] ?? 0,
    //         'domicile_certificate'      => $typeCounts['domicile'] ?? 0,
    //         'state_subject_certificate' => $typeCounts['state'] ?? 0,
    //     ]);
    // }

    // private function fetchCounts($query)
    // {
    //     return $query->select('current_status')
    //         ->selectRaw('COUNT(*) as total')
    //         ->groupBy('current_status')
    //         ->pluck('total', 'current_status');
    // }

    private function fetchStatusCounts($query)
    {
        return $query->select('current_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status');
    }

    private function fetchTypeCounts($query)
    {
        return $query->where('current_status', 'delivered')->select('certificate_type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('certificate_type')
            ->pluck('total', 'certificate_type');
    }
}
