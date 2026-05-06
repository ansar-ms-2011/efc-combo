<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // List services
    public function index(Request $request)
    {
        try {
            $services = Service::with('department');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $services->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $services = $services->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Store new service
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dept_id' => 'required|integer',
            'service_icon' => 'nullable|string|max:100',
            'no_of_days' => 'nullable|string|max:50',
            'service_description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'price' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('services', 'public');
            }

            $service = Service::create([
                'name' => $request->name,
                'dept_id' => $request->dept_id,
                'service_icon' => $request->service_icon ?? null,
                'no_of_days' => $request->no_of_days ?? null,
                'service_description' => $request->service_description ?? null,
                'file' => $filePath,
                'price' => $request->price,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Service saved successfully',
                'data' => $service
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Show single service
    public function show($id)
    {
        try {
            $service = Service::with('department')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $service]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Update service
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dept_id' => 'required|integer',
            'service_icon' => 'nullable|string|max:100',
            'no_of_days' => 'nullable|string|max:50',
            'service_description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'price' => 'required|numeric',
        ]);

        $service = Service::findOrFail($id);

        DB::beginTransaction();
        try {
            $filePath = $service->file;
            if ($request->hasFile('file')) {
                // Optionally delete old file
                if ($service->file) {
                    Storage::disk('public')->delete($service->file);
                }
                $filePath = $request->file('file')->store('services', 'public');
            }

            $service->update([
                'name' => $request->name,
                'dept_id' => $request->dept_id,
                'service_icon' => $request->service_icon ?? null,
                'no_of_days' => $request->no_of_days ?? null,
                'service_description' => $request->service_description ?? null,
                'file' => $filePath,
                'price' => $request->price,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Service updated successfully', 'data' => $service]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete service
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            if ($service->file) {
                Storage::disk('public')->delete($service->file);
            }
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}