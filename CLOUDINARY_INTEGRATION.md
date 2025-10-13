# Cloudinary Image Upload Integration

## Overview
This Laravel application now includes Cloudinary integration for handling image uploads. Cloudinary provides cloud-based image and video management with automatic optimization, transformation, and delivery.

## Setup Instructions

### 1. Environment Configuration
Add the following variables to your `.env` file:

```env
# Cloudinary Configuration
CLOUDINARY_URL=cloudinary://<your_api_key>:<your_api_secret>@dc8bssolt
CLOUDINARY_CLOUD_NAME=dc8bssolt
CLOUDINARY_API_KEY=<your_api_key>
CLOUDINARY_API_SECRET=<your_api_secret>
CLOUDINARY_SECURE=true
```

**Note:** Replace `<your_api_key>` and `<your_api_secret>` with your actual Cloudinary credentials.

### 2. Configuration File
The configuration is stored in `config/cloudinary.php` with the following settings:

- **Default folder:** `3migs-products`
- **Max file size:** 10MB
- **Allowed formats:** jpg, jpeg, png, gif, webp
- **Automatic transformations:** thumbnail (300x300), medium (600x600), large (1200x1200)

## API Endpoints

### Single Image Upload
```
POST /api/v1/images/upload
Content-Type: multipart/form-data

Parameters:
- image (file, required): The image file to upload
- folder (string, optional): Folder name in Cloudinary (default: 3migs-products)
```

**Response:**
```json
{
    "success": true,
    "message": "Image uploaded successfully",
    "data": {
        "public_id": "3migs-products/sample-image",
        "url": "https://res.cloudinary.com/dc8bssolt/image/upload/v1234567890/3migs-products/sample-image.jpg",
        "thumbnail_url": "https://res.cloudinary.com/dc8bssolt/image/upload/w_300,h_300,c_fill,q_auto,f_auto/3migs-products/sample-image.jpg",
        "medium_url": "https://res.cloudinary.com/dc8bssolt/image/upload/w_600,h_600,c_fill,q_auto,f_auto/3migs-products/sample-image.jpg",
        "large_url": "https://res.cloudinary.com/dc8bssolt/image/upload/w_1200,h_1200,c_limit,q_auto,f_auto/3migs-products/sample-image.jpg",
        "width": 1920,
        "height": 1080,
        "format": "jpg",
        "bytes": 245760
    }
}
```

### Multiple Images Upload
```
POST /api/v1/images/upload-multiple
Content-Type: multipart/form-data

Parameters:
- images[] (file[], required): Array of image files to upload
- folder (string, optional): Folder name in Cloudinary
```

### Delete Image
```
DELETE /api/v1/images/delete
Content-Type: application/json

Parameters:
- public_id (string, required): The public ID of the image to delete
```

### Get Image Transformations
```
GET /api/v1/images/transformations?public_id=<public_id>
```

## Usage Examples

### JavaScript/Frontend
```javascript
// Single image upload
const formData = new FormData();
formData.append('image', fileInput.files[0]);
formData.append('folder', 'products');

const response = await fetch('/api/v1/images/upload', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: formData
});

const result = await response.json();
if (result.success) {
    console.log('Image URL:', result.data.url);
    console.log('Thumbnail URL:', result.data.thumbnail_url);
}
```

### PHP/Backend
```php
use App\Services\CloudinaryService;

$cloudinaryService = new CloudinaryService();

// Upload single image
$result = $cloudinaryService->uploadImage($uploadedFile, 'products');

if ($result['success']) {
    $imageUrl = $result['url'];
    $thumbnailUrl = $cloudinaryService->getThumbnailUrl($result['public_id']);
}

// Upload multiple images
$results = $cloudinaryService->uploadMultipleImages($uploadedFiles, 'products');

// Delete image
$deleteResult = $cloudinaryService->deleteImage('products/image-id');
```

## Testing

### Test Page
A test page is available at: `http://localhost:8000/test-cloudinary.html`

This page allows you to:
- Upload single images
- Upload multiple images
- Drag and drop images
- Preview uploaded images
- View upload results and URLs

### Features Tested
- ✅ Single image upload
- ✅ Multiple image upload
- ✅ File validation (size, format)
- ✅ Image transformations (thumbnail, medium, large)
- ✅ Error handling
- ✅ Progress feedback

## File Structure

```
app/
├── Services/
│   └── CloudinaryService.php          # Main service class
├── Http/Controllers/Api/V1/
│   └── ImageUploadController.php       # API controller
config/
└── cloudinary.php                      # Configuration file
public/
└── test-cloudinary.html                # Test page
```

## Error Handling

The service includes comprehensive error handling:

- **Configuration errors:** Missing API credentials
- **File validation:** Size limits, format restrictions
- **Upload errors:** Network issues, Cloudinary API errors
- **Delete errors:** Invalid public IDs

All errors are logged and returned in a consistent format.

## Security Considerations

1. **File validation:** Only image files are accepted
2. **Size limits:** Maximum 10MB per file
3. **CSRF protection:** All uploads require CSRF tokens
4. **Folder organization:** Images are organized in folders
5. **Access control:** Consider adding authentication middleware for production

## Next Steps

1. **Configure credentials:** Add your actual Cloudinary API credentials to `.env`
2. **Test uploads:** Use the test page to verify functionality
3. **Integrate with forms:** Update product creation forms to use Cloudinary
4. **Add authentication:** Consider restricting uploads to authenticated users
5. **Monitor usage:** Set up Cloudinary usage monitoring and alerts

## Support

For issues or questions:
- Check Cloudinary documentation: https://cloudinary.com/documentation
- Review Laravel logs: `storage/logs/laravel.log`
- Test with the provided test page
- Verify environment configuration

