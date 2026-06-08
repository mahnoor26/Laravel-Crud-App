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
    private const S3_DISK = 's3';

    private const ENTITY_MAP = [
        'user' => User::class,
        'customer' => Customer::class,
    ];

    //
    private function rootPrefix(): string
    {
        return trim(config('filesystems.s3_root_prefix', 'mahnoor'), '/');
    }

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

        $fileName = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
        $directory = "{$this->rootPrefix()}/{$entityType}/{$entityId}";

        $path = Storage::disk(self::S3_DISK)->putFileAs(
            $directory,
            $uploadedFile,
            $fileName
        );

        return File::create([
            'name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'file_size' => $uploadedFile->getSize(),
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
        Storage::disk(self::S3_DISK)->delete($file->path);

        return (bool) $file->delete();
    }

    /**
     * Delete all files for a morphable entity (e.g. when deleting a customer).
     */
    public function deleteFilesForEntity(string $entityType, int $entityId): void
    {
        $entityType = strtolower($entityType);
        $modelClass = $this->resolveEntityClass($entityType);

        File::query()
            ->where('fileable_type', $modelClass)
            ->where('fileable_id', $entityId)
            ->each(function (File $file) {
                $file->delete();
            });
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
