<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceInstruction;
use Illuminate\Support\Facades\Auth;


class ServiceInstructionController extends Controller
{

    public function index(Request $request)
{
    $query = ServiceInstruction::with('service')
        ->orderBy('id', 'desc');

    // 🔍 Search filter
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('instruction_title', 'like', "%{$search}%")
              ->orWhere('instruction_description', 'like', "%{$search}%")
              ->orWhereHas('service', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $instructions = $query->paginate(15);

    return response()->json([
        'success' => true,
        'data' => $instructions
    ]);
}

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_title' => 'required|string|max:255',
            'service_instruction' => 'required|string',
        ]);


        $serviceInstruction = ServiceInstruction::create([
            'service_id' => $request->input('service_id'),
            'instruction_title' => $request->input('service_title'),
            'instruction_description' => $request->input('service_instruction'),
            'created_by' => Auth::id() ?? 1,
            'updated_by' => Auth::id() ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service instruction created successfully',
            'data' => $serviceInstruction
        ], 201);
    }

    public function show($id)
    {
        $instruction = ServiceInstruction::with('service')->find($id);

        if (!$instruction) {
            return response()->json([
                'success' => false,
                'message' => 'Instruction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $instruction
        ]);
    }

    // Update the instruction
    public function update(Request $request, $id)
    {
        $instruction = ServiceInstruction::find($id);

        if (!$instruction) {
            return response()->json([
                'success' => false,
                'message' => 'Instruction not found'
            ], 404);
        }

        // Manual validation without Validator
        if (!$request->service_id || !$request->service_title || !$request->service_instruction) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: service_id, service_title, service_instruction required'
            ], 422);
        }

        // Update fields
        $instruction->service_id = $request->service_id;
        $instruction->instruction_title = $request->service_title;
        $instruction->instruction_description = $request->service_instruction;
        $instruction->updated_by = Auth::id() ?? 1;
        $instruction->save();

        return response()->json([
            'success' => true,
            'message' => 'Service instruction updated successfully',
            'data' => $instruction
        ]);
    }


    public function destroy($id)
    {
        $instruction = ServiceInstruction::find($id);

        if (!$instruction) {
            return response()->json([
                'success' => false,
                'message' => 'Service Instruction not found'
            ], 404);
        }

        $instruction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service Instruction deleted successfully'
        ]);
    }
}
