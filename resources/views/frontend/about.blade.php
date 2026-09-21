@extends('layouts.about')

@seo($meta ?? null, 'Our Story — Coffee & Kala', 'The story behind Coffee & Kala — how it started, and what it stands for.', 'our story, about, Coffee & Kala')

@section('content')
    <div class="about-page">
        @include('frontend.partials.about.banner')

        {{-- Admin-authored content (Admin > Our Story), same section
             mechanism/partial/styling as Features/Journals article
             bodies — see App\Support\ArticleContentBuilder. --}}
        @if (!empty($sections))
            <div class="article-theme article-theme--our-story">
                <div class="article-band article-lower">
                    <div class="article-sections">
                        @foreach ($sections as $section)
                            @include('frontend.partials.article.section', ['section' => $section])
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Closing invitation --}}
        <section class="about-closing">
            <div class="about-closing-inner about-reveal about-reveal--up">
                <span class="about-ornament about-ornament--light" aria-hidden="true"></span>
                <x-one-liner page="our-story" class="about-closing-quote" />
            </div>
        </section>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/article.css'])
@endpush

@push('scripts')
    @vite(['resources/js/article.js'])
@endpush
