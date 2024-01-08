<?php

namespace Modules\FileManager\Helpers;

use DomainException;

class FilemanagerHelper
{
    public static function getThumbsImage(): array
    {
        if (! ($thumbs = config('filemanager.thumbs'))) {
            throw new DomainException("'thumbs' params is not founded");
        }

        return $thumbs;
    }

    public static function getImagesExt(): array
    {
        if (! ($images_ext = config('filemanager.images_ext'))) {
            throw new DomainException("'images_ext' params is not founded");
        }

        return $images_ext;
    }
}
