{{-- Chapters Over Coffee — an intimate single-column diary, one entry per "page" --}}
@include('frontend.partials.journal.category-crumb', ['category' => $category])

<header class="jc-diary-head journal-reveal journal-reveal--up">
    <p class="journal-eyebrow">Journal Category</p>
    <h1 class="jc-diary-title">{{ $category['name'] }}</h1>
    <p class="jc-diary-lead">
        Slow mornings and long roads, told one cup at a time — {{ $entries->total() }} {{ $entries->total() === 1 ? 'chapter' : 'chapters' }} and counting.
    </p>
</header>

<ol class="jc-diary-list">
    @foreach ($entries as $entry)
        <li class="jc-diary-entry journal-reveal journal-reveal--up" style="--portal-delay: {{ $loop->index * 0.06 }}s">
            <div class="jc-diary-date">
                <time datetime="{{ $entry['date'] }}">{{ $entry['date_label'] }}</time>
            </div>

            <a href="{{ $entry['href'] }}" class="jc-diary-media" tabindex="-1" aria-hidden="true">
                <img src="{{ $entry['image'] }}" alt="" loading="lazy" width="200" height="200" decoding="async">
            </a>

            <div class="jc-diary-copy">
                <h2 class="jc-diary-entry-title">
                    <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                </h2>
                <p class="jc-diary-excerpt">{{ Str::limit($entry['excerpt'], 80) }}</p>
                <a href="{{ $entry['href'] }}" class="journal-continued">
                    Read the story
                    <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                </a>
            </div>
        </li>
    @endforeach
</ol>

@include('frontend.partials.journal.category-pagination', ['entries' => $entries, 'category' => $category])
@include('frontend.partials.journal.category-footer', ['category' => $category, 'categories' => $categories])
