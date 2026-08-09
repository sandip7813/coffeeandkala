{{-- SECTION 02: Thought of the day — Coffee & Kala Quotes --}}
<section
    id="sec-02"
    class="section-thought"
    aria-label="Thought of the day"
    style="--thought-paper: url('{{ asset('images/home/paper-texture.jpg') }}')"
>
    <svg class="thought-svg-defs" aria-hidden="true" focusable="false">
        <filter id="thought-crumple" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
            <feTurbulence type="fractalNoise" baseFrequency="0.02 0.026" numOctaves="5" seed="7" result="noise" />
            <feDiffuseLighting in="noise" lighting-color="#fff8ec" surfaceScale="1.8" diffuseConstant="1.15" result="light">
                <feDistantLight azimuth="235" elevation="55" />
            </feDiffuseLighting>
        </filter>
    </svg>

    {{-- Shadow lives on this outer wrapper, not on .thought-frame — a clip-path and a
         drop-shadow filter can't share one element, since clip-path is applied after
         the filter and would cut the cast shadow off at the clipped edge. --}}
    <div class="thought-frame-shadow">
        <div class="thought-frame">
            <span class="thought-frame-texture" aria-hidden="true"></span>
            <span class="thought-frame-tape" aria-hidden="true"></span>

            <figure class="thought-quote" data-thought-quote>
                <div class="thought-quote-body">
                    <span class="thought-quote-mark thought-quote-mark--open" aria-hidden="true">
                        <i class="fa-solid fa-quote-left"></i>
                    </span>

                    <blockquote class="thought-quote-text" data-thought-reveal>
                        You can't stop me unless I decide it's time to.
                    </blockquote>

                    <span class="thought-quote-mark thought-quote-mark--close" aria-hidden="true">
                        <i class="fa-solid fa-quote-right"></i>
                    </span>
                </div>

                <figcaption class="thought-quote-byline">
                    <span class="thought-quote-icon" aria-hidden="true">&#9997;&#65039;</span>
                    <span class="thought-quote-brand">Coffee &amp; Kala</span>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
