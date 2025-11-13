<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
}

