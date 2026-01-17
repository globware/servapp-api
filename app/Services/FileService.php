<?php

namespace App\Services;

use App\Models\File;
use App\Services\Uploaders\FileUploaderInterface;

use App\Enums\FileType;

class FileService
{
    private FileUploaderInterface $uploader;
    private ?int $belongsId = null;
    private ?string $belongsType = null;

    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function setBelongs(int $id, string $type): static
    {
        $this->belongsId = $id;
        $this->belongsType = $type;
        return $this;
    }

    private function getFileType($mime)
    {
        if(str_contains(strtolower($mime), 'image')) {
            return FileType::IMAGE->value;
        }elseif(str_contains(strtolower($mime), 'video')){
            return FileType::VIDEO->value;
        }else{
            return FileType::UNKNOWN->value;
        }
    }

    public function save($file, string $folder = 'uploads')
    {
        $upload = $this->uploader->upload($file, $folder);

        $fileModel = new File();
        $fileModel->filename         = $upload['filename'];
        $fileModel->url              = $upload['url'];
        $fileModel->disk             = $upload['disk'];
        $fileModel->path             = $upload['path'];
        $fileModel->size             = $upload['size'];
        $fileModel->formatted_size   = $this->formatSize($upload['size']);
        $fileModel->mime_type        = $upload['mime'];
        $fileModel->file_type = $this->getFileType($upload['mime']);
        $fileModel->original_filename = $upload['original'];
        $fileModel->extension        = $upload['extension'];

        if ($this->belongsId) {
            $fileModel->belongs_id = $this->belongsId;
            $fileModel->belongs_type = $this->belongsType;
        }

        $fileModel->save();

        return $fileModel;
    }

    public function getFile(int $id)
    {
        return File::find($id);
    }

    public function delete(File $file)
    {
        $this->uploader->delete($file->path);

        return $file->delete();
    }

    private function formatSize(int $bytes): string
    {
        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}
