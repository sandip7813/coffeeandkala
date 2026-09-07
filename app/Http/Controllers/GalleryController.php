<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\Meta;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('frontend.gallery', ['plates' => $this->plates(), 'meta' => Meta::forPage('gallery')]);
    }

    /**
     * Every active Gallery upload, newest first, in the flat "plate" shape
     * the Gallery views already expect.
     *
     * @return list<array<string, mixed>>
     */
    private function plates(): array
    {
        return MediaFile::query()
            ->ofType(MediaFile::TYPE_GALLERY)
            ->active()
            ->latest()
            ->get()
            ->map(fn (MediaFile $media, int $index): array => [
                'id' => $media->uuid,
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => Str::limit($media->title, 50),
                'description' => $media->caption,
                'location' => null,
                'src' => $media->large_url,
                'thumb' => $media->thumbnail_url,
            ])
            ->values()
            ->all();
    }
}
