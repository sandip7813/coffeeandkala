{{--
    One Gallery Images slot within a section. Expects: $sectionIndex,
    $slotIndex (int|string, use '__SLOT__' for the blank <template> used by
    admin-articles.js when adding a slot), $type ('features'|'journals', for
    upload format/size limits), and optional $galleryImage (MediaFile) when
    rendering an existing gallery image on the edit form.
--}}
@php
    $galleryImage ??= null;
    $namePrefix = "sections[{$sectionIndex}][gallery][{$slotIndex}]";
    $oldPrefix = "sections.{$sectionIndex}.gallery.{$slotIndex}";
    $maxSizeKb = config("media.{$type}.max_size_kb");
    $formats = implode(',', config("media.{$type}.formats"));
@endphp
<div class="row g-2 align-items-start mb-2 article-gallery-slot" data-gallery-slot>
    @if ($galleryImage?->id)
        <input type="hidden" name="{{ $namePrefix }}[id]" value="{{ $galleryImage->id }}" data-gallery-slot-id-field>
    @endif

    {{-- Column 1: thumbnail (click to view full size) --}}
    <div class="col-2 col-md-1 d-flex align-items-center">
        @if ($galleryImage?->id)
            <a href="{{ $galleryImage->large_url }}" data-fancybox="article-images" data-caption="{{ $galleryImage->caption }}">
                <img src="{{ $galleryImage->thumbnail_url }}" alt="" class="rounded border" style="height:48px; width:48px; object-fit:cover;">
            </a>
        @endif
    </div>

    {{-- Column 2: image --}}
    <div class="col-10 col-md-4">
        <input type="file" name="{{ $namePrefix }}[image]" class="form-control form-control-sm" accept="image/png,image/jpeg,image/webp" data-image-input data-max-kb="{{ $maxSizeKb }}" data-formats="{{ $formats }}">
        <p class="form-text mb-0 small text-danger d-none" data-image-error></p>
    </div>

    {{-- Column 3: caption --}}
    <div class="col-12 col-md-6">
        <input type="text" name="{{ $namePrefix }}[caption]" class="form-control form-control-sm" maxlength="255" placeholder="{{ __('Caption (optional)') }}" value="{{ old($oldPrefix.'.caption', $galleryImage->caption ?? '') }}">
    </div>

    {{-- Column 4: delete --}}
    <div class="col-12 col-md-1 d-flex justify-content-md-center">
        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-gallery-slot title="{{ __('Remove') }}">
            <i class="bi bi-trash" aria-hidden="true"></i>
        </button>
    </div>
</div>
