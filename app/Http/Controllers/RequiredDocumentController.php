<?php

namespace App\Http\Controllers;

use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequiredDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = RequiredDocument::query();

        // Optional search filter
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginate results (15 per page)
        $documents = $query->with('reasonType')->paginate(15);

        return response()->json([
            'data' => $documents,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9 \/ ]+$/'],
            'urdu_name' => 'nullable|string|max:255',
            'service_name' => 'required|string|in:domicile,state,both',
            'service_type' => 'required|string|in:new,duplicate',
            'required_copy' => 'required|in:original,photocopy,scanned',
             'file_type' => 'required|in:pdf,image,both',
            'reason_type_id' => 'nullable|required_if:service_type,duplicate|exists:types,id',
            'active' => 'boolean',
        ]);

        // Generate key from name
        $baseKey = Str::of($request->name)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->value();

        // Ensure unique key
        $key = $baseKey;
        $count = 1;

        while (RequiredDocument::where('key', $key)->exists()) {
            $key = $baseKey . '_' . $count;
            $count++;
        }

        $validated['key'] = $key;

        // Store record
        $document = RequiredDocument::create($validated);

        return response()->json([
            'message' => 'Created successfully',
            'data' => $document
        ], 201);
    }

    public function destroy($id)
    {
        $document = RequiredDocument::findOrFail($id);
        $document->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function show($id)
    {
        return RequiredDocument::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $document = RequiredDocument::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9 \/ ]+$/'],
            'urdu_name' => 'nullable|string|max:255',
            'service_name' => 'required|string|in:domicile,state,both',
            'service_type' => 'required|string|in:new,duplicate',
            'required_copy' => 'required|in:original,photocopy,scanned',
            'file_type' => 'required|in:pdf,image,both',     
            'reason_type_id' => 'nullable|required_if:service_type,duplicate|exists:types,id', // 2. Added nullable to fix error
            'active' => 'boolean',
        ]);

        $document->update($validated);
        if($document->service_type == 'new'){
            $document->update([
                'reason_type_id' => null
            ]);
        }

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $document
        ]);
    }
}

