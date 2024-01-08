<?php

namespace Modules\FileManager\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FileManager\app\Http\Interfaces\FileInterface;
use Modules\FileManager\app\Http\Requests\UpdateFileRequest;
use Modules\FileManager\app\Models\File;

/**
 * @group File
 */
class FileController extends Controller
{
    public function __construct(public FileInterface $fileRepository)
    {
    }

    /**
     * File Get all
     *
     * @response {
     *  "id": "integer",
     *  "title": "string",
     *  "description": "string",
     *  "slug": "string",
     *  "ext": "string",
     *  "file": "string",
     *  "folder": "string",
     *  "domain": "string",
     *  "user_id": "integer",
     *  "folder_id": "integer",
     *  "path": "string",
     *  "size": "integer",
     *  "is_front": "integer",
     *  "deleted_at": "date",
     *  "created_at": "date",
     *  "updated_at": "date",
     * }
     */
    public function index(Request $request)
    {
        return $this->fileRepository->index($request);
    }

    /**
     * File adminIndex get All
     *
     * @response {
     *  "id": "integer",
     *  "title": "string",
     *  "description": "string",
     *  "slug": "string",
     *  "ext": "string",
     *  "file": "string",
     *  "folder": "string",
     *  "domain": "string",
     *  "user_id": "integer",
     *  "folder_id": "integer",
     *  "path": "string",
     *  "size": "integer",
     *  "is_front": "integer",
     *  "deleted_at": "date",
     *  "created_at": "date",
     *  "updated_at": "date",
     * }
     */
    public function adminIndex(Request $request)
    {
        return $this->fileRepository->adminIndex($request);
    }

    /**
     * File view
     *
     * @queryParam id required
     *
     * @response {
     *  "id": "integer",
     *  "title": "string",
     *  "description": "string",
     *  "slug": "string",
     *  "ext": "string",
     *  "file": "string",
     *  "folder": "string",
     *  "domain": "string",
     *  "user_id": "integer",
     *  "folder_id": "integer",
     *  "path": "string",
     *  "size": "integer",
     *  "is_front": "integer",
     *  "deleted_at": "date",
     *  "created_at": "date",
     *  "updated_at": "date",
     * }
     */
    public function show(Request $request, int $id): JsonResponse
    {
        return $this->fileRepository->show($request, $id);
    }

    /**
     * File update
     *
     * @queryParam file required
     *
     * @bodyParam title string
     * @bodyParam description string
     * @bodyParam slug string
     * @bodyParam ext string
     * @bodyParam file string
     * @bodyParam folder string
     * @bodyParam domain string
     * @bodyParam user_id integer
     * @bodyParam folder_id integer
     * @bodyParam path string
     * @bodyParam size integer
     * @bodyParam is_front integer
     */
    public function update(UpdateFileRequest $request, File $file): JsonResponse
    {
        return $this->fileRepository->update($request, $file);
    }

    /**
     * File delete
     *
     * @queryParam file required
     */
    public function destroy(File $file): JsonResponse
    {
        return $this->fileRepository->destroy($file);
    }

    public function frontUpload(Request $request)
    {
        $front_allow_ext = config('filemanager.front_allow_ext');
        $request->validate([
            'files' => 'required',
            'files.*' => "nullable|mimes:$front_allow_ext",
        ]);
        return $this->fileRepository->uploadFile($request, $front_allow_ext, true);
    }

    public function adminUploads(Request $request): JsonResponse
    {
        $admin_allow_ext = config('filemanager.admin_allow_ext');
        $request->validate([
            'files' => 'required',
            'files.*' => "nullable|mimes:$admin_allow_ext",
        ]);

        return $this->fileRepository->uploadFile($request, $admin_allow_ext);
    }
}
