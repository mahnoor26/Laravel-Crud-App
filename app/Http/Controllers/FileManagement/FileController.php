<?php

namespace App\Http\Controllers\FileManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileManagement\UploadFileRequest;
use App\Services\FileService;
use F9Web\ApiResponseHelpers;

class FileController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(private readonly FileService $fileService) {}

    // GET /file/my-files
    public function userFiles()
    {
        return $this->respondWithSuccess([
            'message' => 'Your files fetched successfully',
            'files'   => $this->fileService->getUserFiles(),
        ]);
    }

    // GET API /file/index
    public function index()
    {
        return $this->respondWithSuccess([
            'message' => 'Files Fetched Successfully',
            'files' => $this->fileService->getAllFiles(),
        ]);
    }

    // POST API /file/{entityType}/{entityId}  (entityType: user | customer)
    public function store(UploadFileRequest $request, string $entityType, int $entityId)
    {
            $file = $this->fileService->uploadFile(
                $entityType,
                $entityId,
                $request->file('file')
            );
        

        return $this->respondCreated([
            'message' => 'File uploaded successfully',
            'file' => $file->load(['uploader', 'fileable']),
        ]);
    }

    // GET /file/show/{id}
    public function show($id)
    {
        $file = $this->fileService->getFileById($id);

        return $this->respondWithSuccess([
            'message' => 'File Fetched Successfully',
            'file' => $file,
        ]);
    }

    // DELETE API /file/delete/{id}
    public function destroy($id)
    {
        $this->fileService->deleteFile($id);

        return $this->respondWithSuccess([
            'message' => 'File deleted successfully',
        ]);
    }
}
