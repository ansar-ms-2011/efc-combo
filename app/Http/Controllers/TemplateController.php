<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    // GET /api/templates
    public function index()
    {
        try {
            $templates = Template::latest()->get();

            return response()->json([
                'status' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch templates'
            ], 500);
        }
    }

    // POST /api/templates
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $template = Template::create([
                'name' => $request->name,
                'content' => $request->content,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Template created successfully',
                'data' => $template
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create template'
            ], 500);
        }
    }

    // GET /api/templates/{id}
    public function show($id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $template
        ]);
    }

    // PUT /api/templates/{id}
    public function update(Request $request, $id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update([
            'name' => $request->name,
            'content' => $request->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Template updated successfully',
            'data' => $template
        ]);
    }

    // DELETE /api/templates/{id}
    public function destroy($id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json([
                'status' => false,
                'message' => 'Template not found'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'status' => true,
            'message' => 'Template deleted successfully'
        ]);
    }
}