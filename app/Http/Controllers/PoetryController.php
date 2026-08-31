<?php

namespace App\Http\Controllers;

use App\Support\PoetryCatalog;
use Illuminate\Contracts\View\View;

class PoetryController extends Controller
{
    public function index(): View
    {
        return view('frontend.poetry', [
            'poems' => PoetryCatalog::all(),
        ]);
    }

    public function show(string $poem): View
    {
        $poems = array_map(
            fn (array $p): array => [...$p, 'nearby' => PoetryCatalog::nearby($p['slug'], 3)],
            PoetryCatalog::all(),
        );
        $current = PoetryCatalog::find($poem);

        abort_if($current === null, 404);

        return view('frontend.poetry-show', [
            'poems' => $poems,
            'current' => $current,
        ]);
    }
}
