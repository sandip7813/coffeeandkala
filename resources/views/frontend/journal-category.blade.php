@extends('layouts.journal')

@seo($meta ?? null, $category['name'].' — Journal — Coffee & Kala', $category['name'].' — dispatches from the Coffee & Kala Journal.', $category['name'].', journal, Coffee & Kala')

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
