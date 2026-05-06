<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Uri\Http;

class MediaController extends Controller
{
    /**
     * Media list fetch 
     */
    public function index()
    {
        return response()->json(Media::latest()->get());
    }

    /**
     * Image upload 
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:1024', // Max 1MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // File  'public/uploads' folder  save 
            $path = $file->store('uploads', 'public');
            
            // Database  entry 
            $media = Media::create([
                'name' => Http($file->getClientOriginalName(), PATHINFO_FILENAME),
                // 'alt_text' => '', 
                'url' => $path,
                'seo' =>  ' ',
            ]);

            return response()->json($media, 201);
        }

        return response()->json(['error' => 'File not found'], 400);
    }

    /**
     * SEO Tags and Name update 
     */
    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
         
        ]);

        $media->update([
            'name' => $request->name,
          
        ]);

        return response()->json($media);
    }

    /**
     */
      public function destroy($id) {
        Media::findOrFail($id)->delete();
        return response()->json(['message' => 'Moved to trash']);
    }
}