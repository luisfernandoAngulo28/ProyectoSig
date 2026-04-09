<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Vehicle;

class ProcessVehicleImages implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $vehicleId;
    protected $imageData;

    /**
     * Create a new job instance.
     */
    public function __construct($vehicleId, $imageData)
    {
        $this->vehicleId = $vehicleId;
        $this->imageData = $imageData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $vehicle = Vehicle::find($this->vehicleId);
        if (!$vehicle) {
            \Log::error("Vehicle not found: {$this->vehicleId}");
            return;
        }

        try {
            // Procesar cada imagen
            foreach ($this->imageData as $field => $filePath) {
                if (empty($filePath)) continue;

                \Log::info("Processing vehicle image: {$field} for vehicle {$this->vehicleId}");

                // Determinar la carpeta según el tipo de imagen
                $folder = $this->getImageFolder($field);
                if (!$folder) continue;

                // Subir la imagen
                $filename = \Asset::upload_image($filePath, $folder, null);
                
                if ($filename) {
                    $vehicle->{$field} = $filename;
                    \Log::info("Vehicle image uploaded successfully: {$field} = {$filename}");
                } else {
                    \Log::error("Failed to upload vehicle image: {$field}");
                }
            }

            $vehicle->save();
            \Log::info("Vehicle {$this->vehicleId} images processed successfully");

        } catch (\Throwable $th) {
            \Log::error("ProcessVehicleImages error: " . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Get the appropriate folder name for each image type
     */
    private function getImageFolder($fieldName)
    {
        $folderMap = [
            'vehicle_image' => 'driver-vehicle-vehicle_image',
            'side_image' => 'driver-vehicle-side_image',
            'rua_image' => 'driver-vehicle-ruat_image',
        ];

        return $folderMap[$fieldName] ?? null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        \Log::error("ProcessVehicleImages job failed: " . $exception->getMessage());
    }
}
