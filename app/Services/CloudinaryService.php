<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;
    protected $uploadApi;
    
    public function __construct()
    {
        // Check if configuration is available
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');
        
        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('Cloudinary configuration is incomplete. Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET in your .env file.');
        }
        
        // Configure Cloudinary
        Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'secure' => config('cloudinary.secure', true)
            ]
        ]);
        
        $this->cloudinary = new Cloudinary();
        $this->uploadApi = new UploadApi();
    }
    
    /**
     * Upload a single image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadImage(UploadedFile $file, string $folder = null, array $options = []): array
    {
        try {
            $folder = $folder ?? config('cloudinary.default_folder');
            
            $uploadOptions = array_merge([
                'folder' => $folder,
                'resource_type' => 'image',
                'quality' => 'auto',
                'transformation' => [
                    'width' => 1200,
                    'height' => 1200,
                    'crop' => 'limit',
                    'quality' => 'auto'
                ]
            ], $options);
            
            Log::info('Uploading image to Cloudinary', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'folder' => $folder
            ]);
            
            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);
            
            Log::info('Image uploaded successfully', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url']
            ]);
            
            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'width' => $result['width'],
                'height' => $result['height'],
                'format' => $result['format'],
                'bytes' => $result['bytes']
            ];
            
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Upload multiple images to Cloudinary
     *
     * @param array $files
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadMultipleImages(array $files, string $folder = null, array $options = []): array
    {
        $results = [];
        
        foreach ($files as $file) {
            $results[] = $this->uploadImage($file, $folder, $options);
        }
        
        return $results;
    }

    /**
     * Upload a generic file (image or document) to Cloudinary.
     *
     * @param UploadedFile $file
     * @param array $options
     * @return array
     */
    public function uploadFile(UploadedFile $file, array $options = []): array
    {
        try {
            $folder = $options['folder'] ?? config('cloudinary.default_folder', 'uploads');

            $resourceType = $options['resource_type']
                ?? (in_array(strtolower($file->getClientOriginalExtension()), ['pdf']) ? 'raw' : 'image');

            $uploadOptions = array_merge(
                [
                    'folder' => $folder,
                    'resource_type' => $resourceType,
                    'use_filename' => true,
                    'unique_filename' => true,
                ],
                $options
            );

            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'] ?? $result['url'] ?? null,
                'url' => $result['secure_url'] ?? $result['url'] ?? null,
                'bytes' => $result['bytes'] ?? null,
                'format' => $result['format'] ?? null,
                'resource_type' => $resourceType,
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary generic upload failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName(),
            ]);

            throw $e;
        }
    }
    
    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId
     * @return array
     */
    public function deleteImage(string $publicId): array
    {
        try {
            $result = $this->uploadApi->destroy($publicId);
            
            Log::info('Image deleted from Cloudinary', [
                'public_id' => $publicId,
                'result' => $result
            ]);
            
            return [
                'success' => true,
                'result' => $result
            ];
            
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed', [
                'error' => $e->getMessage(),
                'public_id' => $publicId
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate a transformed image URL
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function getTransformedUrl(string $publicId, array $transformations = []): string
    {
        return $this->cloudinary->image($publicId)->toUrl($transformations);
    }
    
    /**
     * Get thumbnail URL
     *
     * @param string $publicId
     * @return string
     */
    public function getThumbnailUrl(string $publicId): string
    {
        return $this->getTransformedUrl($publicId, config('cloudinary.transformations.thumbnail'));
    }
    
    /**
     * Get medium size URL
     *
     * @param string $publicId
     * @return string
     */
    public function getMediumUrl(string $publicId): string
    {
        return $this->getTransformedUrl($publicId, config('cloudinary.transformations.medium'));
    }
    
    /**
     * Get large size URL
     *
     * @param string $publicId
     * @return string
     */
    public function getLargeUrl(string $publicId): string
    {
        return $this->getTransformedUrl($publicId, config('cloudinary.transformations.large'));
    }
    
    /**
     * Validate file before upload
     *
     * @param UploadedFile $file
     * @return array
     */
    public function validateFile(UploadedFile $file): array
    {
        $maxSize = config('cloudinary.max_file_size');
        $allowedFormats = config('cloudinary.allowed_formats');
        
        $errors = [];
        
        // Check file size
        if ($file->getSize() > $maxSize) {
            $errors[] = "File size must be less than " . ($maxSize / 1024 / 1024) . "MB";
        }
        
        // Check file format
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedFormats)) {
            $errors[] = "File format must be one of: " . implode(', ', $allowedFormats);
        }
        
        // Check if file is actually an image
        if (!getimagesize($file->getRealPath())) {
            $errors[] = "File must be a valid image";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
