<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaFile;

class StoreGalleryFileRequest extends StoreMediaFileRequest
{
    public function type(): string
    {
        return MediaFile::TYPE_GALLERY;
    }
}
