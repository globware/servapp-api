<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Uploaders\LocalUploader;
use App\Services\Uploaders\S3Uploader;
use App\Services\Uploaders\CloudinaryUploader;
use App\Services\Uploaders\FileUploaderInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileUploaderInterface::class, function () {

            return match (config('files.upload_driver')) {
                's3'         => new S3Uploader(),
                'cloudinary' => new CloudinaryUploader(),
                default      => new LocalUploader(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
