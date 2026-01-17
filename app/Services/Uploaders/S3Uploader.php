<?php

namespace App\Services\Uploaders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class S3Uploader implements FileUploaderInterface
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): array
    {
        $disk = 's3';
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        $path = $storage->put($folder, $file);

        return [
            'disk'      => $disk,
            'path'      => $path,
            'url'       => $storage->url($path),
            'size'      => $file->getSize(),
            'mime'      => $file->getClientMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'original'  => $file->getClientOriginalName(),
            'filename'  => basename($path)
        ];
    }

    public function delete(string $path): bool
    {
        return Storage::disk('s3')->delete($path);
    }
}
