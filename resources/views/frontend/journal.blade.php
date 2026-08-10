@extends('layouts.journal')

@section('title', 'Journal — Coffee & Kala')

@section('content')
    <div class="journal-page">
        @include('frontend.partials.journal.hero')
        @include('frontend.partials.journal.categories', ['categoryHighlights' => $categoryHighlights])
        @include('frontend.partials.journal.closing')
    </div>
@endsection
