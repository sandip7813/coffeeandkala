<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Http\Requests\Admin\StoreStudioFileRequest;
use App\Http\Requests\Admin\UpdateStudioFileRequest;
use App\Models\MediaFile;
use Illuminate\Http\RedirectResponse;

class StudioController extends MediaFileController
{
    protected function type(): string
    {
        return MediaFile::TYPE_STUDIO;
    }

    public function store(StoreStudioFileRequest $request, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        return $this->storeMedia($request, $storeMediaFile);
    }

    public function update(UpdateStudioFileRequest $request, MediaFile $media): RedirectResponse
    {
        return $this->updateMedia($request, $media);
    }
}
