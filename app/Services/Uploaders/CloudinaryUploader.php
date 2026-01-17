<?php

namespace App\Services\Uploaders;

use Illuminate\Http\UploadedFile;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryUploader implements FileUploaderInterface
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): array
    {
        $uploaded = Cloudinary::uploadFile(
            $file->getRealPath(),
            ['folder' => $folder]
        );

        return [
            'disk'      => 'cloudinary',
            'path'      => $uploaded->getPublicId(),
            'url'       => $uploaded->getSecurePath(),
            'size'      => $file->getSize(),
            'mime'      => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'original'  => $file->getClientOriginalName(),
            'filename'  => $uploaded->getFileName()
        ];
    }

    public function delete(string $path): bool
    {
        Cloudinary::destroy($path);
        return true;
    }
}
