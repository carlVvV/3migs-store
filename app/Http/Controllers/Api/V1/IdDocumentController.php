<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use App\Services\CloudinaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

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
     * Upload a new ID document for the authenticated user.
     */
    public function store(Request $request, CloudinaryService $cloudinary): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'string', 'max:255'],
            'id_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            $uploadResult = $cloudinary->uploadFile(
                $validated['id_file'],
                [
                    'folder' => 'id_documents/' . $user->id,
                    'resource_type' => $validated['id_file']->getClientOriginalExtension() === 'pdf' ? 'raw' : 'image',
                ]
            );

            $document = IdDocument::create([
                'user_id' => $user->id,
                'type' => $validated['type'] ?? null,
                'file_path' => $uploadResult['secure_url'] ?? $uploadResult['url'],
                'file_public_id' => $uploadResult['public_id'],
                'status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ID document uploaded successfully.',
                'data' => $document,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload ID document.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

