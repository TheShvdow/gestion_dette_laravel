<?php

namespace App\Services;

use App\Services\Interfaces\FileStorageServiceInterface;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryFileStorageService implements FileStorageServiceInterface
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        return $uploadedFile->getSecurePath(); // Return the secure URL of the uploaded file
    }

    public function uploadBase64(string $base64Image, string $folder): string
    {
        
        // Sauvegarde en Base64 (par exemple dans une base de données)
        return $base64Image; // Simplement retourner la chaîne Base64
    }

}
