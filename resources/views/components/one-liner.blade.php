@props(['page' => null, 'type' => null, 'slug' => null])

@php
    $text = $page !== null
        ? \App\Support\PageOneLiner::forPage($page)
        : \App\Support\PageOneLiner::forCategory($type, $slug);
@endphp

@if ($text !== null)
    <blockquote {{ $attributes }}>{!! nl2br(e($text)) !!}</blockquote>
@endif
