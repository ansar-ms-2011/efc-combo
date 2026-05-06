<?php

namespace App\Http\Controllers;

use App\Models\Tehsil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class TehsilController extends Controller
{
    // List
    public function index()
    {
        try {
            $tehsils = Tehsil::paginate(10); // paginate for frontend

            return response()->json([
                'success' => true,
                'data' => $tehsils
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tehsils: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tehsils',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show
    public function show($id)
    {
        try {
            $tehsil = Tehsil::find($id);

            if (!$tehsil) {
                return response()->json(['success' => false, 'message' => 'Tehsil not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $tehsil]);
        } catch (\Exception $e) {
            Log::error('Error fetching tehsil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tehsil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Create
    public function store(Request $request)
    {
             DB::beginTransaction();        //start transaction

        try {
            $request->validate([
                'district_name'    => 'required|string|max:255',
                'tehsil_code'      => 'nullable|string|max:50',
                'tehsil_name'      => 'required|string|max:255',
                'tehsil_name_urdu' => 'nullable|string|max:255',
            ]);

            $tehsil = Tehsil::create([
                'district_name'    => $request->district_name,
                'tehsil_code'      => $request->tehsil_code,
                'tehsil_name'      => $request->tehsil_name,
                'tehsil_name_urdu' => $request->tehsil_name_urdu,
                'created_by'       => Auth::id(),
            ]);

              DB::commit();             //save changes

            return response()->json([
                'success' => true,
                'data' => $tehsil,
                'message' => 'Tehsil created successfully'
            ]);
        } catch (\Exception $e) {

         DB::rollBack();                            // UNDO changes

            Log::error('Error creating tehsil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tehsil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update
    public function update(Request $request, $id)
    {
        try {
            $tehsil = Tehsil::find($id);

            if (!$tehsil) {
                return response()->json(['success' => false, 'message' => 'Tehsil not found'], 404);
            }

            $request->validate([
                'district_name'    => 'required|string|max:255',
                'tehsil_code'      => 'nullable|string|max:50',
                'tehsil_name'      => 'required|string|max:255',
            ]);

            $tehsil->update([
                'district_name'    => $request->district_name,
                'tehsil_code'      => $request->tehsil_code,
                'tehsil_name'      => $request->tehsil_name,
                'tehsil_name_urdu' => $request->tehsil_name_urdu,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tehsil,
                'message' => 'Tehsil updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating tehsil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tehsil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete
    public function destroy($id)
    {
        try {
            $tehsil = Tehsil::find($id);

            if (!$tehsil) {
                return response()->json(['success' => false, 'message' => 'Tehsil not found'], 404);
            }

            $tehsil->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tehsil deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting tehsil: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tehsil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
