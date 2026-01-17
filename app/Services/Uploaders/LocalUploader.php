<?php

namespace App\Services\Uploaders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LocalUploader implements FileUploaderInterface
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): array
    {
        $disk = 'public';

        $path = Storage::disk($disk)->put($folder, $file);

        return [
            'disk'      => $disk,
            'path'      => $path,
            'url'       => asset('storage/' . $path),
            'size'      => $file->getSize(),
            'mime'      => $file->getClientMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'original'  => $file->getClientOriginalName(),
            'filename'  => basename($path)
        ];
    }

    public function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
}
