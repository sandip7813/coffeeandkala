<?php

namespace App\Http\Controllers;

use App\Support\FeatureCatalog;
use Illuminate\Contracts\View\View;

class FeatureController extends Controller
{
    public function index(): View
    {
        return view('frontend.features', [
            'categories' => FeatureCatalog::all(),
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
