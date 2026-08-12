<section id="introduction" class="article-intro" aria-label="Introduction">
    @foreach ($content['intro'] as $paragraph)
        <p>{{ $paragraph }}</p>
    @endforeach
</section>
