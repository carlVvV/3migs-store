<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImageUploadController extends Controller
{
    protected $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    
    /**
     * Upload a single image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadSingle(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'folder' => 'nullable|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $file = $request->file('image');
            $folder = $request->input('folder', '3migs-products');
            
            // Validate file using CloudinaryService
            $validation = $this->cloudinaryService->validateFile($file);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'File validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }
            
            // Upload the image
            $result = $this->cloudinaryService->uploadImage($file, $folder);
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed',
                    'error' => $result['error']
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'public_id' => $result['public_id'],
                    'url' => $result['url'],
                    'thumbnail_url' => $this->cloudinaryService->getThumbnailUrl($result['public_id']),
                    'medium_url' => $this->cloudinaryService->getMediumUrl($result['public_id']),
                    'large_url' => $this->cloudinaryService->getLargeUrl($result['public_id']),
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'format' => $result['format'],
                    'bytes' => $result['bytes']
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during upload',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload multiple images
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'folder' => 'nullable|string|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $files = $request->file('images');
            $folder = $request->input('folder', '3migs-products');
            
            // Validate all files
            $validationErrors = [];
            foreach ($files as $index => $file) {
                $validation = $this->cloudinaryService->validateFile($file);
                if (!$validation['valid']) {
                    $validationErrors["images.{$index}"] = $validation['errors'];
                }
            }
            
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File validation failed',
                    'errors' => $validationErrors
                ], 422);
            }
            
            // Upload all images
            $results = $this->cloudinaryService->uploadMultipleImages($files, $folder);
            
            $successfulUploads = [];
            $failedUploads = [];
            
            foreach ($results as $index => $result) {
                if ($result['success']) {
                    $successfulUploads[] = [
                        'index' => $index,
                        'public_id' => $result['public_id'],
                        'url' => $result['url'],
                        'thumbnail_url' => $this->cloudinaryService->getThumbnailUrl($result['public_id']),
                        'medium_url' => $this->cloudinaryService->getMediumUrl($result['public_id']),
                        'large_url' => $this->cloudinaryService->getLargeUrl($result['public_id']),
                        'width' => $result['width'],
                        'height' => $result['height'],
                        'format' => $result['format'],
                        'bytes' => $result['bytes']
                    ];
                } else {
                    $failedUploads[] = [
                        'index' => $index,
                        'error' => $result['error']
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Images processed',
                'data' => [
                    'successful_uploads' => $successfulUploads,
                    'failed_uploads' => $failedUploads,
                    'total_uploaded' => count($successfulUploads),
                    'total_failed' => count($failedUploads)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Multiple image upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during upload',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete an image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'public_id' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $publicId = $request->input('public_id');
            $result = $this->cloudinaryService->deleteImage($publicId);
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delete failed',
                    'error' => $result['error']
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
                'data' => $result['result']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image delete error', [
                'error' => $e->getMessage(),
                'public_id' => $request->input('public_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during deletion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get image transformation URLs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTransformations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'public_id' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $publicId = $request->input('public_id');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'public_id' => $publicId,
                    'thumbnail_url' => $this->cloudinaryService->getThumbnailUrl($publicId),
                    'medium_url' => $this->cloudinaryService->getMediumUrl($publicId),
                    'large_url' => $this->cloudinaryService->getLargeUrl($publicId),
                    'original_url' => $this->cloudinaryService->getTransformedUrl($publicId)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get transformations error', [
                'error' => $e->getMessage(),
                'public_id' => $request->input('public_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

