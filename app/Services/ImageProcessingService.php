<?php

namespace App\Services;


use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageProcessingService
{
    private string $disk = 'public';
    private int $defaultQuality = 80;

    /**
     * Store and process image
     */
    public function storeAndProcess($file, string $path = 'products', array $options = []): string
    {
        // Generate filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store original
        $stored = Storage::disk($this->disk)->putFileAs($path, $file, $filename);

        // Process image
        if (isset($options['resize'])) {
            $this->resize(Storage::disk($this->disk)->path($stored), $options['resize']);
        }

        // Optimize
        if (isset($options['optimize']) && $options['optimize']) {
            $this->optimize(Storage::disk($this->disk)->path($stored));
        }

        return $stored;
    }

    /**
     * Resize image
     */
    public function resize(string $imagePath, array $dimensions): void
    {
        try {
            $image = Image::make($imagePath);

            // Resize maintaining aspect ratio
            if (isset($dimensions['width']) && isset($dimensions['height'])) {
                $image->fit($dimensions['width'], $dimensions['height']);
            } elseif (isset($dimensions['width'])) {
                $image->resize($dimensions['width'], null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } elseif (isset($dimensions['height'])) {
                $image->resize(null, $dimensions['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $image->save($imagePath, $this->defaultQuality);
        } catch (\Exception $e) {
            Log::error('Image resize failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize image for web
     */
    public function optimize(string $imagePath): void
    {
        try {
            $image = Image::make($imagePath);
            $image->save($imagePath, $this->defaultQuality);
        } catch (\Exception $e) {
            Log::error('Image optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Create thumbnail
     */
    public function createThumbnail(string $imagePath, int $width = 200, int $height = 200): string
    {
        try {
            $image = Image::make($imagePath);
            $image->fit($width, $height);

            $thumbnailPath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/thumbnails/' . pathinfo($imagePath, PATHINFO_FILENAME) . '_thumb.' . pathinfo($imagePath, PATHINFO_EXTENSION);

            // Create thumbnails directory if not exists
            @mkdir(dirname($thumbnailPath), 0755, true);

            $image->save($thumbnailPath, $this->defaultQuality);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed: ' . $e->getMessage());

            return $imagePath;
        }
    }

    /**
     * Crop image
     */
    public function crop(string $imagePath, int $x, int $y, int $width, int $height): void
    {
        try {
            $image = Image::make($imagePath);
            $image->crop($width, $height, $x, $y);
            $image->save($imagePath, $this->defaultQuality);
        } catch (\Exception $e) {
            Log::error('Image crop failed: ' . $e->getMessage());
        }
    }

    /**
     * Rotate image
     */
    public function rotate(string $imagePath, int $degrees): void
    {
        try {
            $image = Image::make($imagePath);
            $image->rotate($degrees);
            $image->save($imagePath, $this->defaultQuality);
        } catch (\Exception $e) {
            Log::error('Image rotation failed: ' . $e->getMessage());
        }
    }

    /**
     * Convert image format
     */
    public function convert(string $imagePath, string $format): void
    {
        try {
            $image = Image::make($imagePath);
            $image->save($imagePath, $this->defaultQuality, $format);
        } catch (\Exception $e) {
            Log::error('Image conversion failed: ' . $e->getMessage());
        }
    }

    /**
     * Add watermark to image
     */
    public function addWatermark(string $imagePath, string $watermarkPath, string $position = 'bottom-right'): void
    {
        try {
            $image = Image::make($imagePath);
            $watermark = Image::make($watermarkPath);

            $image->insert($watermark, $position, 10, 10);
            $image->save($imagePath, $this->defaultQuality);
        } catch (\Exception $e) {
            Log::error('Watermark addition failed: ' . $e->getMessage());
        }
    }

    /**
     * Get image dimensions
     */
    public function getDimensions(string $imagePath): array
    {
        try {
            $image = Image::make($imagePath);

            return [
                'width' => $image->width(),
                'height' => $image->height(),
            ];
        } catch (\Exception $e) {
            Log::error('Get dimensions failed: ' . $e->getMessage());

            return ['width' => 0, 'height' => 0];
        }
    }

    /**
     * Delete image file
     */
    public function delete(string $imagePath): bool
    {
        try {
            return Storage::disk($this->disk)->delete($imagePath);
        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Set image quality
     */
    public function setQuality(int $quality): self
    {
        $this->defaultQuality = max(1, min(100, $quality));

        return $this;
    }

    /**
     * Process multiple images
     */
    public function processMultiple(array $files, string $path = 'products'): array
    {
        $processed = [];

        foreach ($files as $file) {
            $processed[] = $this->storeAndProcess($file, $path);
        }

        return $processed;
    }
}
