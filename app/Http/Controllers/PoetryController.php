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
        $current = PoetryCatalog::find($poem);

        abort_if($current === null, 404);

        return view('frontend.poetry-show', [
            'poem' => $current,
            'total' => PoetryCatalog::count(),
            ...PoetryCatalog::neighbours($poem),
        ]);
    }
}
