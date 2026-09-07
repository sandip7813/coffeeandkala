{{--
    One "Content" tab block. Expects: $index (int|string, use '__INDEX__' for
    the blank <template> used by admin-articles.js when adding a section),
    $type ('features'|'journals', for upload format/size limits), $isEdit
    (bool — the Active/Inactive toggle and drag-to-reorder handle only
    appear on the edit form, never on Add Article), $article (needed to
    build the toggle's/reorder's instant-save URLs), and optional $section
    (ArticleSection, with image/galleryImages/videoCompanionImage eager
    loaded) when rendering an existing section on the edit form. Nothing
    here is required — a section can be saved at any stage of completion.

    Collapsible: the body opens by default (an admin can close it manually)
    — collapse-related ids/attributes are renumbered alongside everything
    else on drag-reorder/add/remove (see renumberSectionAttributes in
    admin-articles.js).
--}}
@php
    $section ??= null;
    $isEdit ??= false;
    $mediaType = old("sections.{$index}.media_type", $section->media_type ?? '');
    $isActive = old("sections.{$index}.is_active", $section->is_active ?? true);
    $statusUrl = $section?->id ? route("admin.{$type}.sections.status.update", [$article, $section->id]) : null;
    $imagePosition = old("sections.{$index}.image_position", $section->image_position ?? '');
    $contentPosition = old("sections.{$index}.content_position", $section->content_position ?? '');
    $youtubeUrl = old("sections.{$index}.youtube_url", $section->youtube_url ?? '');
    $youtubePosition = old("sections.{$index}.youtube_position", $section->youtube_position ?? '');
    $videoCompanionType = old("sections.{$index}.video_companion_type", $section->video_companion_type ?? '');
    $maxSizeKb = config("media.{$type}.max_size_kb");
    $formats = implode(',', config("media.{$type}.formats"));
