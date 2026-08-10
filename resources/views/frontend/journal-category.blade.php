@extends('layouts.journal')

@section('title', $category['name'].' — Journal — Coffee & Kala')

@section('content')
    <div
        @class([
            'journal-page',
            'jc-page',
            'jc-theme--'.$category['id'],
        ])
    >
        @include('frontend.partials.journal.themes.'.$category['id'], [
            'category' => $category,
            'categories' => $categories,
            'entries' => $entries,
        ])
    </div>
@endsection
