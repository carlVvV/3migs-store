<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VeriffWebhookController extends Controller
{
    /**
     * Handle incoming Veriff webhook callbacks.
     */
    public function handle(Request $request): JsonResponse
    {
        // Log that webhook was received
        Log::info('Veriff webhook: Received', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'has_json' => $request->has('verification'),
        ]);

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
                'raw_payload_length' => strlen($rawPayload),
                'payload_preview' => substr($rawPayload, 0, 200),
            ]);
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        // Get the full payload
        $payload = $request->json();
        
        // Get the Veriff status and session ID from the webhook
        $verification = $payload->get('verification');
        $veriffStatus = $verification && isset($verification['status']) ? $verification['status'] : null;
        $sessionId = $verification && isset($verification['id']) ? $verification['id'] : null;

        if (!$veriffStatus || !$sessionId) {
            Log::warning('Veriff webhook: Invalid webhook payload', [
                'payload' => $payload->all(),
            ]);
            return response()->json(['error' => 'Invalid webhook payload'], 400);
        }

        Log::info('Veriff webhook: Processing', [
            'session_id' => $sessionId,
            'veriff_status' => $veriffStatus,
        ]);

        // Find the document
        $document = IdDocument::where('veriff_session_id', $sessionId)->first();

        if ($document) {
            // Map Veriff status to our internal status
            $oldStatus = $document->status;
            $newStatus = $this->mapVeriffStatus($veriffStatus);
            
            // Update the status in our database
            $document->status = $newStatus;
            $saved = $document->save();
            
            // If save() fails, try direct DB update
            if (!$saved) {
                Log::warning('Veriff webhook: save() returned false, trying DB::table()', [
                    'document_id' => $document->id,
                    'new_status' => $newStatus,
                ]);
                DB::table('id_documents')
                    ->where('id', $document->id)
                    ->update(['status' => $newStatus, 'updated_at' => now()]);
                $document->refresh();
            }
            
            Log::info('Veriff webhook: Status updated', [
                'session_id' => $sessionId,
                'document_id' => $document->id,
                'veriff_status' => $veriffStatus,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'saved' => $saved,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Status updated'], 200);
        }

        // If we get a webhook for a session we don't have
        Log::warning('Veriff webhook: Document not found for session', [
            'session_id' => $sessionId,
        ]);
        return response()->json(['error' => 'Document not found for session'], 404);
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

