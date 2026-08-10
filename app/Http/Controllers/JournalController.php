<?php

namespace App\Http\Controllers;

use App\Support\JournalCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class JournalController extends Controller
{
    public function index(): View
    {
        return view('frontend.journal', [
            'categoryHighlights' => JournalCatalog::categoryHighlights(),
        ]);
    }

    public function show(Request $request, string $category): View
    {
        $current = JournalCatalog::findCategory($category);

        abort_if($current === null, 404);

        $entries = JournalCatalog::forCategory($category);
        $perPage = 6;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginator = new LengthAwarePaginator(
            array_slice($entries, ($page - 1) * $perPage, $perPage),
            count($entries),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('frontend.journal-category', [
            'category' => $current,
            'categories' => JournalCatalog::categories(),
            'entries' => $paginator,
        ]);
    }
}
