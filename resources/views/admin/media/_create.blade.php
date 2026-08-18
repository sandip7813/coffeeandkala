{{-- Shared Gallery/Studio upload form. Expects: $type ('gallery'|'studio'), $icon. --}}
<div class="row justify-content-center">
    <div class="col-lg-7">
        <x-adminlte-card :icon="$icon" :title="__('Upload Image')">
            <form method="POST" action="{{ route("admin.{$type}.store") }}" enctype="multipart/form-data"
                  data-page-loading="{{ __('Uploading image…') }}">
                @csrf

                <x-adminlte-input name="title" label="{{ __('Title') }} *" maxlength="255" required :value="old('title')" />
                <x-adminlte-input name="caption" label="{{ __('Caption') }} *" maxlength="255" required :value="old('caption')" />

                <x-adminlte-input-file name="image" label="{{ __('Image') }} *" accept="image/*" required />
                <p class="form-text mb-3">
                    {{ __('Accepted formats:') }} {{ strtoupper(implode(', ', config("media.{$type}.formats"))) }}.
                    {{ __('Max size:') }} {{ number_format(config("media.{$type}.max_size_kb") / 1024, 1) }} MB.
                </p>

                @unless (auth()->user()?->can("approve-{$type}"))
                    <div class="alert alert-info">
                        {{ __('Your upload will be marked as pending until a super admin approves it.') }}
                    </div>
                @endunless

                <div class="d-flex gap-2">
                    <a href="{{ route("admin.{$type}.index") }}" class="btn btn-outline-secondary">{{ __('adminlte.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i> {{ __('adminlte.save') }}
                    </button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>
