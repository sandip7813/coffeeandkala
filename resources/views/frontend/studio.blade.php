@extends('layouts.studio')

@section('title', 'Studio — Coffee & Kala')

@section('content')
    <div class="studio-page">
        @include('frontend.partials.studio.banner')
        @include('frontend.partials.studio.intro', ['works' => $works])
        @include('frontend.partials.studio.sheet', ['works' => $works])
        @include('frontend.partials.studio.closing')
    </div>
@endsection

@php
    $galleriaData = collect($works)->map(function (array $work): array {
        $description = $work['description'];

        if (! empty($work['medium'])) {
            $description .= ' — '.$work['medium'];
        }

        return [
            'image' => $work['src'],
            'thumb' => $work['thumb'],
            'title' => $work['title'],
            'description' => $description,
        ];
    })->values();
@endphp

@push('scripts')
    <script type="application/json" id="galleryPlatesData">@json($galleriaData)</script>
@endpush
