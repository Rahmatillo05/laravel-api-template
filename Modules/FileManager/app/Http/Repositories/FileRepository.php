<?php

namespace Modules\FileManager\app\Http\Repositories;

use App\Http\Repositories\BaseRepository;
use App\Http\Resources\DefaultResource;
use DomainException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\FileManager\app\Http\Interfaces\FileInterface;
use Modules\FileManager\app\Models\File;
use Modules\FileManager\app\Dto\GeneratedPathFileDTO;
use Modules\FileManager\app\Dto\GeneratePathFileDTO;
use Modules\FileManager\Helpers\FilemanagerHelper;
use Throwable;

class FileRepository extends BaseRepository implements FileInterface
{
    /**
     * @var File
     */
    protected mixed $modelClass = File::class;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->generateQuery($request);
        $query->when($request->filled('ext'), function ($query) use ($request) {
            $query->whereIn('ext', explode(',', $request->get('ext')));
        });
        $data = $query->paginate($request->get('per_page'));

        return DefaultResource::collection($data);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $query = $this->generateQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return DefaultResource::collection($data);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $query = $this->generateQuery($request);
        $file = $query->findOrFail($id);
        $file = $file->append($request->get('append', []));

        return okResponse($file);
    }

    public function update(Request $request, File $file): JsonResponse
    {
        $file = $file->update($request->all());
        $this->defaultAppendAndInclude($file, $request);

        return okResponse($file);
    }

    public function destroy(File $file): JsonResponse
    {
        $file->delete();

        return okResponse($file);
    }

    public function uploadFile(Request $request, string $allow_ext, bool $is_front = false): JsonResponse
    {
        $errors = [];
        $files = $request->file('files');
        if (is_array($files)) {
            $response = [];
            foreach ($files as $i => $file) {
                if (! in_array($file->extension(), explode(',', $allow_ext))) {
                    $errors[] = [
                        'index' => $i,
                        'message' => 'Unknown extension',
                        'file' => $file->getClientOriginalName(),
                    ];
                }
                $dto = new GeneratePathFileDTO();
                $dto->file = $file;
                $dto->is_front = $is_front;
                $response[] = $this->storeFile($dto);
            }
        } else {
            if (! in_array($files->extension(), explode(',', $allow_ext))) {
                return invalidData('Unknown extension');
            } else {
                $dto = new GeneratePathFileDTO();
                $dto->file = $files;
                $dto->is_front = $is_front;
                $response[] = $this->storeFile($dto);
            }
        }

        return okResponse($response, meta_data: ['errors' => $errors]);
    }

    private function storeFile(GeneratePathFileDTO $dto)
    {
        DB::beginTransaction();
        try {
            $generatedDTO = $this->generatePath($dto);

            $generatedDTO->origin_name = $dto->file->getClientOriginalName();
            $generatedDTO->file_size = $dto->file->getSize();

            $dto->file->move($generatedDTO->file_folder, $generatedDTO->file_name.'.'.$generatedDTO->file_ext);

            $file = $this->createFileModel($generatedDTO, $dto->is_front);
            $this->createThumbnails($file);

            //            if ($dto->is_front and in_array($generatedDTO->file_ext, FileManagerHelper::getImagesExt())) {
            //                \Illuminate\Support\Facades\File::delete($generatedDTO->file_folder . '/' . $generatedDTO->file_name . '.' . $generatedDTO->file_ext);
            //            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new DomainException($e->getMessage(), $e->getCode());
        } catch (Throwable $e) {
            DB::rollBack();
            throw new DomainException($e->getMessage(), $e->getCode());
        }

        return $file;
    }

    public function generatePath(GeneratePathFileDTO $generatePathFileDTO): GeneratedPathFileDTO
    {
        $generatedPathFileDTO = new GeneratedPathFileDTO();
        $created_at = time();

        $file = $generatePathFileDTO->file;
        $y = date('Y', $created_at);
        $m = date('m', $created_at);
        $d = date('d', $created_at);
        $h = date('H', $created_at);
        $i = date('i', $created_at);

        $folders = [
            $y,
            $m,
            $d,
            $h,
            $i,
        ];

        $file_hash = Str::random(32);
        $file_name = Str::slug($file->getClientOriginalName()).'_'.Str::random(10);
        $basePath = base_path('static');
        $folderPath = '';
        foreach ($folders as $folder) {
            $basePath .= '/'.$folder;
            $folderPath .= $folder.'/';
            if (! is_dir($basePath)) {
                mkdir($basePath, 0777, true);
                chmod($basePath, 0777);
                Storage::makeDirectory('origin/'.$folderPath);
            }
        }
        if (! is_writable($basePath)) {
            throw new DomainException('Path is not writeable');
        }
        $generatedPathFileDTO->file_folder = $basePath;

        $path = $basePath.'/'.$file_hash.'.'.$file->getClientOriginalExtension();
        $generatedPathFileDTO->file_name = $file_hash;

        if ($generatePathFileDTO->useFileName) {
            $generatedPathFileDTO->file_name = $file_name;
            $path = $basePath.$file_name.'.'.$file->getClientOriginalExtension();
        }

        $generatedPathFileDTO->file_ext = $file->getClientOriginalExtension();
        $generatedPathFileDTO->file_path = $path;
        $generatedPathFileDTO->folder_path = $folderPath;

        return $generatedPathFileDTO;
    }

    private function createFileModel(GeneratedPathFileDTO $generatedDTO, $isFront)
    {

        $data = [
            'title' => $generatedDTO->origin_name,
            'description' => $generatedDTO->origin_name,
            'slug' => $generatedDTO->file_name,
            'ext' => $generatedDTO->file_ext,
            'file' => $generatedDTO->file_name.'.'.$generatedDTO->file_ext,
            'folder' => $generatedDTO->folder_path,
            'domain' => config('filemanager.static_url'),
            'user_id' => Auth::id(),
            'path' => $generatedDTO->file_folder,
            'size' => $generatedDTO->file_size,
            'is_front' => $isFront ? 1 : 0,
        ];

        return $this->modelClass::create($data);
    }

    /**
     * @throws Throwable
     */
    private function createThumbnails(File $file): void
    {
        $thumbsImages = FileManagerHelper::getThumbsImage();
        $origin = $file->getDist();
        if (in_array($file->ext, FilemanagerHelper::getImagesExt())) {
            try {
                foreach ($thumbsImages as $thumbsImage) {
                    $width = $thumbsImage['w'];
                    $quality = $thumbsImage['q'];
                    $slug = $thumbsImage['slug'];
                    $newFileDist = $file->path.'/'.$file->slug.'_'.$slug.'.'.$file->ext;
                    $img = Image::make($origin);
                    $height = $width / ($img->getWidth() / $img->getHeight());
                    $img->resize($width, $height)->save($newFileDist, $quality);
                }
            } catch (Throwable $e) {
                throw new $e;
            }
        } else {
            $newFileDist = $file->path.'/'.$file->slug.'.'.$file->ext;
            copy($origin, $newFileDist);
        }
    }
}
