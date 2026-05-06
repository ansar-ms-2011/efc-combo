<?php

namespace App\Http\Controllers;

use App\Models\Uc;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UcController extends Controller
{
    // list

public function index(){

        try{
            $uc = Uc::paginate(10);
            return response()->json([
                'success' => true,
                'data' => $uc
            ]);

        }catch(\Exception $e){

            Log::error('Error fetching uc:'.$e->getMessage());
            return response()->json([

                'success'=> false,
                'message' => 'Failed to fetch uc',
                'error' => $e->getMessage()
            ],500);
        }
}


// show

public function show($id){

        try{
            $uc = Uc::find($id);
             if (!$uc) {
                return response()->json(['success' => false, 'message' => 'Uc not found'], 404);
            }

             return response()->json(['success' => true, 'data' => $uc]);
        }catch(\Exception $e){
            Log::error('Error fetching uc:'.$e->getMessage());
             return response()->json([
                'success' => false,
                'message' => 'Failed to fetch uc',
                'error' => $e->getMessage()
            ], 500);

        }
}

// Create
    public function store(Request $request){

     DB::beginTransaction();        //start transaction

     try{
        $request->validate([

        'city_name'  => 'required|string|max:255',
        'uc_code'    => 'nullable|string|max:50',
        'uc_name'   => 'required|string|max:255',
        ]);

        $uc = Uc::created([
             'city_name'  => $request->city_name,
              'uc_code'   => $request->uc_code,
              'uc_name'  => $request->uc_name,
              'created_by'       => Auth::id(),
        ]);
         DB::commit();             //save changes
          return response()->json([
                'success' => true,
                'data' => $uc,
                'message' => 'Uc created successfully'
            ]);


     }catch(\Exception $e){
         DB::rollBack();                            // UNDO changes

            Log::error('Error creating uc: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tehsil',
                'error' => $e->getMessage()
            ], 500);

     }
    }


}
