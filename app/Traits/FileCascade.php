<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileCascade
{
    // Cascade delete files when the parent entity is deleted.
    public static function bootFileCascade()
    {
        static::deleting(function ($model) {
            $files = $model->files()->get();
            if(!$files->isEmpty()) {
                foreach ($files as $file) {
                    // Delete from S3
                    Storage::disk(config('services.s3.disk'))->delete($file->path);
    
                    // Delete from database
                    $file->delete();
                }
            }

        });
    }
}
