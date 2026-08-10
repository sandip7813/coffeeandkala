{{-- JOURNAL CATEGORIES — one dispatch from every shelf. Each spread keeps the
     alternating numbered-editorial structure (ghost numeral, category,
     headline, excerpt, a way onward), alternating image side per entry via
     CSS :nth-child(even), each kept as its own cozy Coffee Beige card. --}}
<section class="journal-categories" id="journalCategories" aria-labelledby="journalCategoriesTitle">
    <header class="journal-categories-head journal-reveal journal-reveal--up">
        <p class="journal-eyebrow">One Story, Every Shelf</p>
        <h2 id="journalCategoriesTitle" class="journal-categories-title">Explore by Category</h2>
        <p class="journal-categories-lead">
            {{ count($categoryHighlights) }} categories, {{ count($categoryHighlights) }} dispatches — start wherever calls to you.
        </p>
    </header>

    <ol class="journal-categories-list">
        @foreach ($categoryHighlights as $entry)
            <li
                class="journal-category journal-reveal journal-reveal--up"
                style="--portal-delay: {{ $loop->index * 0.08 }}s"
            >
                <article class="journal-category-card">
                    <figure class="journal-category-media">
                        <a href="{{ $entry['href'] }}" tabindex="-1" aria-hidden="true">
                            <img
                                src="{{ $entry['image'] }}"
                                alt=""
                                loading="lazy"
                                width="760"
                                height="950"
                                decoding="async"
                            >
                        </a>
                    </figure>

                    <div class="journal-category-copy">
                        <span class="journal-category-index" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <div class="journal-category-meta">
                            <a href="{{ route('journal.category', $entry['category_id']) }}" class="journal-category-tag">
                                {{ $entry['category_name'] }}
                            </a>
                            <span class="journal-category-meta-sep" aria-hidden="true">·</span>
                            <time datetime="{{ $entry['date'] }}" class="journal-category-date">{{ $entry['date_label'] }}</time>
                        </div>
                        <h3 class="journal-category-title">
                            <a href="{{ $entry['href'] }}">{{ $entry['title'] }}</a>
                        </h3>
                        <p class="journal-category-excerpt">{{ $entry['excerpt'] }}</p>
                        <a href="{{ $entry['href'] }}" class="journal-continued">
                            Read the story
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            </li>
        @endforeach
    </ol>
</section>
