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

        $payload = $request->json('verification');
        
        // Log the payload structure for debugging
        Log::info('Veriff webhook: Payload received', [
            'payload_structure' => $payload ? array_keys($payload) : 'null',
            'full_payload' => $request->json()->all(),
        ]);

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
                'full_payload' => $request->json()->all(),
            ]);
            return response()->json(['message' => 'Document not found.'], 404);
        }

        // Map Veriff status to our database status
        $oldStatus = $document->status;
        $newStatus = $this->mapVeriffStatus($veriffStatus);

        // Only update if status actually changed
        if ($oldStatus !== $newStatus) {
            // Use update() method for more explicit update
            $updated = $document->update(['status' => $newStatus]);
            
            // If update() returns false, try direct DB update as fallback
            if (!$updated) {
                Log::warning('Veriff webhook: Model update() returned false, trying DB::table()', [
                    'document_id' => $document->id,
                    'new_status' => $newStatus,
                ]);
                $updated = \DB::table('id_documents')
                    ->where('id', $document->id)
                    ->update(['status' => $newStatus, 'updated_at' => now()]);
            }
            
            // Refresh the document from database to ensure we have the latest data
            $document->refresh();
            
            // Verify the update was successful
            if ($document->status !== $newStatus) {
                Log::error('Veriff webhook: Status update failed after refresh', [
                    'expected' => $newStatus,
                    'actual' => $document->status,
                    'document_id' => $document->id,
                    'updated_result' => $updated,
                ]);
            } else {
                Log::info('Veriff webhook: Status updated successfully', [
                    'session_id' => $sessionId,
                    'user_id' => $document->user_id,
                    'veriff_status' => $veriffStatus,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'document_id' => $document->id,
                ]);
            }
        } else {
            Log::info('Veriff webhook: Status unchanged', [
                'session_id' => $sessionId,
                'user_id' => $document->user_id,
                'veriff_status' => $veriffStatus,
                'current_status' => $oldStatus,
                'mapped_status' => $newStatus,
            ]);
        }

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

