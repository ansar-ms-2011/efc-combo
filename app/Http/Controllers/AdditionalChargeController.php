<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdditionalCharge;

class AdditionalChargeController extends Controller
{
    public function index()
    {
        $additionalCharges = AdditionalCharge::with('primaryUser', 'temporaryUser')->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $additionalCharges
        ]);
    }

    public function store(Request $request)
    {
        // store notice image
        $noticeImagePath = null;
        if ($request->hasFile('notice_image')) {
            $noticeImage = $request->file('notice_image');
            $noticeImageName = time() . '_' . $noticeImage->getClientOriginalName();
            $noticeImagePath = $noticeImage->storeAs('additional_charges/notice_images', $noticeImageName, 'public');
        }

        // return response()->json([
        //     'success' => true,
        //     'data' => $noticeImagePath
        // ]);


        $additionalCharge = AdditionalCharge::create([
            'primary_user_id' => $request->primary_user_id,
            'temporary_user_id' => $request->temporary_user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notice_number' => $request->notice_number,
            'notice_image' => $noticeImagePath,
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $additionalCharge
        ]);
    }

    public function show($id)
{
    $additionalCharge = AdditionalCharge::findOrFail($id);
    return response()->json([
        'success' => true,
        'data' => $additionalCharge
    ]);
}


   public function update(Request $request, $id)
{
    $additionalCharge = AdditionalCharge::findOrFail($id);

    if ($request->hasFile('notice_image')) {
        $file = $request->file('notice_image');
        $path = $file->store('additional_charges/notice_images', 'public');
        $additionalCharge->notice_image = $path;
    }

    $additionalCharge->update([
        'primary_user_id' => $request->primary_user_id,
        'temporary_user_id' => $request->temporary_user_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'notice_number' => $request->notice_number,
        'description' => $request->description,
    ]);

    return response()->json(['success' => true]);
}


    public function destroy($id)
    {
        $additionalCharge = AdditionalCharge::find($id);
        $additionalCharge->delete();
        return response()->json([
            'success' => true,
            'data' => $additionalCharge
        ]);
    }

    public function getCharges(){
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Super Admin')
            ->orWhere('name', 'DEO');
        })->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
