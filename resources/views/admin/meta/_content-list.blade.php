@if ($items->isEmpty())
    <p class="text-body-secondary">{{ __('Nothing here yet.') }}</p>
@else
    <div class="accordion meta-accordion" id="meta-{{ $type }}-accordion">
        @foreach ($items as $entry)
            @php $record = $entry['record']; $itemMeta = $entry['meta']; @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="meta-{{ $type }}-{{ $record->id }}-heading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#meta-{{ $type }}-{{ $record->id }}-collapse" aria-expanded="false" aria-controls="meta-{{ $type }}-{{ $record->id }}-collapse">
                        {{ $record->title }}
                    </button>
                </h2>
                <div id="meta-{{ $type }}-{{ $record->id }}-collapse" class="accordion-collapse collapse" aria-labelledby="meta-{{ $type }}-{{ $record->id }}-heading">
                    <div class="accordion-body">
                        <form method="POST" action="{{ route('admin.meta.content.update', [$type, $record->id]) }}" data-page-loading="{{ __('Saving SEO…') }}">
                            @csrf
                            @method('PUT')

                            <x-adminlte-input name="meta_title" label="{{ __('Meta Title') }}" :value="$itemMeta->title" />
                            <x-adminlte-textarea name="meta_description" label="{{ __('Meta Description') }}" rows="3">{{ $itemMeta->description }}</x-adminlte-textarea>
                            <x-adminlte-input name="meta_keywords" label="{{ __('Meta Keywords') }}" :value="$itemMeta->keywords" />

                            <div class="d-flex gap-2">
                                <a href="{{ route($editRoute, $record->id) }}" class="btn btn-outline-secondary btn-sm">{{ __('Open full edit page') }}</a>
                                <button type="submit" class="btn btn-primary btn-sm">{{ __('Save SEO') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($paginator->lastPage() > 1)
        <nav class="d-flex justify-content-between align-items-center mt-3" aria-label="{{ __('Pagination') }}">
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                data-meta-page="{{ $paginator->currentPage() - 1 }}"
                data-meta-type="{{ $type }}"
                @disabled($paginator->onFirstPage())
            >
                <i class="bi bi-chevron-left" aria-hidden="true"></i> {{ __('Previous') }}
            </button>
            <span class="text-body-secondary small">{{ __('Page :current of :last', ['current' => $paginator->currentPage(), 'last' => $paginator->lastPage()]) }}</span>
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm"
                data-meta-page="{{ $paginator->currentPage() + 1 }}"
                data-meta-type="{{ $type }}"
                @disabled(! $paginator->hasMorePages())
            >
                {{ __('Next') }} <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </nav>
    @endif
@endif
