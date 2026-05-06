<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Service;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceCenterController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Start query with relations
            $query = ServiceCenter::with(['center', 'service']);

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('center', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Paginate results (default 15 per page)
            $serviceCenters = $query->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $serviceCenters
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($centerId)
    {
        ServiceCenter::where('center_id', $centerId)->delete();

        return response()->json([
            'success' => true
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.center_id' => 'required|exists:centers,id',
            'assignments.*.service_id' => 'required|exists:services,id',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->assignments as $item) {

                ServiceCenter::updateOrCreate(
                    [
                        'center_id' => $item['center_id'],
                        'service_id' => $item['service_id'],
                    ]
                );
            }

            DB::commit();
            // To get fresh data after update
            Cache::forget('grouped-types');
            Cache::forget('users-dropdown-data');

            return response()->json([
                'success' => true,
                'message' => 'Services assigned successfully'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($centerId)
    {
        $services = Service::all();
        $centers = Center::all();

        $assignedServices = ServiceCenter::where('center_id', $centerId)
            ->pluck('service_id')
            ->toArray();

        return response()->json([
            'center_id' => $centerId,
            'assigned_services' => $assignedServices,
            'services' => $services,
            'centers' => $centers,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            // Get all unique center_ids from payload
            $centerIds = collect($request->assignments)->pluck('center_id')->unique();

            // Delete old assignments for these centers
            ServiceCenter::whereIn('center_id', $centerIds)->delete();

            // Insert new assignments
            foreach ($request->assignments as $item) {
                ServiceCenter::create([
                    'center_id' => $item['center_id'],
                    'service_id' => $item['service_id']
                ]);
            }

            DB::commit();

            // To get fresh data after update
            Cache::forget('grouped-types');
            Cache::forget('users-dropdown-data');

            return response()->json([
                'success' => true,
                'message' => 'Center services updated successfully'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
