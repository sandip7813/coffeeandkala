@extends('layouts.article')

@section('title', $article['title'].' — '.$sourceLabel.' — Coffee & Kala')

@section('content')
    <div
        @class([
            'article-theme',
            'article-theme--'.$source,
            'features-theme features-theme--'.$category['id'] => $source === 'features',
            'journal-page jc-theme--'.$category['id'] => $source === 'journal',
        ])
    >
        @php $sidebarPosition = $sidebarPosition ?? 'right'; @endphp

        <article class="article-primary">
            <div class="article-band">
                @include('frontend.partials.article.breadcrumb')
                @include('frontend.partials.article.title-mast')
            </div>

            {{-- Introduction, paired with the article's featured image —
                 above and outside the sidebar row, so the sidebar (Explore
                 the Sections / Recently Published) sits below this section
                 rather than beside it. --}}
            <div class="article-band article-intro-row">
                <div class="article-intro-row-text">
                    @include('frontend.partials.article.intro')
                </div>

                <figure class="article-featured-media">
                    <span class="article-featured-media-accent" aria-hidden="true"></span>
                    <a href="{{ $article['image'] }}" data-fancybox="article-featured">
                        <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy">
                    </a>
                </figure>
            </div>

            {{-- Two real grid columns: column 1 carries the Table of
                 Contents and Editor's Note, left aligned. Column 2 is the
                 Explore the Sections / Recently Published panel, and can
                 sit on either side per category. --}}
            <div @class(['article-upper', 'article-upper--sidebar-left' => $sidebarPosition === 'left'])>
                <div class="article-upper-main">
                    @include('frontend.partials.article.toc')
                    @include('frontend.partials.article.editors-note')
                </div>

                <aside class="article-sidebar" aria-label="More from Coffee & Kala">
                    @include('frontend.partials.article.sidebar')
                </aside>
            </div>

            {{-- Lower section: a single full-width, left-aligned column —
                 Words & Images, FAQ, Author's Note. --}}
            <div class="article-band article-lower">
                <div class="article-sections">
                    @foreach ($content['sections'] as $section)
                        @include('frontend.partials.article.section', ['section' => $section])
                    @endforeach
                </div>

                @include('frontend.partials.article.faq')
                @include('frontend.partials.article.authors-note')
                @include('frontend.partials.article.footer-crosslinks')
            </div>
        </article>
    </div>
@endsection
