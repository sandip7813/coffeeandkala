{{-- One static page's own meta title/description/keywords, saved on its
     own submit — used for every entry in the Main Pages tab, and for the
     Features/Journal/Poetry index page's meta at the top of its own tab.
     Expects $key (a Meta::STATIC_PAGES key) and $meta. --}}
<form method="POST" action="{{ route('admin.meta.page.update', $key) }}" data-page-loading="{{ __('Saving SEO…') }}">
    @csrf
    @method('PUT')

    <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="old('meta_title', $meta->title)" />
    <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ old('meta_description', $meta->description) }}</x-adminlte-textarea>
    <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="old('meta_keywords', $meta->keywords)" />

    <button type="submit" class="btn btn-primary">{{ __('Save SEO') }}</button>
</form>
