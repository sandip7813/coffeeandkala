@extends('layouts.journal')

@seo($meta ?? null, 'Journal — Coffee & Kala', 'Slow dispatches on coffee culture, craft, and everyday ritual — the Coffee & Kala Journal.', 'journal, coffee culture, essays, dispatches, Coffee & Kala')

@section('content')
    <div class="journal-page">
        @include('frontend.partials.journal.hero')
        @include('frontend.partials.journal.categories', ['categoryHighlights' => $categoryHighlights])
        @include('frontend.partials.journal.closing')
    </div>
@endsection
