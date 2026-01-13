<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ImageProcessingService;
use Illuminate\Support\Facades\Log;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $imagePath;
    public array $options;
    public int $maxRetries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(string $imagePath, array $options = [])
    {
        $this->imagePath = $imagePath;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageProcessingService $imageService): void
    {
        try {
            // Set quality if provided
            if (isset($this->options['quality'])) {
                $imageService->setQuality($this->options['quality']);
            }

            // Resize if needed
            if (isset($this->options['resize'])) {
                $imageService->resize($this->imagePath, $this->options['resize']);
            }

            // Optimize
            if (isset($this->options['optimize']) && $this->options['optimize']) {
                $imageService->optimize($this->imagePath);
            }

            // Create thumbnail if needed
            if (isset($this->options['thumbnail']) && $this->options['thumbnail']) {
                $width = $this->options['thumbnail_width'] ?? 200;
                $height = $this->options['thumbnail_height'] ?? 200;
                $imageService->createThumbnail($this->imagePath, $width, $height);
            }

            Log::info('Image processed successfully: ' . $this->imagePath);
        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
