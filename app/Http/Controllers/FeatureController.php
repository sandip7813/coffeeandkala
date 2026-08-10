<?php

namespace App\Http\Controllers;

use App\Support\FeatureCatalog;
use Illuminate\Contracts\View\View;

class FeatureController extends Controller
{
    public function index(): View
    {
        $entries = collect(FeatureCatalog::edition())
            ->map(fn (array $entry): array => [
                ...$entry,
                'href' => route('features.show', $entry['category_id']),
            ])
            ->all();

        return view('frontend.features', [
            'entries' => $entries,
        ]);
    }

    public function show(string $category): View
    {
        $current = FeatureCatalog::find($category);

        abort_if($current === null, 404);

        return view('frontend.features-category', [
            'category' => $current,
            'categories' => FeatureCatalog::all(),
        ]);
    }
}
