<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StoreMediaFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaFileRequest;
use App\Http\Requests\Admin\UpdateMediaFileRequest;
use App\Models\MediaFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Shared Gallery/Studio media management. Both sections have identical
 * functionality (upload, review, approve, activate/deactivate, delete) and
 * differ only in the `media_files.type` they operate on and the permission
 * names they check — GalleryController and StudioController just supply
 * those, plus the type-specific FormRequest classes needed for validation.
 */
abstract class MediaFileController extends Controller
{
    abstract protected function type(): string;

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user?->can("view-{$this->type()}"), 403);

        $filters = $request->only(['title', 'status']);

        $media = MediaFile::query()
            ->ofType($this->type())
            ->with('uploader', 'approver')
            ->when(filled($filters['title'] ?? null), fn ($query) => $query->where('title', 'like', '%'.$filters['title'].'%'))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        return view("admin.{$this->type()}.index", compact('media', 'filters', 'hasActiveFilters'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()?->can("upload-{$this->type()}"), 403);

        return view("admin.{$this->type()}.create");
    }

    protected function storeMedia(StoreMediaFileRequest $request, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        $data = $request->validated();

        $storeMediaFile->handle(
            $this->type(),
            $request->file('image'),
            $data['title'],
            $data['caption'],
            $request->user(),
        );

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', __('Uploaded.'));
    }

    public function edit(MediaFile $media): View
    {
        $this->assertType($media);

        abort_unless(auth()->user()?->can("edit-{$this->type()}"), 403);

        return view("admin.{$this->type()}.edit", compact('media'));
    }

    protected function updateMedia(UpdateMediaFileRequest $request, MediaFile $media): RedirectResponse
    {
        $this->assertType($media);

        $media->update($request->validated());

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', __('Updated.'));
    }

    public function updateStatus(MediaFile $media): RedirectResponse
    {
        $this->assertType($media);

        abort_unless(auth()->user()?->can("change-{$this->type()}-status"), 403);

        $media->update([
            'status' => $media->status === MediaFile::STATUS_ACTIVE
                ? MediaFile::STATUS_INACTIVE
                : MediaFile::STATUS_ACTIVE,
        ]);

        return redirect()->route("admin.{$this->type()}.index", request()->query())
            ->with('status', __('Status updated.'));
    }

    public function approve(MediaFile $media): RedirectResponse
    {
        $this->assertType($media);

        abort_unless(auth()->user()?->can("approve-{$this->type()}"), 403);

        $media->update([
            'status' => MediaFile::STATUS_ACTIVE,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route("admin.{$this->type()}.index", request()->query())
            ->with('status', __('Approved.'));
    }

    public function destroy(MediaFile $media, StoreMediaFile $storeMediaFile): RedirectResponse
    {
        $this->assertType($media);

        abort_unless(auth()->user()?->can("delete-{$this->type()}"), 403);

        $storeMediaFile->deleteFiles($media);
        $media->delete();

        return redirect()->route("admin.{$this->type()}.index")
            ->with('status', __('Deleted.'));
    }

    /**
     * Prevent a gallery URL from operating on a studio record (or vice versa)
     * when both share the same {media} route-model-bound MediaFile.
     */
    private function assertType(MediaFile $media): void
    {
        abort_unless($media->type === $this->type(), 404);
    }
}
