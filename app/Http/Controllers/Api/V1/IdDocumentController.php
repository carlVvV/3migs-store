<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdDocumentController extends Controller
{
    /**
     * List the authenticated user's ID documents.
     */
    public function index(Request $request): JsonResponse
    {
        $documents = $request->user()->idDocuments()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Delete an ID document (only if it's not approved).
     * This is used when a user cancels or abandons verification.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $document = IdDocument::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.',
            ], 404);
        }

        // Only allow deletion if status is pending or rejected
        // Don't delete approved documents
        if ($document->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an approved document.',
            ], 403);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }
}

