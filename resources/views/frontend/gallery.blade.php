@extends('layouts.gallery')

@section('title', 'Gallery — Coffee & Kala')

@section('content')
    <div class="gallery-page">
        @include('frontend.partials.gallery.banner')
        @include('frontend.partials.gallery.intro', ['plates' => $plates])
        @include('frontend.partials.gallery.sheet', ['plates' => $plates])
        @include('frontend.partials.gallery.closing')
    </div>
@endsection

@php
    $galleriaData = collect($plates)->map(function (array $plate): array {
        $description = $plate['description'];

        if (! empty($plate['location'])) {
            $description .= ' — '.$plate['location'];
        }

        return [
            'image' => $plate['src'],
            'thumb' => $plate['thumb'],
            'title' => $plate['title'],
            'description' => $description,
        ];
    })->values();
@endphp

@push('scripts')
    <script type="application/json" id="galleryPlatesData">@json($galleriaData)</script>
@endpush
