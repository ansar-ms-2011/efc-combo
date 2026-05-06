<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\CenterWorkingDay;
use Illuminate\Http\Request;
use App\Models\Demography;
use App\Models\Type;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{
    /**
     * 1. INDEX: List with Names from Relationship
     */
    public function index(Request $request)
    {
        $query = Center::with([ 'district', 'tehsil']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        
        $centers = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $centers
        ]);
    }


    public function getTehsils(Request $request)
    {
        $districtId = $request->query('district_id');

        if (!$districtId) {
            return response()->json([], 400); // Bad request if no district
        }

        // Get all tehsils where parent_id = districtId
        $tehsils = Demography::where('parent_id', $districtId)
                            ->where('type', 'TEHSIL')
                            ->get();

        return response()->json($tehsils);
    }

 public function show($id)
    {
        $center = Center::with(['district', 'tehsil', 'workingDays'])->findOrFail($id);

        // Transform working days into objects for multiselect
        $workingDays = $center->workingDays->map(function($day) {
            return [
                'id' => $day->id,
                'name' => $day->name,
            ];
        });

        return response()->json([
            'id' => $center->id,
            'name' => $center->name,
            'number_of_counters' => $center->number_of_counters,
            'address' => $center->address,
            'working_start' => optional(explode(' - ', $center->timing))[0] ?? null,
            'working_end' => optional(explode(' - ', $center->timing))[1] ?? null,
            'lunch_break_start' => optional(explode(' - ', $center->lunch_break))[0] ?? null,
            'lunch_break_end' => optional(explode(' - ', $center->lunch_break))[1] ?? null,
            'jumma_break_start' => optional(explode(' - ', $center->jumma_break))[0] ?? null,
            'jumma_break_end' => optional(explode(' - ', $center->jumma_break))[1] ?? null,
            'district_id' => $center->district_id,
            'district_name' => $center->district->name ?? null,
            'tehsil_id' => $center->tehsil_id,
            'tehsil_name' => $center->tehsil->name ?? null,
            'working_days' => $workingDays,
            'contact_number' => $center->contact_number,
            'geo_location'   => $center->geo_location,
            'latitude'       => $center->latitude,
            'longitude'      => $center->longitude,
        ]);
    }
    /**
     * 2. STORE: Save IDs to avoid SQL Error
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'address'               => 'required|string|max:255',
            'district_id'        => 'required|integer',
            'tehsil_id'          => 'required|integer',
            'working_days'       => 'required|array', // Vue se array aye ga
            'contact_number' => 'nullable|string|max:20',
            // 'geo_location'   => 'nullable|string',
            'latitude'       => 'nullable|string',
            'longitude'      => 'nullable|string',
        ]);

       

        return DB::transaction(function () use ($request) {
            // Migration Integer hai, is liye sirf pehli ID nikalen
            $firstDayId = !empty($request->working_days) ? $request->working_days[0]['id'] : null;

            $center = Center::create([
                'district_id'        => $request->district_id,
                'tehsil_id'          => $request->tehsil_id,
                'name'               => $request->name,
                'number_of_counters' => $request->number_of_counters,
                'address'            => $request->address,
                'timing'             => $request->timing,
                'lunch_break'        => $request->lunch_break,
                'jumma_break'        => $request->jumma_break,
                 'contact_number' => $request->contact_number,
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
            ]);

            // Pivot table mein tamam IDs save karein
            foreach ($request->working_days as $day) {
                CenterWorkingDay::create([
                    'center_id'      => $center->id,
                    'working_day_id' => $day['id'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Center created successfully',
                'data' => $center
            ], 201);
        });
    }

    /**
     * 3. UPDATE: Sync IDs
     */
   public function update(Request $request, Center $center)
{
    $request->validate([
        'name'         => 'required|string|max:255',
        'address'      => 'required|string|max:255',
        'working_days' => 'required|array',
        'district_id'  => 'required|exists:demographies,id',
        'tehsil_id'    => 'required|exists:demographies,id',
         'contact_number' => 'nullable|string|max:20',
        'latitude'       => 'nullable|string',
        'longitude'      => 'nullable|string',
    ]);

    return DB::transaction(function () use ($request, $center) {

        $center->update([
            'district_id'        => $request->district_id,
            'tehsil_id'          => $request->tehsil_id,
            'name'               => $request->name,
            'number_of_counters' => $request->number_of_counters,
            'address'            => $request->address,
            'timing'             => $request->timing,
            'lunch_break'        => $request->lunch_break,
            'jumma_break'        => $request->jumma_break,
             'contact_number' => $request->contact_number,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
        ]);

        // Remove old working days
        CenterWorkingDay::where('center_id', $center->id)->delete();

        // Insert new working days safely
        $validIds = Type::whereIn('id', $request->working_days)->pluck('id')->toArray();
        foreach ($validIds as $id) {
            CenterWorkingDay::create([
                'center_id'      => $center->id,
                'working_day_id' => $id,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $center->load('workingDays')
        ]);
    });
}

    // public function show(Center $center)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => $center->load('workingDaysRelation.type')
    //     ]);
    // }

    public function destroy(Center $center)
    {
        $center->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
