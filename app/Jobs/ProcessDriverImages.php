<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Driver;

class ProcessDriverImages implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $driverId;
    protected $imageData;

    /**
     * Create a new job instance.
     */
    public function __construct($driverId, $imageData)
    {
        $this->driverId = $driverId;
        $this->imageData = $imageData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $driver = Driver::find($this->driverId);
        if (!$driver) {
            \Log::error("Driver not found: {$this->driverId}");
            return;
        }

        try {
            // Procesar cada imagen
            foreach ($this->imageData as $field => $filePath) {
                if (empty($filePath)) continue;

                \Log::info("Processing image: {$field} for driver {$this->driverId}");

                // Determinar la carpeta según el tipo de imagen
                $folder = $this->getImageFolder($field);
                if (!$folder) continue;

                // Subir la imagen
                $filename = \Asset::upload_image($filePath, $folder, null);
                
                if ($filename) {
                    $driver->{$field} = $filename;
                    \Log::info("Image uploaded successfully: {$field} = {$filename}");
                } else {
                    \Log::error("Failed to upload image: {$field}");
                }
            }

            $driver->save();
            \Log::info("Driver {$this->driverId} images processed successfully");

        } catch (\Throwable $th) {
            \Log::error("ProcessDriverImages error: " . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Get the appropriate folder name for each image type
     */
    private function getImageFolder($fieldName)
    {
        $folderMap = [
            'image' => 'driver-image',
            'license_front_image' => 'driver-license_front_image',
            'license_back_image' => 'driver-license_back_image',
            'ci_front_image' => 'driver-ci_front_image',
            'ci_back_image' => 'driver-ci_back_image',
            'ci_front_image_titular' => 'driver-ci_front_image_titular',
            'ci_back_image_titular' => 'driver-ci_back_image_titular',
            'tic_file' => 'driver-tic_file',
        ];

        return $folderMap[$fieldName] ?? null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        \Log::error("ProcessDriverImages job failed: " . $exception->getMessage());
    }
}
