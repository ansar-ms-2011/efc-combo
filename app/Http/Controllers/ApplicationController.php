<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForwardApplicationRequest;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationBiometric;
use App\Models\ApplicationCertificate;
use App\Models\ApplicationDocument;
use App\Models\WorkflowTransition;
use App\Services\ApplicationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ApplicationService $service
    )
    {
    }

    public function index(Request $request, $status = 'all')
    {
        $applications = $this->service->index($request, $status);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function store(StoreApplicationRequest $request)
    {
        try {
            $data = $request->all();
            $app = $this->service->store($data);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_id' => $app->id,
                    'token' => $app->token,
                    'certificate_type' => $app->certificate_type,
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save application: ' . $e->getMessage(),
                'error' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $data = $this->service->show($id);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching application data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateApplicationRequest $request, Application $application)
    {
        try {
            $app = $this->service->update($request->all(), $application);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully',
                'data' => [
                    'application_id' => $app->id,
                    'token' => $app->token,
                    'certificate_type' => $app->certificate_type,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage(),
                'error' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully'
        ]);
    }

    // forward application
    public function forwardApplication(ForwardApplicationRequest $request)
    {
        try {
            $this->service->forward($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getApplicationCurrentStatus($id)
    {
        try {
            $application = Application::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $application->current_status
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get application current status: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function applicationHistory($id)
    {
        try {

            $history = WorkflowTransition::where('application_id', $id)
                ->with('createdBy')
                ->orderByDesc('id')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteFingerprint(string $id, string $fingerType)
    {
        try {
            $biometric = ApplicationBiometric::where('application_id', $id)
                ->where('finger_type', $fingerType)
                ->first();


            // public function forwardApplication(Request $request)
            // {
            //     try {
            //         $application = Application::findOrFail($request->application_id);
            //         $currentStatus = $application->current_status ?? 'pending';
            //         $action = $request->action ?? 'forward';

            if (!$biometric) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fingerprint not found'
                ], 404);
            }

            if ($biometric->image_path) {
                Storage::disk('public')->delete($biometric->image_path);
            }

            $biometric->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fingerprint: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkCnic(string $cnic)
    {
        try {
            $applications = Application::where('cnic', $cnic)
                ->orWhere('refugee_number', $cnic)
                ->get();

            if ($applications->isEmpty()) {
                return response()->json([
                    'exists' => false,
                    'applications' => []
                ]);
            }

            $applicationsData = $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'certificate_type' => $app->certificate_type,
                    'cnic' => $app->cnic,
                    'refugee_number' => $app->refugee_number
                ];
            });

            return response()->json([
                'exists' => true,
                'applications' => $applicationsData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check CNIC: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateQrCode(string $id)
    {
        try {
            $application = Application::find($id);

            if (!$application || !$application->uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or UUID missing'
                ], 404);
            }

            $qrCode = QrCode::format('svg')
                ->size(200)
                ->generate(url(config('app.qr_code_base_url') . '/application/' . $application->uuid));

            return response($qrCode)
                ->header('Content-Type', 'image/svg+xml');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function workflowHistory(string $id)
    {
        return response()->json([
            'workflowHistory' => $this->service->getWorkFlowHistory($id)
        ]);
    }

    private function saveQrCode($application)
    {
        try {
            if (!$application->uuid) {
                Log::warning('No UUID found for application: ' . $application->id);
                return;
            }

            $qrCode = base64_encode(QrCode::format('svg')->size(200)->generate($application->uuid));
            $qrImage = "data:image/svg+xml;base64,{$qrCode}";

            // Create PNG from SVG using GD
            $img = imagecreatetruecolor(200, 200);
            $white = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $white);

            // For now, save the SVG as base64 in database
            $fileName = 'qr_' . $application->id . '_' . time() . '.txt';
            $filePath = 'applications/qrcodes/' . $fileName;

            Storage::disk('public')->put($filePath, $application->uuid);

            $application->qr_code_path = $filePath;
            $application->saveQuietly();

            Log::info('QR code data saved', ['path' => $filePath]);
        } catch (Exception $e) {
            Log::error('Failed to save QR code: ' . $e->getMessage());
        }
    }

    public function viewDocument(string $id)
    {
        try {
            $documents = ApplicationDocument::where('application_id', $id)->get();

            $formattedDocuments = $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->file_name,
                    'file_path' => Storage::url($doc->file_path),
                    'document_type' => $doc->document_type,
                    'display_name' => $doc->display_name,
                    'is_verified' => (bool)$doc->is_verified,
                    'is_scanned' => (bool)$doc->is_scanned,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedDocuments,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyDocument(Request $request, string $id)
    {
        try {
            $document = ApplicationDocument::findOrFail($id);
            $user = auth()->user();
            $userRole = $user->roles->first()->name ?? null;

            // Update role-specific verification
            if ($userRole === 'AC') {
                $document->verified_by_ac = $request->is_verified;
            } elseif ($userRole === 'ACR') {
                $document->verified_by_acr = $request->is_verified;
            } elseif ($userRole === 'DC') {
                $document->verified_by_dc = $request->is_verified;
            }

            // Update general verification if all roles have verified
            $document->is_verified = $document->verified_by_ac && $document->verified_by_acr && $document->verified_by_dc;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Document verification status updated',
                'data' => [
                    'verified_by_ac' => (bool)$document->verified_by_ac,
                    'verified_by_acr' => (bool)$document->verified_by_acr,
                    'verified_by_dc' => (bool)$document->verified_by_dc,
                    'is_verified' => (bool)$document->is_verified,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update verification status: ' . $e->getMessage()
            ], 500);
        }
    }
}
