<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "url" => $this->url,
            "fileType" => $this->file_type,
            "mimeType" => $this->mime_type,
            "filename" => $this->filename,
            "originalFilename" => $this->original_filename,
            "extension" => $this->extension,
            "size" => $this->size,
            "formattedSize" => $this->formatted_size 
        ];
    }
}
