{{-- Editorial threshold above the collection — sets the "every poem is a
     room" reading of the door motif before the doors themselves appear. --}}
<section class="poetry-intro" aria-labelledby="poetryIntroTitle">
    <div class="poetry-intro-inner poetry-reveal poetry-reveal--up">
        <p class="poetry-eyebrow">All Poems</p>
        <h2 id="poetryIntroTitle" class="poetry-intro-title">
            A home in every poem<br>
            that has found <em>its way to paper</em>
        </h2>
        <span class="poetry-sprig" aria-hidden="true">
            <svg viewBox="0 0 120 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 22c30-14 86-14 116 0" stroke="currentColor" stroke-width="1" />
                <path d="M40 22c2-8 8-13 14-15M40 22c-1-9-9-15-16-16M80 22c-2-8-8-13-14-15M80 22c1-9 9-15 16-16" stroke="currentColor" stroke-width="1" />
                <circle cx="60" cy="22" r="2.5" fill="currentColor" />
            </svg>
        </span>
        <div class="poetry-intro-copy">
            <p>
                Every poem here keeps its own room — its own light, its own hour of the day.
                Nothing is rushed. Nothing is explained. You are simply invited to open a door
                and stand inside a feeling for as long as it takes to read.
            </p>
            <p>
                Take your time. Some rooms want to be entered slowly, and read again before
                the door closes behind you.
            </p>
        </div>
        <p class="poetry-intro-aside">
            {{ count($poems) }} poems, each a door left ajar
        </p>
    </div>
</section>
