<?php

namespace App\Actions\Application;

use App\Models\ApplicationDocument;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadDocuments
{
    public function execute(array $base64Documents, int $applicationId, string $appYearMonthDay): void
    {
        DB::transaction(function () use ($base64Documents, $applicationId, $appYearMonthDay) {
            // Get existing document IDs for this application
            $existingDocIds = ApplicationDocument::where('application_id', $applicationId)
                ->pluck('required_document_id')
                ->toArray();

            $newDocIds = [];
            $removedFromFrontend = [];

            // Process new/updated documents
            foreach ($base64Documents as $doc) {
                if (!empty($doc['required_document_id'])) {
                    $newDocIds[] = $doc['required_document_id'];
                }

                if(!empty($doc['app_doc_id']) && $doc['removed_from_frontend'] === true){
                    $removedFromFrontend[] = $doc['app_doc_id'];
                }

                if (empty($doc['new_file'])) {
                    continue;
                }

                if (!$doc['new_file']) {
                    throw new Exception('Invalid base64 image');
                }

                $imageData = extractImageData($doc['new_file']);
                $path = "applications/{$appYearMonthDay}/documents";

                // Generate unique filename
                $extension = $imageData['extension'];
                $filename = Str::uuid() . '.' . $extension;
                $fullPath = $path . '/' . $filename;

                // Store the file
                Storage::disk('public')->put($fullPath, $imageData['data']);

                // Delete old file if it exists for this document
                $oldDocument = ApplicationDocument::where('application_id', $applicationId)
                    ->where('required_document_id', $doc['required_document_id'])
                    ->first();

                if ($oldDocument && $oldDocument->file_path) {
                    // Delete old file from storage
                    if (Storage::disk('public')->exists($oldDocument->file_path)) {
                        Storage::disk('public')->delete($oldDocument->file_path);
                    }
                }

                // Create or update
                ApplicationDocument::updateOrCreate([
                    'application_id' => $applicationId,
                    'required_document_id' => $doc['required_document_id'],
                ], [
                    'application_id' => $applicationId,
                    'required_document_id' => $doc['required_document_id'],
                    'upload_method' => $doc['upload_method'] ?? null,
                    'file_path' => $fullPath,
                    'mime_type' => $imageData['mime_type'],
                    'original_name' => $doc['original_name'] ?? null,
                ]);
            }

            // Delete documents that are no longer needed (removed from the request)
            $removedDocIds = array_diff($existingDocIds, $newDocIds);

            if (!empty($removedDocIds)) {
                // Get the file paths of documents to delete
                $documentsToDelete = ApplicationDocument::where('application_id', $applicationId)
                    ->whereIn('required_document_id', $removedDocIds)
                    ->get();

                foreach ($documentsToDelete as $document) {
                    // Delete physical file
                    if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                        Storage::disk('public')->delete($document->file_path);
                    }
                    // Delete database record
                    $document->forceDelete();
                }
            }
            // Delete documents that were removed from the frontend
            foreach ($removedFromFrontend as $docId) {
                $document = ApplicationDocument::find($docId);
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                // Delete database record
                $document->forceDelete();
            }
        });
    }
}
