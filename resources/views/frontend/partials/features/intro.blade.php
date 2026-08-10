<section class="features-masthead" aria-labelledby="featuresIntroTitle">
    <div class="features-masthead-inner features-reveal features-reveal--up">
        <div class="features-dateline">
            <span>Edition {{ now()->format('W') }}</span>
            <span aria-hidden="true">·</span>
            <time datetime="{{ now()->toDateString() }}">{{ now()->format('l, F j, Y') }}</time>
            <span aria-hidden="true">·</span>
            <span>Eight Chapters</span>
        </div>

        <div class="features-masthead-rule" aria-hidden="true"></div>

        <p class="features-eyebrow">Stories worth wandering into</p>
        <h2 id="featuresIntroTitle" class="features-masthead-title">Features</h2>
        <p class="features-masthead-tagline">Discover. Inspire. Experience.</p>

        <div class="features-masthead-rule features-masthead-rule--double" aria-hidden="true"></div>

        <p class="features-masthead-lead">
            Longer reads pulled from every chapter of the archive — art and culture, journeys,
            budget trails, luxury escapes, and the roads not yet on any map.
        </p>
    </div>
</section>
