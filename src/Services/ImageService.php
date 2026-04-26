<?php
namespace App\Services;


class ImageService {
    private string $uploadDir;
    private int $maxFileSize = 5242880; // 5MB

    public function __construct() {
        // Absolute path to the new uploads directory
        $this->uploadDir = __DIR__ . '/../../public/uploads/products/';
        
        // Ensure directory exists with correct permissions
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0775, true);
        }
    }

    /**
     * Handles the upload, validation, and resizing of a product image.
     * Returns the base generated filename on success, or throws an Exception on failure.
     */
    public function handleUpload(array $file): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("File upload error code: " . $file['error']);
        }

        if ($file['size'] > $this->maxFileSize) {
            throw new \Exception("File is too large. Maximum size is 5MB.");
        }

        // Strictly validate MIME type using getimagesize (universally supported via GD library)
        // This prevents malicious uploads disguised as images and avoids the missing 'finfo' extension error.
        $imageInfo = getimagesize($file['tmp_name']);
        
        if ($imageInfo === false) {
            throw new \Exception("The uploaded file is not a valid image.");
        }
        
        $mime = $imageInfo['mime'];
        
        $allowedMimes =[
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowedMimes)) {
            throw new \Exception("Invalid file type. Only JPG, PNG, and WebP are allowed.");
        }

        $extension = $allowedMimes[$mime];
        $baseFilename = uniqid('prod_', true) . '.' . $extension;

        // Create smaller version for product cards (e.g., max 300x300)
        $this->resizeAndSave($file['tmp_name'], $this->uploadDir . 'thumb_' . $baseFilename, 300, 300, $mime);
        
        // Create larger version for product detail page (e.g., max 800x800)
        $this->resizeAndSave($file['tmp_name'], $this->uploadDir . 'large_' . $baseFilename, 800, 800, $mime);

        return $baseFilename;
    }

    /**
     * Deletes existing images when a product is deleted or updated.
     */
    public function deleteImages(string $filename): void {
        $thumbPath = $this->uploadDir . 'thumb_' . $filename;
        $largePath = $this->uploadDir . 'large_' . $filename;

        if (file_exists($thumbPath)) unlink($thumbPath);
        if (file_exists($largePath)) unlink($largePath);
    }

    /**
     * Resizes an image preserving aspect ratio and transparency.
     */
    private function resizeAndSave(string $sourcePath, string $destPath, int $maxWidth, int $maxHeight, string $mime): void {
        list($origWidth, $origHeight) = getimagesize($sourcePath);

        // Calculate aspect ratio
        $ratio = $origWidth / $origHeight;
        if ($maxWidth / $maxHeight > $ratio) {
            $maxWidth = $maxHeight * $ratio;
        } else {
            $maxHeight = $maxWidth / $ratio;
        }

        $newWidth = (int)round($maxWidth);
        $newHeight = (int)round($maxHeight);

        $imageCreateFunc = match($mime) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png'  => 'imagecreatefrompng',
            'image/webp' => 'imagecreatefromwebp',
        };

        $imageSaveFunc = match($mime) {
            'image/jpeg' => 'imagejpeg',
            'image/png'  => 'imagepng',
            'image/webp' => 'imagewebp',
        };

        $sourceImage = $imageCreateFunc($sourcePath);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle transparency for PNG and WebP
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Save with excellent quality (90 for JPG/WebP, 9 for PNG compression)
        if ($mime === 'image/png') {
            $imageSaveFunc($newImage, $destPath, 9);
        } else {
            $imageSaveFunc($newImage, $destPath, 90);
        }

        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }
}