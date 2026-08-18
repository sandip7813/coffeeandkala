<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaFile;

class UpdateStudioFileRequest extends UpdateMediaFileRequest
{
    public function type(): string
    {
        return MediaFile::TYPE_STUDIO;
    }
}
