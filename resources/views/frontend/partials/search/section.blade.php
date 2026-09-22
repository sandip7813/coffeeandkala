@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|null $paginator */
@endphp

@if ($paginator && $paginator->total() > 0)
    <section class="search-section search-tab-panel" id="search-{{ $sectionKey }}" data-tab-panel="{{ $sectionKey }}">
        <div class="search-section-heading">
            <h2>{{ $label }}</h2>
            <span class="search-section-count">{{ $paginator->total() }} {{ $paginator->total() === 1 ? 'result' : 'results' }}</span>
        </div>

        <div class="search-section-grid">
            @foreach ($paginator as $item)
                <a href="{{ $item['url'] }}" class="search-card">
                    <span class="search-card-thumb">
                        @if ($item['thumbnail'])
                            <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" loading="lazy">
                        @endif
                    </span>
                    <span class="search-card-body">
                        <span class="search-card-title">{{ $item['title'] }}</span>
                        @if (! empty($item['description']))
                            <span class="search-card-description">{{ $item['description'] }}</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>

        @include('frontend.partials.search.pagination', ['paginator' => $paginator, 'label' => $label, 'sectionKey' => $sectionKey])
    </section>
@endif
