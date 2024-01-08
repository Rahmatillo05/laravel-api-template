<?php

namespace Modules\FileManager\app\Http\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\FileManager\app\Models\File;

interface FileInterface
{
    public function index(Request $request): AnonymousResourceCollection;

    public function adminIndex(Request $request): AnonymousResourceCollection;

    public function show(Request $request, int $id): JsonResponse;

    public function update(Request $request, File $file): JsonResponse;

    public function destroy(File $file): JsonResponse;

    public function uploadFile(Request $request, string $allow_ext, bool $is_front = false): JsonResponse;
}
