<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class VeriffWebhookController extends Controller
{
    /**
     * Handle incoming Veriff webhook callbacks.
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = Config::get('services.veriff.secret_key');

        if (!$secret) {
            Log::error('Veriff webhook: Secret key not configured');
            return response()->json(['message' => 'Webhook secret not configured.'], 500);
        }

        $rawPayload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $rawPayload, $secret);
        $providedSignature = $request->header('X-Hmac-Signature')
            ?? $request->header('x-hmac-signature')
            ?? $request->header('X-HMAC-SIGNATURE');

        if (!$providedSignature || !hash_equals($expectedSignature, $providedSignature)) {
            Log::warning('Veriff webhook: Invalid signature', [
                'expected' => substr($expectedSignature, 0, 10) . '...',
                'provided' => $providedSignature ? substr($providedSignature, 0, 10) . '...' : 'null',
            ]);
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->json('verification');

        if (!$payload || !isset($payload['id'], $payload['status'])) {
            Log::warning('Veriff webhook: Invalid payload structure', [
                'payload_keys' => $payload ? array_keys($payload) : 'null',
            ]);
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $sessionId = $payload['id'];
        $veriffStatus = $payload['status'];

        $document = IdDocument::where('veriff_session_id', $sessionId)->first();

        if (!$document) {
            Log::warning('Veriff webhook: Document not found', [
                'session_id' => $sessionId,
                'veriff_status' => $veriffStatus,
            ]);
            return response()->json(['message' => 'Document not found.'], 404);
        }

        // Map Veriff status to our database status
        $oldStatus = $document->status;
        $newStatus = $this->mapVeriffStatus($veriffStatus);

        $document->status = $newStatus;
        $document->save();

        Log::info('Veriff webhook: Status updated', [
            'session_id' => $sessionId,
            'user_id' => $document->user_id,
            'veriff_status' => $veriffStatus,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        return response()->json(['message' => 'Webhook processed.'], 200);
    }

    /**
     * Map Veriff status values to our internal status values.
     * 
     * Veriff statuses: approved, declined, resubmission_requested, expired, abandoned, pending
     * Our statuses: approved, rejected, pending
     */
    private function mapVeriffStatus(string $veriffStatus): string
    {
        $status = strtolower(trim($veriffStatus));

        switch ($status) {
            case 'approved':
                return 'approved';
            
            case 'declined':
            case 'rejected':
                return 'rejected';
            
            case 'resubmission_requested':
            case 'expired':
            case 'abandoned':
            case 'pending':
            case 'created':
            case 'submitted':
            default:
                return 'pending';
        }
    }
}

