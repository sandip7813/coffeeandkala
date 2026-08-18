<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaFile;

class StoreStudioFileRequest extends StoreMediaFileRequest
{
    public function type(): string
    {
        return MediaFile::TYPE_STUDIO;
    }
}
