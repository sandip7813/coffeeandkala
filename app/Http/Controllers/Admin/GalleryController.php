<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Http\Requests\Admin\StoreGalleryFileRequest;
use App\Http\Requests\Admin\UpdateGalleryFileRequest;
use App\Models\MediaFile;
use Illuminate\Http\RedirectResponse;

class GalleryController extends MediaFileController
{
    protected function type(): string
    {
        return MediaFile::TYPE_GALLERY;
    }

    public function store(StoreGalleryFileRequest $request, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        return $this->storeMedia($request, $storeMediaFile);
    }

    public function update(UpdateGalleryFileRequest $request, MediaFile $media): RedirectResponse
    {
        return $this->updateMedia($request, $media);
    }
}
