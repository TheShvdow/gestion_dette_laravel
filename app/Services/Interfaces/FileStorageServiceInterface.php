<?php

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileStorageServiceInterface
{
    public function upload(UploadedFile $file, string $folder): string;
    public function uploadBase64(string $base64Image, string $folder): string;
}
