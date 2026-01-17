<?php

namespace App\Services\Uploaders;

use Illuminate\Http\UploadedFile;

interface FileUploaderInterface
{
    public function upload(UploadedFile $file, string $folder = ''): array;
    public function delete(string $path): bool;
}
