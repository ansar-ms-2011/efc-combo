<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Demography;
use App\Models\RequiredDocument;
use App\Models\Type;
use Cache;
use Illuminate\Http\Request;
use Log;

class TypeController extends ApiController
{
    // GET /types
    public function index(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:group,item',
            ]);
            if ($request->type == 'group') {
                $query = Type::whereNull('parent_id');
            } else {
                $query = Type::whereNotNull('parent_id');
            }

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('urdu_name', 'LIKE', "%{$searchTerm}%");
                });
            }

            $types = $query->orderBy('created_at', 'desc')->paginate(15);

            return $this->success(message: $types);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return $this->error(message: $th->getMessage());
        }
    }

    // POST /types
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'urdu_name' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:types,id',
            ]);

            $type = Type::create([
                'name' => $request->name,
                'urdu_name' => $request->urdu_name,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Type created successfully',
                'data' => $type
            ], 201);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // GET /types/{id}
    public function show($id)
    {
        try {
            $type = Type::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $type
            ], 200);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // PUT/PATCH /types/{id}
    public function update(Request $request, $id)
    {
        try {
            $type = Type::find($id);

            if (!$type) {
                return response()->json([
                    'status' => false,
                    'message' => 'Type not found'
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'urdu_name' => 'nullable|string|max:255',
                'parent_id' => 'nullable|exists:types,id',
            ]);

            $type->update([
                'name' => $request->name ?? $type->name,
                'urdu_name' => $request->urdu_name ?? $type->urdu_name,
                'parent_id' => $request->parent_id ?? $type->parent_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Type updated successfully',
                'data' => $type
            ], 200);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // DELETE /types/{id}
    public function destroy($id)
    {
        try {
            $type = Type::findOrFail($id);
            $type->delete();

            return response()->json([
                'status' => true,
                'message' => 'Type deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function workingDays()
    {
        // Working Days parent
        $parent = Type::where('parent_id', null)
            ->where('name', 'working_days')
            ->first();

        if (!$parent) {
            return response()->json([]);
        }

        // Us ke children (Monday, Tuesday...)
        return Type::where('parent_id', $parent->id)
            ->select('id', 'name')
            ->get();
    }

    public function getTypesByParentName($parentName)
    {
        $parent = Type::whereNull('parent_id')
            ->where('name', $parentName)
            ->first();

        if (!$parent) {
            return response()->json([]);
        }

        return Type::where('parent_id', $parent->id)
            ->select('id', 'name', 'urdu_name')
            ->get();
    }

    public function getGroupedTypes()
    {
        // Frontend is using this endpoint to get grouped types with
        // an additional flag to clear the cache
        if (request()->has('clear_cache')) {
            Cache::forget('grouped-types');
            Log::info('Cache cleared for grouped-types');
        }
        //Cache is being cleared in Type Model on model events
        return Cache::rememberForever('grouped-types', function () {
            $types = Type::whereNull('parent_id')
                ->with('children:id,parent_id,name,urdu_name')
                ->select(['id', 'name'])->get();
            $types = $types->mapWithKeys(function ($item) {
                return [$item->name => $item->children];
            });
            $centers = Center::with(['services' => function ($q) {
                $q->select('services.id', 'name');
            }])
                ->select('id', 'name', 'district_id', 'tehsil_id')->get();

            $regions = Demography::where('type', 'REGION')
                ->with([
                    'districts:id,name,urdu_name,parent_id',
                    'districts.tehsils:id,name,urdu_name,parent_id',
                    'districts.tehsils.cities:id,name,urdu_name,parent_id',
                ])->select('id', 'name', 'urdu_name', 'parent_id', 'type')->get();
            $requiredDocuments = RequiredDocument::active()
                ->select([
                    'id',
                    'name',
                    'urdu_name',
                    'key',
                    'service_name',
                    'service_type',
                    'required_copy',
                    'reason_type_id',
                    'file_type',
                    'active',
                    'max_size_in_mb',
                    'max_size_in_bytes',
                ])->get();

            $requiredDocuments = $requiredDocuments->map(function ($item) {
                return [
                    ...$item->toArray(),
                    'application_id' => null,
                    'required_document_id' => $item->id,
                    'upload_method' => null,
                    'new_file' => null,
                    'file_path' => null,
                    'mime_type' => null,
                    'original_name' => null,
                    'ac_acr_verified' => null,
                    'ac_acr_verified_date' => null,
                    'dc_verified' => null,
                    'dc_verified_date' => null,
                ];
            });

            return [
                ...$types,
                'regions' => $regions,
                'required_documents' => $requiredDocuments,
                'centers' => $centers,

            ];
        });
    }
}
