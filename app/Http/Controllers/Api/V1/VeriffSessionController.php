<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\IdDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VeriffSessionController extends Controller
{
    /**
     * Create a new Veriff verification session for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $apiKey = Config::get('services.veriff.api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Veriff configuration is missing. Please contact support.',
            ], 500);
        }

        $names = $this->extractNames($user->name ?? '');

        $payload = [
            'verification' => [
                'callback' => url('/veriff-webhook'),
                'person' => [
                    'firstName' => $names['first_name'],
                    'lastName' => $names['last_name'],
                ],
                'document' => [
                    'type' => 'idcard',
                ],
                'vendorData' => (string) $user->id,
                'timestamp' => now()->toIso8601String(),
            ],
        ];

        $response = Http::withHeaders([
            'X-AUTH-CLIENT' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.veriff.me/v1/sessions', $payload);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to create verification session. Please try again later.',
                'error' => $response->json(),
            ], $response->status());
        }

        $data = $response->json('verification');

        if (!$data || !isset($data['url'], $data['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected response from verification provider.',
            ], 500);
        }

        $document = IdDocument::create([
            'user_id' => $user->id,
            'type' => 'veriff',
            'veriff_session_id' => $data['id'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'session_url' => $data['url'],
                'session_id' => $data['id'],
                'document_id' => $document->id,
            ],
        ]);
    }

    /**
     * Extract first and last names from a full name string.
     */
    protected function extractNames(string $fullName): array
    {
        if (trim($fullName) === '') {
            return [
                'first_name' => 'Customer',
                'last_name' => 'Unknown',
            ];
        }

        $parts = preg_split('/\s+/', trim($fullName));

        $first = array_shift($parts) ?: 'Customer';
        $last = !empty($parts) ? implode(' ', $parts) : 'User';

        return [
            'first_name' => Str::limit($first, 50, ''),
            'last_name' => Str::limit($last, 50, ''),
        ];
    }
}

