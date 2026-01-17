<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exceptions\AppException;

use App\Http\Requests\SaveMedia;

use App\Http\Resources\FileResource;

use App\Services\FileService;

use App\Utilities;

class MediaController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    
}
