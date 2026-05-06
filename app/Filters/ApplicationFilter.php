<?php

namespace App\Filters;

class ApplicationFilter
{
    public static function apply($query, $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            if ($request->filled('filterBy')) {
                switch ($request->filterBy) {
                    case 'token':
                        $query->whereHas('appointment', function ($q) use ($search) {
                            $q->where('qmatic_token', 'like', "%{$search}%");
                        });
                        break;

                    case 'identity_number':
                        $query->whereHas('applicant', function ($q) use ($search) {
                            $q->where('identity_number', 'like', "%{$search}%");
                        });
                        break;

                    case 'applicant_name':
                        $query->whereHas('applicant', function ($q) use ($search) {
                            $q->where('full_name', 'like', "%{$search}%");
                        });
                        break;

                    case 'missal':
                        $query->where('missal_no', 'like', "%{$search}%");
                        break;

                    case 'tracking_no':
                        $query->where('tracking_token_no', 'like', "%{$search}%");
                        break;

                    default:
                        break;
                }

            }
        }

        if ($request->filled('region')) {
            $query->where('region_id', $request->region);
        }

        if ($request->filled('region_ids') || $request->filled('district_ids') || $request->filled('tehsil_ids')) {
            $query->where(function ($q) use ($request) {
                if ($request->filled('region_ids')) {
                    $rIds = explode(',', $request->region_ids);
                    $q->orWhereIn('region_id', $rIds);
                }
                if ($request->filled('district_ids')) {
                    $dIds = explode(',', $request->district_ids);
                    $q->orWhereIn('district_id', $dIds);
                }
                if ($request->filled('tehsil_ids')) {
                    $tIds = explode(',', $request->tehsil_ids);
                    $q->orWhereIn('tehsil_id', $tIds);
                }
            });
        } else {
            if (auth()->user()->hasRole('Commissioner')) {
                $query->where('region_id', auth()->user()->region_id);
            }
            if (auth()->user()->hasRole('DC')) {
                $query->where('district_id', auth()->user()->district_id);
            }
            if (auth()->user()->hasRole('AC')) {
                $query->where('tehsil_id', auth()->user()->tehsil_id);
            }
        }

        if ($request->filled('service')) {
            $query->where('certificate_type', $request->service);
        }

        return $query;
    }
}
