<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class VeriffWebhookController extends Controller
{
    /**
     * Handle incoming Veriff webhook callbacks.
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = Config::get('services.veriff.secret_key');

        if (!$secret) {
            return response()->json(['message' => 'Webhook secret not configured.'], 500);
        }

        $rawPayload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $rawPayload, $secret);
        $providedSignature = $request->header('X-Hmac-Signature')
            ?? $request->header('x-hmac-signature')
            ?? $request->header('X-HMAC-SIGNATURE');

        if (!$providedSignature || !hash_equals($expectedSignature, $providedSignature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->json('verification');

        if (!$payload || !isset($payload['id'], $payload['status'])) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $document = IdDocument::where('veriff_session_id', $payload['id'])->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        $status = $payload['status'];

        switch ($status) {
            case 'approved':
            case 'resubmission_requested':
            case 'declined':
            case 'expired':
                $document->status = $status === 'approved' ? 'approved' : ($status === 'declined' ? 'rejected' : 'pending');
                break;
            default:
                $document->status = 'pending';
                break;
        }

        $document->save();

        return response()->json(['message' => 'Webhook processed.'], 200);
    }
}

