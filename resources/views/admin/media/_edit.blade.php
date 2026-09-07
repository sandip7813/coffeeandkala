{{-- Shared Gallery/Studio edit form (title/caption only). Expects: $media, $type ('gallery'|'studio'), $icon. --}}
<div class="row justify-content-center">
    <div class="col-lg-7">
        <x-adminlte-card :icon="$icon" :title="__('Edit Image')">
            <div class="text-center mb-3">
                <a href="{{ $media->large_url }}" data-fancybox="{{ $type }}-edit" data-caption="{{ $media->title }}">
                    <img src="{{ $media->thumbnail_url }}" alt="{{ $media->title }}" class="rounded" width="200" height="200" style="object-fit: cover; cursor: zoom-in;">
                </a>
            </div>

            <form method="POST" action="{{ route("admin.{$type}.update", $media) }}">
                @csrf
                @method('PUT')

                <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="old('title', $media->title)" />
                <x-adminlte-textarea name="caption" label="{{ __('Caption') }} *" rows="4" required>{{ old('caption', $media->caption) }}</x-adminlte-textarea>

                <div class="d-flex gap-2">
                    <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                    </button>
                </div>
            </form>

            @if ($canManageHomeSections ?? false)
                <hr class="my-4">

                <h6 class="mb-2">{{ __('Home Page') }}</h6>
                <div class="form-check form-switch">
                    <input
                        type="checkbox" class="form-check-input" role="switch"
                        id="home-section-toggle"
                        @checked($onHomePage ?? false)
                        @disabled($media->status !== 'active' && ! ($onHomePage ?? false))
                        data-home-section-toggle
                        data-section-label="{{ \App\Support\HomeMediaSections::LABELS[$type] }}"
                        data-article-title="{{ $media->title }}"
                        data-toggle-url="{{ route("admin.{$type}.home-section.toggle", $media) }}"
                    >
                    <label class="form-check-label" for="home-section-toggle">
                        {{ __('Show on the homepage :section carousel. Saves instantly.', ['section' => \App\Support\HomeMediaSections::LABELS[$type]]) }}
                    </label>
                </div>
            @endif
        </x-adminlte-card>
    </div>
</div>
