@extends('layouts.about')

@section('title', 'Our Story — Coffee & Kala')

@section('content')
    <div class="about-page">
        @include('frontend.partials.about.banner')

        <section class="about-page-title">
            <h1>Our Story</h1>
        </section>

        {{-- Image left · content right --}}
        <section class="about-split about-split--image-left">
            <figure class="about-frame about-reveal about-reveal--media">
                <span class="about-frame-accent" aria-hidden="true"></span>
                <a href="{{ asset('images/about/1-left.png') }}" data-fancybox="about-story">
                    <img src="{{ asset('images/about/1-left.png') }}" alt="The creative pulse behind Coffee & Kala" loading="lazy">
                </a>
            </figure>

            <div class="about-split-copy about-reveal about-reveal--copy about-reveal-delay-2">
                <h2 class="about-heading about-heading--pulse">THE CREATIVE PULSE BEHIND COFFEE &amp; KALA</h2>
                <p class="about-copy about-copy--pulse">
                    <i><strong>Kala</strong></i> - a word of <i><strong>Hindi origin</strong></i>, rooted in the Indian
                    consciousness, <i><strong>signifies art</strong></i> but carries an even
                    deeper meaning. It's the gift we're blessed with, the stories we inherit, and the expression we refine.
                    Whether it's the poetry in a photograph, the stillness in a raga (music), or the truth in a sentence,
                    kala is the language of life as it is felt. Holding onto this vision, the founder aimed to unite the
                    beauty, culture, and vastness of two languages under the sun. A blend that seeks to expand the canvas of
                    expression, embracing ideas and subjects on a broader and more inclusive level.
                </p>
                <p class="about-copy about-copy--pulse">
                    Now, every journey begins with a thirst, with a spark of curiosity. Interestingly, for the founder, that
                    spark was coffee. Black, bold, and quiet, it followed her into her thoughts, clarity, and moments of
                    creative chaos. Each cup stirred an idea, and gradually, those ideas took shape as words, brushstrokes,
                    breakthroughs, and aspirations.
                </p>

            </div>
        </section>

        {{-- Content left · image right --}}
        <section class="about-split about-split--image-right">
            <div class="about-split-copy about-reveal about-reveal--copy">
                <p class="about-copy">
                    The founder once believed that stepping into the media industry as a writer and bagging positions at
                    renowned brands was the dream. And for a while, it was. But between working morning deadlines, skipped
                    meals, stretched hours, years of neglected health, and the constant chase to meet someone else's vision,
                    something within her began to shift. The titles were impressive, the paycheques steady, yet the
                    fulfilment was fleeting. She found herself pouring energy into building empires that weren't hers, while
                    her own dreams sat waiting in the corner. So, pocketing six years of the ins and outs of the field,
                    along with the corporate exposure that she could not picture herself breathing in, she made the boldest
                    decision of her life: to stop watering someone else's target and start tending to her own calling.
                </p>
                <p class="about-copy">
                    She'd often find herself staring into the silence between assignments, sipping coffee that had long gone
                    cold. And strangely, in those moments of pause, something always stirred. A poem. A memory. A melody she
                    hadn't sung in years. That's when she knew: the kala needed its own corner. Not to be validated, but to
                    be witnessed.
                </p>
            </div>

            <figure class="about-frame about-frame--offset about-reveal about-reveal--media about-reveal-delay-2">
                <span class="about-frame-accent" aria-hidden="true"></span>
                <a href="{{ asset('images/about/2-right.png') }}" data-fancybox="about-story">
                    <img src="{{ asset('images/about/2-right.png') }}" alt="Open notebook with handwritten notes and a pen"
                        loading="lazy">
                </a>
            </figure>
        </section>

        {{-- Image up · content down --}}
        <section class="about-stack about-stack--image-top">
            <figure class="about-cinema about-reveal about-reveal--media">
                <a href="{{ asset('images/about/3-horizontal.png') }}" data-fancybox="about-story">
                    <img src="{{ asset('images/about/3-horizontal.png') }}" alt="Artist palette and brushes on a wooden table"
                        loading="lazy">
                </a>
            </figure>

            <div class="about-stack-copy about-reveal about-reveal--up about-reveal-delay-2">
                <p class="about-copy about-copy--centered">
                    Every time the founder settled into her chair with a freshly brewed cup of coffee, ideas flowed, but so
                    did the noise. Doubts, worries and presumptions would crowd the mind just as quickly as inspiration did.
                    Who will read this? Who will not? Is it too long? Should I take out another word? Does this make sense?
                    Will it make sense to them? What should I change to draw people closer? What could I be doing better?
                    Should I alter my style? Should I adapt to something else? The questions were endless, and for a while,
                    they were loud enough to make her question the very thing she had always trusted: her own voice.
                    Somewhere along the way, however, she realised that without standing on your own ground, you cannot
                    build a foundation that is truly yours. The more time you surrender to the What, How, Who, If and But,
                    the more quietly you begin moving backwards without even noticing it. It is like walking on a treadmill
                    at the same pace, feeling the motion beneath your feet, while remaining exactly where you started.
                    Readers will engage, viewers will respond, collaborations will be welcomed, conversations will be heard,
                    and criticism will be considered, but none of it should come at the cost of compromising the height of
                    one's craft. Because it takes time to sit in the same chair, across the same table, returning to an idea
                    again and again, scratching at the edges of thought until something honest, distinctive and worth
                    keeping finally emerges. It takes time to ask how, in an age of artificial intelligence and instant
                    production, a piece of work can still carry the unmistakable weight of a human mind. The founder was a
                    writer long before artificial intelligence became a household phrase. She was practising the art of
                    observation, intellect, language and human connection long before machines were being taught to imitate
                    the nuances of human expression. A chair, a table, a flickering lamp and hundreds of cups of coffee have
                    witnessed more carefully framed sentences, restless brainstorming, the merging of languages,
                    uncomfortable questions, unexpected ideas and thought-provoking depths than any machine could ever
                    measure.
                </p>
            </div>
        </section>

        {{-- Content up · image down --}}
        <section class="about-stack about-stack--content-top">
            <div class="about-stack-copy about-reveal about-reveal--up">
                <h2 class="about-heading">The Vision Poured Into Every Page</h2>
                <p class="about-copy about-copy--centered">
                    Coffee & Kala looks forward to being your everyday escape: a coffee-table magazine made for everyone, no
                    matter what's in their cup or their wallet.
                </p>
                <p class="about-copy about-copy--centered">
                    Whether you're sipping chai on a monsoon evening, nursing an espresso at dawn, or flipping through pages
                    during a lazy Sunday afternoon, this space is crafted for you. For the dreamers planning a quiet
                    getaway. For the writers chasing the right sentence. For the readers needing just one line to start or
                    save their day. We come from the middle. The planning, the budgeting, the “maybe next year” kind of
                    lives; and that's exactly why Coffee & Kala refuses to gatekeep experience.
                </p>
                <p class="about-copy about-copy--centered">
                    Here, you'll find travel that's aspirational but also accessible. Visuals that don't just decorate, but
                    tell stories. Articles that don't just inform, but inspire. That, deserves a window to the world even if
                    it's through one heartfelt photograph, one honest poem, one deeply relatable story. And more than
                    anything, we believe in building a community. A circle where readers become writers, writers become
                    wanderers, and wanderers come back with stories the world needs to hear.
                </p>
                <div class="about-pillars" aria-hidden="true">
                    <span>Readers</span>
                    <span>Writers</span>
                    <span>Wanderers</span>
                </div>
            </div>

            <figure class="about-cinema about-cinema--soft about-reveal about-reveal--media about-reveal-delay-2">
                <a href="{{ asset('images/about/4-horizontal.png') }}" data-fancybox="about-story">
                    <img src="{{ asset('images/about/4-horizontal.png') }}"
                        alt="Friends gathered around a table sharing conversation" loading="lazy">
                </a>
            </figure>
        </section>

        {{-- Closing invitation --}}
        <section class="about-closing">
            <div class="about-closing-inner about-reveal about-reveal--up">
                <span class="about-ornament about-ornament--light" aria-hidden="true"></span>
                <blockquote class="about-closing-quote">
                    Every cup holds a story.<br>What's yours?
                </blockquote>
            </div>
        </section>
    </div>
@endsection
