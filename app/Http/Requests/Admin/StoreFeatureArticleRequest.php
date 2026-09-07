<?php

namespace App\Http\Requests\Admin;

use App\Models\Article;

class StoreFeatureArticleRequest extends StoreArticleRequest
{
    public function type(): string
    {
        return Article::TYPE_FEATURE;
    }
}
