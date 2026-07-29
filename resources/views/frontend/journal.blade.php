@extends('layouts.journal')

@section('title', 'Journal — Coffee & Kala')

@section('content')
    <div class="journal-page">
        @include('frontend.partials.journal.banner')
        @include('frontend.partials.journal.intro')
        @include('frontend.partials.journal.edition', ['entries' => $entries])
        @include('frontend.partials.journal.closing')
    </div>
@endsection