@endphp
<div class="card mb-3 article-section {{ $isEdit && ! $isActive ? 'article-section--inactive' : '' }}" data-section>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start {{ $isEdit ? 'article-section-drag-handle' : '' }}" @if ($isEdit) data-drag-handle @endif>
            <h6 class="mb-0"><i class="bi bi-grip-vertical text-muted me-1" aria-hidden="true"></i>{{ __('Section') }} <span data-section-number>{{ is_numeric($index) ? $index + 1 : '' }}</span></h6>
            <div class="d-flex align-items-center gap-3" data-no-drag>
                @if ($isEdit)
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="sections[{{ $index }}][is_active]" value="0">
                        <input
                            type="checkbox" class="form-check-input" role="switch"
                            name="sections[{{ $index }}][is_active]" value="1"
                            id="section-active-{{ $index }}"
                            @checked((bool) $isActive)
                            data-section-active-toggle
                            @if ($statusUrl) data-status-url="{{ $statusUrl }}" @endif
                        >
                        <label class="form-check-label small" for="section-active-{{ $index }}">{{ __('Active') }}</label>
                    </div>
                @endif
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#section-body-{{ $index }}" aria-expanded="true" aria-controls="section-body-{{ $index }}" title="{{ __('Collapse/expand this section') }}">
                    <i class="bi bi-chevron-up" aria-hidden="true" data-collapse-icon></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-section>
                    <i class="bi bi-trash" aria-hidden="true"></i> {{ __('Remove') }}
                </button>
            </div>
        </div>

        <div class="collapse show mt-3" id="section-body-{{ $index }}">

        @if ($section?->id)
            <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section->id }}" data-section-id-field>
        @endif

        <div class="mb-3">
            <label class="form-label">{{ __('Section Title') }}</label>
            <input type="text" name="sections[{{ $index }}][title]" class="form-control" maxlength="255" value="{{ old("sections.{$index}.title", $section->title ?? '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label d-block">{{ __('This section has') }}</label>
            <div class="btn-group" role="group" data-media-type-group>
                <input type="radio" class="btn-check" name="sections[{{ $index }}][media_type]" value="image" id="media-image-{{ $index }}" data-media-type-option="image" @checked($mediaType === 'image')>
                <label class="btn btn-outline-primary" for="media-image-{{ $index }}"><i class="bi bi-image me-1" aria-hidden="true"></i>{{ __('An Image') }}</label>

                <input type="radio" class="btn-check" name="sections[{{ $index }}][media_type]" value="video" id="media-video-{{ $index }}" data-media-type-option="video" @checked($mediaType === 'video')>
                <label class="btn btn-outline-primary" for="media-video-{{ $index }}"><i class="bi bi-youtube me-1" aria-hidden="true"></i>{{ __('A YouTube Video') }}</label>

                <input type="radio" class="btn-check" name="sections[{{ $index }}][media_type]" value="gallery" id="media-gallery-{{ $index }}" data-media-type-option="gallery" @checked($mediaType === 'gallery')>
                <label class="btn btn-outline-primary" for="media-gallery-{{ $index }}"><i class="bi bi-images me-1" aria-hidden="true"></i>{{ __('Gallery Images') }}</label>
            </div>
        </div>

        {{-- Image block --}}
        <div data-media-block="image" style="{{ $mediaType === 'image' ? '' : 'display:none' }}">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('Image Position') }}</label>
                    <select name="sections[{{ $index }}][image_position]" class="form-select" data-image-position>
                        <option value="">{{ __('— Select —') }}</option>
                        <option value="left" @selected($imagePosition === 'left')>{{ __('Left') }}</option>
                        <option value="right" @selected($imagePosition === 'right')>{{ __('Right') }}</option>
                        <option value="center" @selected($imagePosition === 'center')>{{ __('Center') }}</option>
                    </select>
                </div>
                <div class="col-md-6" data-content-position-wrap style="{{ in_array($imagePosition, ['left', 'right'], true) ? '' : 'display:none' }}">
                    <label class="form-label">{{ __('Content Position') }}</label>
                    <select name="sections[{{ $index }}][content_position]" class="form-select">
                        <option value="">{{ __('— Select —') }}</option>
                        <option value="beside" @selected($contentPosition === 'beside')>{{ __('Beside the image') }}</option>
                        <option value="standalone" @selected($contentPosition === 'standalone')>{{ __('Standalone') }}</option>
                    </select>
                    <p class="form-text mb-0">{{ __('"Beside" always places the content on the side opposite the image.') }}</p>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Section Image') }}</label>
                <div class="row g-2 align-items-start">
                    {{-- Column 1: thumbnail (click to view full size) --}}
                    <div class="col-2 col-md-1 d-flex align-items-center">
                        @if ($section?->image)
                            <a href="{{ $section->image->large_url }}" data-fancybox="article-images" data-caption="{{ $section->image->caption }}">
                                <img src="{{ $section->image->thumbnail_url }}" alt="" class="rounded border" style="height:56px; width:56px; object-fit:cover;">
                            </a>
                        @endif
                    </div>

                    {{-- Column 2: image --}}
                    <div class="col-10 col-md-4">
                        <input type="file" name="sections[{{ $index }}][image]" class="form-control" accept="image/png,image/jpeg,image/webp" data-image-input data-max-kb="{{ $maxSizeKb }}" data-formats="{{ $formats }}">
                        <p class="form-text mb-0">{{ strtoupper(str_replace(',', ', ', $formats)) }}, {{ __('max') }} {{ number_format($maxSizeKb / 1024, 1) }} MB.</p>
                        <p class="form-text mb-0 small text-danger d-none" data-image-error></p>
                    </div>

                    {{-- Column 3: caption --}}
                    <div class="col-12 col-md-6">
                        <input type="text" name="sections[{{ $index }}][image_caption]" class="form-control" maxlength="255" placeholder="{{ __('Caption (optional)') }}" value="{{ old("sections.{$index}.image_caption", $section->image?->caption ?? '') }}">
                    </div>

                    {{-- Column 4: remove --}}
                    <div class="col-12 col-md-1 d-flex justify-content-md-center">
                        @if ($section?->image)
                            <div class="form-check" title="{{ __('Remove current image') }}">
                                <input class="form-check-input" type="checkbox" name="sections[{{ $index }}][remove_image]" value="1" id="remove-image-{{ $index }}">
                                <label class="form-check-label small d-md-none" for="remove-image-{{ $index }}">{{ __('Remove current image') }}</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Video block --}}
        <div data-media-block="video" style="{{ $mediaType === 'video' ? '' : 'display:none' }}">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('YouTube Video URL') }}</label>
                    <input type="url" name="sections[{{ $index }}][youtube_url]" class="form-control" value="{{ $youtubeUrl }}" placeholder="https://www.youtube.com/watch?v=…">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('Video Position') }}</label>
                    <select name="sections[{{ $index }}][youtube_position]" class="form-select">
                        <option value="">{{ __('— Select —') }}</option>
                        <option value="left" @selected($youtubePosition === 'left')>{{ __('Left') }}</option>
                        <option value="right" @selected($youtubePosition === 'right')>{{ __('Right') }}</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Beside the Video') }}</label>
                <select name="sections[{{ $index }}][video_companion_type]" class="form-select" data-video-companion-type>
                    <option value="none" @selected($videoCompanionType === 'none' || $videoCompanionType === '')>{{ __('Nothing') }}</option>
                    <option value="text" @selected($videoCompanionType === 'text')>{{ __('The Content below') }}</option>
                    <option value="image" @selected($videoCompanionType === 'image')>{{ __('An Image') }}</option>
                </select>
                <p class="form-text mb-0">{{ __('"The Content below" reuses this section\'s Content field — nothing extra to fill in.') }}</p>
            </div>

            <div class="mb-3" data-video-companion-image-wrap style="{{ $videoCompanionType === 'image' ? '' : 'display:none' }}">
                <label class="form-label">{{ __('Image Beside the Video') }}</label>
                @if ($section?->videoCompanionImage)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <a href="{{ $section->videoCompanionImage->large_url }}" data-fancybox="article-images" data-caption="{{ $section->videoCompanionImage->caption }}">
                            <img src="{{ $section->videoCompanionImage->thumbnail_url }}" alt="" class="rounded border" style="height:64px; width:64px; object-fit:cover;">
                        </a>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sections[{{ $index }}][remove_video_companion_image]" value="1" id="remove-video-companion-image-{{ $index }}">
                            <label class="form-check-label small" for="remove-video-companion-image-{{ $index }}">{{ __('Remove current image') }}</label>
                        </div>
                    </div>
                @endif
                <input type="file" name="sections[{{ $index }}][video_companion_image]" class="form-control mb-2" accept="image/png,image/jpeg,image/webp" data-image-input data-max-kb="{{ $maxSizeKb }}" data-formats="{{ $formats }}">
                <input type="text" name="sections[{{ $index }}][video_companion_image_caption]" class="form-control" maxlength="255" placeholder="{{ __('Caption (optional)') }}" value="{{ old("sections.{$index}.video_companion_image_caption", $section->videoCompanionImage?->caption ?? '') }}">
                <p class="form-text mb-0 small text-danger d-none" data-image-error></p>
            </div>
        </div>

        {{-- Gallery block — each image is its own slot (file + caption), so every image can carry a distinct caption. --}}
        <div data-media-block="gallery" style="{{ $mediaType === 'gallery' ? '' : 'display:none' }}">
            <div class="mb-3">
                <label class="form-label">{{ __('Gallery Images') }} <span class="text-muted small">{{ __('(up to 4 new images per save)') }}</span></label>

                <div data-gallery-slots>
                    @if ($section?->galleryImages)
                        @foreach ($section->galleryImages as $slotIndex => $galleryImage)
                            @include('admin.articles._gallery_image_fields', ['sectionIndex' => $index, 'slotIndex' => $slotIndex, 'galleryImage' => $galleryImage, 'type' => $type])
                        @endforeach
                    @endif
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" data-add-gallery-slot>
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> {{ __('Add Image') }}
                </button>

                <template data-gallery-slot-template>
                    @include('admin.articles._gallery_image_fields', ['sectionIndex' => $index, 'slotIndex' => '__SLOT__', 'galleryImage' => null, 'type' => $type])
                </template>

                <p class="form-text mb-0">{{ __('Accepted formats:') }} {{ strtoupper(str_replace(',', ', ', $formats)) }}. {{ __('Max size:') }} {{ number_format($maxSizeKb / 1024, 1) }} MB {{ __('each.') }}</p>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('Content') }}</label>
            <div class="quill-editor" data-quill-target="sections[{{ $index }}][content]" style="min-height:150px;">{!! old("sections.{$index}.content", $section->content ?? '') !!}</div>
            <textarea name="sections[{{ $index }}][content]" class="d-none" data-quill-input>{{ old("sections.{$index}.content", $section->content ?? '') }}</textarea>
        </div>

        </div>{{-- /.collapse --}}
    </div>
</div>
