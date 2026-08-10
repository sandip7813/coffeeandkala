{{-- The Bigger Picture — a cinematic photo-essay scroll. Big, full-width
     images do the talking; captions sit centered below rather than boxed
     alongside them, so this reads nothing like the small alternating
     Coffee Beige cards on the main Journal page. --}}
@include('frontend.partials.journal.category-crumb', ['category' => $category])

<header class="jc-picture-head journal-reveal journal-reveal--up">
    <p class="journal-eyebrow">Journal Category</p>
    <h1 class="jc-picture-title">{{ $category['name'] }}</h1>
    <p class="jc-picture-lead">
        Essays, portraits, and slow-looking dispatches — {{ $entries->total() }} {{ $entries->total() === 1 ? 'piece' : 'pieces' }} worth lingering on.
    </p>
</header>

<ol class="jc-picture-list">
    @foreach ($entries as $entry)
        <li class="jc-picture-item journal-reveal journal-reveal--up" style="--portal-delay: {{ $loop->index * 0.06 }}s">
            <article class="jc-picture-frame">
                <a href="{{ $entry['href'] }}" class="jc-picture-media">
                    <img src="{{ $entry['image'] }}" alt="" loading="lazy" width="1280" height="720" decoding="async">
                    <span class="jc-picture-count" aria-hidden="true">
                        {{ sprintf('%02d', ($entries->currentPage() - 1) * $entries->perPage() + $loop->iteration) }}
                        / {{ sprintf('%02d', $entries->total()) }}
                    </span>
                </a>

                <div class="jc-picture-caption">
                    <time datetime="{{ $entry['date'] }}" class="jc-picture-date">{{ $entry['date_label'] }}</time>
                    <h2><a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a></h2>
                    <p>{{ $entry['excerpt'] }}</p>
                    <a href="{{ $entry['href'] }}" class="journal-continued">
                        Read the story
                        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        </li>
    @endforeach
</ol>

@include('frontend.partials.journal.category-pagination', ['entries' => $entries, 'category' => $category])
@include('frontend.partials.journal.category-footer', ['category' => $category, 'categories' => $categories])
