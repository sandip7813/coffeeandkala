<section class="features-category-articles" aria-label="Articles in {{ $category['name'] }}">
    <div class="features-category-articles-inner">
        @foreach ($category['articles'] as $article)
            <article
                @class([
                    'features-story',
                    'features-story--featured' => $loop->first,
                    'features-reveal',
                    'features-reveal--up',
                ])
                style="--portal-delay: {{ $loop->index * 0.08 }}s"
            >
                <a href="{{ $article['href'] }}" class="features-story-link">
                    <div class="features-story-media-frame">
                        <div
                            class="features-story-media"
                            style="background-image: url('{{ $article['image'] }}')"
                            role="img"
                            aria-label="{{ $article['title'] }}"
                        ></div>
                    </div>
                    <div class="features-story-body">
                        <div class="features-article-meta">
                            <span class="features-article-tag">{{ $article['tag'] }}</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date_label'] }}</time>
                        </div>
                        <h2 class="features-story-title">{{ $article['title'] }}</h2>
                        <p class="features-story-excerpt">{{ $article['excerpt'] }}</p>
                        <span class="features-article-cta">
                            Read feature
                            <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
</section>
