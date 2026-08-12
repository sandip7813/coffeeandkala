{{-- Worth Knowing — a practical, scannable grid of guide cards --}}
@include('frontend.partials.journal.category-crumb', ['category' => $category])

<header class="jc-guide-head journal-reveal journal-reveal--up">
    <p class="journal-eyebrow">Journal Category</p>
    <h1 class="jc-guide-title">{{ $category['name'] }}</h1>
    <p class="jc-guide-lead">
        Practical routes and honest tips — {{ $entries->total() }} {{ $entries->total() === 1 ? 'guide' : 'guides' }} to plan by.
    </p>
</header>

<ul class="jc-guide-grid">
    @foreach ($entries as $entry)
        <li class="jc-guide-card journal-reveal journal-reveal--up" style="--portal-delay: {{ $loop->index * 0.06 }}s">
            <a href="{{ $entry['href'] }}" class="jc-guide-media">
                <img src="{{ $entry['image'] }}" alt="" loading="lazy" width="640" height="480" decoding="async">
                <span class="jc-guide-pin"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Guide</span>
            </a>
            <div class="jc-guide-body">
                <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
                <h2><a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a></h2>
                <p>{{ Str::limit($entry['excerpt'], 80) }}</p>
                <a href="{{ $entry['href'] }}" class="journal-continued">
                    Read the guide
                    <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                </a>
            </div>
        </li>
    @endforeach
</ul>

@include('frontend.partials.journal.category-pagination', ['entries' => $entries, 'category' => $category])
@include('frontend.partials.journal.category-footer', ['category' => $category, 'categories' => $categories])
