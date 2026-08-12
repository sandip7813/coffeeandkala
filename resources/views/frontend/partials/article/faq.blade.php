{{-- Custom accordion (not <details>) so article.js can animate the
     open/close with a real height transition instead of the instant native
     snap. --}}
<section id="faq" class="article-faq" aria-labelledby="articleFaqHeading">
    <h2 id="articleFaqHeading">Frequently Asked Questions</h2>

    <div class="article-faq-list" data-faq>
        @foreach ($content['faq'] as $item)
            <div class="article-faq-item">
                <button
                    type="button"
                    class="article-faq-question"
                    id="articleFaqQuestion{{ $loop->index }}"
                    aria-expanded="false"
                    aria-controls="articleFaqAnswer{{ $loop->index }}"
                    data-faq-trigger
                >
                    <span>{{ $item['question'] }}</span>
                    <i class="fa-solid fa-plus article-faq-icon" aria-hidden="true"></i>
                </button>
                <div
                    class="article-faq-answer-wrap"
                    id="articleFaqAnswer{{ $loop->index }}"
                    role="region"
                    aria-labelledby="articleFaqQuestion{{ $loop->index }}"
                    data-faq-panel
                >
                    <div class="article-faq-answer">
                        <p>{{ $item['answer'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
