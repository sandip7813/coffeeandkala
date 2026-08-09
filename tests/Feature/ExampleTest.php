<?php

use App\Support\BrandLogo;

test('the home page returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(BrandLogo::path(), false);
    $response->assertSee('The Home Of', false);
    $response->assertSee('Slow Publishing', false);
    $response->assertSee('Articles', false);
    $response->assertDontSee('Stay Close', false);
    $response->assertDontSee('footer-subscribe', false);
});

test('the home hero introduces slow publishing with clarify animation markup', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('hero-editorial', false);
    $response->assertSee('data-hero-reveal', false);
    $response->assertSee('hero-reveal', false);
    $response->assertSee('hero-scroll', false);
    $response->assertSee('hero-scroll-arrow', false);
    $response->assertSee('data-hero-scroll-to="sec-02"', false);
    $response->assertDontSee('href="#sec-featured"', false);
    $response->assertSee('SCROLL', false);
    $response->assertSee('images/home/hero-slow-publishing.jpg', false);
    $response->assertSee('ART. CULTURE. TRAVEL. LIFESTYLE. PEOPLE. PERCEPTION', false);
    $response->assertSee(
        'An independent editorial magazine where every piece is crafted with time, guided by intention and published with purpose',
        false
    );
});

test('the home page keeps the featured stories banner slider beneath the editorial hero', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('data-hero-banner', false);
    $response->assertSee('id="sec-03"', false);
    $response->assertSee('IN THE HEART OF JAIPUR', false);
    $response->assertSee('BREWED AT DAWN', false);
    $response->assertSee('Currently Pouring', false);
    $response->assertSee('data-target="sec-03"', false);
});

test('the home page places the thought of the day quotes between the hero and the banner slider', function () {
    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response->assertSuccessful();
    $response->assertSee('section-thought', false);
    $response->assertSee('thought-frame', false);
    $response->assertSee('data-thought-quote', false);
    $response->assertSee('data-thought-reveal', false);
    $response->assertSee('Thought of the day', false);
    $response->assertSee('images/home/paper-texture.jpg', false);
    $response->assertDontSee('images/home/quote-logo-watermark.png', false);
    $response->assertSee('You can\'t stop me unless I decide it\'s time to.', false);
    $response->assertSee('Coffee &amp; Kala', false);
    $response->assertSee('data-target="sec-02"', false);

    $heroPos = strpos($html, 'id="sec-01"');
    $quotePos = strpos($html, 'id="sec-02"');
    $bannerPos = strpos($html, 'id="sec-03"');
    $storiesPos = strpos($html, 'id="sec-04"');

    expect($heroPos)->not->toBeFalse()
        ->and($quotePos)->not->toBeFalse()
        ->and($bannerPos)->not->toBeFalse()
        ->and($storiesPos)->not->toBeFalse()
        ->and($heroPos)->toBeLessThan($quotePos)
        ->and($quotePos)->toBeLessThan($bannerPos)
        ->and($bannerPos)->toBeLessThan($storiesPos);
});

test('the home page includes three small stories without a carousel', function () {
    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response->assertSuccessful();
    $response->assertSee('section-three-stories', false);
    $response->assertSee('Three Small Stories', false);
    $response->assertSee('Unfold the Stories', false);
    $response->assertSee(route('features', absolute: false), false);
    $response->assertDontSee('Brief pours — short reads for a quieter pause.', false);
    $response->assertSee('The Window Seat in Udaipur', false);
    $response->assertSee('The Second Cup', false);
    $response->assertSee('Tableside Echoes', false);
    $response->assertSee('three-story-card', false);
    $response->assertSee('three-story-overlay', false);
    $response->assertSee('three-story-border', false);
    $response->assertDontSee('The Creative Pulse', false);
    $response->assertDontSee('section-giant-photo', false);

    preg_match('/id="sec-04".*?<\/section>/s', $html, $match);
    expect($match[0] ?? '')
        ->not->toContain('data-home-carousel')
        ->not->toContain('home-embla');
});

test('the home page includes a features unfolded article slider', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('section-visual-feature', false);
    $response->assertSee('data-visual-feature', false);
    $response->assertSee('Features Unfolded', false);
    $response->assertSee('Longer reads that ask you to stay.', false);
    $response->assertSee('At daybreak of the fifteenth day of my search', false);
    $response->assertSee('Continue reading', false);
    $response->assertSee('Unfold the Stories', false);
    $response->assertSee(route('features', absolute: false), false);
    $response->assertSee('visual-feature-panel', false);
    $response->assertDontSee('visual-feature-corner', false);
    $response->assertSee('visual-feature-rule', false);
    $response->assertSee('visual-feature-nav', false);
    $response->assertSee('visual-feature-header', false);
    $response->assertDontSee('The Story So Far', false);
    $response->assertDontSee('home-embla--gallery', false);
});

test('the home page includes a gallery of visual storytelling slider', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('section-gallery-story', false);
    $response->assertSee('data-gallery-story', false);
    $response->assertSee('Gallery of Visual Storytelling', false);
    $response->assertSee('Blue City Mornings', false);
    $response->assertSee('Wander the Exhibition', false);
    $response->assertSee('gallery-story-slide', false);
    $response->assertSee('gallery-story-nav', false);
    $response->assertSee('data-gallery-story-index', false);
    $response->assertSee(route('gallery', absolute: false), false);
    $response->assertSee('id="sec-06"', false);
    $response->assertSee('id="sec-07"', false);
    $response->assertSee('id="sec-08"', false);
});

test('the home page includes a from the journal editorial feature above visual poetry', function () {
    $response = $this->get(route('home'));
    $html = $response->getContent();

    $response->assertSuccessful();
    $response->assertSee('section-journal-feature', false);
    $response->assertSee('data-journal-feature', false);
    $response->assertSee('From the Journal', false);
    $response->assertSee('A Note from a Rainy Evening', false);
    $response->assertSee('Read the Journal', false);
    $response->assertSee('journal-feature-panel', false);
    $response->assertSee('journal-feature-media', false);
    $response->assertDontSee('journal-feature-corner', false);
    $response->assertSee(route('journal', absolute: false), false);
    $response->assertDontSee('home-embla--journal', false);
    $response->assertDontSee('section-journal-carousel', false);

    $journalPos = strpos($html, 'id="sec-07"');
    $poetryPos = strpos($html, 'id="sec-08"');

    expect($journalPos)->not->toBeFalse()
        ->and($poetryPos)->not->toBeFalse()
        ->and($journalPos)->toBeLessThan($poetryPos);

    expect($html)->toContain('section-journal-feature')
        ->and($html)->toContain('Gallery of visual poetry');
});

test('the home page header navigation omits the mailbox and conversations', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('The Mailbox', false);
    $response->assertSee('Articles', false);
    $response->assertSee('Blogs', false);
    $response->assertSee('The Bigger Picture', false);
    $response->assertSee('Worth Knowing', false);
    $response->assertSee('Chapters Over Coffee', false);
    $response->assertSee('href="javascript:void(0)"', false);
    $response->assertDontSee(route('journal', absolute: false).'#the-bigger-picture', false);
    $response->assertDontSee(route('journal', absolute: false).'#worth-knowing', false);
    $response->assertDontSee(route('journal', absolute: false).'#chapters-over-coffee', false);
    $response->assertDontSee('Conversations', false);
    $response->assertDontSee('Pour Your Thoughts', false);
});
