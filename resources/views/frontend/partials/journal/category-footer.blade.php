<section class="jc-more journal-reveal journal-reveal--up" aria-label="More categories">
    <p class="jc-more-label">More from the Journal</p>
    <nav class="jc-more-links" aria-label="Other categories">
        @foreach ($categories as $sibling)
            @continue($sibling['id'] === $category['id'])
            <a href="{{ route('journal.category', $sibling['id']) }}" class="jc-more-link">{{ $sibling['name'] }}</a>
        @endforeach
    </nav>
    <a href="{{ route('journal') }}" class="journal-continued">
        Back to the Journal
        <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
    </a>
</section>
