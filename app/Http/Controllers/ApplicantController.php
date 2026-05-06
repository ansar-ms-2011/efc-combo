<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    //
    public function getApplicantDetails(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required'
        ]);
        $applicant = Applicant::where('identity_number', $request->id)
            ->where('identity_type', $request->type)->first();
        $applicant?->load([
            'certificates',
            'children',
            'refugeeDetails'
        ]);
        return response()->json([
            'applicant' => $applicant
        ]);

    }
}
