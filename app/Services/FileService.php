<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileService
{

    private const ENTITY_MAP = [
        'user' => User::class,
        'customer' => Customer::class,
    ];

    // Get all files uploaded by the authenticated user.
    public function getUserFiles()
    {
        return auth()
                ->user()
                ->files()->with(['uploader', 'fileable'])
                ->get();
    }

     // Get all files.
    public function getAllFiles()
    {
        return File::with(['uploader', 'fileable'])->get();
    }

    // Get file by ID.
    public function getFileById($id)
    {
        return File::with(['uploader', 'fileable'])->findOrFail($id);
    }

     // Upload a file to S3 and persist metadata in the database.
     // S3 layout: {root}/user/{userId}/{fileName} or {root}/customer/{customerId}/{fileName}
    public function uploadFile(string $entityType, int $entityId, UploadedFile $uploadedFile): File
    {
        $entityType = strtolower($entityType);
        $modelClass = $this->resolveEntityClass($entityType);

        $entity = $modelClass::findOrFail($entityId);

        $fileName =  date('Ymd_His') . '_' . $uploadedFile->getClientOriginalExtension();
        $directory =  config('services.s3.root_prefix') . "/{$entityType}/{$entityId}";

        $path = Storage::disk(config('services.s3.disk'))->putFileAs(
            $directory,
            $uploadedFile,
            $fileName
        );

        return File::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'fileable_id' => $entity->id,
            'fileable_type' => $modelClass,
            'uploaded_by' => auth()->id(),
        ]);
    }

    // Delete file from S3 and database.
    public function deleteFile($id): bool
    {
        $file = File::findOrFail($id);
        Storage::disk(config('services.s3.disk'))->delete($file->path);

        return (bool) $file->delete();
    }

    private function resolveEntityClass(string $entityType): string
    {
        if (!isset(self::ENTITY_MAP[$entityType])) {
            throw new InvalidArgumentException(
                'Invalid entity type. Allowed values: ' . implode(', ', array_keys(self::ENTITY_MAP))
            );
        }

        return self::ENTITY_MAP[$entityType];
    }
}
