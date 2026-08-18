<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaFile;

class UpdateGalleryFileRequest extends UpdateMediaFileRequest
{
    public function type(): string
    {
        return MediaFile::TYPE_GALLERY;
    }
}
