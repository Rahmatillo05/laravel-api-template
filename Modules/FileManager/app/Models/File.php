<?php

namespace Modules\FileManager\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Helpers\FilemanagerHelper;

/**
 * This is the model class for table "files".
 */
class File extends Model
{
    use SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'id',
        'title',
        'description',
        'slug',
        'ext',
        'file',
        'folder',
        'domain',
        'user_id',
        'folder_id',
        'path',
        'size',
        'is_front',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['path'];

    protected $appends = ['src'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getDist(): string
    {
        return $this->path.'/'.$this->file;
    }

    public function getThumbsAttribute(): array
    {
        $thumbsImages = FileManagerHelper::getThumbsImage();
        foreach ($thumbsImages as &$thumbsImage) {
            $slug = $thumbsImage['slug'];
            if (in_array($this->ext, FileManagerHelper::getImagesExt())) {
                $src = config('filemanager.static_url').$this->folder.$this->slug.'_'.$slug.'.'.$this->ext;
            } else {
                $src = $this->getSrcAttribute();
            }
            $thumbsImage['src'] = $src;
        }

        return $thumbsImages;
    }

    public function getSrcAttribute(): string
    {
        return $this->domain.$this->folder.$this->file;
    }
}
