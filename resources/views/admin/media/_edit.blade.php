{{-- Shared Gallery/Studio edit form (title/caption only). Expects: $media, $type ('gallery'|'studio'), $icon. --}}
<div class="row justify-content-center">
    <div class="col-lg-7">
        <x-adminlte-card :icon="$icon" :title="__('Edit Image')">
            <div class="text-center mb-3">
                <img src="{{ $media->thumbnail_url }}" alt="{{ $media->title }}" class="rounded" width="200" height="200" style="object-fit: cover;">
            </div>

            <form method="POST" action="{{ route("admin.{$type}.update", $media) }}">
                @csrf
                @method('PUT')

                <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="old('title', $media->title)" />
                <x-adminlte-input name="caption" label="{{ __('Caption') }} *" maxlength="255" required :value="old('caption', $media->caption)" />

                <div class="d-flex gap-2">
                    <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                    </button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
