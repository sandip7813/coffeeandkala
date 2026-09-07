<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Meta;
use App\Support\ArticleContentBuilder;
use Illuminate\Contracts\View\View;

class OurStoryController extends Controller
{
    public function index(): View
    {
        $article = Article::query()
            ->ofType(Article::TYPE_OUR_STORY)
            ->active()
            ->with('sections.image', 'sections.galleryImages', 'sections.videoCompanionImage', 'faqs')
            ->first();

        return view('frontend.about', [
            'sections' => $article ? ArticleContentBuilder::build($article)['sections'] : [],
            'meta' => Meta::forPage('our-story'),
        ]);
    }
}
