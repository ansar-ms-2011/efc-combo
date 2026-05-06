<?php

namespace App\Http\Controllers;

use App\Models\Demography;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\DB;


class DemographyController extends Controller
{
  public function index(Request $request)
{
    try {
        $request->validate([
            'type' => 'string|in:COUNTRY,REGION,DISTRICT,TEHSIL,CITY,UNION_COUNCIL'
        ]);

        $query = Demography::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ✅ parent_id filter
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $demographies = $query->with('parent')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $demographies
        ]);

    } catch (Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching demographies',
            'error' => $th->getMessage()
        ], 500);
    }
}


    // Create
    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required',
                'urdu_name' => 'nullable',
                'code' => 'nullable|string|max:50',
                'type_original' => 'required|in:COUNTRY,REGION,DISTRICT,TEHSIL,CITY,UNION_COUNCIL',
                'parent_id' => 'nullable|exists:demographies,id',
                'is_ajk_district' => 'nullable',
            ]);

            if ($request->type_original === 'COUNTRY' || $request->type_original==='REGION') {
                $parentId = null;
            } else {
                if (!$request->parent_id) {
                    return response()->json(['message' => 'Parent is required'], 422);
                }
            }
            $demography = Demography::create([
                'name' => $request->name,
                'urdu_name' => $request->urdu_name,
                'code' => $request->code,
                'type' => $request->type_original,
                'parent_id' => $request->parent_id,
                'is_ajk_district' => $request->is_ajk_district,
                'created_by' => Auth::id(),
            ]);
            $demography->load('parent');

            DB::commit();

            Cache::forget('grouped-types');

            return response()->json([
                'success' => true,
                'message' => 'Demography created successfully!',
                'data' => $demography
            ], 201);
        } catch (Throwable $th) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating demography',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Show
    public function show($id)
    {
        try {
            $demography = Demography::with('parent')->find($id);

            if (!$demography) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demography not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $demography
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching demography',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Update
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'urdu_name' => 'nullable|string',
                'code' => 'nullable|string|max:50',
                'type_original' => 'required|in:COUNTRY,REGION,DISTRICT,TEHSIL,CITY,UNION_COUNCIL',
                'parent_id' => 'nullable|exists:demographies,id',
                'is_ajk_district' => 'boolean',
            ]);

            $demography = Demography::find($id);

            if (!$demography) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demography not found'
                ], 404);

                $parentId = null;
            }

            // // Parent type map
            // $parentTypeMap = [
            //     'REGION' => 'COUNTRY',
            //     'DISTRICT' => 'REGION',
            //     'TEHSIL' => 'DISTRICT',
            //     'CITY' => 'TEHSIL',
            //     'UNION_COUNCIL' => 'CITY',
            // ];

            // COUNTRY has no parent
            if ($request->type_original === 'COUNTRY') {
                $parentId = null;
            } else {
                if (!$request->parent_id) {
                    return response()->json([
                        'message' => 'Parent is required'
                    ], 422);
                }

                $parent = Demography::find($request->parent_id);

                if (!$parent) {
                    return response()->json([
                        'message' => 'Parent not found'
                    ], 422);
                }
                $parentId = $request->parent_id;
            }

            // Update record
            $demography->update([
                'name' => $request->name,
                'urdu_name' => $request->urdu_name,
                'code' => $request->code,
                'type' => $request->type_original,
                'parent_id' => $request->parent_id,
                'is_ajk_district' => $request->is_ajk_district,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Demography updated successfully!',
                'data' => $demography
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating demography',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    // delete
    public function destroy($id)
    {
        try {
            $demography = Demography::find($id);

            if (!$demography) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demography not found'
                ], 404);
            }

            $demography->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demography deleted successfully!'
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting demography',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Get parents
    public function parents($type)
    {

        $parents = Demography::where('type', $type)
            ->select('id', 'name', 'urdu_name')
            ->orderBy('name')
            ->get();
        return response()->json($parents);
    }

    public function districts()
    {
        return Demography::where('type', 'DISTRICT')
            ->select('id', 'name', 'urdu_name')
            ->orderBy('name')
            ->get();
    }
    public function getTehsils()
    {
        $districtId = request()->get('district_id');
        return Demography::where('type', 'TEHSIL')
            ->where('parent_id', $districtId)  // parent_id = district id
            ->select('id', 'name', 'urdu_name', 'parent_id')
            ->orderBy('name')
            ->get();
    }
}
